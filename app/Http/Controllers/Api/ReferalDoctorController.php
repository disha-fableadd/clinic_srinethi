<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReferalDoctor;
use Illuminate\Http\Request;

class ReferalDoctorController extends Controller
{
    // GET /api/referal_doctors?branch_id=1
    public function index(Request $request)
    {
        $branchId = $request->query('branch_id');

        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Branch ID is required.'
            ], 400);
        }

        $query = ReferalDoctor::where('branch_id', $branchId);

        $mapDoctor = function ($doctor) {
            return [
                'id' => $doctor->id,
                'doctor_name' => $doctor->doctor_name,
                'specialist' => $doctor->specialist,
                'email' => $doctor->email,
                'phone_number' => $doctor->phone_number,
                'branch_id' => $doctor->branch_id,
                'created_at' => $doctor->created_at ? $doctor->created_at->toDateTimeString() : null,
            ];
        };

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('doctor_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('specialist', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone_number', 'like', '%' . $searchValue . '%');
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

            $allowedOrderColumns = ['id', 'doctor_name', 'specialist', 'email', 'phone_number', 'created_at', 'updated_at'];
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

            $doctors = $filteredQuery->forPage($page, $length)->get()->map($mapDoctor);

            return response()->json([
                'success' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $doctors,
            ], 200);
        }

        if ($request->has('page') || $request->has('per_page')) {
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);
            $page = $page > 0 ? $page : 1;
            $perPage = $perPage > 0 ? $perPage : 10;

            $searchValue = $request->input('search');
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('doctor_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('specialist', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone_number', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $doctors = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get()->map($mapDoctor);
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'success' => true,
                'data' => $doctors,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $doctors = $query->orderBy('id', 'desc')->get()->map($mapDoctor);

        return response()->json([
            'success' => true,
            'data' => $doctors
        ], 200);
    }

    // POST /api/referal_doctors
    public function store(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required|string|max:255',
            'specialist' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:referal_doctors,email',
            'phone_number' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $doctor = ReferalDoctor::create([
            'doctor_name' => $request->doctor_name,
            'specialist' => $request->specialist,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'branch_id' => $request->branch_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Referral doctor created successfully',
            'data' => $doctor
        ], 201);
    }

    

    public function show($id)
    {
        $doctor = ReferalDoctor::find($id);
        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        return response()->json([
            'id' => $doctor->id,
            'doctor_name' => $doctor->doctor_name,
            'specialization' => $doctor->specialist, // renamed
            'phone' => $doctor->phone_number,        // renamed
            'email' => $doctor->email,

        ]);
    }
    // PUT /api/referal_doctors/{id}
    public function update(Request $request, ReferalDoctor $referalDoctor)
    {
        $request->validate([
            'doctor_name' => 'nullable|string|max:255',
            'specialist' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:referal_doctors,email,' . $referalDoctor->id,
            'phone_number' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $referalDoctor->update([
            'doctor_name' => $request->doctor_name,
            'specialist' => $request->specialist,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'branch_id' => $request->branch_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Referral doctor updated successfully',
            'data' => $referalDoctor
        ], 200);
    }

    // DELETE /api/referal_doctors/{id}
    public function destroy(ReferalDoctor $referalDoctor)
    {
        $referalDoctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Referral doctor deleted successfully'
        ], 200);
    }
}
