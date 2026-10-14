<?php



namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentTemplate;
use App\Models\HomeAdvice;
use App\Models\PatientAssignAssessment;
use App\Models\PatientAssignHomeadvice;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;
use App\Models\Symptom;
use App\Models\Treatment;
use App\Models\Diagnosis;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use App\Models\PaymentHistory;
use App\Models\TreatmentBooking;
use App\Models\AssignedTherapy;
use App\Models\RadiologyReport;
use App\Models\Patients;
use App\Models\User;
use App\Models\Followup;
use App\Models\Soap;
use App\Models\ReferalDoctor;

use App\Models\Appointments;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash; // Add this at the top

use App\Helpers\SmsHelper;
use App\Helpers\WhatsAppHelper;
use App\Helpers\EmailHelper;
use App\Models\MedicalReport;

class PatientController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:sanctum');
    }

    public function razorpayPatient(Request $request)
    {
        $branchId = $request->get('branch_id');
        $patients = Patients::select('id', 'fullname', 'phone')
            ->where('branch_id', $branchId)
            ->orderBy('fullname')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $patients
        ]);
    }




    public function getYears(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Base query based on role
        $query = Patients::query();

        if ($user->role->name === 'Doctor') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('treatmentBookings.treatment', function ($q2) use ($user) {
                        $q2->where('doctor_id', $user->id);
                    });
            });
        } elseif (!in_array($user->role->name, ['Admin', 'Staff'])) {
            $query->where('user_id', $user->id);
        }

        // Get unique years from created_at
        $years = $query->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'years' => $years
        ], 200);
    }

    public function filterPatients(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Patients::with(['treatmentBookings.treatment']);

        // --- Apply branch filter if provided ---
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // --- Patient type filter ---
        if ($request->filled('patient_type') && $request->patient_type !== 'All') {
            $query->where('patient_type', $request->patient_type);
        }

        // --- Pagination (optional) ---
        $perPage = $request->get('per_page', 10);

        $patients = $query->orderBy('id', 'desc')->paginate($perPage);

        // --- Add diagnosis names ---
        $patients->getCollection()->transform(function ($patient) {
            $diagnosisIds = is_array($patient->diagnosis_id)
                ? $patient->diagnosis_id
                : (is_string($patient->diagnosis_id) ? json_decode($patient->diagnosis_id, true) : (is_int($patient->diagnosis_id) ? [$patient->diagnosis_id] : []));

            $patient->diagnosis_name = Diagnosis::whereIn('id', $diagnosisIds)
                ->pluck('name')
                ->implode(', ') ?: 'No diagnosis';

            return $patient;
        });

        return response()->json($patients, 200);
    }



    public function patientHistoryPdf(Request $request, $id)
    {
        try {
            // ✅ Load patient with all related history
            $patient = Patients::with([
                'followups.doctor',
                'followups.treatment',
                'appointments.doctor',
                'payments.dailyData.collectedBy',
                'medicalReports',
                'treatment',   // ✅ singular, belongsTo
                'treatmentPayments.booking'
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
                'patient' => $patient,
                'followups' => $patient->followups,
                'appointments' => $patient->appointments,
                'payments' => $patient->payments,
                'reports' => $patient->medicalReports,
                'treatment' => $patient->treatment,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone' => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email' => $settings['clinic_email'] ?? 'clinic@example.com',
                'treatmentPayments' => $patient->treatmentPayments ?? [],
            ];

            // ✅ Generate PDF
            $pdf = Pdf::loadView('patients.patient_history_pdf', $data)
                ->setPaper('A4', 'portrait');

            // ✅ Save PDF in public/storage/patient-history/
            $folder = public_path('storage/patient-history/');
            $filename = "Patient_History_{$patient->id}.pdf";
            $path = $folder . $filename;

            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $pdf->save($path);

            // ✅ Public URL
            $fileUrl = url('public/storage/patient-history/' . $filename);

            // 👉 JSON response if API/Postman
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status' => true,
                    'message' => 'Patient History PDF generated successfully.',
                    'file_url' => $fileUrl,
                    'file_name' => $filename,
                ], 200);
            }

            // 👉 Browser download
            return response()->download($path, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Patient History PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }







    public function exportCsv(Request $request)
    {
        $patients = Patients::with(['treatment'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($patients->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No patients found to export.'
            ]);
        }

        $filename = 'patients_export_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        // ✅ CSV Header (added "Patient Unique ID")
        fputcsv($file, [
            'ID',
            'Patient Unique ID',   // <-- new column
            'Full Name',
            'Email',
            'Phone',
            'Age',
            'Birthdate',
            'Patient Type',
            'Referral Source',
            'Source Details',
            'Address',
            'City',
            'State',
            'Blood Group',
            'Treatment',
            'Symptoms',
            'Note',
            'Note Type',
            'Note Interval'
        ]);

        $sr = 1;
        foreach ($patients as $patient) {
            fputcsv($file, [
                $sr++,
                $patient->patient_unique_id,
                $patient->fullname,
                $patient->email,
                $patient->phone,
                $patient->age,
                $patient->birthdate,
                $patient->patient_type,
                $patient->referral_source,
                is_array($patient->source_details) ? implode(', ', $patient->source_details) : $patient->source_details,
                $patient->address,
                $patient->city,
                $patient->state,
                $patient->blood_group,
                $patient->treatment->name ?? '',
                is_array($patient->symptoms) ? implode(', ', $patient->symptoms) : '',
                is_array($patient->note) ? json_encode($patient->note) : $patient->note,
                $patient->note_type,
                $patient->note_interval
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Patients exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);
        $hashedPassword = Hash::make($request->password);

        // Update password in users table
        $user->password = $hashedPassword;
        $user->save();

        // Also update password in patients table (if patient record exists)
        $patient = Patients::where('login_patient_id', $user->id)->first();
        if ($patient) {
            $patient->password = $hashedPassword; // or a plain one if you store it unencrypted (not recommended)
            $patient->save();
        }

        $isSelf = auth()->check() && auth()->id() == $user->id;
        if ($isSelf) {
            auth('web')->logout();
            session()->invalidate();
            session()->regenerateToken();
        }
        return response()->json([
            'message' => 'Password updated successfully!',
            'logout' => $isSelf,
        ]);
    }



    public function getPatients(Request $request)
    {
        $user = Auth::user(); // Get the logged-in user
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $branchId = $request->get('branch_id'); // ✅ get branch id from request
        if ($user->role->name === 'Admin' || $user->role->name === 'Staff') {
            // Admin and Staff see all patients of that branch
            $patients = Patients::with(['treatmentBookings.treatment'])
                ->where('branch_id', $branchId)
                ->orderBy('id', 'desc') // Sort by id in descending order
                ->get();
        } elseif ($user->role->name === 'Staff') {
            // Doctor: patients they created OR patients assigned via treatments in this branch
            $patients = Patients::with(['treatmentBookings.treatment'])
                ->where('branch_id', $branchId)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id) // patients doctor created
                        ->orWhereHas('treatmentBookings.treatment', function ($q2) use ($user) {
                            $q2->where('doctor_id', $user->id); // patients assigned via treatments
                        });
                })
                ->orderBy('id', 'desc') // Sort by id in descending order
                ->get();
        } elseif ($user->role->name === 'Patient') {
            // Patient sees only their own record
            $patient = Patients::with(['treatmentBookings.treatment'])
                ->where('login_patient_id', $user->id)
                ->where('branch_id', $branchId)
                ->orderBy('id', 'desc') // Sort by id in descending order
                ->first();
            if ($patient) {
                $patients = collect([$patient]);
            } else {
                return response()->json([
                    'patients' => [],
                    'message' => 'No patient data found for this user in this branch',
                ], 200);
            }
        } else {
            // Other users see only patients they created in that branch
            $patients = Patients::with(['treatmentBookings.treatment'])
                ->where('branch_id', $branchId)
                ->where('user_id', $user->id)
                ->orderBy('id', 'desc') // Sort by id in descending order
                ->get();
        }
        // Add diagnosis_name to each patient
        $patients = $patients->map(function ($patient) {
            $patient->diagnosis_name = $patient->diagnoses_names; // use accessor
            return $patient;
        });

        return response()->json(['patients' => $patients], 200);
    }

    public function list(Request $request)
    {
        $query = Patients::select('id', 'fullname')
            ->whereIn('id', function ($q) {
                $q->select('patient_id')
                    ->from('daily_data');
            })
            ->orderBy('fullname');

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $query->where('fullname', 'like', '%' . $request->search . '%');
        }

        $patients = $query->get();

        return response()->json([
            'patients' => $patients
        ]);
    }




    public function getpatientsforIPD()
    {
        // Get patient IDs from IPD admissions table
        $ipdPatientIds = \App\Models\IpdAdmission::pluck('patient_id')->unique();

        // Fetch full details of patients admitted in IPD
        $patients = \App\Models\Patients::whereIn('id', $ipdPatientIds)->get();

        return response()->json([
            'patients' => $patients
        ]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // --- Check Search Keyword ---
        if (!$request->filled('search')) {
            return response()->json(['error' => 'Search keyword is required'], 422);
        }

        $search = $request->search;

        // --- Base Query (with role restrictions) ---
        if ($user->role->name === 'Admin' || $user->role->name === 'Staff') {
            $query = Patients::with(['treatmentBookings.treatment']);
        } elseif ($user->role->name === 'Doctor') {
            $query = Patients::with(['treatmentBookings.treatment'])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('treatmentBookings.treatment', function ($q2) use ($user) {
                            $q2->where('doctor_id', $user->id);
                        });
                });
        } else {
            $query = Patients::with(['treatmentBookings.treatment'])
                ->where('user_id', $user->id);
        }

        // --- 🔍 Search Only ---
        $query->where(function ($q) use ($search) {
            $q->where('fullname', 'LIKE', "%{$search}%")
                ->orWhere('patient_unique_id', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('patient_type', 'LIKE', "%{$search}%");
        });

        // --- Execute ---
        $patients = $query->orderBy('id', 'desc')->get()->map(function ($patient) {
            // Diagnosis Names
            $diagnosisIds = is_array($patient->diagnosis_id) ? $patient->diagnosis_id : [$patient->diagnosis_id];
            $patient->diagnosis_name = Diagnosis::whereIn('id', $diagnosisIds ?? [])
                ->pluck('name')
                ->implode(', ') ?: 'No diagnosis';

            // Referral Details
            if (!empty($patient->source_details)) {
                $details = is_string($patient->source_details)
                    ? json_decode($patient->source_details, true)
                    : $patient->source_details;

                if ($patient->referral_source === 'Doctors' && isset($details['doctor_id'])) {
                    $doctor = ReferalDoctor::find($details['doctor_id']);
                    if ($doctor) {
                        $patient->source_details = [
                            'referral_id' => $doctor->id,
                            'referral_name' => $doctor->doctor_name,
                            'specialist' => $doctor->specialist,
                            'email' => $doctor->email,
                            'phone_number' => $doctor->phone_number,
                        ];
                    } else {
                        $patient->source_details = [
                            'referral_id' => $details['doctor_id'],
                            'referral_name' => 'Unknown Doctor',
                        ];
                    }
                } else {
                    $patient->source_details = $details;
                }
            } else {
                $patient->source_details = (object) [];
            }

            return $patient;
        });

        return response()->json([
            'total' => $patients->count(),
            'patients' => $patients
        ], 200);
    }

    public function page(Request $request)
    {
        $user = Auth::user(); // Get logged-in user

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // --- Base Query ---
        if ($user->role->name === 'Admin' || $user->role->name === 'Staff') {
            $query = Patients::with(['treatmentBookings.treatment']);
        } elseif ($user->role->name === 'Staff') {
            // Doctor: see patients they added OR patients assigned via treatmentBookings
            $query = Patients::with(['treatmentBookings.treatment'])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id) // patients added by doctor
                        ->orWhereHas('treatmentBookings.treatment', function ($q2) use ($user) {
                            $q2->where('doctor_id', $user->id); // patients linked via treatments
                        });
                });
        } else {
            // Other users only see patients they created
            $query = Patients::with(['treatmentBookings.treatment'])
                ->where('user_id', $user->id);
        }

        // --- Apply filters ---
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('type')) {
            $query->where('patient_type', $request->type);
        }

        // --- Pagination ---
        $perPage = $request->get('per_page', 10); // default 10 records per page
        $patients = $query->orderBy('id', 'desc')
            ->paginate($perPage); // Laravel auto uses ?page=1,2,3...

        // --- Add diagnosis name ---
        // $patients->getCollection()->transform(function ($patient) {
        //     // $patient->diagnosis_name = $patient->diagnoses_names; // use accessor
        //     // return $patient;
        //        $patient->diagnosis_name = Diagnosis::whereIn('id', $diagnosisIds ?? [])
        //         ->pluck('name')
        //         ->implode(', ') ?: 'No diagnosis';
        //     return $patient;
        // });
        $patients->getCollection()->transform(function ($patient) {

            // Normalize diagnosis_id to array
            if (is_array($patient->diagnosis_id)) {
                $diagnosisIds = $patient->diagnosis_id;
            } elseif (is_string($patient->diagnosis_id)) {
                $diagnosisIds = json_decode($patient->diagnosis_id, true);
            } elseif (is_numeric($patient->diagnosis_id)) {
                $diagnosisIds = [$patient->diagnosis_id];
            } else {
                $diagnosisIds = [];
            }

            // Make sure it's always an array
            $diagnosisIds = (array) $diagnosisIds;

            // Avoid empty errors
            if (empty($diagnosisIds)) {
                $patient->diagnosis_name = 'No diagnosis';
                return $patient;
            }

            $patient->diagnosis_name = Diagnosis::whereIn('id', $diagnosisIds)
                ->pluck('name')
                ->implode(', ') ?: 'No diagnosis';

            return $patient;
        });



        return response()->json($patients, 200);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $branchId = $request->input('branch_id');
        $page = $request->input('page');
        $perPage = (int) $request->input('per_page', 10);
        $searchValue = $request->input('search');

        // --- Base Query ---
        if ($user->role->name === 'Admin' || $user->role->name === 'Staff') {
            $query = Patients::with(['treatmentBookings.treatment']);
        } elseif ($user->role->name === 'Doctor') {
            $query = Patients::with(['treatmentBookings.treatment'])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('treatmentBookings.treatment', function ($q2) use ($user) {
                            $q2->where('doctor_id', $user->id);
                        });
                });
        } else {
            $query = Patients::with(['treatmentBookings.treatment'])
                ->where('user_id', $user->id);
        }

        // --- Filters ---
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('patient_type', $request->type);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // --- Search ---
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('fullname', 'like', "%{$searchValue}%")
                    ->orWhere('patient_unique_id', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        $query->orderBy('id', 'desc');

        // If no page is provided, return all (backward compatibility)
        if (!$page) {
            $patients = $query->get()->map(function ($patient) {
                return $this->formatPatientData($patient);
            });
            return response()->json([
                'total' => $patients->count(),
                'patients' => $patients
            ], 200);
        }

        // Paginated response
        $patients = $query->paginate($perPage, ['*'], 'page', (int)$page);
        
        $data = collect($patients->items())->map(function ($patient) {
            return $this->formatPatientData($patient);
        });

        return response()->json([
            'patients' => $data,
            'pagination' => [
                'current_page' => $patients->currentPage(),
                'last_page'    => $patients->lastPage(),
                'per_page'     => $patients->perPage(),
                'total'        => $patients->total(),
            ]
        ], 200);
    }

    private function formatPatientData($patient)
    {
        // Diagnosis Names
        $diagnosisIds = is_array($patient->diagnosis_id) ? $patient->diagnosis_id : [$patient->diagnosis_id];
        $patient->diagnosis_name = Diagnosis::whereIn('id', $diagnosisIds ?? [])
            ->pluck('name')
            ->implode(', ') ?: 'No diagnosis';

        // Referral Details
        if (!empty($patient->source_details)) {
            $details = is_string($patient->source_details)
                ? json_decode($patient->source_details, true)
                : $patient->source_details;

            if ($patient->referral_source === 'Doctors' && isset($details['doctor_id'])) {
                $doctor = ReferalDoctor::find($details['doctor_id']);
                if ($doctor) {
                    $patient->source_details = [
                        'referral_id' => $doctor->id,
                        'referral_name' => $doctor->doctor_name,
                        'specialist' => $doctor->specialist,
                        'email' => $doctor->email,
                        'phone_number' => $doctor->phone_number,
                    ];
                } else {
                    $patient->source_details = [
                        'referral_id' => $details['doctor_id'],
                        'referral_name' => 'Unknown Doctor',
                    ];
                }
            } else {
                $patient->source_details = $details;
            }
        } else {
            $patient->source_details = (object) [];
        }

        return $patient;
    }



    public function store(Request $request, SmsService $smsService, WhatsAppService $whatsappService)
    {
        $loggedInUserId = auth()->id();

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:patients,email',
            'password' => 'nullable|string',
            'fullname' => 'required|string|max:255',
            'address' => 'nullable|string|max:401',
            'state' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'referral_source' => 'nullable|string',
            'source_details' => 'nullable|array',
            // 'diagnosis_id' => 'required|exists:diagnosis,id',
            'diagnosis_id' => 'nullable|array',
            'diagnosis_id.*' => 'nullable|integer|exists:diagnosis,id',
            'branch_id' => 'required|exists:branches,id', // ✅ add branch validation

            'speech_note' => 'nullable|array',
            'speech_note.*' => 'nullable|string',
            'audio_note' => 'nullable|array',
            'audio_note.*' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'nullable|integer|exists:symptoms,id',
            'symptom_images' => 'nullable|array',
            'symptom_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        // 🖼 Handle profile image upload (same as before)
        $imagePath = null;
        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $destinationPath = public_path('uploads/patients');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $filename);
            $imagePath = 'uploads/patients/' . $filename;
        }

        // 📝 Handle notes (same as before)
        $combinedNotes = [];
        $noteType = $request->input('note_type', 'speech');
        $interval = $request->input('note_interval', '4010');

        if ($request->has('speech_note') && $noteType === 'speech') {
            foreach ($request->speech_note as $speechNote) {
                if (!empty($speechNote)) {
                    $combinedNotes[] = [
                        'type' => 'speech',
                        'content' => $speechNote,
                        'created_at' => now()->toDateTimeString()
                    ];
                }
            }
        }

        if ($request->has('audio_note') && $noteType === 'audio') {
            foreach ($request->audio_note as $audioNote) {
                if (!empty($audioNote)) {
                    $audioPath = $this->saveAudioNote($audioNote);
                    $combinedNotes[] = [
                        'type' => 'audio',
                        'content' => $audioPath,
                        'created_at' => now()->toDateTimeString()
                    ];
                }
            }
        }

        // 🖼 Handle symptom images
        $symptomImagePaths = [];
        if ($request->hasFile('symptom_images')) {
            foreach ($request->file('symptom_images') as $image) {
                $destinationPath = public_path('uploads/symptom_images');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move($destinationPath, $filename);
                $symptomImagePaths[] = 'uploads/symptom_images/' . $filename;
            }
        }

        $password = $request->password ?? $request->fullname;

        // 👤 Create login user
        $loginUser = \App\Models\User::create([
            'role_id' => 5,
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'profile' => $imagePath,
            'password' => Hash::make($password),
            'created_by' => $loggedInUserId,
        ]);


        if ($request->uniqueId) {
            $uniqueId = $request->uniqueId;
        } else {
            // ✅ Get last patient_unique_id
            $lastPatient = \App\Models\Patients::orderBy('id', 'desc')->first();

            if ($lastPatient && preg_match('/TP(\d+)/', $lastPatient->patient_unique_id, $matches)) {
                $nextNumber = (int) $matches[1] + 1; // increment last number
            } else {
                $nextNumber = 1; // start from 1 if no patient exists
            }

            $uniqueId = 'TP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }


        $patient = Patients::create([
            'user_id' => $loggedInUserId,
            'login_patient_id' => $loginUser->id,
            'patient_unique_id' => $uniqueId, // 👈 Add this line
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($password),
            'phone' => $request->phone,
            'address' => $request->address,
            'age' => $request->age,
            'birthdate' => $request->birthdate,
            'patient_type' => $request->patient_type,
            'referral_source' => $request->referral_source,
            'source_details' => !empty($request->source_details) ? json_encode($request->source_details) : null,
            'state' => $request->state,
            'city' => $request->city,
            // 'blood_group' => $request->blood_group,
            // 'medical_history' => $request->medical_history,
            'note' => !empty($combinedNotes) ? $combinedNotes : null,
            'note_type' => $noteType,
            'note_interval' => $interval,
            // 'diagnosis_id' => $request->diagnosis_id,
            'diagnosis_id' => !empty($request->diagnosis_id) ? $request->diagnosis_id : [],


            'profile' => $imagePath,
            'symptoms' => $request->has('symptoms') ? $request->symptoms : null,
            'symptom_images' => !empty($symptomImagePaths) ? $symptomImagePaths : null,
            'branch_id' => $request->branch_id, // ✅ Store branch I
            'symptom_remarks' => $request->symptom_remarks ?? null,
        ]);

        // Default permissions
        $defaultPermissions = [
            ['module_id' => 6],
            ['module_id' => 2],
        ];
        foreach ($defaultPermissions as $perm) {
            \App\Models\UserPermission::create([
                'user_id' => $loginUser->id,
                'module_id' => $perm['module_id'],
                'create' => 1,
                'view' => 1,
                'update' => 1,
                'delete' => 1,
            ]);
        }
        $treatment = Treatment::find($request->treatment_id);
        $treatmentName = $treatment ? $treatment->name : 'N/A';

        $message = "Welcome {$patient->fullname}, your profile has been created successfully.\n"
            . "Treatment: {$treatmentName}\n"
            . "Email: {$patient->email}\n"
            . "Password: {$password}";

        Notification::store($message, $loginUser->id, $patient->id, 'patient');

        if (!empty($patient->phone)) {
            $smsService->send_sms($patient->phone, $message);
            Log::info("📨 SMS sent to Patient ID: {$patient->id}, Phone: {$patient->phone}");
        } else {
            Log::warning("⚠️ Patient ID: {$patient->id} has no phone number. SMS not sent.");
        }


        return response()->json([
            'message' => 'Patient created successfully',
            'patient' => $patient,
        ], 200);
    }













    private function saveAudioNote($base64Audio)
    {
        $audioData = base64_decode(preg_replace('#^data:audio/\w+;base64,#i', '', $base64Audio));
        $destinationPath = public_path('uploads/patient_notes/audio');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $filename = 'audio_' . time() . '_' . uniqid() . '.mp3';
        $filePath = 'uploads/patient_notes/audio/' . $filename;

        file_put_contents(public_path($filePath), $audioData);

        return $filePath;
    }





    public function show($id)
    {
        $patient = Patients::with(['treatment'])->find($id);
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 401);
        }
        // Treatment fallback
        $patient->treatment_name = $patient->treatment?->name ?? 'No treatment';
        // Diagnosis names
        $diagnosisIds = is_array($patient->diagnosis_id)
            ? $patient->diagnosis_id
            : (json_decode($patient->diagnosis_id, true) ?? []);
        // Fetch names
        $diagnosisNames = !empty($diagnosisIds)
            ? Diagnosis::whereIn('id', $diagnosisIds)->pluck('name')->toArray()
            : [];
        // Attach to patient
        $patient->diagnosis_names = $diagnosisNames;
        // Decode symptom_images safely
        $patient->symptom_images = $patient->symptom_images ?? [];
        // Decode symptoms and convert to integers
        $symptomIds = $patient->symptoms ?? [];
        $symptomIds = is_array($symptomIds) ? array_map('intval', $symptomIds) : [];
        // Get symptom names
        $symptomNames = !empty($symptomIds)
            ? Symptom::whereIn('id', $symptomIds)->pluck('name')->toArray()
            : [];
        // Fetch follow-ups, appointments, payment history, etc.
        $followups = Followup::with(['followup_doctor', 'treatment'])->where('patient_id', $id)->get();
        $appointments = Appointments::with(['appointment_doctor', 'treatment'])->where('patient_id', $id)->get();
        $paymentHistory = PaymentHistory::with(['dailyData'])
            ->whereHas('dailyData', fn($q) => $q->where('patient_id', $id))->get();
        $treatments = TreatmentBooking::with(['treatment', 'paymentHistory'])->where('patient_id', $id)->get();
        $therapies = AssignedTherapy::with(['therapy', 'doctor'])->where('patient_id', $id)->get();
        $soaps = Soap::with(['user'])->where('patient_id', $id)->get();

        // Fetch assessments and manually append templates
        $assesment = PatientAssignAssessment::with(['user'])->where('patient_id', $id)->get();
        $assesment->each(function ($item) {
            $templateIds = $item->template_id;
            if (is_array($templateIds)) {
                $item->templates = AssessmentTemplate::whereIn('id', $templateIds)->get();
            } else {
                $item->templates = collect(); // Empty collection if not an array
            }
        });

        // Fetch home advice and manually append templates
        $homeadvice = PatientAssignHomeadvice::with(['user'])->where('patient_id', $id)->get();
        $homeadvice->each(function ($item) {
            $templateIds = $item->template_id;
            if (is_array($templateIds)) {
                $item->templates = HomeAdvice::whereIn('id', $templateIds)->get();
            } else {
                $item->templates = collect(); // Empty collection if not an array
            }
        });

        $reports = MedicalReport::where('patient_id', $id)->get();
        $allSymptoms = Symptom::select('id', 'name')->get();

        return response()->json([
            'patient' => $patient,
            'diagnosis_ids' => $patient->diagnosis_id ?? [],
            'symptom_ids' => $symptomIds,
            'symptoms' => $symptomNames,
            'followups' => $followups,
            'appointments' => $appointments,
            'paymentHistory' => $paymentHistory,
            'treatments' => $treatments,
            'assesment' => $assesment,
            'homeadvice' => $homeadvice,
            'therapies' => $therapies,
            'soaps' => $soaps,
            'reports' => $reports,
            'all_symptoms' => $allSymptoms,
            'symptom_remarks' => $patient->symptom_remarks,
            'symptom_images' => $patient->symptom_images,
        ]);
    }





    public function update(Request $request, $id, SmsService $smsService)
    {
        try {
            $patient = Patients::find($id);

            if (!$patient) {
                return response()->json(['success' => false, 'message' => 'Patient not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'nullable|email|unique:patients,email,' . $id,
                'fullname' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'address' => 'nullable|string|max:401',
                'state' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                // 'diagnosis_id' => 'required|integer|exists:diagnosis,id',
                'diagnosis_id' => 'nullable|array',
                'diagnosis_id.*' => 'integer|exists:diagnosis,id',

                'speech_note.*' => 'nullable|string',
                'audio_note.*' => 'nullable|string',
                'medical_history' => 'nullable|string',
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'note_type' => 'nullable|string|in:speech,audio',
                'note_interval' => 'nullable|string',
                'symptoms.*' => 'nullable|integer|exists:symptoms,id',
                'symptom_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',

            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Handle profile image
            $imagePath = $patient->getRawOriginal('profile');
            if ($request->hasFile('profile')) {
                if ($imagePath && File::exists(public_path($imagePath))) {
                    File::delete(public_path($imagePath));
                }
                $image = $request->file('profile');
                $destinationPath = public_path('uploads/patients');
                if (!File::exists($destinationPath))
                    File::makeDirectory($destinationPath, 0755, true);
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move($destinationPath, $filename);
                $imagePath = 'uploads/patients/' . $filename;
            }

            // Handle symptom images
            $symptomImagePaths = $patient->symptom_images ?? [];
            $symptomImagePaths = is_array($symptomImagePaths) ? $symptomImagePaths : json_decode($symptomImagePaths, true);
            if ($request->hasFile('symptom_images')) {
                foreach ($request->file('symptom_images') as $image) {
                    $destinationPath = public_path('uploads/symptom_images');
                    if (!File::exists($destinationPath))
                        File::makeDirectory($destinationPath, 0755, true);
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move($destinationPath, $filename);
                    $symptomImagePaths[] = 'uploads/symptom_images/' . $filename;
                }
            }

            // Remove selected symptom images
            if ($request->has('removed_symptom_images')) {
                $removedImages = json_decode($request->removed_symptom_images, true);
                if (is_array($removedImages)) {
                    foreach ($removedImages as $img) {
                        $fullPath = public_path($img);
                        if (File::exists($fullPath))
                            File::delete($fullPath);
                    }
                    $symptomImagePaths = array_values(array_diff($symptomImagePaths, $removedImages));
                }
            }

            // Handle notes
            $combinedNotes = $patient->note ?? [];
            $noteType = $request->note_type ?? $patient->note_type ?? 'speech';
            $noteInterval = $request->note_interval ?? $patient->note_interval ?? '4010';
            $newNotes = [];

            if ($request->has('speech_note') && $noteType === 'speech') {
                foreach ($request->speech_note as $speechNote) {
                    if (!empty(trim($speechNote))) {
                        $newNotes[] = [
                            'type' => 'speech',
                            'content' => trim($speechNote),
                            'created_at' => now()->toDateTimeString()
                        ];
                    }
                }
            }

            if ($request->has('audio_note') && $noteType === 'audio') {
                foreach ($request->audio_note as $audioNote) {
                    if (!empty($audioNote)) {
                        $audioPath = str_starts_with($audioNote, 'data:audio') ? $this->saveAudioNote($audioNote) : ltrim(parse_url($audioNote, PHP_URL_PATH), '/');
                        $newNotes[] = [
                            'type' => 'audio',
                            'content' => $audioPath,
                            'created_at' => now()->toDateTimeString()
                        ];
                    }
                }
            }

            if (!empty($newNotes)) {
                $combinedNotes = $newNotes;
            }

            // Update linked user table as well
            if ($patient->login_patient_id) {
                $user = \App\Models\User::find($patient->login_patient_id);

                if ($user) {
                    $userUpdateData = [
                        'fullname' => $request->fullname,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'profile' => $imagePath,
                    ];

                    if ($request->filled('password')) {
                        $userUpdateData['password'] = Hash::make($request->password);
                    }

                    $user->update($userUpdateData);
                }
            }



            // Update data
            $updateData = [
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'age' => $request->age,
                'state' => $request->state,
                'city' => $request->city,
                // 'blood_group' => $request->blood_group,
                // 'medical_history' => $request->medical_history,
                'note' => $combinedNotes,
                'note_type' => $noteType,
                'note_interval' => $noteInterval,
                'diagnosis_id' => $request->diagnosis_id, // now an array
                'profile' => $imagePath,
                'symptoms' => $request->symptoms,
                'symptom_images' => $symptomImagePaths,
                'patient_type' => $request->patient_type,
                'referral_source' => $request->referral_source ?? $patient->referral_source,
                'source_details' => $request->source_details ?? $patient->source_details,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $patient->update($updateData);

            return response()->json(['success' => true, 'message' => 'Patient updated successfully', 'data' => $patient]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function destroy($id)
    {
        $patient = Patients::find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 401);
        }

        // Check for related records in appointments
        if ($patient->appointments()->exists()) {
            return response()->json(['message' => 'Cannot delete patient. Related appointments exist.'], 400);
        }

        // Check for related records in followups
        if ($patient->followups()->exists()) {
            return response()->json(['message' => 'Cannot delete patient. Related follow-ups exist.'], 400);
        }

        // Check for related records in medical_reports
        if ($patient->medicalReports()->exists()) {
            return response()->json(['message' => 'Cannot delete patient. Related medical reports exist.'], 400);
        }

        // Check for related records in patient_discharge_dets
        if ($patient->dischargeDetails()->exists()) {
            return response()->json(['message' => 'Cannot delete patient. Related discharge details exist.'], 400);
        }

        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }
}
