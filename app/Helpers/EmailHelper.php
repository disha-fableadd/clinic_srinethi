<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EmailHelper
{
    public static function sendEmail($to, $subject, $view = null, $data = [], $body = null)
    {
        try {
            $fromAddress = Config::get('mail.from.address');
            $fromName = Config::get('mail.from.name');

            if (empty($fromAddress)) {
                throw new Exception('Mail from address is not configured.');
            }

            Mail::send([], [], function (Message $message) use ($to, $subject, $view, $data, $body) {
                $message->to($to)
                    ->subject($subject)
                    ->from(Config::get('mail.from.address'), Config::get('mail.from.name'));

                if ($view) {
                    $html = view($view, $data)->render();
                    $message->html($html);
                } elseif ($body) {
                    $message->html($body);
                }
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    public static function testEmail()
    {
        try {
            $result = self::sendEmail('dishafablead82@gmail.com', 'Test Email', null, [], 'This is a plain text test email.');

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Email Sent Successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send email'
                ], 500);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email sending failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
