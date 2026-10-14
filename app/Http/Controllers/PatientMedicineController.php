<?php

namespace App\Http\Controllers;

use App\Models\PatientMedicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PatientMedicineController extends Controller
{
    public function index()
    {
        return view('patient_medicine.index');
    }
    public function create()
    {
        return view('patient_medicine.create');
    }

    

        public function export(Request $request)
{
    $branchId = $request->query('branch_id'); // get branch_id from query string

    // Load PatientMedicine with related patient & treatment, filtered by branch_id
    $patientMedicines = PatientMedicine::with(['patient', 'treatment'])
        ->when($branchId, function ($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })
        ->get();

    $csvData = [];
    $csvData[] = [ 'Patient Name', 'Treatment Name', 'Medicine Name(s)', 'Note', 'Created At', 'Updated At'];

    foreach ($patientMedicines as $item) {
        // Decode medicine_ids (assuming it's a JSON string or array)
        $medicineIds = is_array($item->medicine_id)
            ? $item->medicine_id
            : json_decode($item->medicine_id, true);

        // Safely fetch medicine names
        $medicineNames = [];
        if (!empty($medicineIds) && is_array($medicineIds)) {
            $medicineNames = \App\Models\Medicine::whereIn('id', $medicineIds)->pluck('name')->toArray();
        }

        $csvData[] = [
          
            $item->patient->fullname ?? 'N/A',
            $item->treatment->name ?? 'N/A',
            !empty($medicineNames) ? implode(', ', $medicineNames) : 'N/A',
            $item->note ?? 'N/A',
            $item->created_at ? $item->created_at->format('d-M-Y h:i A') : 'N/A',
            $item->updated_at ? $item->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'patient_medicines_export_' . now()->format('Ymd_His') . '.csv';
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
