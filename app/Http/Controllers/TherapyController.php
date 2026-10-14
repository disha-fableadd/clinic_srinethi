<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Therapy;

class TherapyController extends Controller
{
    public function index()
    {
        return view('therapy.index');
    }
    public function create()
    {
        return view('therapy.create');
    }





    public function exportTherapies(Request $request)
    {
        $branchId = $request->get('branch_id'); // Get branch_id from query string

        $query = Therapy::with('branch');

        if ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch
        }

        $therapies = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['Therapy Name', 'Description', 'Duration (Minutes)', 'Cost', 'Status', 'GST Option', 'Product GST', 'Branch ID', 'Branch Name', 'Created At', 'Updated At'];

        foreach ($therapies as $therapy) {
            $csvData[] = [
                $therapy->name,
                $therapy->description,
                $therapy->duration_minutes,
                $therapy->cost,
                $therapy->status,
                $therapy->gst_option ?? 'Without GST',
                $therapy->product_gst ? json_encode($therapy->product_gst) : 'N/A',
                $therapy->branch_id ?? 'N/A',
                $therapy->branch ? $therapy->branch->name : 'N/A',
                $therapy->created_at ? $therapy->created_at->format('d-M-Y h:i A') : 'N/A',
                $therapy->updated_at ? $therapy->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $filename = 'therapies_export_' . now()->format('Ymd_His') . '.csv';
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
