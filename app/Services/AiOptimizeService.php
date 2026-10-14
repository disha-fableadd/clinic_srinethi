<?php

namespace App\Services;

use App\Models\AiCredential;
use Illuminate\Support\Facades\Http;
use Exception;

class AiOptimizeService
{
    /**
     * Call OpenAI API
     */
    public static function callOpenAI(string $apiKey, string $prompt, string $model = 'gpt-4o-mini', ?string $systemPrompt = null): string
    {
        $systemPrompt = $systemPrompt ?? 'You are an expert SEO content optimizer. Analyze and improve the provided content for SEO.';

        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/responses', [
                'model' => $model, // Now using gpt-4o-mini
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->failed()) {
                throw new Exception('OpenAI API Error: ' . $response->body());
            }
        


            return $response->json('output_text', '');
        } catch (Exception $e) {
            throw new Exception('Failed to call OpenAI: ' . $e->getMessage());
        }
    }



    /**
     * Call Claude API
     */
    public static function callClaude(string $apiKey, string $prompt, string $model = 'claude-3-sonnet-20240229', ?string $systemPrompt = null): string
    {
        $systemPrompt = $systemPrompt ?? 'You are an expert SEO content optimizer. Analyze and improve the provided content for SEO.';

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => 2000,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->failed()) {
                throw new Exception('Claude API Error: ' . $response->body());
            }

            return $response->json('content.0.text', '');
        } catch (Exception $e) {
            throw new Exception('Failed to call Claude: ' . $e->getMessage());
        }
    }

    /**
     * Call Google Gemini API
     */
    public static function callGemini(string $apiKey, string $prompt, string $model = 'gemini-1.5-flash', ?string $systemPrompt = null)
    {
        $systemPrompt = $systemPrompt ?? 'You are an expert SEO content optimizer. Analyze and improve the provided content for SEO.';
        $fullPrompt = "{$systemPrompt}\n\n{$prompt}";

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $fullPrompt]]]
                ]
            ]);

            if ($response->failed()) {
                throw new Exception('Gemini API Error: ' . $response->body());
            }

            return $response->json('candidates.0.content.parts.0.text', '');
        } catch (Exception $e) {
            throw new Exception('Failed to call Gemini: ' . $e->getMessage());
        }
    }




    /**
     * Call AI API based on the service name
     */
    // public static function callApi(AiCredential $credential, string $prompt, ?string $systemPrompt = null): string
    // {
    //     $service = strtolower($credential->service_name);

    //     return match (true) {
    //         str_contains($service, 'openai') || str_contains($service, 'gpt') => 
    //             self::callOpenAI($credential->api_key, $prompt, 'gpt-4o-mini', $systemPrompt),
    //         str_contains($service, 'claude') || str_contains($service, 'anthropic') => 
    //             self::callClaude($credential->api_key, $prompt, 'claude-3-sonnet-20240229', $systemPrompt),
    //         str_contains($service, 'gemini') || str_contains($service, 'google') => 
    //             self::callGemini($credential->api_key, $prompt, 'gemini-2.0-flash', $systemPrompt),
    //         default => throw new Exception('Unsupported AI service provider: ' . $credential->service_name)
    //     };
    // }
    //     public static function callApi(AiCredential $credential, string $prompt, ?string $systemPrompt = null): string
    // {
    //     $service = strtolower($credential->service_name);

    //     return match (true) {
    //         str_contains($service, 'openai') || str_contains($service, 'gpt') =>
    //             self::callOpenAI($credential->api_key, $prompt, 'gpt-4o-mini', $systemPrompt),

    //         str_contains($service, 'claude') || str_contains($service, 'anthropic') =>
    //             self::callClaude($credential->api_key, $prompt, 'claude-3-sonnet-20240229', $systemPrompt),

    //         str_contains($service, 'gemini') || str_contains($service, 'google') =>
    //             self::callGemini($credential->api_key, $prompt, 'gemini-1.5-flash-latest', $systemPrompt),

    //         default => throw new Exception('Unsupported AI service provider: ' . $credential->service_name)
    //     };
    // }



    // new function
    public static function callApi(string $serviceName, string $prompt, ?string $systemPrompt = null): string
    {
        $credential = AiCredential::where('service_name', $serviceName)
            ->where('is_default', 1)
            ->first();

        if (!$credential) {
            throw new Exception("No active API key found for service: {$serviceName}");
        }

        $service = strtolower($credential->service_name);

        return match (true) {
            str_contains($service, 'openai') || str_contains($service, 'gpt') =>
            self::callOpenAI($credential->api_key, $prompt, 'gpt-4o-mini', $systemPrompt),

            str_contains($service, 'claude') || str_contains($service, 'anthropic') =>
            self::callClaude($credential->api_key, $prompt, 'claude-3-sonnet-20240229', $systemPrompt),

            // str_contains($service, 'gemini') || str_contains($service, 'google') =>
            // self::callGemini($credential->api_key, $prompt, 'gemini-1.5-flash', $systemPrompt),
           

            default =>
            throw new Exception('Unsupported AI service provider: ' . $credential->service_name),
        };
    }





    /**
     * Calculate basic SEO metrics
     */
    public static function calculateSeoMetrics(string $content): array
    {
        $wordCount = str_word_count($content);
        $sentences = count(preg_split('/[.!?]+/', $content)) - 1;
        $syllables = preg_match_all('/[aeiouy]/i', $content);

        $readabilityScore = $sentences > 0 ? round((206.835 - 1.015 * ($wordCount / max($sentences, 1)) - 84.6 * ($syllables / max($wordCount, 1))), 2) : 0;

        return [
            'word_count' => $wordCount,
            'readability_score' => max(0, $readabilityScore),
            'sentence_count' => $sentences,
            'character_count' => strlen($content),
        ];
    }

    /**
     * Extract keywords from content
     */
    public static function extractKeywords(string $content, int $limit = 10): array
    {
        $commonWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they'];

        $words = str_word_count(strtolower($content), 1);

        $keywords = array_filter($words, fn($word) => !in_array($word, $commonWords) && strlen($word) > 3);

        $frequency = array_count_values($keywords);

        arsort($frequency);

        return array_slice($frequency, 0, $limit, true);
    }
}
