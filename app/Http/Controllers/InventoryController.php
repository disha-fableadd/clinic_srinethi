<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index'); 
    }
    public function create()
    {
        return view('inventory.create'); 
    }


public function exportInventory(Request $request)
{
    $branchId = $request->query('branch_id'); // ✅ get branch_id from query string

    // Load inventory with related supplier & filter by branch_id
    $inventories = Inventory::with('supplier')
        ->when($branchId, function ($query) use ($branchId) {
            return $query->where('branch_id', $branchId);
        })
        ->get();

    $csvData = [];
    $csvData[] = ['Item Name', 'Quantity', 'Supplier Name', 'Purchase Date', 'Status', 'Created At', 'Updated At'];

    foreach ($inventories as $item) {
        $csvData[] = [
            $item->item_name,
            $item->quantity,
            $item->supplier->name ?? 'N/A',
            $item->purchase_date ? date('d-M-Y', strtotime($item->purchase_date)) : 'N/A',
            ucfirst($item->status),
            $item->created_at ? $item->created_at->format('d-M-Y h:i A') : 'N/A',
            $item->updated_at ? $item->updated_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'inventory_export_' . now()->format('Ymd_His') . '.csv';
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
