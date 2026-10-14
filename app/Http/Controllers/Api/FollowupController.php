<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Followup;
use App\Models\Notification;
use App\Models\Patients;
use App\Models\User;
use App\Models\Setting;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class FollowupController extends Controller
{

     public function countByDoctor(Request $request)
    {
        $branchId = $request->branch_id;
        $today = Carbon::today();

        if (!$branchId) {
            return response()->json([
                'status' => false,
                'message' => 'Branch ID is required'
            ], 400);
        }

        $followups = Followup::selectRaw('doctor_id, COUNT(*) as followup_count')
            ->where('branch_id', $branchId)
            ->whereDate('date', $today)
            ->groupBy('doctor_id')
            ->get();

        $data = $followups->map(function ($item) {
            $doctor = User::find($item->doctor_id);
            return [
                'doctor_id' => $item->doctor_id,
                'doctor_name' => $doctor ? $doctor->fullname : 'Unknown',
                'followup_count' => $item->followup_count,
                'profile' => $doctor && $doctor->profile ? asset($doctor->profile) : asset('admin/assets/img/img1.png')
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
    private function getAuthenticatedPatient(): ?Patients
    {
        $user = Auth::user();

        if (optional($user?->role)->name !== 'Patient') {
            return null;
        }

        return Patients::where('login_patient_id', $user->id)->first();
    }

    private function getScopedFollowupOrFail(int $id): Followup
    {
        $query = Followup::with(['patient', 'doctor', 'treatment', 'branch']);
        $patient = $this->getAuthenticatedPatient();

        if ($patient) {
            $query->where('patient_id', $patient->id)
                ->where('branch_id', $patient->branch_id);
        }

        return $query->findOrFail($id);
    }

    private function notifyFollowupParticipants(
        Followup $followup,
        string $doctorMessage,
        string $patientMessage,
        string $adminMessage
    ): void
    {
        $sharedRecipientIds = Notification::userIdsForRoles(
            ['SuperAdmin', 'Admin', 'Receptionist'],
            $followup->branch_id,
            [$followup->doctor_id]
        );

        if ($followup->doctor_id) {
            Notification::store(
                $doctorMessage,
                $followup->doctor_id,
                $followup->id,
                'followup',
                auth()->id(),
                $followup->patient_id
            );
        }

        $patient = Patients::find($followup->patient_id);
        $patientUserId = $patient?->login_patient_id;

        if ($patientUserId) {
            Notification::store(
                $patientMessage,
                $patientUserId,
                $followup->id,
                'followup',
                auth()->id(),
                $followup->patient_id
            );
        }

        Notification::notifyUsers(
            $adminMessage,
            $sharedRecipientIds,
            $followup->id,
            'followup',
            auth()->id(),
            $followup->patient_id
        );
    }
    


    public function followupPdf(Request $request, $id)
    {
        try {
            // ✅ Load followup with relations
            $followup = $this->getScopedFollowupOrFail($id)->loadMissing([
                'patient',
                'followup_doctor',
                'treatment'
            ]);

            // ✅ Fetch clinic details
            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
                'clinic_city',
                'clinic_state',
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo);

            // ✅ Data for Blade
            $data = [
                'date'          => now()->format('d-m-Y'),
                'patient'       => $followup->patient,
                'followup'      => $followup,
                'doctor'        => $followup->followup_doctor,
                'treatment'     => $followup->treatment,
                'clinic_logo'   => $clinic_logo_path,
                'clinic_name'   => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone'  => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email'  => $settings['clinic_email'] ?? 'clinic@example.com',
            ];

            // ✅ Generate PDF
            $pdf = Pdf::loadView('followup.followup_pdf', $data)
                ->setPaper('A4', 'portrait');

            $folder   = 'followups/';
            $filename = "Followup_{$followup->id}.pdf";

            // ✅ Save in storage/app/public/followups/
            $path = storage_path('app/public/' . $folder . $filename);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            $pdf->save($path);

            // ✅ Public URL (with /public/ like your demo)
            $fileUrl = url('public/storage/' . $folder . $filename);

            // 👉 If API/Postman → return JSON
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Followup PDF generated successfully.',
                    'file_url'  => $fileUrl,
                    'file_name' => $filename,
                ]);
            }

            // 👉 Otherwise (Browser) → download directly
            return response()->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Followup PDF generation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $patient = $this->getAuthenticatedPatient();

        $followups = Followup::with(['patient', 'doctor', 'treatment'])
            ->orderBy('date', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $followups->where('branch_id', $branchId);
        }

        if ($patient) {
            $followups->where('patient_id', $patient->id)
                ->where('branch_id', $patient->branch_id);
        }

        $user = Auth::user();
        if ($user && in_array($user->role->name, ['Staff', 'Doctor'])) {
            $followups->where('doctor_id', $user->id);
        }

        $followups = $followups->get();

        if ($followups->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No follow-ups found to export.'
            ]);
        }

        $filename = 'followups_export_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        // CSV Header
        fputcsv($file, [
            'ID',
            'Patient Name',
            'Doctor Name',
            'Treatment Name',
            'Follow-up Date',
            'Follow-up Type',
            'Follow-up Update',

            'Created At'
        ]);

        $sr = 1;
        foreach ($followups as $followup) {
            fputcsv($file, [
                $sr++,
                $followup->patient->fullname ?? '',
                $followup->doctor->fullname ?? '',
                $followup->treatment->name ?? '',
                $followup->date,
                $followup->followup_type,
                $followup->followup_update,

                $followup->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Follow-ups exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }







    public function index(Request $request)
    {
        $user = Auth::user(); // Get the logged-in user
        $patient = $this->getAuthenticatedPatient();

        $query = Followup::with(['patient', 'doctor', 'treatment', 'branch']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($patient) {
            $query->where('patient_id', $patient->id)
                ->where('branch_id', $patient->branch_id);
        }

        if (in_array($user->role->name, ['Staff', 'Doctor'])) {
            $query->where('doctor_id', $user->id);
        }
        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable || $request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);

            if ($hasDataTable) {
                $length = (int) $request->input('length', 10);
                if ($length === -1) {
                    $length = 0;
                }
                $perPage = $length > 0 ? $length : 10;
                $start = (int) $request->input('start', 0);
                $page = (int) floor($start / $perPage) + 1;
            }

            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $hasDataTable
                ? $request->input('search.value', $request->input('search'))
                : $request->input('search');

            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('date', 'like', '%' . $searchValue . '%')
                        ->orWhere('followup_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('followup_update', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('treatment', function ($t) use ($searchValue) {
                            $t->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && (int) $request->input('length') === -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $followups = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'followups' => $followups,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $followups = $query->orderBy('id', 'desc')->get();


        // $followups = Followup::with(['patient', 'doctor', 'treatment'])
        //     ->orderBy('id', 'desc') // Order followups by ID in descending order
        //     ->get();

        // dd($followups);

        return response()->json([
            'status'    => true,
            'followups' => $followups
        ], 200);
    }





    public function createFollowup(Request $request, SmsService $smsService)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to create follow-ups.'
            ], 403);
        }

        $loggedInUserId = auth()->id();

       
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'required|exists:treatments,id',
            'doctor_id' => 'required|exists:user,id', // Fixed table name
            'date' => 'required|date',
            'followup_update' => 'nullable|string',
            'followup_type' => 'required|string',
        ]);

       
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        // Create follow-up
        $followup = Followup::create([
            'user_id' => $loggedInUserId, // Ensure user_id is set
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'treatment_id' => $request->treatment_id,
            'date' => $request->date,
            'followup_type' => $request->followup_type,
            'followup_update' => $request->followup_update,
        ]);

        if ($followup) {
            // Get patient full name
            $patient = \App\Models\Patients::find($request->patient_id);
            $patientName = ucfirst($patient->fullname ?? '');
            $patientUserId = $patient?->login_patient_id;
            $doctor = User::find($request->doctor_id);
            $doctorFullName = $doctor->fullname ?? '';
            $actorLabel = optional(auth()->user()->role)->name === 'Patient'
                ? "Patient {$patientName}"
                : (auth()->user()->fullname ?? auth()->user()->name ?? 'Staff');

            // Format follow-up date
            $followupDateTime = Carbon::createFromFormat('Y-m-d', $request->date)->format('d/m/Y');

            $this->notifyFollowupParticipants(
                $followup,
                "Dr {$doctorFullName} You have a New Follow-up scheduled! For patient {$patientName} on {$followupDateTime}.",
                "Dear {$patientName}, your follow-up with Dr. {$doctorFullName} has been scheduled on {$followupDateTime}.",
                "{$actorLabel} created a follow-up for patient {$patientName} with Dr. {$doctorFullName} on {$followupDateTime}."
            );

            // ✅ Send SMS to Doctor
            if (!empty($doctor->phone)) {
                $doctorSms = "Follow-up scheduled with patient {$patientName} on {$followupDateTime}.";
                $smsService->send_sms($doctor->phone, $doctorSms);
                Log::info("📨 SMS sent to Doctor ID: {$doctor->id}, Phone: {$doctor->phone}, Msg: {$doctorSms}");
            } else {
                Log::warning("⚠️ Doctor ID: {$request->doctor_id} has no phone. SMS not sent.");
            }

            // Notify Patient if login ID exists
            $patientUserId = $patient?->login_patient_id;
            if ($patientUserId) {
                // ✅ Send SMS to Patient
                if (!empty($patient->phone)) {
                    $patientSms = "Dear {$patientName}, your follow-up with Dr. {$doctorFullName} is scheduled on {$followupDateTime}.";
                    $smsService->send_sms($patient->phone, $patientSms);
                    Log::info("📨 SMS sent to Patient ID: {$patient->id}, Phone: {$patient->phone}, Msg: {$patientSms}");
                } else {
                    Log::warning("⚠️ Patient ID: {$patient->id} has no phone. SMS not sent.");
                }
            } else {
                Log::warning("⚠️ No login ID found for Patient ID: {$request->patient_id}. Notification skipped.");
            }
        }


        return response()->json(['message' => 'Follow-up created successfully', 'data' => $followup], 200);
    }



   


    public function store(Request $request, SmsService $smsService)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to create follow-ups.'
            ], 403);
        }

        $loggedInUserId = auth()->id();

        try {
            $validatedData = $request->validate([
                'patient_id'     => 'required|exists:patients,id',
                'treatment_id'   => 'nullable|exists:treatments,id',
                'doctor_id'      => 'required|exists:user,id',
                'date'           => 'required|date',
                'followup_update' => 'nullable|string',
                'followup_type'  => 'nullable|string',
                'branch_id'      => 'required|exists:branches,id', // ✅ validate branch_id
            ]);

            $patient = Patients::find($request->patient_id);
            $treatmentId = $request->treatment_id ?? ($patient->treatment_id ?? null);
            $followupType = $request->followup_type ?? 'Regular followup';

            $followupDate = Carbon::parse($request->date)->format('Y-m-d');

            // 2️⃣ Check if follow-up already exists for this patient on this date
            $existingFollowup = Followup::where('patient_id', $request->patient_id)
                ->whereDate('date', $followupDate)
                ->first();
            // ✅ Create follow-up with branch_id
            $followup = Followup::create([
                'user_id'        => $loggedInUserId,
                'patient_id'     => $request->patient_id,
                'doctor_id'      => $request->doctor_id,
                'treatment_id'   => $treatmentId,
                'date'           => $request->date,
                'followup_type'  => $followupType,
                'followup_update' => $request->followup_update,
                'branch_id'      => $request->branch_id, // ✅ store branch
            ]);

            if ($followup) {
                // 🔔 Patient & Doctor notifications + SMS logic (same as your existing code)
                $patient = Patients::find($request->patient_id);
                $patientName = ucfirst($patient->fullname ?? '');
                $patientPhone = $patient->phone ?? '';
                $patientUserId = $patient?->login_patient_id;
                $doctor = User::find($request->doctor_id);
                $doctorFullName = $doctor->fullname ?? '';
                $doctorPhone = $doctor->phone ?? '';
                $followupDateFormatted = Carbon::parse($request->date)->format('d/m/Y');

                $followupDateTime = Carbon::createFromFormat('Y-m-d', $request->date)->format('d/m/Y');
                $actorLabel = optional(auth()->user()->role)->name === 'Patient'
                    ? "Patient {$patientName}"
                    : (auth()->user()->fullname ?? auth()->user()->name ?? 'Staff');


                $this->notifyFollowupParticipants(
                    $followup,
                    "Dr {$doctorFullName} You have a New Follow-up scheduled! For patient {$patientName} on {$followupDateTime}.",
                    "Dear {$patientName}, your follow-up with Dr. {$doctorFullName} has been scheduled on {$followupDateTime}.",
                    "{$actorLabel} created a follow-up for patient {$patientName} with Dr. {$doctorFullName} on {$followupDateTime}."
                );

                if (!empty($doctorPhone)) {
                    $doctorSms = "Dr {$doctorFullName} You have a New Follow-up scheduled with patient {$patientName} on {$followupDateTime}.";
                    $smsService->send_sms($doctorPhone, $doctorSms);
                }

                if ($patientUserId && !empty($patientPhone)) {
                    $patientSms = "Dear {$patientName}, your follow-up with Dr. {$doctorFullName} is scheduled on {$followupDateTime}.";
                    $smsService->send_sms($patientPhone, $patientSms);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Follow-up created successfully',
                'data'    => $followup
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error while creating follow-up',
                'errors'  => $e->errors()
            ], 401);
        }
    }



    public function show($id)
    {
        $followup = $this->getScopedFollowupOrFail($id);

        if (!$followup) {
            return response()->json(['message' => 'Follow-up not found'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $followup
        ], 200);
    }

    public function update(Request $request, $id)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to update follow-ups.'
            ], 403);
        }

        try {
            $validatedData = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'treatment_id' => 'required|exists:treatments,id',
                'doctor_id' => 'required|exists:user,id',
                'date' => 'required|date',
                'followup_update' => 'nullable|string',
                'followup_type' => 'required|string',
            ]);

            $followup = Followup::findOrFail($id);
            $oldDate = $followup->date;
            $oldType = $followup->followup_type;
            $oldUpdate = $followup->followup_update;
            $followup->update($validatedData);
            $followup->refresh();

            if (
                $oldDate !== $followup->date ||
                $oldType !== $followup->followup_type ||
                $oldUpdate !== $followup->followup_update
            ) {
                $doctor = User::find($followup->doctor_id);
                $patient = Patients::find($followup->patient_id);
                $doctorFullName = $doctor->fullname ?? 'Doctor';
                $patientName = ucfirst($patient->fullname ?? 'Patient');
                $followupDateTime = Carbon::parse($followup->date)->format('d/m/Y');

                $this->notifyFollowupParticipants(
                    $followup,
                    "Dr {$doctorFullName}, follow-up for patient {$patientName} has been updated to {$followupDateTime}.",
                    "Dear {$patientName}, your follow-up with Dr. {$doctorFullName} has been updated to {$followupDateTime}.",
                    "Follow-up for patient {$patientName} with Dr. {$doctorFullName} has been updated to {$followupDateTime}."
                );
            }

            return response()->json(['message' => 'Follow-up updated successfully', 'data' => $followup], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 401);
        }
    }

    public function destroy($id)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to delete follow-ups.'
            ], 403);
        }

        $followup = Followup::find($id);
        if (!$followup) {
            return response()->json(['message' => 'Follow-up not found'], 401);
        }

        $followup->delete();
        return response()->json(['message' => 'Follow-up deleted successfully'], 200);
    }
}
