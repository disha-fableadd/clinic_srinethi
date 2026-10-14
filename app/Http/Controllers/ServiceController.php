<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ServiceController extends Controller
{
    public function index()
    {
        return view('service.index');
    }
    public function create()
    {
        return view('service.create');
    }

    
    public function exportServices(Request $request)
    {
        $branchId = $request->get('branch_id');

        // Make sure Services model has a 'branch' relation
        $query = Services::with('branch');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $services = $query->orderBy('created_at', 'desc')->get();

      

        $csvData = [];
        $csvData[] = ['Branch ID', 'Branch Name', 'Service ID',  'Service Name', 'Description',  'Price',  'Created At', 'Updated At'];

        foreach ($services as $service) {
            $csvData[] = [
                $service->branch_id ?? 'N/A',
                $service->branch->name ?? 'N/A',
                $service->id,
                $service->department,
                $service->description,
                // $service->service ?? 'N/A',
                $service->cost ?? 'N/A',
                // $service->status,
                $service->created_at ? $service->created_at->format('d-M-Y h:i A') : 'N/A',
                $service->updated_at ? $service->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $branchName = $branchId ? ($services->first()->branch->name ?? 'branch') : 'all';
        $filename = 'services_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
