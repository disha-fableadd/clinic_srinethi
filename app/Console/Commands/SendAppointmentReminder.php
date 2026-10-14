<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointments;
use App\Models\Patients;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SmsController;

class SendAppointmentReminder extends Command
{
    protected $signature = 'sms:send-reminders';  // Command to run
    protected $description = 'Send SMS and Email reminders for upcoming appointments';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
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
                        'clinic_location'=>$appointment->clinic_location
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
