<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Support\Facades\Response;
class MedicineController extends Controller
{
    public function index()
    {
        return view('medicine.index'); 
    }
    public function create()
    {
        // $categories = Categories::all();
        return view('medicine.create'); 
    }








public function exportMedicines(Request $request)
{
    $branchId = $request->query('branch_id'); // get branch_id from query

    // Load medicines with category, filter by branch_id
    $medicines = Medicine::with('category')
        ->when($branchId, function ($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })
        ->get();

    $csvData = [];
    $csvData[] = [
        'ID',
        'Name',
        'Category',
        'Description',
        'Quantity',
        'Unit',
        'Manufacture Date',
        'Expiry Date',
        'Created At',
        'Updated At',
    ];
          $sr = 1;

    foreach ($medicines as $medicine) {
        $csvData[] = [
            $sr++,
            $medicine->name,
            $medicine->category->name ?? 'N/A',
            $medicine->description,
            $medicine->quantity ?? 'N/A',
            $medicine->unit ?? 'N/A',
            $medicine->manufacture_date ? \Carbon\Carbon::parse($medicine->manufacture_date)->format('d-M-Y') : 'N/A',
            $medicine->expiry_date ? \Carbon\Carbon::parse($medicine->expiry_date)->format('d-M-Y') : 'N/A',
            $medicine->created_at ? $medicine->created_at->format('d-M-Y h:i A') : 'N/A',
            $medicine->updated_at ? $medicine->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'medicines_export_' . now()->format('Ymd_His') . '.csv';
    $handle = fopen('php://temp', 'r+');
    stream_filter_append($handle, 'convert.iconv.UTF-8/UTF-16LE');

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
