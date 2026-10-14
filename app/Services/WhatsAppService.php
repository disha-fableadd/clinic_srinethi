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
        if (empty($this->apiUrl) || empty($this->phoneNumberId) || empty($this->token)) {
            \Log::warning("⚠️ WhatsApp API credentials not configured in settings. Skipping template message.");
            return null;
        }

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

        try {
            $response = Http::timeout(5)->withToken($this->token)
                ->post($url, $payload);

            return $response->json();
        } catch (\Throwable $e) {
            \Log::error("❌ WhatsApp sendTemplateMessage error: " . $e->getMessage());
            return null;
        }
    }

    public function sendTextMessage($to, $message)
    {
        if (empty($this->apiUrl) || empty($this->phoneNumberId) || empty($this->token)) {
            \Log::warning("⚠️ WhatsApp API credentials not configured in settings. Skipping text message.");
            return null;
        }

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

        try {
            $response = Http::timeout(5)->withToken($this->token)
                ->post($url, $payload);

            return $response->json();
        } catch (\Throwable $e) {
            \Log::error("❌ WhatsApp sendTextMessage error: " . $e->getMessage());
            return null;
        }
    }
}
