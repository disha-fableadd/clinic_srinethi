<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Invoice;
use App\Models\Medicine;
use App\Models\PathologyTest;
use App\Models\Patients;
use App\Models\RadiologyTest;
use App\Models\Services;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Therapy;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use App\Models\DailyData;
use App\Models\MedicalReport;
use App\Models\PatientDischargeDets;
use App\Models\TreatmentBooking;
use App\Models\ProjectType;

class SettingController extends Controller
{



    public function notificationView(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->name ?? null;

        if ($userRole == 'Admin') {
            // Admin can see all notifications
            $notifications = Notification::orderBy('created_at', 'desc')->get();
        } elseif ($userRole == 'Patient') {
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();

            if ($patient) {
                $notifications = Notification::where('receiver_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No notifications found for this patient.',
                    'data' => [],
                    'totalCount' => 0,
                    'unreadCount' => 0,
                    'readCount' => 0
                ], 404);
            }
        } else {
            // Others see only their notifications
            $notifications = Notification::where('receiver_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Count totals
        $totalCount = $notifications->count();
        $unreadCount = $notifications->where('is_read', false)->count();
        $readCount = $notifications->where('is_read', true)->count();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'totalCount' => $totalCount,
            'unreadCount' => $unreadCount,
            'readCount' => $readCount
        ]);
    }


    public function update(Request $request, string $id)
    {
        $notification = Notification::findOrFail($id);
        // print_r($notification);
        if ($notification->read == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Notification is already marked as read',
            ]);
        }
        if ($notification->receiver_id != Auth::user()->id && Auth::user()->role->name != 'Admin') {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to mark this notification as read',
            ]);
        }



        $notification->update(['is_read' => 1]);
        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    public function saveSmsStatus(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'sms_status'],
            ['value' => $request->status]
        );

        return response()->json(['success' => true, 'status' => $request->status]);
    }

    public function saveProjectType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_type_id' => ['required', 'integer', 'exists:project_types,id'],
        ]);

        $projectType = ProjectType::findOrFail($validated['project_type_id']);

        Setting::updateOrCreate(
            ['key' => 'project_type_id'],
            ['value' => (string) $projectType->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Project type updated successfully.',
            'data' => [
                'project_type_id' => (int) $projectType->id,
                'name' => $projectType->name,
                'code' => $projectType->code,
            ],
        ]);
    }

    public function saveWhatsappStatus(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'whatsapp_status'],
            ['value' => $request->status]
        );

        return response()->json(['success' => true, 'status' => $request->status]);
    }

    public function saveSettings(Request $request)
    {

        // Validate incoming data (optional but recommended)
        $request->validate([
            'firebasejson' => 'nullable|file|mimes:json|max:2048',
        ]);

        // Handle Firebase JSON file upload
        $firebaseJsonPath = null;
        if ($request->hasFile('firebase_json')) {
            $file = $request->file('firebase_json');
            $filename = 'firebase_' . time() . '.' . $file->getClientOriginalExtension();
            $firebaseJsonPath = $file->storeAs('firebase', $filename, 'public');
        }

        $settings = [
            'default_sms_service' => $request->sms_method,
            'twilio_sid' => $request->twilio_sid,
            'twilio_token' => $request->twilio_token,
            'twilio_number' => $request->twilio_number,
            'two_factor_api_key' => $request->two_factor_api_key,

            'mail_host' => $request->mail_host,
            'mail_mailer' => $request->mail_mailer,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,


            'whatsapp_token' => $request->whatsapp_token,
            'whatsapp_phone_number_id' => $request->whatsapp_phone_number_id,
            'whatsapp_api_url' => $request->whatsapp_api_url,
            'apikey' => $request->apikey,
            'auth_domain' => $request->auth_domain,
            //  'firebase_api_key' => $request->firebase_api_key,
            // 'firebase_auth_domain' => $request->firebase_auth_domain,
            // 'firebase_database_url' => $request->firebase_database_url,
            // 'firebase_project_id' => $request->firebase_project_id,
            // 'firebase_storage_bucket' => $request->firebase_storage_bucket,
            // 'firebase_messaging_sender_id' => $request->firebase_messaging_sender_id,
            // 'firebase_app_id' => $request->firebase_app_id,
            'firebase_json' => $firebaseJsonPath,
        ];

        // dd($settings);
        foreach ($settings as $key => $value) {
            // Only save non-empty values
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json(['message' => 'Settings saved successfully'], 200);
    }


    public function getSmsSettings(Request $request)
    {
        // Check for sms_method parameter
        $smsMethod = $request->sms_method;

        // Retrieve SMS settings if sms_method is provided
        if ($smsMethod) {
            // Fetch SMS-related settings
            $smsSettings = Setting::whereIn('key', [
                'twilio_sid',
                'twilio_token',
                'twilio_number',
                'two_factor_api_key'
            ])->get()->pluck('value', 'key');

            return response()->json([
                'data' => [
                    'twilio_sid' => $smsSettings['twilio_sid'] ?? '',
                    'twilio_token' => $smsSettings['twilio_token'] ?? '',
                    'twilio_number' => $smsSettings['twilio_number'] ?? '',
                    'two_factor_api_key' => $smsSettings['two_factor_api_key'] ?? ''
                ]
            ]);
        }

        // If sms_method is not provided, fetch email settings
        $emailSettings = Setting::whereIn('key', [
            'mail_host',
            'mail_mailer',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name'
        ])->get()->pluck('value', 'key');

        return response()->json([
            'data' => [
                'mail_host' => $emailSettings['mail_host'] ?? '',
                'mail_mailer' => $emailSettings['mail_mailer'] ?? '',
                'mail_port' => $emailSettings['mail_port'] ?? '',
                'mail_username' => $emailSettings['mail_username'] ?? '',
                'mail_password' => $emailSettings['mail_password'] ?? '',
                'mail_encryption' => $emailSettings['mail_encryption'] ?? '',
                'mail_from_address' => $emailSettings['mail_from_address'] ?? '',
                'mail_from_name' => $emailSettings['mail_from_name'] ?? ''
            ]
        ]);
    }


    public function getStatus()
    {
        return response()->json([
            'sms_status' => Setting::where('key', 'sms_status')->value('value') ?? 'off',
            'whatsapp_status' => Setting::where('key', 'whatsapp_status')->value('value') ?? 'off',
            'smtp_status' => Setting::where('key', 'smtp_status')->value('value') ?? 'off',
            'firebase_status' => Setting::where('key', 'firebase_status')->value('value') ?? 'off',
            'razorpay_status' => Setting::where('key', 'razorpay_status')->value('value') ?? 'off',
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sms,whatsapp,smtp,firebase,razorpay',
            'status' => 'required|in:on,off',
        ]);

        // Check Razorpay keys if trying to turn it ON
        if ($request->type === 'razorpay' && $request->status === 'on') {
            $razorpayKey = Setting::where('key', 'razorpay_key')->value('value');
            $razorpaySecret = Setting::where('key', 'razorpay_secret')->value('value');

            if (!$razorpayKey || !$razorpaySecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add Razorpay Key and Secret before enabling the service.'
                ], 400);
            }
        }

        // Update the status
        Setting::updateOrCreate(
            ['key' => $request->type . '_status'],
            ['value' => $request->status]
        );

        return response()->json(['success' => true]);
    }


    public function getWhatsappSettings()
    {
        $settings = \DB::table('settings')
            ->whereIn('key', ['whatsapp_token', 'whatsapp_phone_number_id', 'whatsapp_api_url'])
            ->pluck('value', 'key');

        return response()->json(['data' => $settings]);
    }

    public function getFirebaseSettings(): JsonResponse
    {
        // Get the stored file path from settings
        $firebaseJsonPath = Setting::where('key', 'firebase_json')->value('value');

        // Extract the file name if path exists
        $firebaseJsonName = $firebaseJsonPath ? basename($firebaseJsonPath) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'firebase_json_name' => $firebaseJsonName,
                'firebase_json_path' => $firebaseJsonPath ? asset('storage/' . $firebaseJsonPath) : null,
            ],
        ]);
    }

    public function downloadFirebaseJson()
    {
        $firebaseJsonPath = Setting::where('key', 'firebase_json')->value('value');

        if (!$firebaseJsonPath || !Storage::disk('public')->exists($firebaseJsonPath)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return Storage::disk('public')->download($firebaseJsonPath);
    }



    // razorpay
    // public function saveRazorpaySettings(Request $request): JsonResponse
    // {
    //     Setting::updateOrCreate(
    //         ['key' => 'razorpay_key'],
    //         ['value' => $request->razorpay_key]
    //     );

    //     // Store secret in PLAIN TEXT (no encryption)
    //     if ($request->filled('razorpay_secret')) {
    //         Setting::updateOrCreate(
    //             ['key' => 'razorpay_secret'],
    //             ['value' => $request->razorpay_secret]
    //         );
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Razorpay settings saved successfully'
    //     ]);
    // }


    public function saveRazorpaySettings(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_key' => 'required|string',
            'razorpay_secret' => 'required|string',
        ]);

        try {
            // 🔍 TRY CONNECTING TO RAZORPAY
            $api = new Api($request->razorpay_key, $request->razorpay_secret);

            // Simple test API call
            $api->order->all(['count' => 1]);

            // ✅ IF NO EXCEPTION → CREDENTIALS ARE VALID
            Setting::updateOrCreate(
                ['key' => 'razorpay_key'],
                ['value' => $request->razorpay_key]
            );

            Setting::updateOrCreate(
                ['key' => 'razorpay_secret'],
                ['value' => $request->razorpay_secret]
            );

            return response()->json([
                'success' => true,
                'status' => 'connected',
                'message' => 'Razorpay connected successfully. Credentials are valid.'
            ]);

        } catch (\Exception $e) {
            // ❌ INVALID CREDENTIALS OR CONNECTION ISSUE
            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => 'Razorpay connection failed.Please check your key ID and Secret.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /* ===============================
       GET RAZORPAY SETTINGS
    =============================== */

    public function getRazorpaySettings(): JsonResponse
    {
        $razorpayKey = Setting::where('key', 'razorpay_key')->value('value');
        $razorpaySecret = Setting::where('key', 'razorpay_secret')->value('value');

        return response()->json([
            'success' => true,
            'data' => [
                'razorpay_key' => $razorpayKey,
                'razorpay_secret' => $razorpaySecret, // 👈 REAL VALUE
            ],
        ]);
    }

    // daily data
    public function generateLink(Request $request)
    {
        // ✅ 0️⃣ Check Razorpay service status
        $razorpayStatus = Setting::where('key', 'razorpay_status')->value('value');

        if ($razorpayStatus !== 'on') {
            return response()->json([
                'message' => 'Payment Link service is currently OFF. Please enable it from settings.'
            ], 403);
        }

        // 1️⃣ Get DailyData with patient
        $dailyData = DailyData::with('patient')->findOrFail($request->daily_id);

        $patientPhone = $dailyData->patient->phone ?? null;
        $patientName = $dailyData->patient->fullname ?? null;

        // 2️⃣ Razorpay API
        $api = new Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );

        // 3️⃣ Create payment link
        $paymentLink = $api->paymentLink->create([
            'amount' => $request->amount * 100,
            'currency' => 'INR',
            'description' => 'Name :- ' . $patientName . ' || Phone :- ' . $patientPhone,
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => [
                'sms' => true,
                'email' => false
            ],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
        ]);
    }

    //header button
    public function generateLinkpatient(Request $request)
    {
        if (Setting::where('key', 'razorpay_status')->value('value') !== 'on') {
            return response()->json(['message' => 'Payment Link service is OFF'], 403);
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($request->amount <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'No pending amount to generate link'
            ], 422);
        }

        $patient = Patients::findOrFail($request->patient_id);

        $api = new Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );
        $patientName = $patient->fullname;
        $patientPhone = $patient->phone;

        $paymentLink = $api->paymentLink->create([
            'amount' => $request->amount * 100,
            'currency' => 'INR',
            'description' => "Name :- {$patientName} || Phone :- {$patientPhone}",
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => ['sms' => true],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'status' => true,
            'payment_link' => $paymentLink['short_url'],
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
        ]);
    }



    public function getPendingAmounts($patientId)
    {
        // Daily Data pending
        $dailyAmount = DailyData::where('patient_id', $patientId)
            ->where('status', 'pending')
            ->sum('remain_amount');

        // Treatment pending
        $treatmentAmount = TreatmentBooking::where('patient_id', $patientId)
            ->where('status', 'pending')
            ->sum('remain_amount');

        // IPD Discharge pending
        $dischargeAmount = PatientDischargeDets::where('patient_id', $patientId)
            ->where('payment_status', 'unpaid')
            ->sum(DB::raw('total_bill - amount_paid'));

        $total = $dailyAmount + $treatmentAmount + $dischargeAmount;

        return response()->json([
            'daily' => $dailyAmount,
            'treatment' => $treatmentAmount,
            'discharge' => $dischargeAmount,
            'total' => $total,
        ]);
    }




    // treatment-booking

    public function generateLinkTreatmentBooking(Request $request)
    {
        // ✅ 0️⃣ Razorpay status check
        if (Setting::where('key', 'razorpay_status')->value('value') !== 'on') {
            return response()->json([
                'message' => 'Payment Link service is currently OFF. Please enable it from settings.'
            ], 403);
        }

        // 1️⃣ Get TreatmentBooking with patient
        $booking = TreatmentBooking::with('patient')->findOrFail($request->booking_id);

        $patientPhone = $booking->patient->phone ?? null;
        $patientName = $booking->patient->fullname ?? null;
        $amount = $booking->remain_amount ?? 0;

        if ($amount <= 0) {
            return response()->json([
                'message' => 'No pending amount for payment.'
            ], 400);
        }

        // 2️⃣ Razorpay API
        $api = new \Razorpay\Api\Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );

        // 3️⃣ Create payment link
        $paymentLink = $api->paymentLink->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'description' => "Name :- {$patientName} || Phone :- {$patientPhone}",
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => [
                'sms' => true,
                'email' => false
            ],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
            'amount' => $amount,
        ]);
    }

    // discharge
    public function generateDischargePaymentLink(Request $request)
    {
        if (Setting::where('key', 'razorpay_status')->value('value') !== 'on') {
            return response()->json([
                'message' => 'Payment Link service is currently OFF. Please enable it from settings.'
            ], 403);
        }
        $discharge = PatientDischargeDets::with('patient')
            ->findOrFail($request->discharge_id);

        // ✅ BLOCK if already paid
        if ($discharge->payment_status === 'paid') {
            return response()->json([
                'message' => 'No pending amount. Payment already completed.'
            ], 400);
        }

        $patientPhone = $discharge->patient->phone ?? null;
        $patientName = $discharge->patient->fullname ?? null;
        $amount = $discharge->total_bill ?? 0;

        if ($amount <= 0) {
            return response()->json([
                'message' => 'No pending amount for payment.'
            ], 400);
        }

        $api = new \Razorpay\Api\Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );

        $paymentLink = $api->paymentLink->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'description' => 'Name :- ' . $patientName . ' || Phone :- ' . $patientPhone,
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => [
                'sms' => true,
                'email' => false
            ],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
            'amount' => $amount,
        ]);
    }

    // medical report
    public function generateReportPaymentLink(Request $request)
    {
        if (Setting::where('key', 'razorpay_status')->value('value') !== 'on') {
            return response()->json([
                'message' => 'Payment Link service is currently OFF. Please enable it from settings.'
            ], 403);
        }

        $request->validate([
            'report_id' => 'required|exists:medical_reports,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $report = MedicalReport::with('patient')->findOrFail($request->report_id);

        // ✅ Block if already paid
        if ($report->payment_status === 'paid') {
            return response()->json([
                'message' => 'Payment already completed.'
            ], 400);
        }

        $patientName = $report->patient->fullname ?? 'Patient';
        $patientPhone = $report->patient->phone ?? null;
        $amount = $request->amount; // ✅ dynamic amount

        $api = new \Razorpay\Api\Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );

        $paymentLink = $api->paymentLink->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'description' => "Name :- {$patientName} || Phone :- {$patientPhone}",
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => [
                'sms' => true,
                'email' => false
            ],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
            'amount' => $amount,
        ]);
    }


    // invoice
    public function generateInvoicePaymentLink(Request $request)
    {
        if (Setting::where('key', 'razorpay_status')->value('value') !== 'on') {
            return response()->json([
                'message' => 'Payment Link service is currently OFF. Please enable it from settings.'
            ], 403);
        }

        $invoice = Invoice::with('patient')->findOrFail($request->invoice_id);

        // ✅ Block if already paid
        if ($invoice->payment_status === 'paid') {
            return response()->json([
                'message' => 'No pending amount. Payment already completed.'
            ], 400);
        }

        $patientName = $invoice->patient->fullname ?? 'Customer';
        $patientPhone = $invoice->patient->phone ?? null;
        $amount = $invoice->grand_total ?? 0;

        if ($amount <= 0) {
            return response()->json([
                'message' => 'No pending amount for payment.'
            ], 400);
        }

        $api = new \Razorpay\Api\Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );

        $paymentLink = $api->paymentLink->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'description' => "Name :- {$patientName} || Phone :- {$patientPhone}",
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => [
                'sms' => true,
                'email' => false
            ],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'customer_name' => $patientName,
            'customer_phone' => $patientPhone,
            'amount' => $amount,
        ]);
    }

    // appointment
    public function generateAppointmentPaymentLink(Request $request)
    {
        if (Setting::where('key', 'razorpay_status')->value('value') !== 'on') {
            return response()->json([
                'message' => 'Payment Link service is currently OFF. Please enable it from settings.'
            ], 403);
        }

        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $appointment = Appointments::with('patient')->findOrFail($request->appointment_id);

        if ($appointment->payment_status === 'paid') {
            return response()->json([
                'message' => 'Payment already completed.'
            ], 400);
        }

        $patientName = $appointment->patient->fullname ?? 'Patient';
        $patientPhone = $appointment->patient->phone ?? null;
        $amount = $request->amount;

        $api = new \Razorpay\Api\Api(
            Setting::getValue('razorpay_key'),
            Setting::getValue('razorpay_secret')
        );

        $paymentLink = $api->paymentLink->create([
            'amount' => $amount * 100,
            'currency' => 'INR',
            'description' => "Name :- {$patientName} || Phone :- {$patientPhone}",
            'customer' => [
                'name' => $patientName,
                'contact' => $patientPhone,
            ],
            'notify' => [
                'sms' => true,
                'email' => false
            ],
            'reminder_enable' => true,
        ]);

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'patient_name' => $patientName,
            'patient_phone' => $patientPhone,
            'amount' => $amount,
        ]);
    }










    public function pdf($id)
    {
        try {
            $invoice = Invoice::with('patient')->findOrFail($id);

            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
                'clinic_city',
                'clinic_state',
                'tax_name',
                'tax_type' // Add tax_type to the settings keys
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo); // for PDF compatibility

            $clinic_name = $settings['clinic_name'] ?? 'Sunshine Clinic';
            $clinic_address = $settings['clinic_address'] ?? '123 Health Street';
            $clinic_phone = $settings['clinic_phone'] ?? 'dummy phone';
            $clinic_email = $settings['clinic_email'] ?? 'clinic@example.com';
            $clinic_city = $settings['clinic_city'] ?? 'xyz';
            $clinic_state = $settings['clinic_state'] ?? 'xyz';
            $tax_name = $settings['tax_name'] ?? 'Tax';
            $tax_type = $settings['tax_type'] ?? 'percentage'; // Default to percentage if not set

            // dd($tax_type);
            $services = is_array($invoice->types_details)
                ? $invoice->types_details
                : json_decode($invoice->types_details, true);

            $subtotal = collect($services)->sum(function ($item) {
                return (float) ($item['total'] ?? 0);
            });

            $tax = $invoice->tax ?? 0;
            $discount = $invoice->discount ?? 0;
            $discount_type = $invoice->discount_type ?? 'fixed_amount';

            $taxAmount = ($tax_type === 'percentage')
                ? ($subtotal * $tax) / 100
                : $tax;

            $discountAmount = $discount_type === 'percentage'
                ? (($subtotal + $taxAmount) * $discount) / 100
                : $discount;

            $grandTotal = ($subtotal - $discountAmount) + $taxAmount;

            $invoiceData = [
                'invoice_no' => $invoice->type_id,
                'date' => \Carbon\Carbon::parse($invoice->date)->format('d-m-Y'),
                'patient' => $invoice->patient,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $clinic_name,
                'clinic_address' => $clinic_address,
                'clinic_phone' => $clinic_phone,
                'clinic_email' => $clinic_email,
                'clinic_city' => $clinic_city,
                'clinic_state' => $clinic_state,
                'tax_name' => $tax_name,
                'tax_type' => $tax_type, // Include tax_type in the data
                'services' => $services,
                'invoice_type' => $invoice->type,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'discount_amount' => $discountAmount,
                'discount_type' => $discount_type,
                'grand_total' => $grandTotal,
                'payment_type' => $invoice->payment_type ?? 'N/A',
                'payment_status' => $invoice->payment_status ?? 'N/A',
            ];
            // dd($invoiceData);

            $pdf = Pdf::loadView('invoice.pdf', $invoiceData)->setPaper('A4', 'portrait');

            return $pdf->download("Invoice_{$invoice->type}_{$invoice->type_id}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }

    public function invoice(Request $request, $id = null)
    {
        $userId = $request->user()->id;

        // Get branch_id from session, fallback to user's branch_id if not set
        $branchId = session('branch_id') ?? $request->user()->branch_id;
        // dd($branchId);
        // Patients (branch-wise)
        $patients = Patients::where('branch_id', $branchId)->get();

        // Treatments (branch-wise)
        $treatments = Treatment::where('branch_id', $branchId)->get();

        // Medicines (branch-wise)
        $medicines = Medicine::where('branch_id', $branchId)->get();

        // Pathology Services (branch-wise) – include gst_option and product_gst for invoice GST display
        $pathologyServices = PathologyTest::where('branch_id', $branchId)->get()
            ->map(function ($item) {
                return [
                    'id' => 'pathology_' . $item->id,
                    'name' => $item->test_name,
                    'cost' => $item->cost,
                    'service_type' => 'pathology',
                    'gst_option' => $item->gst_option ?? 'Without GST',
                    'product_gst' => $item->product_gst,
                ];
            });

        // Radiology Services (branch-wise) – include gst_option and product_gst for invoice GST display
        $radiologyServices = RadiologyTest::where('branch_id', $branchId)->get()
            ->map(function ($item) {
                return [
                    'id' => 'radiology_' . $item->id,
                    'name' => $item->test_name,
                    'cost' => $item->cost,
                    'service_type' => 'radiology',
                    'gst_option' => $item->gst_option ?? 'Without GST',
                    'product_gst' => $item->product_gst,
                ];
            });

        $services = $pathologyServices->concat($radiologyServices)->values();

        // Therapies (branch-wise)
        $therapies = Therapy::where('branch_id', $branchId)->get();

        // Appointments (branch + completed)
        $appointments = Appointments::with('treatment')
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'name' => 'Appointment #' . $appointment->id . ' - ' . ($appointment->treatment->name ?? 'No Treatment'),
                    'unit' => $appointment->treatment->unit ?? 0,
                    'treatment_id' => $appointment->treatment_id,
                ];
            });

        // Tax Settings
        $taxLabel = DB::table('settings')->where('key', 'tax_name')->value('value') ?? 'Tax';
        $taxValue = DB::table('settings')->where('key', 'value')->value('value') ?? '0';
        $taxtype = DB::table('settings')->where('key', 'tax_type')->value('value') ?? 'percentage';

        return view('invoice.create', compact(
            'patients',
            'treatments',
            'services',
            'medicines',
            'appointments',
            'therapies',
            'taxLabel',
            'taxValue',
            'taxtype'
        ));
    }



    public function generateInvoice(Request $request)
    {
        try {
            // Step 1: Validate Input
            $validatorRules = [
                'invoice_type' => 'required|in:treatment,service,medicine,followup,appointment,therapy,discharge',
                'patient_id' => 'required|exists:patients,id',
                'branch_id' => 'required|exists:branches,id', // ✅ ADD THIS
                'items' => 'required|array|min:1',
                // service invoice uses ids like pathology_1 / radiology_1; others are integers
                'items.*.type_service' => 'required',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.total' => 'required|numeric|min:0',
                'items.*.gst_option' => 'nullable|string|in:With GST,Without GST',
                'items.*.product_gst' => 'nullable',
                'grandTotal' => 'required|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'discount_type' => 'nullable|in:fixed_amount,percentage',
                'discount' => 'nullable|numeric|min:0',
                'payment_type' => 'required|in:Cash,Online',
                'payment_status' => 'required|in:Paid,Pending',
            ];

            if ($request->invoice_type === 'service') {
                $validatorRules['items.*.type_service'] = ['required', 'string', 'regex:/^(pathology|radiology)_\\d+$/'];
            } else if (!in_array($request->invoice_type, ['appointment', 'discharge'])) {
                $validatorRules['items.*.type_service'] = ['required', 'integer'];
            }

            if (in_array($request->invoice_type, ['appointment', 'discharge'])) {
                $validatorRules['items.*.type_service'] = 'nullable';
                $validatorRules['items.*.quantity'] = 'nullable|integer';
            }

            $validator = Validator::make($request->all(), $validatorRules);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
            }

            // Step 2: Manual service validation
            if ($request->invoice_type === 'service') {
                foreach ($request->items as $item) {
                    $sid = $item['type_service'] ?? null;
                    if (!$sid) continue;
                    if (!is_string($sid) || !preg_match('/^(pathology|radiology)_(\\d+)$/', $sid, $m)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Invalid service ID: {$sid}"
                        ], 422);
                    }
                    $model = $m[1] === 'pathology' ? PathologyTest::class : RadiologyTest::class;
                    $valid = $model::find((int) $m[2]);
                    if (!$valid) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Invalid service ID: {$sid}"
                        ], 422);
                    }
                }
            }
            if ($request->invoice_type === 'therapy') {
                foreach ($request->items as $item) {
                    $tid = $item['type_service'] ?? null;
                    if (!$tid) continue;
                    if (!Therapy::find($tid)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Invalid therapy ID: {$tid}"
                        ], 422);
                    }
                }
            }

            // Step 3: Fetch Required Data
            $invoice_type = $request->invoice_type;
            $patient_id = $request->patient_id;
            $patient = Patients::find($patient_id);
            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
                'clinic_city',
                'clinic_state',
                'tax_name',
                'tax_type'
            ])->pluck('value', 'key');

            // Step 4: Medicine stock validation
            if ($invoice_type === 'medicine') {
                foreach ($request->items as $item) {
                    $medicine = \App\Models\Medicine::find($item['type_service']);
                    if ($medicine && $medicine->quantity < $item['quantity']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Insufficient stock for medicine: {$medicine->name}"
                        ], 422);
                    }
                }
            }

            // Step 5: Map Items (with GST fields)
            $services = collect($request->items)->map(function ($item) use ($invoice_type) {
                $row = [
                    'id' => $item['type_service'] ?? null,
                    'name' => $this->getServiceName($invoice_type, $item['type_service'] ?? null),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ];
                $row['gst_option'] = $item['gst_option'] ?? 'Without GST';
                $row['product_gst'] = isset($item['product_gst']) ? (is_array($item['product_gst']) ? $item['product_gst'] : json_decode($item['product_gst'], true)) : null;
                return $row;
            });

            // Step 6: Prepare Invoice Data
            $invoiceData = [
                'invoice_no' => rand(1000, 9999),
                'date' => now()->format('d-m-Y'),
                'branch_id' => $request->branch_id, // ✅ STORE HERE
                'patient' => $patient,
                'clinic_logo' => $settings['clinic_logo'] ?? 'default-logo.png',
                'clinic_name' => $settings['clinic_name'] ?? 'My Clinic',
                'services' => $services,
                'tax_type' => $settings['tax_type'] ?? '',
                'grand_total' => $request->grandTotal,
                'invoice_type' => $invoice_type,
                'tax' => $request->input('tax', 0),
                'discount_type' => $request->discount_type,
                'discount' => $request->discount,
                'clinic_address' => $settings['clinic_address'] ?? '',
                'clinic_phone' => $settings['clinic_phone'] ?? '',
                'clinic_state' => $settings['clinic_state'] ?? '',
                'clinic_city' => $settings['clinic_city'] ?? '',
                'clinic_email' => $settings['clinic_email'] ?? '',
                'tax_name' => $settings['tax_name'] ?? '',
                'payment_type' => $request->payment_type,
                'payment_status' => $request->payment_status,
            ];

            // Step 7: Generate PDF
            $pdf = Pdf::loadView('invoice.pdf', $invoiceData);

            $invoiceDirectory = public_path('uploads/invoices');
            if (!file_exists($invoiceDirectory)) {
                mkdir($invoiceDirectory, 0755, true);
            }

            $pdfFileName = 'invoice_' . $invoiceData['invoice_no'] . '.pdf';
            $pdfPath = $invoiceDirectory . '/' . $pdfFileName;
            $pdf->save($pdfPath);

            $host = request()->getHost();
            if ($host === '127.0.0.1' || $host === 'localhost') {
                $pdfUrl = asset('uploads/invoices/' . $pdfFileName);
            } else {
                $pdfUrl = asset('public/uploads/invoices/' . $pdfFileName);
            }

            // Step 8: Store to DB
            $invoice = \App\Models\Invoice::create([
                'patient_id' => $patient_id,
                'branch_id' => $request->branch_id, // ✅ STORE HERE
                'type' => $invoice_type,
                'type_id' => $invoiceData['invoice_no'],
                'date' => now()->format('Y-m-d'),
                'instruction' => $request->input('instruction', ''),
                'types_details' => $services,
                'grand_total' => $request->grandTotal,
                'tax' => $request->input('tax', 0),
                'discount_type' => $request->discount_type,
                'discount' => $request->discount,
                'payment_type' => $request->payment_type,
                'payment_status' => $request->payment_status,
            ]);

            // Step 9: Decrease stock
            if ($invoice_type === 'medicine') {
                foreach ($request->items as $item) {
                    DB::table('medicines')
                        ->where('id', $item['type_service'])
                        ->decrement('quantity', $item['quantity']);
                }
            }

            return response()->json([
                'message' => 'Invoice generated and saved successfully',
                'url' => $pdfUrl,
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }







    public function generateAppointmentInvoice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
                // 'patient_name' => 'required|string',
                // 'treatment_name' => 'required|string',
                'date' => 'required|date',
                'price' => 'required|numeric|min:0',
                'instruction' => 'nullable|string',
                'branch_id' => 'required|exists:branches,id', // ✅ ADD THIS
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 401);
            }

            $appointment = Appointments::with('doctor', 'patient', 'treatment')->findOrFail($request->appointment_id);

            // Mark appointment as completed
            $appointment->status = 'completed';
            $appointment->save();

            // Prepare invoice data
            $invoiceNo = rand(1000, 9999);
            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
                'clinic_city',
                'clinic_state',
                'tax_name'
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo);

            $invoiceData = [
                'invoice_no' => rand(1000, 9999),
                'date' => $request->date,
                'patient' => [
                    'id' => $appointment->patient->id,
                    'name' => $appointment->patient->fullname,
                ],
                'doctor' => [
                    'id' => $appointment->doctor->id,
                    'name' => $appointment->doctor->fullname,
                ],
                'types_details' => [
                    [
                        'id' => $request->appointment_id,  // replace '11' with actual treatment ID
                        'name' => 'Appointment',
                        'quantity' => '1',
                        'price' => number_format($request->price, 2, '.', ''),
                        'total' => number_format($request->price, 2, '.', ''),
                    ]
                ],
                'price' => $request->price,
                'instruction' => $request->instruction ?? '',
                'total' => $request->price,
                'clinic' => [
                    'logo' => $clinic_logo_path,
                    'name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                    'address' => $settings['clinic_address'] ?? '123 Health Street',
                    'phone' => $settings['clinic_phone'] ?? '0000000000',
                    'email' => $settings['clinic_email'] ?? 'clinic@example.com',
                    'city' => $settings['clinic_city'] ?? 'City',
                    'state' => $settings['clinic_state'] ?? 'State',
                    'tax_name' => $settings['tax_name'] ?? 'Tax',
                ],
                'tax' => 0,
                'name' => 'Appointment',
                'discount' => [
                    'type' => null,
                    'value' => null,
                    'amount' => null,
                ],
                'grand_total' => number_format($request->price, 2, '.', ''),
                'payment' => [
                    'type' => null,
                    'status' => 'pending',
                ]
            ];

            $pdf = Pdf::loadView('invoice.pdf', [
                'invoice_type' => 'appointment', // <- ✅ this is the missing variable
                'invoice_no' => $invoiceData['invoice_no'],
                'date' => $invoiceData['date'],
                'patient' => (object) [
                    'id' => $appointment->patient->id,
                    'fullname' => $appointment->patient->fullname,
                    'phone' => $appointment->patient->phone ?? '--',
                    'address' => $appointment->patient->address ?? '--',
                ],

                'services' => [ // appointment is a single item, but we convert it into service-like structure
                    [
                        'name' => 'Appointment',
                        'quantity' => 1,
                        'price' => $invoiceData['price'],
                        'total' => $invoiceData['total'],
                    ]
                ],
                'clinic_logo' => $invoiceData['clinic']['logo'],
                'clinic_name' => $invoiceData['clinic']['name'],
                'clinic_phone' => $invoiceData['clinic']['phone'],
                'clinic_email' => $invoiceData['clinic']['email'],
                'tax' => $invoiceData['tax'],
                'tax_name' => $invoiceData['clinic']['tax_name'],
                'discount_type' => $invoiceData['discount']['type'],
                'discount' => $invoiceData['discount']['value'],
                'payment_type' => $invoiceData['payment']['type'],
                'payment_status' => $invoiceData['payment']['status'],
            ]);
            // dd($pdf);
            // dd($invoiceData['payment']['status']);


            // Save PDF
            $invoiceDirectory = public_path('uploads/invoices');
            if (!file_exists($invoiceDirectory)) {
                mkdir($invoiceDirectory, 0755, true);
            }

            $pdfFileName = 'invoice_' . $invoiceNo . '.pdf';
            $pdfPath = $invoiceDirectory . '/' . $pdfFileName;
            $pdf->save($pdfPath);
            // $pdfUrl = asset('uploads/invoices/' . $pdfFileName);
            $host = request()->getHost();
            if ($host === '127.0.0.1' || $host === 'localhost') {
                $pdfUrl = asset('uploads/invoices/' . $pdfFileName);
            } else {
                $pdfUrl = asset('public/uploads/invoices/' . $pdfFileName);
            }


            // Save to database
            Invoice::create([
                'patient_id' => $appointment->patient_id,
                'type' => 'appointment',
                'branch_id' => $request->branch_id, // ✅ STORE HERE
                'type_id' => $invoiceNo,
                'date' => $request->date,
                'instruction' => $request->instruction ?? '',
                // 'types_details' => json_encode($invoiceData),
                'types_details' => json_encode($invoiceData['types_details']),
                'grand_total' => $request->price,
                'tax' => 0,
                // 'amount' => $request->price,
                'discount_type' => null,
                'discount' => null,
                'payment_type' => null,
                'payment_status' => 'pending',
                'pdf' => $pdfFileName,
            ]);

            return response()->json(['status' => true, 'url' => $pdfUrl]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 401);
        }
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            try {
                $branchId = $request->branch_id;

                if (!$branchId) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Branch ID is required',
                        'data' => []
                    ], 422);
                }

                $invoices = Invoice::with('patient')
                    ->where('branch_id', $branchId) // ✅ FILTER HERE
                    ->orderBy('id', 'desc')
                    ->get();

                return response()->json([
                    'status' => true,
                    'message' => 'Invoices fetched successfully',
                    'data' => $invoices,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to fetch invoices: ' . $e->getMessage(),
                ], 500);
            }
        }

        return view('invoice.index');
    }







    private function getServiceName($invoice_type, $service_id)
    {
        if (!$service_id) {
            return $invoice_type === 'discharge' ? 'Discharge' : ($invoice_type === 'appointment' ? 'Appointment' : ucfirst($invoice_type));
        }
        if ($invoice_type == 'treatment') {
            return Treatment::find($service_id)->name ?? ucfirst($invoice_type);
        }
        if ($invoice_type == 'medicine') {
            return Medicine::find($service_id)->name ?? ucfirst($invoice_type);
        }
        if ($invoice_type == 'appointment') {
            return Appointments::find($service_id)->name ?? ucfirst($invoice_type);
        }
        if ($invoice_type == 'therapy') {
            return Therapy::find($service_id)->name ?? ucfirst($invoice_type);
        }
        if ($invoice_type == 'service') {
            if (is_string($service_id) && preg_match('/^(pathology|radiology)_(\\d+)$/', $service_id, $m)) {
                if ($m[1] === 'pathology') {
                    return \App\Models\PathologyTest::find((int) $m[2])->test_name ?? 'Unknown Service';
                }
                return \App\Models\RadiologyTest::find((int) $m[2])->test_name ?? 'Unknown Service';
            }
            // Backward-compat: if stored as numeric
            $pathology = \App\Models\PathologyTest::find($service_id);
            if ($pathology) return $pathology->test_name;
            $radiology = \App\Models\RadiologyTest::find($service_id);
            if ($radiology) return $radiology->test_name;
            return 'Unknown Service';
        }
        return ucfirst($invoice_type);
    }





    public function getSettings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function getTreatment()
    {
        $treatments = Treatment::all();
        return response()->json($treatments);
    }

    // public function getServices()
    // {
    //     $services = Services::all();
    //     return response()->json($services);
    // }

    public function getServices()
    {
        $pathologyServices = PathologyTest::select('id', 'test_name as name')->get();
        $radiologyServices = RadiologyTest::select('id', 'test_name as name')->get();


        // Merge both collections into one
        $services = $pathologyServices->merge($radiologyServices)->values();

        return response()->json($services);
    }
    public function getMedicines()
    {
        $medicines = Medicine::all();
        return response()->json($medicines);
    }

    public function destroy($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Optional: Check dependencies before deleting
            // e.g., if invoice is linked with payments or reports

            $invoice->delete();

            return response()->json([
                'message' => 'Invoice deleted successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Invoice not found.'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the invoice.'
            ], 401);
        }
    }
}
