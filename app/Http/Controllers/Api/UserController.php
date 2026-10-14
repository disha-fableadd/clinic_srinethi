<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeNotifications;
use App\Models\Appointments;
use App\Models\Followup;
use App\Models\Treatment;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use App\Models\Patients;
use Illuminate\Support\Facades\Mail;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\UserPermission;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Helpers\SmsHelper;
use App\Helpers\EmailHelper;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\DailyData;
use App\Models\PaymentHistory;
use App\Models\Expense;
use App\Models\Setting;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('checkUserLimit')->only('store');
    }

    public function getAllFollowups() {}

    // public function getTodayStats()
    // {
    //     $today = Carbon::today();

    //     // Total income
    //     $todayIncome = PaymentHistory::whereDate('created_at', $today)
    //         ->sum('paid_amount');

    //     // Unique patients
    //     $todayPatients = DailyData::whereDate('created_at', $today)
    //         ->distinct('patient_id')
    //         ->count('patient_id');

    //     $todayBirthdays = UserDetails::whereMonth('birth_date', $today->month)
    //         ->whereDay('birth_date', $today->day)
    //         ->count();

    //     $todayExpense = Expense::whereDate('date_time', $today)
    //         ->sum('amount');

    //     return response()->json([
    //         'today_income' => $todayIncome,
    //         'today_patients' => $todayPatients,
    //         'today_birthdays' => $todayBirthdays,
    //         'today_expense' => $todayExpense, // ✅ Added
    //     ]);
    // }



    public function getTodayStats(Request $request)
    {
        $today = Carbon::today();
        $branchId = $request->branch_id;

        $todayIncome = DailyData::whereDate('date', $today)
            ->when($branchId, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->with('paymentHistories')
            ->get()
            ->flatMap(function ($daily) {
                return $daily->paymentHistories;
            })
            ->sum('paid_amount');


        // ✅ Patients (branch-wise)
        $patientQuery = Patients::whereDate('created_at', $today);
        if ($branchId) {
            $patientQuery->where('branch_id', $branchId);
        }
        $todayPatients = $patientQuery->count();

        // Birthdays
        // $todayBirthdays = UserDetails::whereMonth('birth_date', $today->month)
        //     ->whereDay('birth_date', $today->day)
        //     ->count();


        $birthdayQuery = UserDetails::query();

        if ($branchId) {
            $birthdayQuery->whereHas('user', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $todayBirthdays = $birthdayQuery
            ->whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->count();


        // Expenses
        $expenseQuery = Expense::whereDate('date_time', $today);
        if ($branchId) {
            $expenseQuery->where('branch_id', $branchId);
        }
        $todayExpense = $expenseQuery->sum('amount');

        return response()->json([
            'today_income' => $todayIncome,
            'today_patients' => $todayPatients,
            'today_birthdays' => $todayBirthdays,
            'today_expense' => $todayExpense,
        ]);
    }




    public function getTodayBirthdayUsers(Request $request)
    {
        $today = Carbon::today();
        $branchId = $request->branch_id;

        $query = UserDetails::query()
            ->join('user', 'user_details.user_id', '=', 'user.id') // ✅ join users
            ->whereMonth('user_details.birth_date', $today->month)
            ->whereDay('user_details.birth_date', $today->day)
            ->select('user_details.birth_date', 'user.fullname', 'user.branch_id');

        if (!empty($branchId)) {
            $query->where('user.branch_id', $branchId); // ✅ filter by branch
        }

        $users = $query->get()->map(function ($row) {
            return [
                'fullname'   => $row->fullname ?? 'N/A',
                'birth_date' => Carbon::parse($row->birth_date)->format('d M Y'),
            ];
        });

        return response()->json([
            'count' => $users->count(),
            'users' => $users
        ]);
    }











    // public function getalluserss()
    // {
    //     // Only include users with role_id 2, 3, or 4
    //     $allowedRoleIds = [2, 3, 4];

    //     $users = User::whereIn('role_id', $allowedRoleIds)
    //         ->orderByDesc('id')
    //         ->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Users fetched successfully',
    //         'users' => $users,
    //     ]);
    // }

    public function getalluserss(Request $request)
    {
        $allowedRoleIds = [2, 3, 4]; // Staff roles
        $branchId = $request->branch_id;

        $users = User::whereIn('role_id', $allowedRoleIds)
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            // Ensure latest users appear first
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Users fetched successfully',
            'users' => $users,
        ]);
    }




    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id');

        $users = User::with(['role', 'details', 'creator', 'branch'])
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $users->where('branch_id', $branchId);
        }

        $users = $users->get();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No users found to export.'
            ]);
        }

        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
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
            'Full Name',
            'Email',
            'Phone',
            'Role',
            'Branch',
            'Created By',
            'Profile URL',
            'Gender',
            'Birth Date',
            'Address',
            'City',
            'State',
            'Shift',
            'Salary',
            'Created At'
        ]);

        $sr = 1;

        foreach ($users as $user) {

            fputcsv($file, [
                $sr++,
                $user->fullname,
                $user->email,
                $user->phone,
                $user->role->name ?? '',
                $user->branch->branch_name ?? '',
                $user->creator->fullname ?? '',
                $user->profile,
                $user->details->gender ?? '',
                $user->details->birth_date ?? '',
                $user->details->address ?? '',
                $user->details->city ?? '',
                $user->details->state ?? '',
                $user->details->shift ?? '',
                $user->details->salary ?? '',
                $user->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Users exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }



    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 401);
        }

        $user = User::findOrFail($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        $isSelf = auth()->check() && auth()->id() == $user->id;

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.',
            'logout' => $isSelf, // Flag for frontend
        ]);
    }



    public function getUsers(Request $request)
    {
        $excludedRoles = [1, 2, 5]; // 1: SuperAdmin, 2: Admin, 5: Patient

        // Get parameters from request
        $branchId = $request->input('branch_id');
        $page = $request->input('page');
        $perPage = (int) $request->input('per_page', 10);
        $searchValue = $request->input('search');

        $query = User::whereNotIn('role_id', $excludedRoles)
            ->when($branchId, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            });

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if ($searchValue) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('fullname', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%")
                        ->orWhere('phone', 'like', "%{$searchValue}%");
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

            $allowedOrderColumns = ['id', 'fullname', 'email', 'phone', 'created_at', 'updated_at'];
            if ($orderColumn && in_array($orderColumn, $allowedOrderColumns, true)) {
                $filteredQuery->orderBy($orderColumn, $orderDir === 'desc' ? 'desc' : 'asc');
            } else {
                $filteredQuery->orderBy('created_at', 'desc');
            }

            $length = (int) $request->input('length', 10);
            if ($length === -1) {
                $length = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }
            $length = $length > 0 ? $length : 10;
            $start = (int) $request->input('start', 0);
            $page = (int) floor($start / $length) + 1;

            $users = $filteredQuery->forPage($page, $length)->get()->map(function ($user) {
                $user->roleName = $user->role ? $user->role->name : 'N/A';
                $user->joinDate = $user->created_at->toDateString();
                return $user;
            });

            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $users,
            ]);
        }

        // Search functionality (non-DataTables)
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('fullname', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        // If no page is provided, return all users (maintaining backward compatibility for dropdowns)
        if (!$page) {
            $users = $query->get()->map(function ($user) {
                $user->roleName = $user->role ? $user->role->name : 'N/A';
                $user->joinDate = $user->created_at->toDateString();
                return $user;
            });
            return response()->json(['data' => $users]);
        }

        // Paginated response for DataTables
        $users = $query->paginate($perPage, ['*'], 'page', (int)$page);

        $data = collect($users->items())->map(function ($user) {
            $user->roleName = $user->role ? $user->role->name : 'N/A';
            $user->joinDate = $user->created_at->toDateString();
            return $user;
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ]
        ]);
    }



    public function getAppointmentStatusCounts(Request $request)
    {
        $user = auth()->user();
        $filter = $request->input('filter', 'month');
        $branchId = $request->input('branch_id'); // ✅ get branch_id

        $query = Appointments::query();

        // Apply branch filter
        if (!empty($branchId)) {
            $query->where('branch_id', (int) $branchId); // ✅ branch filter
        }

        // Apply role restrictions
        if ($user->role->name !== 'Admin') {
            if ($user->role->name !== 'Receptionist') {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('doctor_id', $user->id);
                });
            }
        }

        // Apply date filter
        switch ($filter) {
            case 'week':
                $startDate = now()->startOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                break;
            default:
                $startDate = now()->startOfMonth();
        }

        $query->whereDate('date', '>=', $startDate);

        // Get appointments list
        $appointments = (clone $query)->with(['doctor', 'patient', 'treatment'])->get();

        // Get counts per status
        $statusCountsRaw = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = ['upcoming', 'confirmed', 'completed', 'cancelled', 'follow-up'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = $statusCountsRaw[$status] ?? 0;
        }

        return response()->json([
            'statuses' => $statuses,
            'counts' => $counts,
            'appointments' => $appointments
        ]);
    }


    public function getpatients()
    {
        // dd($user = Auth::user()->role->name);
        $doctorId = Auth::id();


        $patientData = Patients::join('treatments', 'patients.treatment_id', '=', 'treatments.id')
            ->where('treatments.doctor_id', $doctorId)
            ->select(DB::raw('MONTH(patients.created_at) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();


        $patientsByMonth = array_fill(0, 12, 0);

        foreach ($patientData as $data) {
            $patientsByMonth[$data->month - 1] = $data->count;
        }

        return response()->json(['patients' => $patientsByMonth]);
    }



    public function getTodayFollowup(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $todayDate = now()->toDateString(); // Get today's date
        $branchId = $request->branch_id; // branch_id from AJAX/localStorage

        if (in_array($user->role->name, ['Admin', 'Receptionist'])) {
            // Admin/Receptionist: All follow-ups for today (branch-wise if provided)
            $query = Followup::whereDate('date', $todayDate);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalFollowup = $query->count();
        } elseif ($user->role->name === 'Doctor') {
            // Doctor: follow-ups created by or assigned to the doctor
            $query = Followup::whereDate('date', $todayDate)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('doctor_id', $user->id);
                });

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalFollowup = $query->count();
        } elseif ($user->role->name === 'Patient') {
            // Patient: count their follow-ups
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();
            if (!$patient) {
                return response()->json(['total_followup' => 0]);
            }

            $query = Followup::whereDate('date', $todayDate)
                ->where('patient_id', $patient->id);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalFollowup = $query->count();
        } else {
            // Other roles: follow-ups created by this user
            $query = Followup::whereDate('date', $todayDate)
                ->where('user_id', $user->id);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalFollowup = $query->count();
        }

        return response()->json(['total_followup' => $totalFollowup]);
    }






    public function getTodayAppointment(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $loggedInUserId = $user->id;
        $todayDate = now()->toDateString();
        $roleName = $user->role->name;
        $branchId = $request->branch_id; // ✅ branch_id from AJAX/localStorage

        // Start query
        $query = Appointments::whereDate('date', $todayDate);

        // Apply branch filter if provided
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Role-based filtering
        if ($roleName === 'Doctor') {
            // Doctor sees appointments they created or are assigned to
            $query->where(function ($q) use ($loggedInUserId) {
                $q->where('user_id', $loggedInUserId)
                    ->orWhere('doctor_id', $loggedInUserId);
            });
        } elseif (in_array($roleName, ['Admin', 'Receptionist'])) {
            // Admin & Receptionist: already see all, no extra filter

        } elseif ($roleName === 'Patient') {
            // Patient: Find patient_id by login_patient_id
            $patient = \App\Models\Patients::where('login_patient_id', $loggedInUserId)->first();

            if (!$patient) {
                return response()->json(['total_appointments' => 0]);
            }

            $query->where('patient_id', $patient->id);
        } else {
            // Other roles: see appointments they created
            $query->where('user_id', $loggedInUserId);
        }

        $totalAppointments = $query->count();

        return response()->json(['total_appointments' => $totalAppointments]);
    }







    public function getTotalPatient(Request $request)
    {
        $user = auth()->user(); // Retrieve the authenticated user
        $branchId = $request->branch_id; // ✅ get branch_id from request

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->role->name === 'Admin' || $user->role->name === 'Receptionist') {
            // Admin/Receptionist: count all patients branch-wise
            $totalPatients = Patients::when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->count();
        } elseif ($user->role->name === 'Doctor') {
            // Doctor: count patients created by them or assigned to them branch-wise
            $totalPatients = Patients::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('treatment', function ($q) use ($user) {
                        $q->where('doctor_id', $user->id);
                    });
            })
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
        } else {
            // Other users: count only their patients branch-wise
            $totalPatients = Patients::where('user_id', $user->id)
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
        }

        return response()->json(['total_patient' => $totalPatients]);
    }





    public function getTotalAppointment(Request $request)
    {
        $user = auth()->user(); // Get the logged-in user
        $branchId = $request->branch_id; // ✅ Get branch ID from request

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (in_array($user->role->name, ['Admin', 'Receptionist'])) {
            // Admin or Receptionist: Count all appointments branch-wise
            $totalAppointments = Appointments::when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->count();
        } elseif ($user->role->name === 'Doctor') {
            // Doctor: Count appointments assigned to the doctor branch-wise
            $totalAppointments = Appointments::where(function ($query) use ($user) {
                $query->where('doctor_id', $user->id)
                    ->orWhere('user_id', $user->id);
            })
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
        } elseif ($user->role->name === 'Patient') {
            // Patient: Count only their own appointments branch-wise
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();

            if (!$patient) {
                return response()->json(['total_appointment' => 0]);
            }

            $totalAppointments = Appointments::where('patient_id', $patient->id)
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
        } else {
            // Other roles: Count appointments created by the user branch-wise
            $totalAppointments = Appointments::where('user_id', $user->id)
                ->when($branchId, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
        }

        return response()->json(['total_appointment' => $totalAppointments]);
    }







    public function profile($id)
    {
        $userId = Auth::id();  // Get the logged-in user's ID
        $user = User::with('details')->find($id);

        if ($user) {
            // Add role and other details to the user
            $user->roleName = $user->role ? $user->role->name : 'N/A';
            $user->address = $user->details ? $user->details->address : 'N/A';
            $user->city = $user->details ? $user->details->city : 'N/A';
            $user->state = $user->details ? $user->details->state : 'N/A';
            $user->gender = $user->details ? $user->details->gender : 'N/A';
            $user->birth_date = $user->details ? $user->details->birth_date : 'N/A';
            $user->shift = $user->details ? $user->details->shift : 'N/A';
            $user->salary = $user->details ? $user->details->salary : 'N/A';

            // Fetch user permissions
            $permissions = UserPermission::where('user_id', $id)->get();
            $user->permissions = $permissions;

            // Return the user data along with the userId in a single response
            return response()->json([
                'user' => $user,
                'userId' => $userId
            ]);
        }

        return response()->json(['message' => 'User not found'], 401);
    }










      public function index(Request $request)
    {
        $excludedRoles = [1, 2, 5];
        $branchId = $request->input('branch_id');


        $branchId = $request->input('branch_id');

        $users = User::whereNotIn('role_id', $excludedRoles)
            ->when($branchId != null, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            // Ensure latest users appear first
            ->orderByDesc('id')
            ->get();


        $users->map(function ($user) {
            $user->roleName = $user->role ? $user->role->name : 'N/A';
            return $user;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Users fetched successfully',
            'data' => $users,
        ]);
    }







    public function store(Request $request)
    {
        DB::beginTransaction();
        $loggedInUserId = auth()->id();

        try {
            $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
            $roleValidation = $currentProjectTypeId === 3 ? 'nullable|int|max:255' : 'required|int|max:255';

            // Validate main user data including branch_id
            $userValidator = Validator::make($request->all(), [
                'role_id' => $roleValidation,
                'fullname' => 'required|string|max:255',
                'email' => 'required|email|unique:user,email',
                'phone' => 'nullable|string|max:15',
                'password' => 'required|string',
                'branch_id' => 'required|exists:branches,id', // branch validation
            ]);

            if ($userValidator->fails()) {
                return response()->json(['errors' => $userValidator->errors()], 401);
            }

            // Handle profile image upload
            $imagePath = null;
            if ($request->hasFile('profile')) {
                $fileValidator = Validator::make($request->all(), [
                    'profile' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                ]);

                if ($fileValidator->fails()) {
                    return response()->json(['errors' => $fileValidator->errors()], 401);
                }

                $image = $request->file('profile');
                $destinationPath = public_path('uploads/users');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move($destinationPath, $filename);
                $imagePath = 'uploads/users/' . $filename;
            }

            // Create the user
            $user = User::create([
                'role_id' => (int) $request->role_id,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'branch_id' => $request->branch_id, // Store branch_id
                'profile' => $imagePath,
                'password' => bcrypt($request->password),
                'created_by' => $loggedInUserId,
            ]);

            if ($user) {
                // dd($user);
                try {
                    
                    Mail::raw(
                        "Hello {$user->fullname},\n\n" .
                            "Your account has been created successfully.\n\n" .
                            "Login Details:\n" .
                            "Email: {$user->email}\n" .
                            "Password: {$request->password}\n\n" .
                            "Thank you!",
                        function ($message) use ($user) {
                            $message->to($user->email)
                                ->from(config('mail.from.address'), config('mail.from.name'))
                                ->subject('Your Account Details');
                        }
                    );

                    \Log::info('✅ Email sent successfully', [
                        'email' => $user->email
                    ]);
                } catch (\Exception $e) {
                    \Log::error('❌ Email sending failed', [
                        'error' => $e->getMessage(),
                        'email' => $user->email
                    ]);
                }
            }

            // Send notifications (internal, WhatsApp, SMS)
            if ($user) {
                // Internal notification
                Notification::store(
                    "Welcome onboard, {$user->fullname}! We're excited to have you with us.",
                    $user->id,
                    $user->id,
                    'user'
                );

                // Normalize phone for WhatsApp
                $phone = $user->phone;
                if (strlen($phone) == 10) { // assume local number
                    $phone = "91" . $phone;
                }

                // Fetch clinic name and admin fullname
                $clinicName = DB::table('settings')->where('key', 'clinic_name')->value('value');
                $adminName = DB::table('user')->where('role_id', 2)->value('fullname');

                // WhatsApp welcome message
                $whatsapp = new WhatsAppService();
                $whatsapp->sendWelcomeTemplate(
                    $phone,
                    $user->fullname,
                    $clinicName,
                    $user->email,
                    $adminName
                );

                // SMS welcome message
                $smsMessage = "Welcome onboard, {$user->fullname}! 🎉\n"
                    . "Your account at {$clinicName} is created.\n"
                    . "Email: {$user->email}\n"
                    . "Password: {$request->password}\n"
                    . "Admin: {$adminName}";

                $smsService = new SmsService();
                $smsResponse = $smsService->send_sms($user->phone, $smsMessage);
                if ($smsResponse) {
                    Log::info("✅ SMS sent successfully to {$user->phone}");
                } else {
                    Log::error("❌ Failed to send SMS to {$user->phone}");
                }
            }

            // Validate and store user details
            $detailsValidator = Validator::make($request->all(), [
                'address' => 'required|string|max:401',
                'state' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'salary' => 'nullable|numeric|min:0',
            ]);

            if ($detailsValidator->fails()) {
                DB::rollBack();
                return response()->json(['errors' => $detailsValidator->errors()], 401);
            }

            $salary = $request->filled('salary') ? $request->salary : 0;

            $details = UserDetails::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'shift' => $request->shift,
                'salary' => $salary,
            ]);

            // Validate and store permissions
            $permissions = json_decode($request->permissions, true);
            $permissionsValidator = Validator::make(['permissions' => $permissions], [
                'permissions' => 'required|array',
                'permissions.*.module_id' => 'required|exists:modules,id',
                'permissions.*.create' => 'nullable|boolean',
                'permissions.*.view' => 'nullable|boolean',
                'permissions.*.update' => 'nullable|boolean',
                'permissions.*.delete' => 'nullable|boolean',
            ]);

            if ($permissionsValidator->fails()) {
                DB::rollBack();
                return response()->json(['errors' => $permissionsValidator->errors()], 401);
            }

            foreach ($permissions as $permission) {
                if (!empty($permission['create']) || !empty($permission['view']) || !empty($permission['update']) || !empty($permission['delete'])) {
                    UserPermission::create([
                        'user_id' => $user->id,
                        'module_id' => $permission['module_id'],
                        'create' => $permission['create'] ?? false,
                        'view' => $permission['view'] ?? false,
                        'update' => $permission['update'] ?? false,
                        'delete' => $permission['delete'] ?? false,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User, Details, and Permissions created successfully',
                'user' => $user,
                'details' => $details,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while creating the user. Please try again later.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function sendNotification(Request $request, $id)
    {
        $loggedInUserId = auth()->id();

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 401);
        }

        $type = 'edit';
        $isEdit = $type == 'edit';
        $emailSent = false;
        $smsSent = false;
        $settings = (new Setting())->getSettings();
        $smtpEnabled = ($settings['smtp_status'] ?? 'off') === 'on';
        $smsEnabled = ($settings['sms_status'] ?? 'off') === 'on';

        if ($smtpEnabled && !empty($user->email)) {
            try {
                $emailSent = EmailHelper::sendEmail(
                    $user->email,
                    $isEdit ? "Your profile has been updated" : "Welcome to Our Clinic",
                    "emails.welcome",
                    ['user' => $user]
                );
            } catch (\Exception $e) {
                Log::error('Email sending failed: ' . $e->getMessage());
            }
        }

        if ($smsEnabled && !empty($user->phone)) {
            try {
                $smsHelper = new SmsHelper();
                $smsSent = $smsHelper->send_sms(
                    $user->phone,
                    $isEdit
                        ? "Hello {$user->fullname}, your account details have been updated successfully!"
                        : "Welcome {$user->fullname}, your account has been created successfully!"
                );
            } catch (\Exception $e) {
                Log::error('SMS sending failed: ' . $e->getMessage());
            }
        }

        $successChannels = [];
        $failedChannels = [];

        if ($emailSent) {
            $successChannels[] = 'Email';
        } elseif ($smtpEnabled && !empty($user->email)) {
            $failedChannels[] = 'Email';
        }

        if ($smsSent) {
            $successChannels[] = 'SMS';
        } elseif ($smsEnabled && !empty($user->phone)) {
            $failedChannels[] = 'SMS';
        }

        if (!empty($successChannels) && empty($failedChannels)) {
            $message = implode(' & ', $successChannels) . " sent to {$user->fullname} [{$user->email}]";
        } elseif (!empty($successChannels) && !empty($failedChannels)) {
            $message = implode(' & ', $successChannels) . " sent, but " . implode(' & ', $failedChannels) . " failed for {$user->fullname}";
        } elseif (!empty($failedChannels)) {
            $message = "Failed to send " . implode(' & ', $failedChannels) . " to {$user->fullname}";
        } else {
            $message = "Email and SMS notifications services are off";
        }

        Notification::store($message, $loggedInUserId, $loggedInUserId, 'user');

        return response()->json([
            'message' => $message,
            'email_sent' => $emailSent,
            'sms_sent' => $smsSent,
            'status' => true
        ]);
    }





    public function show($id)
    {
        // Fetch user with details
        $user = User::with('details')->find($id);

        if ($user) {
            // Add role and other details to the user
            $user->roleName = $user->role ? $user->role->name : 'N/A';
            $user->address = $user->details ? $user->details->address : 'N/A';
            $user->city = $user->details ? $user->details->city : 'N/A';
            $user->state = $user->details ? $user->details->state : 'N/A';
            $user->gender = $user->details ? $user->details->gender : 'N/A';
            $user->birth_date = $user->details ? $user->details->birth_date : 'N/A';
            $user->shift = $user->details ? $user->details->shift : 'N/A';
            $user->salary = $user->details ? $user->details->salary : 'N/A';
            $user->role_id = $user->role ? $user->role->id : null;
            // Fetch user permissions
            $permissions = UserPermission::where('user_id', $id)
                ->get();

            // Add permissions to the user response
            $user->permissions = $permissions;
            Log::info($user);
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found'], 401);
    }




    public function Update(Request $request, $id = null)
    {
        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
        $roleValidation = $currentProjectTypeId === 3 ? 'nullable|max:255' : 'required|max:255';

        // Validate User Input
        $userValidator = Validator::make($request->all(), [
            'role_id' => $roleValidation,
            // 'username' => "required|string|max:255|unique:user,username," . $id,
            'fullname' => 'required|string|max:255',
            'email' => "required|email|unique:user,email," . $id,
            'phone' => 'nullable|string|max:15',
            // 'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'password' => $id ? 'nullable|string|min:6' : 'required|string|min:6',
        ]);

        if ($userValidator->fails()) {
            return response()->json(['errors' => $userValidator->errors()], 401);
        }

        $user = $id ? User::findOrFail($id) : new User();

        $imagePath = null;

        if ($request->hasFile('profile')) {
            $fileValidator = Validator::make($request->all(), [
                'profile' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            if ($fileValidator->fails()) {
                return response()->json(['errors' => $fileValidator->errors()], 401);
            }

            $image = $request->file('profile');
            $destinationPath = public_path('uploads/users');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $filename);
            $imagePath = 'uploads/users/' . $filename;
        }

        if ($imagePath) {
            $user->profile = $imagePath;
        }

        // Update or Create User
        $user->role_id = $request->role_id;
        // $user->username = $request->username;
        $user->fullname = $request->fullname;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $detailsValidator = Validator::make($request->all(), [
            'address' => 'required|string|max:401',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            // 'gender' => 'required|in:Male,Female,Other',
            // 'birth_date' => 'required|date',
            // 'shift' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',
        ]);

        if ($detailsValidator->fails()) {
            return response()->json(['errors' => $detailsValidator->errors()], 401);
        }

        $salary = $request->filled('salary') ? $request->salary : 0;

        $details = UserDetails::updateOrCreate(
            ['user_id' => $user->id],
            [
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'shift' => $request->shift,
                'salary' => $salary,
            ]
        );

        $permissions = json_decode($request->permissions, true);

        $permissionsValidator = Validator::make(['permissions' => $permissions], [
            'permissions' => 'required|array',
            'permissions.*.module_id' => 'required|exists:modules,id',
            'permissions.*.create' => 'nullable|boolean',
            'permissions.*.view' => 'nullable|boolean',
            'permissions.*.update' => 'nullable|boolean',
            'permissions.*.delete' => 'nullable|boolean',
        ]);

        if ($permissionsValidator->fails()) {
            return response()->json(['errors' => $permissionsValidator->errors()], 401);
        }

        $currentPermissions = UserPermission::where('user_id', $user->id)->get();

        foreach ($permissions as $permission) {
            $permission['create'] = $permission['create'] ?? false;
            $permission['view'] = $permission['view'] ?? false;
            $permission['update'] = $permission['update'] ?? false;
            $permission['delete'] = $permission['delete'] ?? false;

            $existingPermission = $currentPermissions->firstWhere('module_id', $permission['module_id']);

            if (
                $existingPermission &&
                $existingPermission->create == $permission['create'] &&
                $existingPermission->view == $permission['view'] &&
                $existingPermission->update == $permission['update'] &&
                $existingPermission->delete == $permission['delete']
            ) {
                continue;
            }

            if ($existingPermission) {
                $existingPermission->update([
                    'create' => $permission['create'],
                    'view' => $permission['view'],
                    'update' => $permission['update'],
                    'delete' => $permission['delete'],
                ]);
            } else {
                UserPermission::create([
                    'user_id' => $user->id,
                    'module_id' => $permission['module_id'],
                    'create' => $permission['create'],
                    'view' => $permission['view'],
                    'update' => $permission['update'],
                    'delete' => $permission['delete'],
                ]);
            }
        }






        return response()->json([
            'message' => $id ? 'User updated successfully' : 'User created successfully',
            'user' => $user,
            'details' => $details,
        ], $id ? 200 : 200);
    }











    // public function destroy($id)
    // {

    //     $user = User::find($id);


    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 401);
    //     }


    //     $user->details()->delete();
    //     $user->permissions()->delete();


    //     $user->delete();


    //     return response()->json(['message' => 'User and related details deleted successfully']);
    // }


    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        // Check if the user has related treatments
        if ($user->treatments()->exists()) {
            return response()->json(['message' => 'Cannot delete user doctor. Related treatments exist.'], 400);
        }

        $user->details()->delete();
        $user->permissions()->delete();
        $user->delete();

        return response()->json(['message' => 'User and related details deleted successfully']);
    }
}
