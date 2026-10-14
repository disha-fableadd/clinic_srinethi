<?php

namespace App\Services;

use App\Models\Appointments;
use App\Models\Patients;
use App\Models\User;
use App\Models\Notification;
use App\Services\FCMService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    public function create($request)
    {
        $appointment = Appointments::create([
            'user_id' => auth()->id(),
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'treatment_id' => $request->treatment_id,
            'status' => $request->status,
            'date' => $request->date,
            'duration' => $request->duration,
            'appoint_type' => $request->appoint_type,
            'clinic_location' => $request->clinic_location,
            'followup_update' => $request->followup_update,
            'branch_id' => $request->branch_id,
        ]);

        if ($appointment) {

            $doctor = User::find($request->doctor_id);
            $patient = Patients::find($request->patient_id);

            $doctorName = $doctor->fullname ?? '';
            $patientName = $patient->fullname ?? '';

            $dateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->date . ' ' . $request->duration
            )->format('d/m/Y h:i A');

            // Notification
            Notification::store(
                "Dr {$doctorName} You have a New appointment with Patient {$patientName} on {$dateTime}.",
                $request->doctor_id,
                $appointment->id,
                'appointment',
                auth()->id(),
                $request->patient_id
            );

            Log::info("Notification stored");

            // FCM
            if (!empty($doctor->fcm_token)) {
                try {
                    (new FCMService())->sendNotification(
                        $doctor->fcm_token,
                        'New Appointment Scheduled',
                        "Dr {$doctorName} You have a new appointment with patient {$patientName} on {$dateTime}.",
                        ['appointment_id' => (string) $appointment->id]
                    );
                } catch (\Exception $e) {
                    Log::warning($e->getMessage());
                }
            }

            // SMS (optional)
            if (!empty($doctor->phone)) {
                $msg = "Dr {$doctorName} appointment with {$patientName} on {$dateTime}";
                // (new SmsService())->send_sms($doctor->phone, $msg);
            }
        }

        return $appointment;
    }
}