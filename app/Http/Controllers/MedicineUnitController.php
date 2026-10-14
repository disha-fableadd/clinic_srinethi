<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicineUnit;
use Illuminate\Support\Facades\Response;

class MedicineUnitController extends Controller
{
    //
    public function index()
{
    return view('medicine_unit.index');
}

public function exportCsv(Request $request)
{
    $branchId = $request->get('branch_id');

    $query = MedicineUnit::query();

    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    $units = $query->get();

    if ($units->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No medicine units found for export.'
        ], 404);
    }

    // CSV Header
    $csvData = [];
    $csvData[] = [
        'Unit ID',
        'Unit Name',
        'Branch ID',
        'Created At',
        'Updated At'
    ];
          $sr = 1;

    foreach ($units as $unit) {
        $csvData[] = [
            $sr++,
            $unit->unit ?? 'N/A',
            $unit->branch_id ?? 'N/A',
            $unit->created_at ? $unit->created_at->format('d-M-Y h:i A') : 'N/A',
            $unit->updated_at ? $unit->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'medicine_units_export_' . now()->format('Ymd_His') . '.csv';

    // Create CSV in memory
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
