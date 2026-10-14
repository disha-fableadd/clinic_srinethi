<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\OtProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OtProcedureController extends Controller
{

    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $procedures = OtProcedure::with(['patient', 'doctor'])
            ->orderBy('procedure_date', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $procedures->where('branch_id', $branchId);
        }

        $procedures = $procedures->get();

        if ($procedures->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No OT procedures found to export.'
            ]);
        }

        $filename = 'ot_procedures_export_' . now()->format('Ymd_His') . '.csv';
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
            'Patient Name',
            'Doctor Name',
            'Procedure Name',
            'Procedure Date',
            'Operation Notes',
            'Status',
            'Created At'
        ]);

        $sr = 1;
        foreach ($procedures as $row) {
            fputcsv($file, [
                $sr++,
                $row->patient->fullname ?? '',
                $row->doctor->fullname ?? '',
                $row->procedure_name ?? '',
                $row->procedure_date ? $row->procedure_date->format('Y-m-d') : '',
                $row->operation_notes ?? '',
                $row->status ?? '',
                $row->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'OT procedures exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }



  


    public function index(Request $request)
    {
        $user = auth()->user();

        $query = OtProcedure::with(['patient', 'doctor', 'branch']);

        // ✅ Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // ✅ Filter if doctor
        if ($user->role->name === 'Doctor') {
            $query->where('doctor_id', $user->id);
        }

        $mapProcedure = function ($proc) {
            return [
                'id' => $proc->id,
                'patient_name' => $proc->patient->fullname ?? 'N/A',
                'patient_image' => $proc->patient->profile ?? null,
                'doctor_name' => $proc->doctor->fullname ?? 'N/A',
                'procedure_name' => $proc->procedure_name ?? 'N/A',
                'procedure_date' => optional($proc->procedure_date)->format('d-m-Y'),
                'status' => $proc->status ?? 'scheduled',
            ];
        };

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('procedure_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('procedure_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
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

            $allowedOrderColumns = ['id', 'procedure_name', 'procedure_date', 'status', 'created_at', 'updated_at'];
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

            $procedures = $filteredQuery->forPage($page, $length)->get()->map($mapProcedure);

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $procedures
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
                    $q->where('procedure_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('procedure_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $procedures = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get()->map($mapProcedure);
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'data' => $procedures,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $procedures = $query->orderBy('id', 'desc')->get()->map($mapProcedure);

        return response()->json([
            'status' => true,
            'data' => $procedures
        ], 200);
    }





    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:user,id',


            'procedure_name' => 'required|string|max:255',
            'procedure_date' => 'required|date',
            'operation_notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed',
            'branch_id' => 'required|exists:branches,id', // ✅ validate branch
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $procedure = OtProcedure::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'procedure_name' => $request->procedure_name,
            'procedure_date' => $request->procedure_date,
            'operation_notes' => $request->operation_notes,
            'status' => $request->status ?? 'scheduled',
            'branch_id' => $request->branch_id, // ✅ store branch from request
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OT Procedure created successfully.',
            'data' => $procedure,
        ], 201);
    }



    public function destroy($id)
    {
        $oTProcedure = OtProcedure::find($id);

        if (!$oTProcedure) {
            return response()->json(['status' => false, 'message' => 'OT Procedure not found.'], 404);
        }

        $oTProcedure->delete();

        return response()->json(['status' => true, 'message' => 'OT Procedure deleted successfully.']);
    }
    public function show($id)
    {
        $oTProcedure = OtProcedure::with(['patient', 'doctor']) // if you have relationships
            ->where('id', $id)
            ->first();

        if (!$oTProcedure) {
            return response()->json(['status' => false, 'message' => 'OT not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $oTProcedure]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:user,id',
            'procedure_name' => 'required|string|max:255',
            'procedure_date' => 'required|date',
            'operation_notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed',
        ]);

        $procedure = OtProcedure::find($id);
        if (!$procedure) {
            return response()->json(['status' => false, 'message' => 'OT Procedure not found.'], 404);
        }

        $procedure->update($validated);

        return response()->json(['status' => true, 'message' => 'OT Procedure updated successfully.', 'data' => $procedure,]);
    }
}
