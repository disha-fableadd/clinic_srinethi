<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PathologyTestController extends Controller
{
    public function index()
    {
        return view('pathology.index');
    }
    public function create()
    {
        return view('pathology.create');
    }

   

    public function exportPathology(Request $request)
    {
        $branchId = $request->get('branch_id');

        // Get pathology tests with branch relationship
        $query = \App\Models\PathologyTest::with('branch');

        // Apply branch filter if branch_id exists
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $pathologies = $query->orderBy('created_at', 'desc')->get();

        if ($pathologies->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No pathology tests found to export.'
            ]);
        }

        $csvData = [];
        // CSV Header
        $csvData[] = ['Test Name', 'Test Code', 'Sample Type', 'Normal Range', 'Cost', 'GST Option', 'Product GST', 'Report Format', 'Branch ID', 'Branch Name'];

        foreach ($pathologies as $test) {
            $csvData[] = [
                $test->test_name,
                $test->test_code,
                $test->sample_type,
                $test->normal_range,
                $test->cost,
                $test->gst_option,
                $test->product_gst,
                $test->report_format,
                $test->branch_id ?? 'N/A',
                $test->branch->name ?? 'N/A',
            ];
        }

        $branchName = $branchId ? ($pathologies->first()->branch->name ?? 'branch') : 'all';
        $filename = 'pathology_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
