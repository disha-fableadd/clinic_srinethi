<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentTemplate;
use App\Models\PatientAssignAssessment;
use App\Models\Patients;
use Illuminate\Http\Request;


class PatientAssignAssessmentController extends Controller
{
    
   

public function index(Request $request)
{
    $branchId = $request->query('branch_id');

    
    $query = AssessmentTemplate::select('id', 'name', 'description');

    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    $templates = $query->get();

    return response()->json($templates);
}





    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'assessment_id' => 'required|array',
            'assessment_id.*' => 'exists:assessment_template,id',
            // 'description' => 'nullable|array',
            // 'description.*' => 'nullable|string',
        ]);

        // Store as JSON
        $assignment = PatientAssignAssessment::create([
            'branch_id' => $request->branch_id,
            'patient_id' => $request->patient_id,
            'template_id' => $request->assessment_id,      // array
            'description' => $request->description ?? [], // array
        ]);

        return response()->json([
            'status' => true,
            'data' => $assignment,
            'message' => 'Assessment assigned successfully!'
        ]);
    }


  
    public function show($patient_id)
    {
        $patient = Patients::findOrFail($patient_id);
        return view('patient_details', [
            'patient_id' => $patient_id,
            'patient' => $patient,
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|integer',
            'template_id' => 'required|exists:assessment_templates,id',
        ]);

        $assignment = PatientAssignAssessment::findOrFail($id);
        $assignment->update($request->only('branch_id', 'template_id'));

        return response()->json([
            'success' => true,
            'message' => 'Assessment updated successfully!',
            'data' => $assignment
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $assignment = PatientAssignAssessment::findOrFail($id);
        $assignment->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }


  
public function getByPatient($patientId)
{
    $assignments = PatientAssignAssessment::where('patient_id', $patientId)
        ->with(['patient'])
        ->get();

    // Manually append templates for each assignment
    $assignments->each(function ($item) {
        $templateIds = $item->template_id;
        if (is_array($templateIds)) {
            $item->templates = AssessmentTemplate::whereIn('id', $templateIds)->get();
        } else {
            $item->templates = collect(); // Empty collection if not an array
        }
    });

    $templates = AssessmentTemplate::pluck('name', 'id');
    return response()->json([
        'status' => true,
        'assessments' => $assignments,
        'templates' => $templates
    ]);
}



}
