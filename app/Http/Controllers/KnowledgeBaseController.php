<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        return view('admin.knowledge-base');
    }

    // public function search(Request $request)
    // {
    //     $query = $request->input('q');
    //     $apiKey = env('GOOGLE_API_KEY');
    //     $searchEngineId = env('GOOGLE_SEARCH_ENGINE_ID');

    //     $url = "https://www.googleapis.com/customsearch/v1?key={$apiKey}&cx={$searchEngineId}&q=" . urlencode($query);

    //     $response = Http::get($url);

    //     if ($response->successful()) {
    //         return response()->json($response->json());
    //     } else {
    //         return response()->json(['error' => 'Something went wrong.'], 500);
    //     }
    // }

    public function search(Request $request)
    {
        try {
            $query = $request->input('q');

            if (!$query) {
                return response()->json(['error' => 'Query is required'], 400);
            }

            $apiKey = env('GOOGLE_API_KEY');
            $searchEngineId = env('GOOGLE_SEARCH_ENGINE_ID');

            if (!$apiKey || !$searchEngineId) {
                return response()->json(['error' => 'Missing API Key or Search Engine ID'], 500);
            }

            $url = "https://www.googleapis.com/customsearch/v1?key={$apiKey}&cx={$searchEngineId}&q=" . urlencode($query);

            $response = Http::get($url);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                Log::error('Google API error: ' . $response->body());
                return response()->json(['error' => 'Failed to fetch results', 'api_response' => $response->body()], 500);
            }
        } catch (\Exception $e) {
            Log::error('KnowledgeBase Search Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

} 

