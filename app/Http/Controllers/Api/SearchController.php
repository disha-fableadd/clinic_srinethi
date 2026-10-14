<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\Modules;
class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query');
        $userId = auth()->id(); // Assuming user is authenticated

        // Get modules matching the search query
        $modules = Modules::where('name', 'LIKE', "%$query%")->get();

        // Get user's allowed module IDs
        $allowedModuleIds = DB::table('user_permissions')
            ->where('user_id', $userId)
            ->pluck('module_id')
            ->toArray();

        // Filter modules based on permission
        $filteredModules = $modules->map(function ($module) use ($allowedModuleIds) {
            $module->hasPermission = in_array($module->id, $allowedModuleIds);
            return $module;
        });

        return response()->json($filteredModules);
    }

}
