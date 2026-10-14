<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PatientController extends Controller
{
    public function index()
    {
        return view('patients.index');
    }
    public function create()
    {
        return view('patients.create');
    }

   



    public function exportPatients(Request $request)
    {
        $branchId = $request->get('branch_id'); // comes from query string

        $query = Patients::with(['treatment', 'branch'])->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $patients = $query->get();

        if ($patients->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No patients found to export.'
            ]);
        }

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

        $header = [
            'Patient ID',
            'Branch ID',      // ✅ added
            'Branch Name',    // ✅ added
            'Full Name',
        ];
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Email';
        }
        $header = array_merge($header, [
            'Phone',
            'Address',
            'Age',
            'Birthdate',
            'Patient Type',
            'Referral Source',
            'Source Details',
            'State',
            'City',
            'Note',
            'Treatment',
            'Note Type',
            'Note Interval',
            'Symptoms',
            'Symptom Remarks',
        ]);
        
        $csvData = [];
        $csvData[] = $header;

        $sr = 1;

        foreach ($patients as $patient) {
            $row = [
               $sr++,
                $patient->branch_id,
                $patient->branch->name ?? 'N/A',
                $patient->fullname,
            ];
            if ($currentProjectTypeId !== 3) {
                $row[] = $patient->email;
            }
            $row = array_merge($row, [
                $patient->phone,
                $patient->address,
                $patient->age,
                $patient->birthdate,
                $patient->patient_type,
                $patient->referral_source,
                is_array($patient->source_details) ? implode(', ', array_map(
                    fn($k, $v) => "$k: $v",
                    array_keys($patient->source_details),
                    $patient->source_details
                )) : $patient->source_details,
                is_array($patient->state) ? implode(', ', $patient->state) : $patient->state,
                is_array($patient->city) ? implode(', ', $patient->city) : $patient->city,
                is_array($patient->note) ? implode(', ', array_map(
                    fn($n) => $n['type'] . ': ' . $n['content'],
                    $patient->note
                )) : $patient->note,
                $patient->treatment->name ?? 'N/A',
                $patient->note_type,
                $patient->note_interval,
                is_array($patient->symptomDetails)
                    ? implode(', ', array_column($patient->symptomDetails, 'name'))
                    : ($patient->symptomDetails ? $patient->symptomDetails->pluck('name')->implode(', ') : ''),
                $patient->symptom_remarks ?? '',
            ]);
            $csvData[] = $row;
        }

        // ✅ filename includes branch name
        $branchName = $branchId ? ($patients->first()->branch->name ?? 'branch') : 'all';
        $filename = 'patients_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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




    // load patient in branchid wise in dropdown
    public function getByBranch($branch_id)
    {
        $patients = Patients::where('branch_id', $branch_id)->get(['id', 'name']);
        return response()->json($patients);
    }
}
