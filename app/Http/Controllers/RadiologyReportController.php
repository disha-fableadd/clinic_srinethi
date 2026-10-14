<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RadiologyReport;
use App\Models\Patients;
use App\Models\RadiologyTest;

class RadiologyReportController extends Controller
{
    public function index()
    {
        return view('radiology-reports.index');
    }

    public function create()
    {
        return view('radiology-reports.create');
    }
    public function edit($id)
    {
        return view('radiology-reports.edit');
    }
    public function show($id)
    {
        return view('radiology-reports.show');
    }

  

    public function exportRadiologyReports(Request $request)
{
    $branchId = $request->get('branch_id'); // Branch ID from query string

    $query = \App\Models\RadiologyReport::with(['patient', 'test', 'branch']);

    if ($branchId) {
        $query->where('branch_id', $branchId); // Filter by branch
    }

    $reports = $query->orderBy('created_at', 'desc')->get();

    if ($reports->isEmpty()) {
        // Show browser alert if no data found
        return response("<script>alert('No radiology reports found for the selected branch.'); window.history.back();</script>");
    }

    $csvData = [];
    $csvData[] = [
        'Branch ID',
        'Branch Name',
        'Patient ID',
        'Patient Name',
        'Test Name',
        'Report Date',
        'Report File',
        'Created At',
        'Updated At'
    ];

    foreach ($reports as $report) {
        $csvData[] = [
            $report->branch_id ?? 'N/A',
            $report->branch->name ?? 'N/A',
            $report->patient_id ?? 'N/A',
            $report->patient->fullname ?? 'N/A',
            $report->test->test_name ?? 'N/A',
            $report->report_date ?? 'N/A',
            $report->report_file ? asset($report->report_file) : 'N/A',
            $report->created_at ? $report->created_at->format('d-M-Y h:i A') : 'N/A',
            $report->updated_at ? $report->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $branchName = $branchId ? ($reports->first()->branch->name ?? 'branch') : 'all';
    $filename = 'radiology_reports_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

    $handle = fopen('php://temp', 'r+');
    foreach ($csvData as $line) {
        fputcsv($handle, $line);
    }
    rewind($handle);
    $contents = stream_get_contents($handle);
    fclose($handle);

    return response($contents, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$filename",
    ]);
}

}
