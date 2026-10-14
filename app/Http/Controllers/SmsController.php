<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Patients;
use App\Models\Setting;
use App\Models\SmsTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SendSms;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class SmsController extends Controller
{
    public function index()
    {
        return view('sms.index');
    }
    public function template()
    {
        return view('sms.template');
    }

    public function create()
    {
        return view('sms.create');
    }

    public function getSmsData()
    {
        $smsData = SendSms::with('user', 'template')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $smsData]);
    }

    // app/Http/Controllers/SmsController.php
    public function deleteSms($id)
    {
        try {
            $sms = SendSms::findOrFail($id); // Find the SMS by ID or fail
            $sms->delete(); // Delete the SMS entry

            return response()->json(['message' => 'SMS deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete SMS', 'error' => $e->getMessage()], 400);
        }
    }




    public function sendAppointmentReminders($testMode = false)
    {
        if ($testMode) {
            // Static test data
            $appointments = [
                (object) [
                    'date' => Carbon::now()->addHours(24)->toDateString(),
                    'duration' => Carbon::now()->addHours(24)->format('H:i'),
                    'status' => 'confirmed',
                    'patient_id' => 1,
                    'doctor_id' => 2
                ]
            ];

            $patient = (object) [
                'name' => 'John Doe',
                'phone' => '8780685029'
            ];

            $doctor = (object) [
                'name' => 'Dr. Smith',
                'phone' => '9824781385'
            ];

            foreach ($appointments as $appointment) {
                // Patient Reminder
                $patientMessage = "Reminder: You have an appointment with {$doctor->name} on {$appointment->date} at {$appointment->duration}.";
                $this->sendSms($patient->phone, $patientMessage);

                // Doctor Reminder
                $doctorMessage = "Reminder: You have an appointment with {$patient->name} on {$appointment->date} at {$appointment->duration}.";
                $this->sendSms($doctor->phone, $doctorMessage);
            }

            return "Test reminders sent successfully.";
        } else {
            $now = Carbon::now();
            $reminderOffsets = [24, 12, 2];

            foreach ($reminderOffsets as $offset) {
                // Convert offset into a Carbon instance
                $reminderTime = $now->copy()->addHours($offset);

                Log::info("Looking for appointments on date: {$reminderTime->toDateString()} and time: {$reminderTime->format('H:i')}");

                $appointments = Appointments::where('date', $reminderTime->toDateString())
                    ->where('duration', $reminderTime->format('H:i:00'))
                    ->whereIn('status', ['confirmed', 'upcoming'])
                    ->get();

                Log::info("Checking for appointments at {$reminderTime->format('Y-m-d H:i')}, Found: " . $appointments->count());

                foreach ($appointments as $appointment) {
                    $patient = Patients::find($appointment->patient_id);
                    $doctor = User::find($appointment->doctor_id);



                    // if ($patient) {
                    //     $patientMessage = "Reminder: You have an appointment with Dr. {$doctor->fullname} on {$appointment->date} at {$appointment->duration}.";
                    //     Log::info("Sending SMS to Patient ({$patient->phone}): $patientMessage");
                    //     $this->sendSms($patient->phone, $patientMessage);
                    // }
                    // if ($doctor) {
                    //     $doctorMessage = "Reminder: You have an appointment with {$patient->fullname} on {$appointment->date} at {$appointment->duration}.";
                    //     Log::info("Sending SMS to Doctor ({$doctor->phone}): $doctorMessage");
                    //     $this->sendSms($doctor->phone, $doctorMessage);
                    // }

                    if ($patient && $doctor) {
                        // SMS Reminder for Patient
                        $patientMessage = "Reminder: You have an appointment with Dr. {$doctor->fullname} on {$appointment->date} at {$appointment->duration}.";
                        $this->sendSms($patient->phone, $patientMessage);

                        // Email Reminder for Patient
                        $this->sendEmail($patient->email, 'Appointment Reminder', 'emails.appointment_reminder', [
                            'recipient' => $patient->fullname,
                            'doctor' => $doctor->fullname,
                            'date' => $appointment->date,
                            'time' => $appointment->duration,
                            'clinic_location' => $appointment->clinic_location

                        ]);

                        // SMS Reminder for Doctor
                        $doctorMessage = "Reminder: You have an appointment with {$patient->fullname} on {$appointment->date} at {$appointment->duration}.";
                        $this->sendSms($doctor->phone, $doctorMessage);

                        // Email Reminder for Doctor
                        $this->sendEmail($doctor->email, 'Appointment Reminder', 'emails.doctor', [
                            'recipient' => $doctor->fullname,
                            'patient' => $patient->fullname,
                            'date' => $appointment->date,
                            'time' => $appointment->duration

                        ]);
                    }

                }
            }
            return "Real reminders sent successfully.";
        }
    }



    public static function sendEmail($to, $subject, $view = null, $data = [], $body = null)
    {
        try {
            // Fetch email settings from the database
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

            // Configure mail settings dynamically
            Config::set('mail.mailers.smtp.host', $emailSettings['mail_host'] ?? '');
            Config::set('mail.mailers.smtp.port', $emailSettings['mail_port'] ?? '');
            Config::set('mail.mailers.smtp.username', $emailSettings['mail_username'] ?? '');
            Config::set('mail.mailers.smtp.password', $emailSettings['mail_password'] ?? '');
            Config::set('mail.mailers.smtp.encryption', $emailSettings['mail_encryption'] ?? '');
            Config::set('mail.from.address', $emailSettings['mail_from_address'] ?? '');
            Config::set('mail.from.name', $emailSettings['mail_from_name'] ?? '');

            Log::info("Attempting to send email to: $to with subject: $subject");

            Mail::send([], [], function (Message $message) use ($to, $subject, $view, $data, $body, $emailSettings) {
                $message->to($to)
                    ->subject($subject)
                    ->from($emailSettings['mail_from_address'], $emailSettings['mail_from_name']);

                if ($view) {
                    $html = view($view, $data)->render();
                    $message->html($html);
                } elseif ($body) {
                    $message->html($body);
                }
            });

            Log::info("Email sent successfully to: $to");
            return true;
        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }


    public function sendSms($toNumber, $message)
    {
        // Fetch SMS settings from the database
        $smsSettings = Setting::whereIn('key', [
            'twilio_sid',
            'twilio_token',
            'twilio_number'
        ])->get()->pluck('value', 'key');

        $account_sid = $smsSettings['twilio_sid'] ?? '';
        $auth_token = $smsSettings['twilio_token'] ?? '';
        $twilio_number = $smsSettings['twilio_number'] ?? '';

        if (!$account_sid || !$auth_token || !$twilio_number) {
            Log::error("Twilio credentials are missing in the database.");
            return false;
        }

        // Ensure the phone number is in the correct format
        if (strpos($toNumber, '+') !== 0) {
            $toNumber = '+91' . ltrim($toNumber, '0'); // Assuming Indian numbers by default
        }

        // Initialize cURL to send SMS via Twilio API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'From' => $twilio_number,
                'To' => $toNumber,
                'Body' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic " . base64_encode("$account_sid:$auth_token"),
                "Content-Type: application/x-www-form-urlencoded",
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::error("Twilio SMS Error: " . $error);
            return false;
        }

        Log::info("SMS Sent via Twilio: " . $response);
        return $response;
    }



    public function handleSendSms(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        $templateId = $request->input('template_id');
        $senderId = $request->input('sender_id');
        $service = $request->input('service');

        if (empty($userIds) || !$templateId) {
            return response()->json(['message' => 'Missing required fields.'], 400);
        }

        $messageTemplate = SmsTemplate::find($templateId); // Assuming SmsTemplate is your model
        if (!$messageTemplate) {
            return response()->json(['message' => 'Invalid SMS Template.'], 400);
        }

        $message = $messageTemplate->content;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->phone) {
                $this->sendSms($user->phone, $message);
            }
        }

        return response()->json(['message' => 'SMS sent successfully.']);
    }


    // public function exportSms()
    // {
    //     $smsRecords = SendSms::with(['user', 'template'])->get();

    //     $csvData = [];
    //     $csvData[] = ['Sr No', 'User Name(s)', 'Template Name', 'Service', 'Sender ID', 'Created At'];

    //     foreach ($smsRecords as $index => $sms) {
    //         $userName = 'N/A';

    //         if (is_iterable($sms->user)) {
    //             $userName = collect($sms->user)->pluck('fullname')->join(', ');
    //         } else {
    //             $userName = $sms->user->fullname ?? 'N/A';
    //         }

    //         $csvData[] = [
    //             $index + 1,
    //             $userName,
    //             $sms->template->name ?? 'N/A',
    //             $sms->service ?? 'N/A',
    //             $sms->sender_id ?? 'N/A',
    //             $sms->created_at ? $sms->created_at->format('d-M-Y h:i A') : 'N/A',
    //         ];
    //     }

    //     $filename = 'sms_export_' . now()->format('Ymd_His') . '.csv';
    //     $handle = fopen('php://temp', 'r+');

    //     foreach ($csvData as $line) {
    //         fputcsv($handle, $line);
    //     }

    //     rewind($handle);
    //     $contents = stream_get_contents($handle);
    //     fclose($handle);

    //     return Response::make($contents, 200, [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => "attachment; filename=$filename",
    //     ]);
    // }


      public function exportSms(Request $request)
{
    $branchId = $request->query('branch_id'); // Get branch_id from query

    $smsRecords = SendSms::with(['user', 'template'])
        ->when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch_id if provided
        })
        ->get();

    $csvData = [];
    $csvData[] = ['Sr No', 'User Name(s)', 'Template Name', 'Service', 'Sender ID', 'Created At'];

    foreach ($smsRecords as $index => $sms) {
        $userName = 'N/A';

        if (is_iterable($sms->user)) {
            $userName = collect($sms->user)->pluck('fullname')->join(', ');
        } else {
            $userName = $sms->user->fullname ?? 'N/A';
        }

        $csvData[] = [
            $index + 1,
            $userName,
            $sms->template->name ?? 'N/A',
            $sms->service ?? 'N/A',
            $sms->sender_id ?? 'N/A',
            $sms->created_at ? $sms->created_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'sms_export_' . now()->format('Ymd_His') . '.csv';
    $handle = fopen('php://temp', 'r+');

    foreach ($csvData as $line) {
        fputcsv($handle, $line);
    }

    rewind($handle);
    $contents = stream_get_contents($handle);
    fclose($handle);

    return Response::make($contents, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$filename",
    ]);
}



    // public function export()
    // {
    //     $templates = SmsTemplate::with('user')->get();

    //     $csvData = [];
    //     $csvData[] = ['Sr No', 'User Name', 'Template Name', 'Content', 'Status'];

    //     foreach ($templates as $index => $template) {
    //         $csvData[] = [
    //             $index + 1,
    //             optional($template->user)->fullname ?? 'N/A',
    //             $template->name,
    //             $template->content,
    //             ucfirst($template->status),
    //         ];
    //     }

    //     // Convert to CSV
    //     $filename = 'sms_templates_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
    //     $handle = fopen('php://temp', 'r+');
    //     foreach ($csvData as $row) {
    //         fputcsv($handle, $row);
    //     }
    //     rewind($handle);

    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    //     ];

    //     return Response::stream(function () use ($handle) {
    //         fpassthru($handle);
    //     }, 200, $headers);
    // }

      public function export(Request $request)
{
    $branchId = $request->query('branch_id'); // Get branch_id from query string

    // Fetch SMS templates, optionally filter by branch_id
    $templates = SmsTemplate::with('user')
        ->when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
        ->get();

    $csvData = [];
    $csvData[] = ['Sr No', 'User Name', 'Template Name', 'Content', 'Status'];

    foreach ($templates as $index => $template) {
        $csvData[] = [
            $index + 1,
            optional($template->user)->fullname ?? 'N/A',
            $template->name,
            $template->content,
            ucfirst($template->status),
        ];
    }

    // Convert to CSV
    $filename = 'sms_templates_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
    $handle = fopen('php://temp', 'r+');
    foreach ($csvData as $row) {
        fputcsv($handle, $row);
    }
    rewind($handle);

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    return Response::stream(function () use ($handle) {
        fpassthru($handle);
    }, 200, $headers);
}

}
