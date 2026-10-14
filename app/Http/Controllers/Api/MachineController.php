<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id');
        $query = Machine::when($branchId, function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable || $request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);

            if ($hasDataTable) {
                $length = (int) $request->input('length', 10);
                if ($length === -1) {
                    $length = 0;
                }
                $perPage = $length > 0 ? $length : 10;
                $start = (int) $request->input('start', 0);
                $page = (int) floor($start / $perPage) + 1;
            }

            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $hasDataTable
                ? $request->input('search.value', $request->input('search'))
                : $request->input('search');

            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('description', 'like', '%' . $searchValue . '%')
                        ->orWhere('price', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && (int) $request->input('length') === -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $machines = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'machines' => $machines,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ]);
        }

        $machines = $query->get();
        return response()->json($machines);
    }

    // POST /api/machines
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id' => 'nullable|integer',
        ]);

        $machine = Machine::create($request->all());

        return response()->json([
            'message' => 'Machine created successfully!',
            'data' => $machine
        ], 201);
    }

    // GET /api/machines/{id}
    public function show($id)
    {
        $machine = Machine::findOrFail($id);
        return response()->json($machine);
    }

    // PUT /api/machines/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id' => 'nullable|integer',
        ]);

        $machine = Machine::findOrFail($id);
        $machine->update($request->all());

        return response()->json([
            'message' => 'Machine updated successfully!',
            'data' => $machine
        ]);
    }

    // DELETE /api/machines/{id}
    public function destroy($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();

        return response()->json(['message' => 'Machine deleted successfully!']);
    }
}
