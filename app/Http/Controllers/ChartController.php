<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        $projectType = Setting::where('key', 'project_type_id')->value('value');

        if ($projectType == 3) {
            return view('chart_physio');
        }

        return view('chart');
    }
}
