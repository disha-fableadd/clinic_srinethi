<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\OpdVisit;
use App\Models\Patients;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class OpdVisitController extends Controller
{


    public function opdVisitPdf($id)
    {
        try {
            $opdVisit = OpdVisit::with(['patient', 'doctor'])->findOrFail($id);

            // Fetch clinic details
            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo);

            // Pass to blade
            $data = [
                'date' => now()->format('d-m-Y'),
                'opdVisit' => $opdVisit,
                'patient' => $opdVisit->patient,
                'doctor' => $opdVisit->doctor,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone' => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email' => $settings['clinic_email'] ?? 'clinic@example.com',
            ];

            $pdf = \Pdf::loadView('opd_visit.opd_visit_pdf', $data)->setPaper('A4', 'portrait');

            return $pdf->download("OpdVisit_{$opdVisit->id}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'OPD Visit PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $visits = OpdVisit::with(['patient', 'doctor'])
        ->orderBy('visit_date', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $visits->where('branch_id', $branchId);
    }

    $visits = $visits->get();

    if ($visits->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No OPD visits found to export.'
        ]);
    }

    $filename = 'opd_visits_export_' . now()->format('Ymd_His') . '.csv';
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
        'Visit Date',
        'Chief Complaint',
        'Diagnosis',
        'Prescription',
        'Consultation Fees',
        'Status',
      
        'Created At'
    ]);

    $sr = 1;
    foreach ($visits as $row) {
        fputcsv($file, [
            $sr++,
            $row->patient->fullname ?? '',
            $row->doctor->fullname ?? '',
            $row->visit_date ? $row->visit_date->format('Y-m-d') : '',
            $row->chief_complaint ?? '',
            $row->diagnosis ?? '',
            $row->prescription ?? '',
            $row->consultation_fees,
            $row->status ?? '',
          
            $row->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'OPD visits exported successfully.',
       'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}



    



    public function index(Request $request)
    {
        $user = auth()->user();

        $query = OpdVisit::with(['patient', 'doctor']);

        // ✅ Filter by branch_id if sent from frontend
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // ✅ Filter if doctor
        if ($user && $user->role->name === 'Doctor') {
            $query->where('doctor_id', $user->id);
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('visit_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhere('chief_complaint', 'like', '%' . $searchValue . '%')
                        ->orWhere('diagnosis', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsTotal = (clone $query)->count();
            $recordsFiltered = (clone $filteredQuery)->count();

            $columns = $request->input('columns', []);
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir', 'asc');
            $orderColumn = null;
            if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex]['data'])) {
                $orderColumn = $columns[$orderColumnIndex]['data'];
            }

            $allowedOrderColumns = ['id', 'visit_date', 'status', 'consultation_fees', 'created_at', 'updated_at'];
            if ($orderColumn && in_array($orderColumn, $allowedOrderColumns, true)) {
                $filteredQuery->orderBy($orderColumn, $orderDir === 'desc' ? 'desc' : 'asc');
            } else {
                $filteredQuery->orderBy('id', 'desc');
            }

            $length = (int) $request->input('length', 10);
            if ($length === -1) {
                $length = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }
            $length = $length > 0 ? $length : 10;
            $start = (int) $request->input('start', 0);
            $page = (int) floor($start / $length) + 1;

            $opdVisits = $filteredQuery->forPage($page, $length)->get();

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $opdVisits,
            ], 200);
        }

        // Discharge-style pagination support (page/per_page)
        if ($request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $request->input('search');
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('visit_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhere('chief_complaint', 'like', '%' . $searchValue . '%')
                        ->orWhere('diagnosis', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $opdVisits = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'data' => $opdVisits,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $opdVisits = $query->orderBy('id', 'desc')->get();

        return response()->json(['opd_visits' => $opdVisits]);
    }



    public function store(Request $request, SmsService $smsService)
    {
        $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'doctor_id'         => 'required|exists:user,id', // fixed table name users
            'branch_id'         => 'required|exists:branches,id', // ✅ add branch validation
            'visit_date'        => 'required|date',
            'consultation_fees' => 'required|numeric',
            'chief_complaint'   => 'nullable|string',
            'diagnosis'         => 'nullable|string',
            'prescription'      => 'nullable',
            'status'            => 'nullable|in:active,completed',
        ]);

        try {
            // ✅ Create OPD visit with branch_id
            $visit = OpdVisit::create([
                'branch_id'        => $request->branch_id, // ✅ store branch_id
                'patient_id'       => $request->patient_id,
                'doctor_id'        => $request->doctor_id,
                'visit_date'       => $request->visit_date,
                'consultation_fees' => $request->consultation_fees,
                'chief_complaint'  => $request->chief_complaint,
                'diagnosis'        => $request->diagnosis,
                'prescription'     => json_encode($request->prescription ?? []),
                'status'           => $request->status,
            ]);

            // Get patient details
            $patient = Patients::find($request->patient_id);
            $patientName = ucfirst($patient->fullname ?? '');
            $patientPhone = $patient->phone ?? '';
            $patientUserId = $patient?->login_patient_id;

            // Get doctor details
            $doctor = User::find($request->doctor_id);
            $doctorName = $doctor->fullname ?? '';
            $doctorPhone = $doctor->phone ?? '';
            $doctorUserId = $doctor->id;

            $visitDate = Carbon::parse($request->visit_date)->format('d/m/Y');

            // 🔔 Notify patient
            $patientMessage = "Dear {$patientName}, your OPD visit has been scheduled with Dr. {$doctorName} on {$visitDate}. Consultation Fees: {$request->consultation_fees}.";
            if ($patientUserId) {
                Notification::store($patientMessage, $patientUserId, $visit->id, 'opd');
                if (!empty($patientPhone)) {
                    $smsService->send_sms($patientPhone, $patientMessage);
                }
            }

            // 🔔 Notify doctor
            $doctorMessage = "New OPD visit scheduled: Patient {$patientName} on {$visitDate}. Consultation Fees: {$request->consultation_fees}.";
            if ($doctor) {
                Notification::store($doctorMessage, $doctorUserId, $visit->id, 'opd');
                if (!empty($doctorPhone)) {
                    $smsService->send_sms($doctorPhone, $doctorMessage);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'OPD created successfully with notifications and SMS.',
                'data'    => $visit,
            ]);
        } catch (\Exception $e) {
            \Log::error("❌ Failed to create OPD: " . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create OPD.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $visit = OpdVisit::find($id);

        if (!$visit) {
            return response()->json(['status' => false, 'message' => 'Visit not found.'], 404);
        }

        $visit->delete();

        return response()->json(['status' => true, 'message' => 'Visit deleted successfully.']);
    }
    public function show($id)
    {
        $visit = OpdVisit::with(['patient', 'doctor']) // if you have relationships
            ->where('id', $id)
            ->first();

        if (!$visit) {
            return response()->json(['status' => false, 'message' => 'OPD not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $visit]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'doctor_id' => 'required|integer|exists:user,id',
            'visit_date' => 'required|date',
            'consultation_fees' => 'required|numeric',
            'chief_complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'prescription' => 'nullable|array',
            'status' => 'nullable|in:active,completed',
        ]);

        $visit = OpdVisit::find($id);
        if (!$visit) {
            return response()->json(['status' => false, 'message' => 'OPD not found.'], 404);
        }

        $visit->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'visit_date' => $request->visit_date,
            'consultation_fees' => $request->consultation_fees,
            'chief_complaint' => $request->chief_complaint,
            'diagnosis' => $request->diagnosis,
            'prescription' => json_encode($request->prescription),
            'status' => $request->status,
        ]);


        return response()->json([
            'status' => true,
            'message' => 'OPD updated successfully.',
            'data' => $visit,
        ]);
    }
}
