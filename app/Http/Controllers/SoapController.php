<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\Soap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class SoapController extends Controller
{
    public function index()
    {
        // Get only logged-in user's SOAP records
        $soaps = Soap::where('user_id', Auth::id())->get();
        return view('soap.index', compact('soaps'));
    }

    public function create()
    {
        $patients = Patients::all(); // 🔹 fetch all patients
        return view('soap.create', compact('patients'));
    }


  
    public function exportCsv(Request $request)
    {
        $branchId = $request->get('branch_id'); // branch id from request

        $query = Soap::with(['patient', 'branch'])->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId); // filter by branch
        }

        $soaps = $query->get();

        if ($soaps->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No SOAP records found to export.'
            ]);
        }

        $csvData = [];
        $csvData[] = [
            'SOAP ID',
            'Branch ID',
            'Branch Name',
            'Patient Name',
            'Date',
            'Subjective',
            'Objective',
            'Assessment',
            'Plan',
            'Created At',
            'Updated At'
        ];
              $sr = 1;

        foreach ($soaps as $soap) {
            $csvData[] = [
               $sr++,
                $soap->branch_id ?? 'N/A',
                $soap->branch ? $soap->branch->name : 'N/A',
                $soap->patient->fullname ?? 'N/A',
                $soap->date ?? 'N/A',
                is_array($soap->subjective) ? implode(" | ", $soap->subjective) : $soap->subjective,
                is_array($soap->objective) ? implode(" | ", $soap->objective) : $soap->objective,
                is_array($soap->assessment) ? implode(" | ", $soap->assessment) : $soap->assessment,
                is_array($soap->plan) ? implode(" | ", $soap->plan) : $soap->plan,
                $soap->created_at ? $soap->created_at->format('Y-m-d H:i:s') : '',
                $soap->updated_at ? $soap->updated_at->format('Y-m-d H:i:s') : '',
            ];
        }

        $filename = 'soap_export_' . now()->format('Ymd_His') . '.csv';
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
