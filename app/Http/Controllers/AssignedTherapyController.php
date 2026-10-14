<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\AssignedTherapy;

class AssignedTherapyController extends Controller
{
    public function index()
    {
        return view('assign-therapy.index');
    }
    public function create()
    {
        return view('assign-therapy.create');
    }



    public function exportAssignedTherapies(Request $request)
    {
        $branchId = $request->get('branch_id'); // Branch ID from query string

        $query = AssignedTherapy::with(['patient', 'therapy', 'doctor', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch
        }

        $assignedTherapies = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'Patient Name',
            'Therapy Name',
            'Doctor Name',
            'Type',
            'Start Date',
            'End Date',
            'Status',
            'Branch ID',
            'Branch Name',
            'Created At',
            'Updated At'
        ];

        foreach ($assignedTherapies as $item) {
            $csvData[] = [
                $item->patient->fullname ?? 'N/A',
                $item->therapy->name ?? 'N/A',
                $item->doctor->fullname ?? 'N/A',
                $item->type ?? 'N/A',
                $item->start_date ?? 'N/A',
                $item->end_date ?? 'N/A',
                $item->status ?? 'N/A',
                $item->branch_id ?? 'N/A',             // Branch ID
                $item->branch->name ?? 'N/A',         // Branch Name
                $item->created_at ? $item->created_at->format('d-M-Y h:i A') : 'N/A',
                $item->updated_at ? $item->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $filename = 'assigned_therapies_export_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return Response::make($contents, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }
}
