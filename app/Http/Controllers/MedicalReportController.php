<?php

namespace App\Http\Controllers;

use App\Models\MedicalReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MedicalReportController extends Controller
{
    public function index()
    {
        return view('report.index');
    }
    public function create()
    {
        return view('report.create');
    }

   
     public function exportReports()
    {
        $reports = MedicalReport::with(['patient', 'doctor'])->get();

        $csvData = [];
        $csvData[] = ['Patient Name', 'Doctor Name', 'Type', 'Description', 'File URL', 'Created At'];

        foreach ($reports as $report) {
            // Case 1: report_type is a string column
            $typeName = $report->report_type ?? 'N/A';

            // Case 2: if report_type is foreign key -> use relation instead
            // $typeName = $report->typeData->name ?? 'N/A';

            $csvData[] = [
                $report->patient->fullname ?? 'N/A',
                $report->doctor->fullname ?? 'N/A',
                $typeName,
                $report->description ?? 'N/A',
                $report->file_path ? asset($report->file_path) : 'N/A',
                $report->created_at ? $report->created_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $filename = 'reports_export_' . now()->format('Ymd_His') . '.csv';
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
