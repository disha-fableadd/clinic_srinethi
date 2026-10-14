<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RadiologyTest;
use Illuminate\Support\Facades\Response;

class RadiologyTestController extends Controller
{
    public function index()
    {
        return view('radiology-tests.index');
    }

    public function create()
    {
        return view('radiology-tests.create');
    }

    public function show($id)
    {
        return view('radiology-tests.show');
    }

    public function edit($id)
    {
        return view('radiology-tests.edit');
    }

  


    public function exportRadiologyTests(Request $request)
    {
        $branchId = $request->get('branch_id'); // Branch ID from query string

        $query = RadiologyTest::with('branch'); // Make sure RadiologyTest has 'branch' relation

        if ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch
        }

        $radiologyTests = $query->orderBy('created_at', 'desc')->get();

       


        if ($radiologyTests->isEmpty()) {
            // Trigger browser alert via JavaScript
            return response("<script>alert('No radiology tests found for the selected branch.'); window.history.back();</script>");
        }

        $csvData = [];
        // CSV Header
        $csvData[] = [
            'Branch ID',
            'Branch Name',
            'Test Name',
            'Test Code',
            'Body Part',
            'Cost',
            'GST Option',
            'Product GST',
            'Report Format',
            'Created At',
            'Updated At'
        ];

        foreach ($radiologyTests as $test) {
            $csvData[] = [
                $test->branch_id ?? 'N/A',
                $test->branch->name ?? 'N/A',
                $test->test_name,
                $test->test_code,
                $test->body_part,
                number_format($test->cost, 2),
                $test->gst_option ?? 'N/A',
                $test->product_gst ?? 'N/A',
                strtoupper($test->report_format ?? 'N/A'),
                $test->created_at ? $test->created_at->format('d-M-Y h:i A') : 'N/A',
                $test->updated_at ? $test->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $branchName = $branchId ? ($radiologyTests->first()->branch->name ?? 'branch') : 'all';
        $filename = 'radiology_tests_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
