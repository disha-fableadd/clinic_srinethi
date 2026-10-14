<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\AiOptimizeService;
use Illuminate\Http\Request;

class AiOptimizeController extends Controller
{
    public function optimize(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'service_name' => 'nullable|string'
        ]);

        // $service = strtolower($request->service_name ?? 'openai');

        $result = AiOptimizeService::callApi(
            $request->service_name,
            $request->content,
            'Improve this medical content for SEO'
        );

        return response()->json([
            'success' => true,
            'service' => $request->service_name ?? 'default',
            'result' => $result
        ]);
    }
}
