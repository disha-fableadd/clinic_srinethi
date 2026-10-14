<?php

namespace App\Jobs;

use App\Helpers\EmailHelper;
use App\Helpers\SmsHelper;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWelcomeNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user, $createdBy;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $createdBy)
    {
        $this->user = $user;
        $this->createdBy = $createdBy;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $emailSent = false;
        $smsSent = false;

        try {
            $emailSent = EmailHelper::sendEmail(
                $this->user->email,
                "Welcome to Our Clinic",
                "emails.welcome",
                ['user' => $this->user]
            );
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        try {
            $smsHelper = new SmsHelper();
            $smsSent = $smsHelper->send_sms(
                $this->user->phone,
                "Welcome {$this->user->fullname}, your account has been created successfully!"
            );
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
        }

        $channels = [];
        if ($emailSent) $channels[] = 'Email';
        if ($smsSent) $channels[] = 'SMS';

        $statusMsg = implode(' & ', $channels);
        $message = $statusMsg
            ? "$statusMsg sent to {$this->user->fullname} ({$this->user->email})"
            : "Failed to send Email/SMS to {$this->user->fullname}";

        Notification::store($message, $this->createdBy, $this->user->id, 'user');
    }
}
