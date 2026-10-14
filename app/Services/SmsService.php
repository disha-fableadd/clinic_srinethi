<?php

namespace App\Services;

use App\Models\Setting;

class SmsService
{
    public function send_sms($to_number, $message)
    {
        $settingModel = new Setting();
        $settings = $settingModel->getSettings();

        // ✅ check sms_status before doing anything
        $smsStatus = $settings['sms_status'] ?? 'off';
        if ($smsStatus !== 'on') {
            \Log::info("⚠️ SMS not sent. sms_status = OFF in settings.");
            return false;
        }

        // ✅ normalize phone number
        if (strpos($to_number, '+91') !== 0) {
            $to_number = '+91' . ltrim($to_number, '0');
        }

        $default_sms_service = $settings['default_sms_service'] ?? 'twilio';

        if ($default_sms_service === '2factor') {
            $two_factor_api_key = $settings['two_factor_api_key'] ?? null;
            if (!$two_factor_api_key)
                return false;

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://2factor.in/API/V1/' . $two_factor_api_key . '/SMS',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query([
                    'module' => 'TRANS_SMS',
                    'apikey' => $two_factor_api_key,
                    'to' => $to_number,
                    'from' => 'HEADER',
                    'msg' => $message,
                ]),
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            \Log::info("📨 2Factor SMS Response: " . $response);
            if ($error) {
                \Log::error("❌ 2Factor SMS Error: " . $error);
            }

            return !$error;
        } elseif ($default_sms_service === 'twilio') {
            $account_sid = $settings['twilio_sid'] ?? null;
            $auth_token = $settings['twilio_token'] ?? null;
            $twilio_number = $settings['twilio_number'] ?? null;

            if (!$account_sid || !$auth_token || !$twilio_number)
                return false;

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query([
                    'From' => $twilio_number,
                    'To' => $to_number,
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

            \Log::info("📨 Twilio SMS Response: " . $response);
            if ($error) {
                \Log::error("❌ Twilio SMS Error: " . $error);
            }

            return !$error;
        }

        return false;
    }

}
