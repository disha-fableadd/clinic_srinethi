<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
class InventoryController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }



   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $items = Inventory::with('supplier')->orderBy('purchase_date', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $items->where('branch_id', $branchId);
    }

    $items = $items->get();

    if ($items->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No inventory items found to export.'
        ]);
    }

    $filename = 'inventory_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    // CSV Header
    fputcsv($file, [
        'ID',
        'Item Name',
        'Quantity',
        'Supplier Name',
        'Purchase Date',
        'Status',
    
        'Created At'
    ]);

    $sr = 1;
    foreach ($items as $item) {
        fputcsv($file, [
            $sr++,
            $item->item_name,
            $item->quantity,
            $item->supplier->name ?? '',
            $item->purchase_date,
            $item->status,
           
            $item->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Inventory exported successfully.',
       'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}
    public function index(Request $request)
    {
        $query = Inventory::with(['supplier', 'branch']);

        // branch-wise filter if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('item_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('quantity', 'like', '%' . $searchValue . '%')
                        ->orWhere('purchase_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('supplier', function ($s) use ($searchValue) {
                            $s->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsTotal = (clone $query)->count();
            $recordsFiltered = (clone $filteredQuery)->count();

            $columns = $request->input('columns', []);
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir', 'asc');
            $orderColumn = null;
            if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex]['data'])) {
                $orderColumn = $columns[$orderColumnIndex]['data'];
            }

            $allowedOrderColumns = ['id', 'item_name', 'quantity', 'purchase_date', 'status', 'created_at', 'updated_at'];
            if ($orderColumn && in_array($orderColumn, $allowedOrderColumns, true)) {
                $filteredQuery->orderBy($orderColumn, $orderDir === 'desc' ? 'desc' : 'asc');
            } else {
                $filteredQuery->orderBy('id', 'desc');
            }

            $length = (int) $request->input('length', 10);
            if ($length === -1) {
                $length = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }
            $length = $length > 0 ? $length : 10;
            $start = (int) $request->input('start', 0);
            $page = (int) floor($start / $length) + 1;

            $inventory = $filteredQuery->forPage($page, $length)->get();

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $inventory,
            ]);
        }

        // Discharge-style pagination support (page/per_page)
        if ($request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $request->input('search');
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('item_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('quantity', 'like', '%' . $searchValue . '%')
                        ->orWhere('purchase_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('supplier', function ($s) use ($searchValue) {
                            $s->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $inventory = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'data' => $inventory,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ]);
        }

        $inventory = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data'   => $inventory
        ]);
    }



    public function store(Request $request)
{
    $loggedInUserId = auth()->id();

    $validator = Validator::make($request->all(), [
        'item_name'     => 'required|string',
        'quantity'      => 'required|integer',
        'supplier_id'   => 'required|exists:suppliers,id',
        'purchase_date' => 'required|date',
        'status'        => 'required|in:active,inactive,expired',
        'branch_id'     => 'required|exists:branches,id', // ✅ validate branch_id
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $inventory = Inventory::create([
        'user_id'       => $loggedInUserId,
        'item_name'     => $request->item_name,
        'quantity'      => $request->quantity,
        'supplier_id'   => $request->supplier_id,
        'purchase_date' => $request->purchase_date,
        'status'        => $request->status,
        'branch_id'     => $request->branch_id, // ✅ store branch_id
    ]);

    return response()->json([
        'message'   => 'Inventory item created successfully',
        'inventory' => $inventory
    ], 201);
}

    /**
     * Get a single inventory item by ID.
     */
    public function show($id)
    {
        $inventory = Inventory::with('supplier')->findOrFail($id);

        return response()->json($inventory, 200);
    }

    /**
     * Update an existing inventory item.
     */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string',

            'quantity' => 'required|integer',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',

            'status' => 'required|in:active,inactive,expired',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $inventory->update([
            'item_name' => $request->item_name,

            'quantity' => $request->quantity,
            'supplier_id' => $request->supplier_id,
            'purchase_date' => $request->purchase_date,

            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Inventory item updated successfully',
            'inventory' => $inventory
        ], 200);
    }

    /**
     * Delete an inventory item.
     */
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json(['message' => 'Inventory item deleted successfully'], 200);
    }
}

