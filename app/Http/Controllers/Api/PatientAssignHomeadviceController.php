<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeAdvice;
use App\Models\PatientAssignHomeadvice;
use Illuminate\Http\Request;

class PatientAssignHomeadviceController extends Controller
{


   

    public function index(Request $request)
    {
        $branchId = $request->query('branch_id');

        $query = HomeAdvice::select('id', 'template_name', 'description');

        if ($branchId) {
            $query->where('branch_id', $branchId); // filter by branch
        }

        $advices = $query->get();

        return response()->json($advices);
    }



    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'homeadvice_id' => 'required|array',             // array of selected templates
            'homeadvice_id.*' => 'exists:home_advice,id',    // each template must exist
            // 'description' => 'nullable|array',               // array of descriptions
            // 'description.*' => 'nullable|string',
        ]);

        // Store as JSON arrays in a single record
        $assignment = PatientAssignHomeadvice::create([
            'branch_id' => $request->branch_id,
            'patient_id' => $request->patient_id,
            'template_id' => $request->homeadvice_id,        // store array of template IDs
            'description' => $request->description ?? [],   // store array of descriptions
        ]);

        return response()->json([
            'status' => true,
            'data' => $assignment,
            'message' => 'Home advice assigned successfully!'
        ]);
    }


    // Show single
    public function show($id)
    {
        $assignment = PatientAssignHomeadvice::with(['branch', 'patient', 'template'])->findOrFail($id);
        return response()->json($assignment);
    }

    // Update
    public function update(Request $request, $id)
    {
        $assignment = PatientAssignHomeadvice::findOrFail($id);

        $request->validate([
            'branch_id' => 'sometimes|exists:branches,id',
            'patient_id' => 'sometimes|exists:patients,id',
            'template_id' => 'sometimes|exists:homeadvices,id',
        ]);

        $assignment->update($request->all());
        return response()->json(['status' => true, 'data' => $assignment]);
    }

    // Delete
    public function destroy($id)
    {
        $assignment = PatientAssignHomeadvice::findOrFail($id);
        $assignment->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }

    // Get by Patient
    public function getByPatient($patientId)
    {
        $homeadvices = PatientAssignHomeadvice::with(['patient'])
            ->where('patient_id', $patientId)
            ->get();

        // Manually append templates for each home advice
        $homeadvices->each(function ($item) {
            $templateIds = $item->template_id;
            if (is_array($templateIds)) {
                $item->templates = HomeAdvice::whereIn('id', $templateIds)->get();
            } else {
                $item->templates = collect(); // Empty collection if not an array
            }
        });

        return response()->json([
            'status' => true,
            'homeadvices' => $homeadvices
        ]);
    }
}
