<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id');

        $baseQuery = TaxRate::where(function ($query) {
                $query->where('isDeleted', 0)->orWhereNull('isDeleted');
            })
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });

        // If not a DataTables request, keep the old response format
        if (!$request->has('length') && !$request->has('start') && !$request->has('draw')) {
            // Support custom pagination (discharge-style) when page/per_page are provided
            if ($request->has('page') || $request->has('per_page')) {
                $page = (int) $request->input('page', 1);
                $perPage = (int) $request->input('per_page', 10);
                $page = $page > 0 ? $page : 1;
                $perPage = $perPage > 0 ? $perPage : 10;

                $searchValue = $request->input('search');
                $filteredQuery = clone $baseQuery;
                if (!empty($searchValue)) {
                    $filteredQuery->where(function ($query) use ($searchValue) {
                        $query->where('tax_name', 'like', '%' . $searchValue . '%')
                            ->orWhere('tax_rate', 'like', '%' . $searchValue . '%')
                            ->orWhere('status', 'like', '%' . $searchValue . '%');
                    });
                }

                $recordsFiltered = (clone $filteredQuery)->count();
                $taxes = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
                $lastPage = (int) ceil($recordsFiltered / $perPage);

                return response()->json([
                    'data' => $taxes,
                    'pagination' => [
                        'current_page' => $page,
                        'last_page' => $lastPage > 0 ? $lastPage : 1,
                        'per_page' => $perPage,
                        'total' => $recordsFiltered,
                    ],
                ]);
            }

            $taxes = $baseQuery->get();
            return response()->json($taxes);
        }

        // --- DataTables server-side handling ---
        $searchValue = $request->input('search.value', $request->input('search'));
        $filteredQuery = clone $baseQuery;

        if (!empty($searchValue)) {
            $filteredQuery->where(function ($query) use ($searchValue) {
                $query->where('tax_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('tax_rate', 'like', '%' . $searchValue . '%')
                    ->orWhere('status', 'like', '%' . $searchValue . '%');
            });
        }

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = $request->input('columns', []);
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderColumn = null;
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex]['data'])) {
            $orderColumn = $columns[$orderColumnIndex]['data'];
        }

        $allowedOrderColumns = ['id', 'tax_name', 'tax_rate', 'status', 'created_at', 'updated_at'];
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

        $taxes = $filteredQuery->forPage($page, $length)->get();
        $lastPage = (int) ceil($recordsFiltered / $length);

        return response()->json([
            'data' => $taxes,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
                'per_page' => $length,
                'total' => $recordsFiltered,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tax_name' => 'required|string|max:255',
            'tax_rate' => 'required|numeric',
            'status' => 'required|string',
            'branch_id' => 'nullable|integer',
        ]);

        $tax = TaxRate::create($request->all());

        return response()->json([
            'message' => 'Tax rate created successfully!',
            'data' => $tax
        ], 201);
    }

    public function show($id)
    {
        $tax = TaxRate::where(function ($query) {
                $query->where('isDeleted', 0)->orWhereNull('isDeleted');
            })->findOrFail($id);
        return response()->json($tax);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tax_name' => 'required|string|max:255',
            'tax_rate' => 'required|numeric',
            'status' => 'required|string',
            'branch_id' => 'nullable|integer',
        ]);

        $tax = TaxRate::where(function ($query) {
                $query->where('isDeleted', 0)->orWhereNull('isDeleted');
            })->findOrFail($id);
        $tax->update($request->all());

        return response()->json([
            'message' => 'Tax rate updated successfully!',
            'data' => $tax
        ]);
    }

    public function destroy($id)
    {
        $tax = TaxRate::findOrFail($id);
        $tax->update(['isDeleted' => 1]);

        return response()->json(['message' => 'Tax rate deleted successfully!']);
    }
}
