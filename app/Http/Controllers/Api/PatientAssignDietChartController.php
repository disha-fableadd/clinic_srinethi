<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DietChart;
use App\Models\PatientAssignDietChart;
use Illuminate\Http\Request;

use App\Models\DietTemplate;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Patients;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientAssignDietChartController extends Controller
{
    //Fetch all diet templates
    public function index()
    {
        $templates=DietChart::select('id','name','description')->get();
        return response()->json($templates);
    }

   
      // Store new diet chart assignment
      public function store(Request $request)
      {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'diet_template_id' => 'required|array',
           'diet_template_id.*' => 'exists:diet_charts,id',


           
        ]);
        $assignment =PatientAssignDietChart::create([
            'branch_id' => $request->branch_id,
            'patient_id' => $request->patient_id,
            'diet_template_id' => $request->diet_template_id,
            'description' => $request->description ?? [],
        ]);
         return response()->json([
            'status' => true,
            'data' => $assignment,
            'message' => 'Diet Chart assigned successfully!'
        ]);

      }
       // Show patient details (for form view, if needed)
    public function show($patient_id)
    {
        $patient = Patients::findOrFail($patient_id);
        return view('patient_details', [
            'patient_id' => $patient_id,
            'patient' => $patient,
        ]);
    }
     // Update a diet chart assignment
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|integer',
            'diet_template_id' => 'required|exists:diet_templates,id',
        ]);

        $assignment = PatientAssignDietChart::findOrFail($id);
        $assignment->update($request->only('branch_id', 'diet_template_id'));

        return response()->json([
            'success' => true,
            'message' => 'Diet Chart updated successfully!',
            'data' => $assignment
        ]);
    }

   

    public function destroy($id)
{
    $assignment = PatientAssignDietChart::find($id);

    if (!$assignment) {
        return response()->json([
            'status' => false,
            'message' => 'Diet chart assignment not found.'
        ], 404);
    }

    $assignment->delete();

    return response()->json([
        'status' => true,
        'message' => 'Diet chart assignment deleted successfully.'
    ]);
}

     public function getByPatient($patientId)
    {
        $assignments = PatientAssignDietChart::where('patient_id', $patientId)
            ->with('patient')
            ->get();

        // Append templates for each assignment
        $assignments->each(function ($item) {
            $templateIds = $item->diet_template_id;
            if (is_array($templateIds)) {
                $item->templates = DietChart::whereIn('id', $templateIds)->get();
            } else {
                $item->templates = collect();
            }
        });

        return response()->json([
            'status' => true,
            'dietCharts' => $assignments
        ]);
    }









}
