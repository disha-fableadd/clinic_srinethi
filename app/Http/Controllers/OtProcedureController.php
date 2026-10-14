<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\OtProcedure;

class OtProcedureController extends Controller
{
    public function index()
    {
        return view('ot_procedure.index');
    }
    public function create()
    {
        return view('ot_procedure.create');
    }
    public function edit($id)
    {
        return view('ot_procedure.edit', compact('id'));
    }

    public function display($id)
    {
        return view('ot_procedure.show', compact('id'));
    }

    

        public function exportOtProcedures(Request $request)
{
    $branchId = $request->query('branch_id'); // Get branch_id from query parameter

    // Load OT procedures with related patient & doctor, filter by branch_id
    $procedures = OtProcedure::with(['patient', 'doctor'])
        ->when($branchId, function ($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })
        ->get();

    $csvData = [];
    $csvData[] = [
       
        'Patient Name',
        'Doctor ID',
        'Doctor Name',
        'Procedure Name',
        'Procedure Date',
        'Operation Notes',
        'Status',
        'Created At',
        'Updated At'
    ];

    foreach ($procedures as $procedure) {
        $csvData[] = [
         
            $procedure->patient->fullname ?? 'N/A',
            $procedure->doctor_id,
            $procedure->doctor->fullname ?? 'N/A',
            $procedure->procedure_name,
            $procedure->procedure_date ? \Carbon\Carbon::parse($procedure->procedure_date)->format('d-M-Y') : 'N/A',
            $procedure->operation_notes,
            $procedure->status,
            $procedure->created_at ? $procedure->created_at->format('d-M-Y h:i A') : 'N/A',
            $procedure->updated_at ? $procedure->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'ot_procedures_export_' . now()->format('Ymd_His') . '.csv';
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
