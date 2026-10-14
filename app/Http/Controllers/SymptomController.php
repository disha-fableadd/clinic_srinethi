<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Symptom;
use Illuminate\Http\Request;

class SymptomController extends Controller
{
    public function index()
    {
        return view('symptoms.index'); 
    }
    public function create()
    {
        return view('symptoms.create'); 
    }


    public function getByBranch($branchId)
    {
        $symptoms = Symptom::where('branch_id', $branchId)
            ->select('id', 'name')
            ->get();

        return response()->json($symptoms);
    }
}
