<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
class WhatsAppService
{
    protected $token;
    protected $phoneNumberId;
    protected $apiUrl;

  

    public function __construct()
    {
        $this->token = Db::table('settings')->where('key', 'whatsapp_token')->value('value');
        $this->phoneNumberId = DB::table('settings')->where('key', 'whatsapp_phone_number_id')->value('value');
        $this->apiUrl = DB::table('settings')->where('key', 'whatsapp_api_url')->value('value');
    }
    public function sendTemplateMessage($to, $templateName = "hello_world", $lang = "en_US")
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,  // receiver phone with country code
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => ["code" => $lang]
            ]
        ];

        $response = Http::withToken($this->token)
            ->post($url, $payload);

        return $response->json();
    }

   

    public function sendWelcomeTemplate($to, $fullname, $clinicName, $email, $signature)
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => "welcome_clinic", 
                "language" => ["code" => "en_US"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $fullname],    // {{1}} -> Patient name
                            ["type" => "text", "text" => $clinicName],  // {{2}} -> Clinic name
                            ["type" => "text", "text" => $email],       // {{3}} -> Email
                            ["type" => "text", "text" => $signature],   // {{4}} -> Signature/Doctor
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($this->token)->post($url, $payload);

        return $response->json();
    }


    public function sendTextMessage($to, $message)
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => [
                "preview_url" => false,
                "body" => $message
            ]
        ];

        $response = Http::withToken($this->token)
            ->post($url, $payload);

        return $response->json();
    }
}
