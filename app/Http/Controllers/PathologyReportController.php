<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\PathologyReport; // or your actual model name

class PathologyReportController extends Controller
{
    public function index()
    {
        return view('pathology_reports.index');
    }
    public function create()
    {
        return view('pathology_reports.create');
    }





   


    public function exportPathologyReports(Request $request)
    {
        $branchId = $request->get('branch_id'); // Branch ID from query string

        $query = PathologyReport::with(['patient', 'test', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        if ($reports->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No pathology reports found to export.'
            ]);
        }

        $csvData = [];
        // CSV Header
        $csvData[] = [
            'Branch ID',
            'Branch Name',
            'Patient Name',
            'Test Name',
            'Sample Collected Date',
            'Report Date',
            'Result',
            'Report File'
        ];

        foreach ($reports as $report) {
            $csvData[] = [
                $report->branch_id ?? 'N/A',
                $report->branch->name ?? 'N/A',
                $report->patient->fullname ?? 'N/A',
                $report->test->test_name ?? 'N/A',
                $report->sample_collected_date ?? 'N/A',
                $report->report_date ?? 'N/A',
                $report->result ?? 'N/A',
                $report->report_file ?? 'N/A',
            ];
        }

        $branchName = $branchId ? ($reports->first()->branch->name ?? 'branch') : 'all';
        $filename = 'pathology_reports_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
