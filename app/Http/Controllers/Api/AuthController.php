<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Branch;
use App\Models\UserDetails;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\UserPermission;
use Validator;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Models\Modules;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use App\Services\ProjectTypeSettingsService;

class AuthController extends Controller
{
    public function __construct(
        private readonly ProjectTypeSettingsService $projectTypeSettingsService
    ) {
    }

    protected function findUserByLoginEmail(string $email): ?User
    {
        $normalizedEmail = mb_strtolower(trim($email));

        if (DB::getSchemaBuilder()->hasColumn('user', 'email_search')) {
            $userId = DB::table('user')
                ->where('email_search', $normalizedEmail)
                ->value('id');

            if ($userId) {
                return User::find($userId);
            }
        }

        $user = User::where('email', $normalizedEmail)->first();

        if ($user) {
            return $user;
        }

        // Fallback for older rows where email may be encrypted without a search column.
        return User::all()->first(function (User $candidate) use ($normalizedEmail) {
            return mb_strtolower((string) $candidate->email) === $normalizedEmail;
        });
    }





    public function downloadPdf($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        if (!$invoice->pdf_url) {
            return response()->json([
                'status' => false,
                'message' => 'PDF URL not stored'
            ], 404);
        }

        // Parse URL and remove leading '/public/'
        $parsedUrl = parse_url($invoice->pdf_url, PHP_URL_PATH);
        $parsedUrl = preg_replace('/^\/public\//', '', $parsedUrl);

        // Correct file path
        $filePath = public_path($parsedUrl);

        if (!file_exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice PDF not found',
                'path_checked' => $filePath // For debugging
            ], 404);
        }

        // Prepare file URL & name
        $fileUrl = url('public/' . $parsedUrl);
        $fileName = 'invoice_' . $invoice->id . '.pdf';

        return response()->json([
            'status' => true,
            'message' => 'Invoice PDF ready to download.',
            'file_url' => $fileUrl,
            'file_name' => $fileName
        ]);
    }




    public function updateClinicDetails(Request $request)
    {
        // Validate input
        $request->validate([
            'clinic_name' => 'nullable|string|max:255',
            'clinic_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'clinic_email' => 'nullable|email|max:255',
            'clinic_phone' => 'nullable|string|max:20',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_city' => 'nullable|string|max:100',
            'clinic_state' => 'nullable|string|max:100',
            'tax_name' => 'nullable|string|max:100',
            'tax_type' => 'nullable|in:percentage,fixed_amount',
            'value' => 'nullable|numeric|min:0',
        ]);

        // Store basic info in settings
        $fields = [
            'clinic_name' => $request->clinic_name,
            'clinic_email' => $request->clinic_email,
            'clinic_phone' => $request->clinic_phone,
            'clinic_address' => $request->clinic_address,
            'clinic_city' => $request->clinic_city,
            'clinic_state' => $request->clinic_state,
            'tax_name' => $request->tax_name,

            'tax_type' => $request->tax_type,
            'value' => $request->value,
        ];



        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                Setting::updateOrInsert(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Handle logo upload
        $imagePath = null;
        if ($request->hasFile('clinic_logo')) {
            $image = $request->file('clinic_logo');
            $destinationPath = public_path('uploads/clinic_logos');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $filename);
            $imagePath = 'uploads/clinic_logos/' . $filename;

            Setting::updateOrInsert(
                ['key' => 'clinic_logo'],
                ['value' => $imagePath]
            );
        }

        // return response()->json([
        //     'message' => 'Clinic details updated successfully',
        //     'clinic_name' => Setting::where('key', 'clinic_name')->value('value'),
        //     'clinic_logo' => Setting::where('key', 'clinic_logo')->value('value'),
        // ], 200);

        return response()->json([
            'status' => true,
            'message' => 'Clinic information updated successfully.',
        ]);
    }









    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user,email'
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);
        $user->reset_token = $token;
        $user->save();

        // Send Reset Email
        // Mail::send('', ['token' => ], function ($message) use ($user) {
        //     $message->to($user->email);
        //     $message->subject('Password Reset Request');
        // });

        EmailHelper::sendEmail('dishafablead82@gmail.com', 'FORGOT PASSWORD ', 'emails.reset-password', ['token' => $token], null);

        return response()->json(['message' => 'Reset link sent to your email.']);
    }


    public function resetPassword(Request $request)
    {

        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);


        $user = User::where('reset_token', $request->token)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid token.'], 400);
        }


        $user->password = Hash::make($request->password);

        $user->reset_token = null;
        $user->save();


        return response()->json(['message' => 'Password reset successfully.'], 200);
    }



    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = trim($request->email);

    $maxAttempts = 5;
    $lockMinutes = 5;

    $attemptKey = 'login_attempts_' . $email;
    $lockKey = 'login_lock_' . $email;

    // Check if user is locked
    if (Cache::has($lockKey)) {

        $remainingSeconds = Cache::get($lockKey) - time();

        return response()->json([
            'status' => false,
            'message' => 'Too many login attempts. Try again later.',
            'lock_time' => $remainingSeconds
        ], 429);
    }

    $user = $this->findUserByLoginEmail($email);

    if (!$user || !Hash::check($request->password, $user->password)) {

        $attempts = Cache::increment($attemptKey);

        Cache::put($attemptKey, $attempts, now()->addMinutes($lockMinutes));

        if ($attempts >= $maxAttempts) {

            Cache::put($lockKey, time() + ($lockMinutes * 60), now()->addMinutes($lockMinutes));

            return response()->json([
                'status' => false,
                'message' => "Too many attempts. Login disabled for $lockMinutes minutes.",
                'lock_time' => $lockMinutes * 60
            ], 429);
        }

        return response()->json([
            'status' => false,
            'message' => 'Username or password is invalid.',
            'attempts_left' => $maxAttempts - $attempts
        ], 401);
    }

    // Check if user's plan has expired
    $planExpiredStatus = $this->checkPlanExpiration($user);
    if ($planExpiredStatus['expired']) {
        return response()->json([
            'status' => false,
            'message' => 'Your subscription plan has expired. Please renew to continue.',
            'plan_expired' => true,
            'plan_data' => $planExpiredStatus['plan_data']
        ], 403);
    }

    // Reset attempts after successful login
    Cache::forget($attemptKey);
    Cache::forget($lockKey);

    Auth::login($user);
    $this->projectTypeSettingsService->ensureDefaultProjectType();
    $user = Auth::user();
    $token = $user->createToken('authToken')->plainTextToken;

    $permissionsFormatted = [];
    $modules = Modules::all();

    if (strtolower($user->role->name ?? '') === 'admin') {

        foreach ($modules as $module) {
            $permissionsFormatted[$module->name] = [
                'id' => null,
                'user_id' => (string) $user->id,
                'module_id' => (string) $module->id,
                'view' => "1",
                'create' => "1",
                'update' => "1",
                'delete' => "1",
                'created_at' => now()->toDateTimeString(),
            ];
        }

        $branch = Branch::first();

    } else {

        $userPermissions = UserPermission::where('user_id', $user->id)->get()->keyBy('module_id');

        foreach ($modules as $module) {

            $perm = $userPermissions->get($module->id);

            $permissionsFormatted[$module->name] = [
                'id' => $perm ? (string) $perm->id : null,
                'user_id' => (string) $user->id,
                'module_id' => (string) $module->id,
                'view' => $perm ? (string) $perm->view : "0",
                'create' => $perm ? (string) $perm->create : "0",
                'update' => $perm ? (string) $perm->update : "0",
                'delete' => $perm ? (string) $perm->delete : "0",
                'created_at' => $perm ? $perm->created_at->toDateTimeString() : now()->toDateTimeString(),
            ];
        }

        $branch = Branch::find($user->branch_id);
    }

    session([
        'access_token' => $token,
        'user_id' => $user->id,
        'role' => $user->role->name ?? null,
        'permissions' => $permissionsFormatted,
        'branch_id' => $branch->id ?? null,
        'branch_name' => $branch->name ?? null,
        'show_today_appointment_modal' => true
    ]);

    $userData = [
        'id' => $user->id,
        'branch_id' => $user->branch_id ?? null,
        'branch_name' => $branch->name ?? null,
        'fullname' => $user->fullname,
        'email' => $user->email,
        'phone' => $user->phone,
        'profile' => url('public/profile/' . ($user->profile ?? 'default.png')),
        'role' => $user->role->name ?? 'client',
        'status' => $user->status,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ];

    $responseData = array_merge(
        ['users' => $userData],
        ['token' => $token],
        $permissionsFormatted
    );

    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'data' => $responseData
    ]);
}



    
    public function loginAPIs(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->findUserByLoginEmail($request->email);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email does not exist.',
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username or password is invalid.',
            ], 401);
        }

        // Check if user's plan has expired
        $planExpiredStatus = $this->checkPlanExpiration($user);
        if ($planExpiredStatus['expired']) {
            return response()->json([
                'status' => false,
                'message' => 'Your subscription plan has expired. Please renew to continue.',
                'plan_expired' => true,
                'plan_data' => $planExpiredStatus['plan_data']
            ], 403);
        }

        Auth::login($user);
        $this->projectTypeSettingsService->ensureDefaultProjectType();
        $user = Auth::user();

        if ($request->filled('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        $token = $user->createToken('authToken')->plainTextToken;

        $permissionsFormatted = [];
        $modules = Modules::all(); // Get all modules

        if (strtolower($user->role->name ?? '') === 'admin') {
            // Admin: give full permissions for all modules
            foreach ($modules as $module) {
                $permissionsFormatted[$module->name] = [
                    'id' => null,
                    'user_id' => (string) $user->id,
                    'module_id' => (string) $module->id,
                    'view' => "1",
                    'create' => "1",
                    'update' => "1",
                    'delete' => "1",
                    'created_at' => now()->toDateTimeString(),
                ];
            }
        } else {
            // Non-admin: fetch user permissions from DB
            $userPermissions = UserPermission::where('user_id', $user->id)->get()->keyBy('module_id');

            foreach ($modules as $module) {
                $perm = $userPermissions->get($module->id);
                $permissionsFormatted[$module->name] = [
                    'id' => $perm ? (string) $perm->id : null,
                    'user_id' => (string) $user->id,
                    'module_id' => (string) $module->id,
                    'view' => $perm ? (string) $perm->view : "0",
                    'create' => $perm ? (string) $perm->create : "0",
                    'update' => $perm ? (string) $perm->update : "0",
                    'delete' => $perm ? (string) $perm->delete : "0",
                    'created_at' => $perm ? $perm->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            }
        }
        // $branch = Branch::first();
        // $branch = Branch::find($user->branch_id);
        if (strtolower($user->role->name ?? '') === 'admin') {
            $branch = Branch::first();
        } else {
            $branch = Branch::find($user->branch_id);
        }
        // Prepare user data
        $userData = [
            'id' => $user->id,
            'branch_id' => $user->branch_id ?? null,
            'branch_name' => $branch->name ?? null, //  Add branch name
            'fullname' => $user->fullname,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'phone' => $user->phone,
            'profile' => url('public/profile/' . ($user->profile ?? 'default.png')),
            'role' => $user->role->name ?? 'client',
            'status' => $user->status,
            'fcm_token' => $user->fcm_token,
            'forget_pass_key' => $user->forget_pass_key,
            'is_deleted' => $user->is_deleted,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        $responseData = array_merge(
            ['users' => $userData],
            ['token' => $token],
            $permissionsFormatted
        );

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => $responseData
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
            Auth::logout();
        }

        session()->invalidate();
        session()->regenerateToken();
        Session::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
                'data' => null
            ]);
        }

        return redirect('/');
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function profile(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'User not found'], 401);
        }

        // Eager load the 'role' and 'details' relationships
        $user = Auth::user()->load('role', 'details');

        return response()->json([
            'user' => $user,
            'userId' => $user->id,
            'userDetails' => $user->details,
            'role' => $user->role,
        ]);
    }

    public function updateProfile(Request $request)
    {

        $validated = $request->validate([
            // 'username' => 'required|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:15',
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'profile' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'shift' => 'nullable|string|max:50',
        ]);


        $user = $request->user();


        $imagePath = null;

        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/users');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // Move file to public folder

            $image->move($destinationPath, $filename);

            // Store relative path in the database
            $imagePath = 'uploads/users/' . $filename;
        }
        if ($imagePath) {
            $user->profile = $imagePath;
        }

        // Update user details
        $user->update([
            // 'username' => $validated['username'],
            'email' => $validated['email'],
            // 'profile' => $imagePath ?? $user->profile, // ✅ Corrected this line
            'fullname' => $validated['fullname'],
            'phone' => $validated['phone']
        ]);


        $userDetails = $user->details;
        if (!$userDetails) {

            $userDetails = new UserDetails();
            $userDetails->user_id = $user->id;
        }



        $userDetails->gender = $validated['gender'] ?? $userDetails->gender;
        $userDetails->birth_date = $validated['birth_date'] ?? $userDetails->birth_date;
        $userDetails->address = $validated['address'] ?? $userDetails->address;
        $userDetails->city = $validated['city'] ?? $userDetails->city;
        $userDetails->state = $validated['state'] ?? $userDetails->state;
        $userDetails->shift = $validated['shift'] ?? $userDetails->shift;

        $userDetails->fill([
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'address' => $validated['address'],
            'shift' => $validated['shift'],
            'city' => $validated['city'],
            'state' => $validated['state'],
        ]);

        $userDetails->save(); // This will handle both insert and update

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function changePassword(Request $request)
    {
        // echo "abc";
        // die;
        // $request->validate([
        //     'current_password' => 'required',
        //     'new_password' => 'required|min:6|confirmed',
        // ]);

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 401);
        }

        $user = Auth::user();

        // print_r($user);
        // die; 
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    /**
     * Check if user's plan has expired
     * @return array with 'expired' status and plan data
     */
    private function checkPlanExpiration($user)
    {
        if (!$user || !$user->plan_id) {
            return ['expired' => false, 'plan_data' => null];
        }

        $plan = \App\Models\Plan::find($user->plan_id);
        if (!$plan || !$plan->end_date) {
            return ['expired' => false, 'plan_data' => null];
        }

        $today = \Carbon\Carbon::today();
        $expiryDate = \Carbon\Carbon::parse($plan->end_date);

        // If plan has expired (end_date is in the past)
        if ($expiryDate->isPast() || $expiryDate->isToday()) {
            return [
                'expired' => true,
                'plan_data' => [
                    'plan_name' => $plan->name,
                    'expiry_date' => $expiryDate->format('d M Y'),
                    'expired_days' => $today->diffInDays($expiryDate, false),
                    'price' => $plan->price,
                    'duration' => $plan->duration
                ]
            ];
        }

        return ['expired' => false, 'plan_data' => null];
    }
}

