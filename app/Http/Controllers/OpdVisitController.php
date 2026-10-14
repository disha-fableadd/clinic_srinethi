<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\OpdVisit;

class OpdVisitController extends Controller
{
    public function index()
    {
        return view('opd_visit.index');
    }
    public function create()
    {
        return view('opd_visit.create');
    }
    public function edit($id)
    {
        return view('opd_visit.edit', compact('id'));
    }

    public function display($id)
    {
        return view('opd_visit.show', compact('id'));
    }

    


    public function exportOpdVisits(Request $request)
    {
        $branchId = $request->get('branch_id'); // Branch ID from query string

        $query = OpdVisit::with(['patient', 'doctor', 'branch']); // Make sure OpdVisit has 'branch' relation

        if ($branchId) {
            $query->where('branch_id', $branchId); // Filter by branch
        }

        $opdVisits = $query->orderBy('visit_date', 'desc')->get();

        if ($opdVisits->isEmpty()) {
            // Show alert if no data found
            return response("<script>alert('No OPD visits found for the selected branch.'); window.history.back();</script>");
        }

        $csvData = [];
        $csvData[] = [
            'Branch ID',
            'Branch Name',
            'Patient ID',
            'Patient Name',
            'Doctor ID',
            'Doctor Name',
            'Visit Date',
            'Chief Complaint',
            'Diagnosis',
            'Prescription',
            'Consultation Fees',
            'Status',
            'Created At',
            'Updated At',
        ];

        foreach ($opdVisits as $visit) {
            $csvData[] = [
                $visit->branch_id ?? 'N/A',
                $visit->branch->name ?? 'N/A',
                $visit->patient_id,
                $visit->patient->fullname ?? 'N/A',
                $visit->doctor_id,
                $visit->doctor->fullname ?? 'N/A',
                $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('d-M-Y') : 'N/A',
                $visit->chief_complaint ?? 'N/A',
                $visit->diagnosis ?? 'N/A',
                $visit->prescription ?? 'N/A',
                $visit->consultation_fees ?? '0.00',
                $visit->status ?? 'N/A',
                $visit->created_at ? $visit->created_at->format('d-M-Y h:i A') : 'N/A',
                $visit->updated_at ? $visit->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $branchName = $branchId ? ($opdVisits->first()->branch->name ?? 'branch') : 'all';
        $filename = 'opd_visits_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
