<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SeoContentOptimizer;
use App\Models\AiCredential;
use App\Services\AiOptimizeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Http;

class SeoOptimizerController extends Controller
{
    /**
     * Get all SEO optimizations for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();

        $optimizations = SeoContentOptimizer::where('user_id', $user->id)
            ->with('aiProvider:id,service_name')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (SeoContentOptimizer $optimization) {
                return [
                    'id' => $optimization->id,
                    'model_name' => $optimization->model_name,
                    'ai_provider' => $optimization->aiProvider?->service_name,
                    'word_count' => $optimization->word_count,
                    'readability_score' => $optimization->readability_score,
                    'keyword_density' => $optimization->keyword_density,
                    'input_preview' => substr($optimization->input_content, 0, 100) . '...',
                    'created_at' => $optimization->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'SEO optimizations retrieved successfully',
            'data' => $optimizations
        ]);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'language' => 'nullable|string|max:10',
            'page_id' => 'nullable|integer',
        ]);
    

        $user = auth()->user();
            
        $prompt = $validated['content'];
        $apiKey = env('OPENAI_API_KEY');

        try {
            // Responses API expects 'input' instead of 'messages'
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4o-mini',
                'input' => [
                    ['role' => 'system', 'content' => 'You are an expert chatbot. Reply conversationally.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->failed()) {
                throw new \Exception('OpenAI API error: ' . $response->body());
            }

            $data = $response->json();
            $botResponse = $data['output_text'] ?? 'Sorry, I could not generate a response.';

            $sessionId = $request->input('session_id') ?? (string) \Str::uuid();
            dd($user);
            \App\Models\ChatHistory::create([
                 'user_id' => $user->id,

                'session_id' => $sessionId,
                'page_id' => $validated['page_id'] ?? null,
                'user_question' => $prompt,
                'bot_response' => $botResponse,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat generated successfully',
                'data' => [
                    'session_id' => $sessionId,
                    'user_question' => $prompt,
                    'bot_response' => $botResponse,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }




//   public function store(Request $request)
// {
//     $validated = $request->validate([
//         'content' => 'required|string',
//         'language' => 'nullable|string|max:10',
//         'page_id' => 'nullable|integer',
//     ]);

       
//     $user = auth()->user();
//     $prompt = $validated['content'];

//     $apiKey = env('OPENAI_API_KEY');
     

//     try {
//         // Correct endpoint + correct payload
//         $response = Http::withHeaders([
//             'Authorization' => "Bearer {$apiKey}",
//             'Content-Type' => 'application/json',
//         ])->post('https://api.openai.com/v1/chat/completions', [
//             'model' => 'gpt-4.1',
//             'messages' => [
//                 ['role' => 'system', 'content' => 'You are an expert chatbot. Reply conversationally.'],
//                 ['role' => 'user', 'content' => $prompt],
//             ],
//         ]);
      
//         if ($response->failed()) {
//             throw new \Exception('OpenAI API error: ' . $response->body());
//         }

//         $data = $response->json();
//         $botResponse = $data['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

//         $sessionId = $request->input('session_id') ?? (string) \Str::uuid();

//         \App\Models\ChatHistory::create([
//             'user_id' => $user->id,
//             'session_id' => $sessionId,
//             'page_id' => $validated['page_id'] ?? null,
//             'user_question' => $prompt,
//             'bot_response' => $botResponse,
//         ]);
        


//         return response()->json([
//             'success' => true,
//             'message' => 'Chat generated successfully',
//             'data' => [
//                 'session_id' => $sessionId,
//                 'user_question' => $prompt,
//                 'bot_response' => $botResponse,
//             ]
//         ], 201);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => $e->getMessage(),
//         ], 500);
//     }
// }


    /**
     * Get a specific optimization result
     */
    public function show(SeoContentOptimizer $seoOptimizer)
    {
        $user = Auth::user();

        if ($seoOptimizer->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'You do not have access to this resource'
            ], 403);
        }

        return response()->json([
            'id' => $seoOptimizer->id,
            'model_name' => $seoOptimizer->model_name,
            'input_content' => $seoOptimizer->input_content,
            'ai_response' => $seoOptimizer->ai_response,
            'final_prompt' => $seoOptimizer->final_prompt,
            'word_count' => $seoOptimizer->word_count,
            'readability_score' => $seoOptimizer->readability_score,
            'keyword_density' => json_decode($seoOptimizer->keyword_density, true),
            'suggestions' => json_decode($seoOptimizer->suggestions, true),
            'created_at' => $seoOptimizer->created_at,
        ]);
    }

    /**
     * Delete an optimization record
     */
    public function destroy(SeoContentOptimizer $seoOptimizer)
    {
        $user = Auth::user();

        if ($seoOptimizer->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'You do not have access to this resource'
            ], 403);
        }

        $seoOptimizer->delete();

        return response()->json([
            'message' => 'Optimization record deleted successfully'
        ]);
    }

    /**
     * Get default AI credentials available for optimization
     */
    public function getAvailableCredentials()
    {
        $credentials = AiCredential::where('is_default', true)
            ->orWhere(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->select('id', 'service_name', 'expires_at')
            ->get();

        return response()->json([
            'message' => 'Available AI credentials',
            'data' => $credentials
        ]);
    }

    private function buildPrompt(string $content, string $type): string
    {
        return "
            You are an advanced SEO analysis engine.

            Analyze the provided content and return a STRICT JSON response following this structure:

            {
            \"performance_score\": \"number (percentage)\",
            \"keyword_density\": \"number (percentage)\",
            \"readability_score\": \"number (0-100)\",
            \"word_count\": \"number\",
            \"links_found\": \"number\",
            \"optimized_content\": \"string\",
            \"suggestions\": {}
            }

            IMPORTANT:
            - Only return JSON. No markdown.
            - Do not explain your output.
            - Do not break structure.
            - For keyword density: calculate based on primary keywords you identify.
            - For readability_score: use a 0–100 scale.
            - links_found: count any URLs present in the text.

            CONTENT TO ANALYZE:
            \"\"\"$content\"\"\"

            OPTIMIZATION TYPE: $type

            Based on the optimization type, include specific improvements in the `extra` field:

            TYPE = blog
            extra = {
            \"improved_headings\": [\"H1\", \"H2\", \"H3\"],
            \"meta_description\": \"string\",
            \"internal_link_opportunities\": [\"string\"]
            }

            TYPE = product_description
            extra = {
            \"benefits\": [\"string\"],
            \"unique_selling_points\": [\"string\"],
            \"cta\": \"string\",
            \"schema_suggestion\": \"string\"
            }

            TYPE = meta_description
            extra = {
            \"meta_description\": \"string (max 160 characters)\"
            }

            TYPE = title
            extra = {
            \"title_options\": [\"Title 1\", \"Title 2\", \"Title 3\", \"Title 4\", \"Title 5\"]
            }
            ";
    }
}
