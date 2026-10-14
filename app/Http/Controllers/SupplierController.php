<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('supplier.index');
    }
    public function create()
    {
        return view('supplier.create');
    }


    
      public function exportSuppliers(Request $request)
{
    $branchId = $request->query('branch_id'); // ✅ get branch_id from query

    // Fetch suppliers branch-wise
    $suppliers = Supplier::when($branchId, function ($query) use ($branchId) {
        return $query->where('branch_id', $branchId);
    })->get();

    // Prepare the CSV data
    $csvData = [];
    $csvData[] = ['S.No', 'Company Name', 'Person Name', 'Phone', 'Email', 'Address', 'Created At', 'Updated At'];

    foreach ($suppliers as $index => $supplier) {
        $csvData[] = [
            $index + 1, // S.No
            $supplier->name,
            $supplier->contact_person,
            $supplier->phone,
            $supplier->email,
            $supplier->address,
            $supplier->created_at ? $supplier->created_at->format('d-M-Y h:i A') : 'N/A',
            $supplier->updated_at ? $supplier->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    // Create the CSV file
    $filename = 'suppliers_export_' . now()->format('Ymd_His') . '.csv';
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
