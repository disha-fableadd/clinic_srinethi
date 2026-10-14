<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SupplierController extends Controller
{
    public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $suppliers = Supplier::withCount('inventory')->orderBy('name');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $suppliers->where('branch_id', $branchId);
    }

    $suppliers = $suppliers->get();

    if ($suppliers->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No suppliers found to export.'
        ]);
    }

    $filename = 'suppliers_export_' . now()->format('Ymd_His') . '.csv';
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
        'Name',
        'Contact Person',
        'Phone',
        'Email',
        'Address',
        'Total Items Supplied',
        'Created At'
    ]);

    $sr = 1;
    foreach ($suppliers as $supplier) {
        fputcsv($file, [
            $sr++,
            $supplier->name,
            $supplier->contact_person,
            $supplier->phone,
            $supplier->email,
            $supplier->address,
            $supplier->inventory_count, // from withCount
            $supplier->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Suppliers exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}

     public function getSuppliers()
     {
         $user = Auth::user(); // Get the logged-in user
     
         if (!$user) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         $suppliers = Supplier::where('user_id', $user->id)
                              ->orderBy('id', 'desc')
                              ->get();
     
         return response()->json(['suppliers' => $suppliers]);
     }
     
   

  public function index(Request $request)
{
    $query = Supplier::query();

    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
    }
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

    // DataTables server-side request
    if ($hasDataTable) {
        $searchValue = $request->input('search.value', $request->input('search'));
        $filteredQuery = clone $query;

        if (!empty($searchValue)) {
            $filteredQuery->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('contact_person', 'like', '%' . $searchValue . '%')
                    ->orWhere('phone', 'like', '%' . $searchValue . '%')
                    ->orWhere('email', 'like', '%' . $searchValue . '%')
                    ->orWhere('address', 'like', '%' . $searchValue . '%');
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

        $allowedOrderColumns = ['id', 'name', 'contact_person', 'phone', 'email', 'address', 'created_at', 'updated_at'];
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

        $suppliers = $filteredQuery->forPage($page, $length)->get();

        return response()->json([
            'status' => true,
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $suppliers,
        ], 200);
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
                $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('contact_person', 'like', '%' . $searchValue . '%')
                    ->orWhere('phone', 'like', '%' . $searchValue . '%')
                    ->orWhere('email', 'like', '%' . $searchValue . '%')
                    ->orWhere('address', 'like', '%' . $searchValue . '%');
            });
        }

        $recordsFiltered = (clone $filteredQuery)->count();
        $suppliers = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
        $lastPage = (int) ceil($recordsFiltered / $perPage);

        return response()->json([
            'data' => $suppliers,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
                'per_page' => $perPage,
                'total' => $recordsFiltered,
            ],
        ], 200);
    }

    $suppliers = $query->orderBy('id', 'desc')->get();

    // return plain array (unchanged for existing ajax)
    return response()->json($suppliers, 200);
}


     
   
     public function store(Request $request)
{
    $loggedInUserId = auth()->id();

    $validator = Validator::make($request->all(), [
        'name'           => 'required|string',
        'contact_person' => 'required|string',
        'phone'          => 'required|string',
        'email'          => 'required|email|unique:suppliers,email',
        'address'        => 'required|string',
        'branch_id'      => 'required|exists:branches,id', // ✅ validate branch_id
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 401);
    }

    $supplier = Supplier::create([
        'name'           => $request->name,
        'contact_person' => $request->contact_person,
        'phone'          => $request->phone,
        'email'          => $request->email,
        'address'        => $request->address,
        'branch_id'      => $request->branch_id, // ✅ store branch_id
        'user_id'        => $loggedInUserId,
    ]);

    return response()->json([
        'message'  => 'Supplier created successfully',
        'supplier' => $supplier
    ], 200);
}


  
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier, 200);
    }

    /**
     * Update an existing supplier.
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $supplier->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier
        ], 200);
    }

    /**
     * Delete a supplier.
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully'], 200);
    }
}
