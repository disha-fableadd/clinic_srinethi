<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentHistory;
use App\Models\Followup;
use App\Models\PatientMedicine;
use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\Branch;
use App\Models\Notification;
use App\Models\Patients;
use App\Models\User;
use App\Models\Treatment;
use App\Services\FCMService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\AppointmentService;

class AppointmentController extends Controller
{
    protected $appointmentService;
    public function __construct(AppointmentService $appointmentService)
    {
        $this->middleware('auth:sanctum');
        $this->appointmentService = $appointmentService;
    }



    public function appointmentPdf(Request $request, $id)
    {
        try {
            // ✅ Load appointment with relations
            $appointment = Appointments::with([
                'patient',
                'doctor',
                'treatment',
                'appointment_history'
            ])->findOrFail($id);

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
                'date' => now()->format('d-m-Y'),
                'patient' => $appointment->patient,
                'appointment' => $appointment,
                'doctor' => $appointment->doctor,
                'treatment' => $appointment->treatment,
                'history' => $appointment->appointment_history,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone' => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email' => $settings['clinic_email'] ?? 'clinic@example.com',
            ];

            // ✅ Generate PDF
            $pdf = Pdf::loadView('appointment.appointment_pdf', $data)
                ->setPaper('A4', 'portrait');

            $folder = 'appointments/';
            $filename = "Appointment_{$appointment->id}.pdf";

            // ✅ Save in storage/app/public/appointments/
            $path = storage_path('app/public/' . $folder . $filename);

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            $pdf->save($path);

            // ✅ Public URL (like your demo link)
            $fileUrl = url('public/storage/' . $folder . $filename);

            // 👉 If Postman/API client → return JSON
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status' => true,
                    'message' => 'Appointment PDF generated successfully.',
                    'file_url' => $fileUrl,
                    'file_name' => $filename,
                ], 200);
            }

            // 👉 If browser → download PDF directly
            return response()->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $branchId = $request->input('branch_id'); // ✅ branch filter

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $appointments = Appointments::with(['patient', 'doctor', 'treatment'])
            ->where('user_id', $userId);

        // ✅ branch filter
        if ($branchId) {
            $appointments->where('branch_id', $branchId);
        }

        // ✅ role-based filter (like your dropdown example)
        if ($user->role->name === 'Doctor') {
            $appointments->where('doctor_id', $user->id);
        } elseif ($user->role->name === 'Patient') {
            $patient = Patients::where('login_patient_id', $user->id)->first();
            if ($patient) {
                $appointments->where('patient_id', $patient->id);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No patient data found for this user'
                ], 200);
            }
        }

        $appointments = $appointments->orderByDesc('date')->get();

        if ($appointments->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No appointments found to export.'
            ]);
        }

        $filename = 'appointments_export_' . now()->format('Ymd_His') . '.csv';
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
            'Treatment',
            'Appointment Type',
            'Status',
            'Date',
            'Duration',
            'Clinic Location',
            'Follow-up Update',
            'Created At'
        ]);

        $sr = 1;
        foreach ($appointments as $appt) {
            fputcsv($file, [
                $sr++,
                $appt->patient->fullname ?? '',
                $appt->doctor->fullname ?? '',
                $appt->treatment->name ?? '',
                $appt->appoint_type,
                $appt->status,
                $appt->date,
                $appt->duration,
                $appt->clinic_location,
                $appt->followup_update,
                $appt->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Appointments exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }


    public function getcalander(Request $request)
    {
        $user = Auth::user();
        $query = Appointments::with(['patient', 'doctor', 'treatment']);

        // Only restrict for users who are not Admin or Receptionist
        if (!in_array($user->role->name, ['Admin', 'Receptionist'])) {
            $query->where(function ($q) use ($user) {
                if ($user->role->name == 'Doctor') {
                    $q->where('doctor_id', $user->id);
                } elseif ($user->role->name == 'Patient') {
                    $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();
                    if ($patient) {
                        $q->where('patient_id', $patient->id);
                    } else {
                        return response()->json([]);
                    }
                }
            });
        }

        // Apply branch_id filter if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Apply status filter if provided and not 'All'
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        // Apply date filter if provided
        if ($request->filled('filter')) {
            $today = \Carbon\Carbon::today();
            switch ($request->filter) {
                case 'Today':
                    $query->whereDate('date', $today);
                    break;
                case 'Upcoming':
                    $query->whereDate('date', '>', $today);
                    break;
                case 'Past':
                    $query->whereDate('date', '<', $today);
                    break;
            }
        }

        // Fetch and format appointments
        $appointments = $query->get()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => ucfirst($appointment->title),
                'start' => $appointment->date,
                'status' => $appointment->status,
                'patient_name' => $appointment->patient?->fullname ?? 'N/A',
                'doctor_name' => $appointment->doctor?->fullname ?? 'N/A',
                'treatment_name' => $appointment->treatment?->name ?? 'N/A',
                'backgroundColor' => $this->getEventColor($appointment->status),
                'borderColor' => $this->getEventColor($appointment->status),
            ];
        });

        return response()->json($appointments);
    }

    public function getStatuses()
    {
        // Dynamically fetch statuses
        $statuses = ['All', 'upcoming', 'confirmed', 'completed', 'cancelled', 'follow-up'];
        return response()->json($statuses);
    }

    private function getEventColor($status)
    {
        $colors = [
            'upcoming' => '#007bff',  // Blue
            'confirmed' => '#005c6b',  // Green
            'completed' => '#28a745',  // Teal
            'cancelled' => '#dc3545',  // Red
            'follow-up' => '#ffc107',    // Orange
        ];
        return $colors[$status] ?? '#7f8c8d';
    }




    public function indexForApp(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->input('branch_id');

        $query = Appointments::with(['patient', 'doctor', 'treatment']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($user->role->name == 'Staff') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role->name === 'Patient') {
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            } else {
                return response()->json([
                    'total' => 0,
                    'appointments' => [],
                    'message' => 'No patient data found for this user'
                ], 200);
            }
        }

        // ✅ Filter only today's and upcoming appointments
        $today = now()->toDateString();
        $query->whereDate('date', '>=', $today);

        $appointments = $query->orderBy('date', 'asc')->get();

        return response()->json([
            'appointments' => $appointments,
            'total' => $appointments->count(),
        ], 200);
    }


    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->input('branch_id');

        $query = Appointments::with(['patient', 'doctor', 'treatment']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($user->role->name === 'Doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role->name === 'Patient') {
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            } else {
                return response()->json([
                    'total' => 0,
                    'data' => [],
                    'message' => 'No patient data found for this user'
                ], 200);
            }
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('status', 'like', '%' . $searchValue . '%')
                        ->orWhere('appoint_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('date', 'like', '%' . $searchValue . '%')
                        ->orWhere('duration', 'like', '%' . $searchValue . '%')
                        ->orWhere('clinic_location', 'like', '%' . $searchValue . '%')
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

            $recordsTotal = (clone $query)->count();
            $recordsFiltered = (clone $filteredQuery)->count();

            $columns = $request->input('columns', []);
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir', 'asc');
            $orderColumn = null;
            if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex]['data'])) {
                $orderColumn = $columns[$orderColumnIndex]['data'];
            }

            $allowedOrderColumns = ['id', 'status', 'date', 'duration', 'appoint_type', 'created_at', 'updated_at'];
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

            $appointments = $filteredQuery->forPage($page, $length)->get();

            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $appointments,
                'appointments' => $appointments,
                'total' => $appointments->count(),
            ], 200);
        }

        if ($request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $request->input('search');
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('status', 'like', '%' . $searchValue . '%')
                        ->orWhere('appoint_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('date', 'like', '%' . $searchValue . '%')
                        ->orWhere('duration', 'like', '%' . $searchValue . '%')
                        ->orWhere('clinic_location', 'like', '%' . $searchValue . '%')
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
            $appointments = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'appointments' => $appointments,
                'total' => $appointments->count(),
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $appointments = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'appointments' => $appointments, // Match the key with your frontend
            'total' => $appointments->count(),
        ], 200);
    }

    public function todayAppointmentsAndFollowups(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->input('branch_id');
        $today = now()->toDateString();

        // ---------------- Appointments ----------------
        $appointmentsQuery = Appointments::with(['patient', 'doctor', 'treatment']);
        if ($branchId) {
            $appointmentsQuery->where('branch_id', $branchId);
        }
        if ($user->role->name == 'Staff') {
            $appointmentsQuery->where('doctor_id', $user->id);
        } elseif ($user->role->name === 'Patient') {
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();
            if ($patient) {
                $appointmentsQuery->where('patient_id', $patient->id);
            } else {
                $appointments = collect(); // empty collection
            }
        }

        if (!isset($appointments)) {
            $appointments = $appointmentsQuery->whereDate('date', $today)
                ->orderBy('date', 'asc')
                ->get();
        }

        // ---------------- Followups ----------------
        $followupsQuery = Followup::with(['patient', 'doctor', 'treatment', 'branch']);
        if ($branchId) {
            $followupsQuery->where('branch_id', $branchId);
        }
        if ($user->role->name == 'Staff') {
            $followupsQuery->where('doctor_id', $user->id);
        } elseif ($user->role->name === 'Patient') {
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();
            if ($patient) {
                $followupsQuery->where('patient_id', $patient->id);
            } else {
                $followups = collect(); // empty collection
            }
        }

        if (!isset($followups)) {
            $followups = $followupsQuery->whereDate('date', $today)
                ->orderBy('date', 'asc')
                ->get();
        }

        // ---------------- Response ----------------
        return response()->json([
            'appointments' => $appointments,
            'appointments_total' => $appointments->count(),
            'followups' => $followups,
            'followups_total' => $followups->count(),
        ], 200);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'patient_id' => 'required|exists:patients,id',
    //         'doctor_id' => 'required|exists:user,id',
    //         'treatment_id' => 'required|exists:treatments,id',
    //         'status' => 'required|string',
    //         'date' => 'required|date_format:Y-m-d',
    //         'duration' => ['nullable', 'regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/'],
    //         'appoint_type' => 'nullable|string',
    //         'clinic_location' => 'nullable|string',
    //         'followup_update' => 'nullable|string',
    //         'branch_id' => 'required|exists:branches,id', // ✅ must come from frontend/localStorage
    //     ]);


    //     try {
    //         $loggedInUserId = auth()->id();

    //         $appointment = Appointments::create([
    //             'user_id' => $loggedInUserId,
    //             'patient_id' => $request->patient_id,
    //             'doctor_id' => $request->doctor_id,
    //             'treatment_id' => $request->treatment_id,
    //             'status' => $request->status,
    //             'date' => $request->date,
    //             'duration' => $request->duration,
    //             'appoint_type' => $request->appoint_type,
    //             'clinic_location' => $request->clinic_location,
    //             'followup_update' => $request->followup_update,
    //             'branch_id' => $request->branch_id, // ✅ store from request/localStorage
    //         ]);

    //         if ($appointment) {

    //             // Load doctor and patient
    //             $doctor = User::find($request->doctor_id);
    //             $patient = Patients::find($request->patient_id);

    //             $doctorFullName = $doctor->fullname ?? '';
    //             $patientFullName = $patient->fullname ?? '';
    //             $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->duration)
    //                 ->format('d/m/Y h:i A');

    //             // Store Notification in DB
    //             Notification::store(

    //                 "Dr {$doctorFullName} You have a New appointment with Patient {$patientFullName} has been scheduled on {$appointmentDateTime}.",
    //                 $request->doctor_id,
    //                 $appointment->id,
    //                 'appointment',
    //                 auth()->id(),
    //                 $request->patient_id
    //             );

    //             Log::info("🔔 Notification stored for Doctor ID: {$request->doctor_id} (Appointment ID: {$appointment->id})");

    //             // Initialize FCM Service
    //             $fcm = new FCMService();
    //             //   dd( $fcm);
    //             // Doctor FCM
    //             if (!empty($doctor->fcm_token)) {
    //                 try {
    //                     $fcm->sendNotification(
    //                         $doctor->fcm_token,
    //                         'New Appointment Scheduled',
    //                         "Dr {$doctorFullName} You have a new appointment with patient {$patientFullName} on {$appointmentDateTime}.",
    //                         ['appointment_id' => (string) $appointment->id]
    //                     );
    //                 } catch (\Exception $e) {
    //                     Log::warning("⚠️ FCM error for Doctor ID {$doctor->id}: {$e->getMessage()}");
    //                 }
    //             }



    //             // Optionally, SMS to Doctor
    //             $smsService = new SmsService();
    //             if (!empty($doctor->phone)) {
    //                 $doctorSms = "Dr {$doctorFullName} You have a New appointment scheduled with patient {$patientFullName} on {$appointmentDateTime}.";
    //                 // $smsService->send_sms($doctor->phone, $doctorSms);
    //             }
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Appointment created successfully.',
    //             'data' => $appointment
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to create appointment.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function store(Request $request)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to create appointments.'
            ], 403);
        }

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
        $statusValidation = $currentProjectTypeId === 3 ? 'nullable|string' : 'required|string';

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:user,id',
            'treatment_id' => 'required|exists:treatments,id',
            'status' => $statusValidation,
            'date' => 'required|date_format:Y-m-d',
            'duration' => ['nullable', 'regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/'],
            'appoint_type' => 'nullable|string',
            'clinic_location' => 'nullable|string',
            'followup_update' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id',
        ]);

        try {
            $appointment = $this->appointmentService->create($request);

            return response()->json([
                'status' => true,
                'message' => 'Appointment created successfully.',
                'data' => $appointment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create appointment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $appointment = Appointments::with([
                'patient',
                'doctor',
                'treatment',
                'appointment_history' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $appointment
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Appointment not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 500);
        }
    }




    /**
     * Delete an appointment.
     */



    public function update(Request $request, $id)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to update appointments.'
            ], 403);
        }

        $appointment = Appointments::findOrFail($id);

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
        $statusValidation = $currentProjectTypeId === 3 ? 'nullable|string' : 'required|string';

        // Validate input
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:user,id',
            'treatment_id' => 'required|exists:treatments,id',
            'status' => $statusValidation,
            'date' => 'required|date',
            'duration' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/'],
            'medicines' => 'nullable|array',
            'medicines.*' => 'exists:medicines,id',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        // Track old values
        $oldDate = $appointment->date;
        $oldTime = $appointment->duration;
        $oldStatus = $appointment->status;

        // Update the appointment
        $appointment->update($request->all());

        // Check if key fields changed (date, duration, status)
        if (
            $oldDate !== $request->date ||
            $oldTime !== $request->duration ||
            $oldStatus !== $request->status
        ) {
            // Store history
            AppointmentHistory::create([
                'appointment_id' => $appointment->id,
                'date' => $request->date,
                'time' => $request->duration,
                'status' => $request->status,
                'comment' => 'Updated by system on appointment edit', // or allow dynamic note
                'created_by' => auth()->id(),
            ]);
        }

        // Handle medicines if status is completed
        if ($request->status === 'completed' && $request->has('medicines')) {
            // Delete previous medicine entries for this appointment
            PatientMedicine::where('appointment_id', $appointment->id)->delete();

            PatientMedicine::create([
                'patient_id' => $appointment->patient_id,
                'medicine_id' => json_encode($request->medicines),
                'appointment_id' => $appointment->id,
                'note' => $request->note,
            ]);
        }

        return response()->json([
            'message' => 'Appointment updated successfully',
            'appointment' => $appointment
        ], 200);
    }




    public function destroy($id)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to delete appointments.'
            ], 403);
        }

        $appointment = Appointments::findOrFail($id);
        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully'], 200);
    }


    public function complete(Request $request)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return response()->json([
                'status' => false,
                'message' => 'Patients are not allowed to complete appointments.'
            ], 403);
        }

        $appointment = Appointments::find($request->id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 401);
        }

        $appointment->status = 'completed';
        $appointment->save();

        return response()->json(['message' => 'Appointment marked as completed.']);
    }
}
