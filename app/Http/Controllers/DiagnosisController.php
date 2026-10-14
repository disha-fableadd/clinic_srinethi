<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Diagnosis;

class DiagnosisController extends Controller
{
    public function index()
    {
        return view('diagnosis.index');
    }
    public function create()
    {
        return view('diagnosis.create');
    }



    public function exportCsv(Request $request)
    {
        $branchId = $request->query('branch_id');

        $query = Diagnosis::orderBy('name', 'desc');

        // filter by branch if provided
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $diagnoses = $query->get();

        $csvData = [];
        $csvData[] = [
            'ID',
            'Diagnosis Name',
            'Description',
            'Branch ID',
        ];
              $sr = 1;

        foreach ($diagnoses as $diagnosis) {
            $csvData[] = [
               $sr++,
                $diagnosis->name ?? 'N/A',
                $diagnosis->description ?? 'N/A',
                $diagnosis->branch_id ?? 'N/A',
            ];
        }

        $filename = 'diagnosis_export_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
