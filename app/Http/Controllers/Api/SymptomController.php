<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Symptom;
use Illuminate\Http\Request;

class SymptomController extends Controller
{
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'details' => 'nullable|string',
    //     ]);

    //     Symptom::create([
    //         'name' => $request->name,
    //         'details' => $request->details,
    //     ]);

    //     return response()->json(['message' => 'Symptom added successfully']);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'details' => 'nullable|string',
    //     ]);

    //     $symptom = Symptom::create([
    //         'name' => $request->name,
    //         'details' => $request->details,
    //     ]);

    //     return response()->json([
    //         'message' => 'Symptom added successfully',
    //         'symptom' => $symptom
    //     ], 200);
    // }




    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            // 'branch_id' => 'required|exists:branches,id',
        ]);

        $symptom = Symptom::create([
            'name' => $request->name,
            'details' => $request->details,
            'branch_id' => $request->branch_id,
        ]);

        return response()->json([
            'message' => 'Symptom added successfully',
            'symptom' => $symptom
        ], 200);
    }



    // public function getSymptoms(Request $request)
    // {
    //     $query = Symptom::query();

    //     if ($request->has('branch_id') && !empty($request->branch_id)) {
    //         $query->where('branch_id', $request->branch_id);
    //     }

    //     $symptoms = $query->select('id', 'name')->orderBy('name', 'asc')->get();

    //     return response()->json($symptoms);
    // }

    public function getSymptoms(Request $request)
    {
        $query = Symptom::query();

        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $query->where('branch_id', $request->branch_id);
        }

        $symptoms = $query->select('id', 'name')->orderBy('name', 'asc')->get();

        return response()->json(['symptoms' => $symptoms]);
    }



    // public function index()
    // {
    //     $symptoms = Symptom::all();

    //     return response()->json($symptoms, 200);
    // }

    public function index(Request $request)
    {
        // Get branch_id from request
        $branchId = $request->query('branch_id');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch ID is required.'
            ], 400);
        }

        // Fetch symptoms only for this branch
        $symptoms = Symptom::where('branch_id', $branchId)->get();

        return response()->json($symptoms, 200);
    }


    public function destroy($id)
    {
        $symptom = Symptom::find($id);

        if (!$symptom) {
            return response()->json([
                'message' => 'Symptom not found.'
            ], 401);
        }

        $symptom->delete();

        return response()->json([
            'message' => 'Symptom deleted successfully.'
        ], 200);
    }

    public function show($id)
    {
        $symptom = Symptom::find($id);

        if (!$symptom) {
            return response()->json(['message' => 'Symptom not found.'], 401);
        }

        return response()->json($symptom, 200);
    }


    public function update(Request $request, $id)
    {
        $symptom = Symptom::find($id);

        if (!$symptom) {
            return response()->json(['message' => 'Symptom not found.'], 401);
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
        ]);

        // Update the symptom
        $symptom->update($validated);

        return response()->json(['message' => 'Symptom updated successfully.', 'data' => $symptom], 200);
    }
}
