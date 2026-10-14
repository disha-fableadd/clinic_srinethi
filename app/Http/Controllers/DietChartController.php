<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DietChart;
use Illuminate\Http\Request;

class DietChartController extends Controller
{
    public function index()
    {
        $dietCharts = DietChart::all();
        return view('dietchart.index', compact('dietCharts'));
    }

    // 🔹 Show create form
    public function create()
    {
        return view('dietchart.create');
    }

    // 🔹 Export all diet charts as CSV
    public function exportDietChart(Request $request)
    {
        $branchId = $request->get('branch_id');

        $query = DietChart::orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $dietCharts = $query->get();

        $csvData = [];
        $csvData[] = [
            'DietChart ID',
            'Branch ID',
            'Branch Name',
            'Diet Name',
            'Description',
            'Time',
        ];

        foreach ($dietCharts as $chart) {

            // Convert arrays to comma-separated strings
            $descriptions = is_array($chart->description) ? implode(', ', $chart->description) : $chart->description;
            $times = is_array($chart->time) ? implode(', ', $chart->time) : $chart->time;

            $csvData[] = [
                $chart->id,
                $chart->branch_id ?? 'N/A',
                // $chart->branch->branch ?? 'N/A',
                $chart->name ?? 'N/A',
                $descriptions ?? 'N/A',
                $times ?? 'N/A',
            ];
        }

        $filename = 'diet_chart_export_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
