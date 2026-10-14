<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentTemplate;
use App\Models\Therapy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AssessmentController extends Controller
{
    public function index()
    {
        return view('assessment.index');
    }
    public function create()
    {

        return view('assessment.create');
    }

    // show method

    public function show($id)
    {
        $assessment = AssessmentTemplate::findOrFail($id);

        // No need to decode JSON because of model casts

        return view('assessment.show', compact('assessment'));
    }

    public function edit($id)
    {
        $assessment = AssessmentTemplate::findOrFail($id);  // fetch assessment by ID
        return view('assessment.edit', compact('assessment'));  // pass it to view
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|array|min:1',
            'title.*' => 'required|string|max:255',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string',
        ]);

        $assessment = AssessmentTemplate::findOrFail($id);
        $assessment->name = $request->input('name');
        $assessment->title = json_encode($request->input('title'));
        $assessment->description = json_encode($request->input('description'));
        // Add more fields if needed
        $assessment->save();

        return redirect()->route('assessment.index')->with('success', 'Assessment updated successfully!');
    }



  
    public function exportAssessments(Request $request)
    {
        $branchId = $request->get('branch_id'); // get branch id from query param

        $query = AssessmentTemplate::orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $assessments = $query->get();

        $csvData = [];
        $csvData[] = ['ID', 'Branch ID', 'Branch Name', 'Template Name', 'Titles', 'Descriptions'];
  $sr = 1;

        foreach ($assessments as $assessment) {
            $titles = $assessment->title ?? [];
            $descriptions = $assessment->description ?? [];
            // $images = $assessment->image ?? [];

            // Join with comma instead of |
            $titlesStr = is_array($titles) ? implode(',', $titles) : $titles;
            $descriptionsStr = is_array($descriptions) ? implode(',', $descriptions) : $descriptions;
            // $imagesStr = is_array($images) ? implode(',', $images) : $images;
            $csvData[] = [
                $sr++,
                $assessment->branch_id ?? 'N/A',             // Branch ID
                $assessment->branch->name ?? 'N/A',
                $assessment->name ?? 'N/A',
                $titlesStr,
                $descriptionsStr,
                // $imagesStr,
            ];
        }

        // Generate CSV
        $filename = 'assessments_export_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}