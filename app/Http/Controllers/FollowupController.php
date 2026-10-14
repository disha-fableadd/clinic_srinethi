<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FollowupController extends Controller
{
    public function index()
    {
        return view('followup.index');
    }
    public function create()
    {
        return view('followup.create');
    }


   

    public function exportCsv(Request $request)
    {
        $branchId = $request->get('branch_id');

        // Build query with relationships
        $query = Followup::with(['patient', 'doctor', 'treatment', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $followups = $query->get();

        if ($followups->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No followups found for export.'
            ], 404);
        }

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

        // Prepare CSV header
        $header = [
            'Followup ID',
            'Branch ID',
            'Branch Name',
            'Patient Name',
            'Doctor Name',
        ];
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Treatment Name';
        }
        $header[] = 'Followup Date';
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Followup Type';
            $header[] = 'Followup Update';
        }
        $header[] = 'Created At';
        $header[] = 'Updated At';

        $csvData = [];
        $csvData[] = $header;

        $sr = 1;

        // Add rows
        foreach ($followups as $followup) {
            $row = [
               $sr++,
                $followup->branch_id,
                $followup->branch->name ?? 'N/A',
                $followup->patient->fullname ?? 'N/A',
                $followup->doctor->fullname ?? 'N/A',
            ];
            if ($currentProjectTypeId !== 3) {
                $row[] = $followup->treatment->name ?? 'N/A';
            }
            $row[] = $followup->date ?? 'N/A';
            if ($currentProjectTypeId !== 3) {
                $row[] = $followup->followup_type ?? 'N/A';
                $row[] = $followup->followup_update ?? 'N/A';
            }
            $row[] = $followup->created_at ? $followup->created_at->format('d-M-Y h:i A') : 'N/A';
            $row[] = $followup->updated_at ? $followup->updated_at->format('d-M-Y h:i A') : 'N/A';
            
            $csvData[] = $row;
        }

        // ✅ Filename with branch name (or 'all')
        $branchName = $branchId
            ? ($followups->first()->branch->name ?? 'branch')
            : 'all';

        $filename = 'followups_export_' .
            str_replace(' ', '_', strtolower($branchName)) .
            '_' . now()->format('Ymd_His') . '.csv';

        // Create CSV in memory
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        // Return CSV response
        return Response::make($contents, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }
}
