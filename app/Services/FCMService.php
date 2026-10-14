<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use APP\Models\Setting;

class FCMService
{
    protected $projectId;
    protected $clientEmail;
    protected $privateKey;

    public function __construct()
    {
        $firebaseFile = Setting::where('key', "firebase_json")->first();
        // $firebaseFilePath = url('public/').$firebaseFile->value;
        $path = storage_path('app/public/'.$firebaseFile->value);

        $config = json_decode(file_get_contents($path), true);
        // dd($config);
        // $this->projectId = $config['project_id'];
         $this->projectId   = $config['project_id']   ?? null;
        $this->clientEmail = $config['client_email'] ?? null;
        $this->privateKey = $config['private_key'] ?? null;
    }

    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function getAccessToken()
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss' => $this->clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwtHeader = $this->base64UrlEncode(json_encode($header));
        $jwtClaim = $this->base64UrlEncode(json_encode($claims));
        $unsignedJwt = $jwtHeader . '.' . $jwtClaim;

        openssl_sign($unsignedJwt, $signature, $this->privateKey, 'SHA256');
        $signedJwt = $unsignedJwt . '.' . $this->base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $signedJwt,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
            ],
        ];

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $response = Http::withToken($accessToken)
            ->post($url, $payload);

        return $response->json();
    }
}
