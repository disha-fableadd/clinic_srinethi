<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PathologyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PathologyReportController extends Controller
{

   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $reports = PathologyReport::with(['patient', 'test'])
        ->orderBy('report_date', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $reports->where('branch_id', $branchId);
    }

    $reports = $reports->get();

    if ($reports->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No pathology reports found to export.'
        ]);
    }

    $filename = 'pathology_reports_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    // CSV Header
    fputcsv($file, [
        'ID',
        'Patient Name',
        'Test Name',
        'Sample Collected Date',
        'Report Date',
        'Result',
        'Report File URL',
        'Created At'
    ]);

    $sr = 1;
    foreach ($reports as $report) {
        fputcsv($file, [
            $sr++,
            $report->patient->fullname ?? '',
            $report->test->test_name ?? '',
            $report->sample_collected_date ? $report->sample_collected_date->format('Y-m-d') : '',
            $report->report_date ? $report->report_date->format('Y-m-d') : '',
            $report->result ?? '',
            $report->report_file ? url('uploads/reports/' . $report->report_file) : '',
            $report->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Pathology reports exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}




    public function index(Request $request)
    {
        $query = PathologyReport::with(['patient', 'test']);

        // ✅ If branch_id is passed, filter by it
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable || $request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);

            if ($hasDataTable) {
                $length = (int) $request->input('length', 10);
                if ($length === -1) {
                    $length = 0;
                }
                $perPage = $length > 0 ? $length : 10;
                $start = (int) $request->input('start', 0);
                $page = (int) floor($start / $perPage) + 1;
            }

            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $hasDataTable
                ? $request->input('search.value', $request->input('search'))
                : $request->input('search');

            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('sample_collected_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('report_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('result', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('test', function ($t) use ($searchValue) {
                            $t->where('test_name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && $request->input('length') == -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $reports = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Pathology reports fetched successfully',
                'pathology_report' => $reports,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        return response()->json($query->get());
    }



    public function store(Request $request)
    {
        // ✅ Validate request including branch_id
        $request->validate([
            'patient_id'            => 'required|exists:patients,id',
            'test_id'               => 'required|exists:pathology_tests,id',
            'sample_collected_date' => 'required|date',
            'report_date'           => 'required|date',
            'result'                => 'required|string',
            'report_file'           => 'required|file|mimes:pdf|max:2048',
            'branch_id'             => 'required|exists:branches,id', // validate branch
        ]);

        // ✅ Upload report file
        $reportPath = null;
        if ($request->hasFile('report_file')) {
            $file = $request->file('report_file');
            $destinationPath = public_path('uploads/pathology_reports');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $filename);
            $reportPath = 'uploads/pathology_reports/' . $filename;
        }

        // ✅ Save to database including branch_id
        $report = PathologyReport::create([
            'patient_id'            => $request->patient_id,
            'test_id'               => $request->test_id,
            'sample_collected_date' => $request->sample_collected_date,
            'report_date'           => $request->report_date,
            'result'                => $request->result,
            'report_file'           => $reportPath,
            'branch_id'             => $request->branch_id,
        ]);

        return response()->json([
            'message' => 'Report created successfully',
            'data'    => $report
        ]);
    }

    public function show($id)
    {
        $report = PathologyReport::with(['patient', 'test'])->findOrFail($id);
        return response()->json($report);
    }

    public function update(Request $request, $id)
    {
        $report = PathologyReport::findOrFail($id);

        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'test_id' => 'sometimes|exists:pathology_tests,id',
            'sample_collected_date' => 'sometimes|date',
            'report_date' => 'sometimes|date',
            'result' => 'sometimes|string',
            'report_file' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        $data = $request->only([
            'patient_id',
            'test_id',
            'sample_collected_date',
            'report_date',
            'result'
        ]);

        if ($request->hasFile('report_file')) {
            // Delete old file
            if ($report->report_file && Storage::disk('public')->exists($report->report_file)) {
                Storage::disk('public')->delete($report->report_file);
            }

            $data['report_file'] = $request->file('report_file')->store('pathology_reports', 'public');
        }

        $report->update($data);

        return response()->json(['message' => 'Report updated successfully', 'data' => $report]);
    }

    public function destroy($id)
    {
        $report = PathologyReport::findOrFail($id);

        // Delete file
        if ($report->report_file && Storage::disk('public')->exists($report->report_file)) {
            Storage::disk('public')->delete($report->report_file);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
