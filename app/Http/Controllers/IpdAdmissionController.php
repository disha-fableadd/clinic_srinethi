<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\IpdAdmission;

class IpdAdmissionController extends Controller
{
    public function index()
    {
        return view('ipd_admit.index');
    }
    public function create()
    {
        return view('ipd_admit.create');
    }


  


    public function ipd(Request $request)
    {
        $branchId = $request->get('branch_id'); // Branch ID from query string

        $query = IpdAdmission::with(['patient', 'doctor', 'treatment', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch
        }

        $ipdAdmissions = $query->orderBy('admission_date', 'desc')->get();

        if ($ipdAdmissions->isEmpty()) {
            // Show alert if no data found
            return response("<script>alert('No IPD admissions found for the selected branch.'); window.history.back();</script>");
        }

        $csvData = [];
        $csvData[] = [
            'Branch ID',
            'Branch Name',
            'Patient Name',
            'Doctor Name',
            'Treatment',
            'Admission Date',
            'Room Number',
            'Bed Number'
        ];

        foreach ($ipdAdmissions as $ipd) {
            $csvData[] = [
                $ipd->branch_id ?? 'N/A',
                $ipd->branch->name ?? 'N/A',
                $ipd->patient->fullname ?? 'N/A',
                $ipd->doctor->fullname ?? 'N/A',
                $ipd->treatment->name ?? 'N/A',
                $ipd->admission_date ?? 'N/A',
                $ipd->room_number ?? 'N/A',
                $ipd->bed_number ?? 'N/A',
            ];
        }

        $branchName = $branchId ? ($ipdAdmissions->first()->branch->name ?? 'branch') : 'all';
        $filename = 'ipd_admissions_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
