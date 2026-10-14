<?php

namespace App\Http\Controllers;

use App\Services\AiOptimizeService;
use Illuminate\Http\Request;

class AiOptimizeController extends Controller
{
    public function optimize(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'service' => 'nullable|string' // openai | gemini | claude
        ]);

        $service = $request->service ?? 'openai';

        $result = AiOptimizeService::callApi(
            $service,
            $request->content,
            'Improve this medical content for SEO'
        );

        return response()->json([
            'success' => true,
            'service' => $service,
            'result' => $result
        ]);
    }
}
