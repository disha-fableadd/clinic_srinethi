<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientDischargeDets;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

use Exception;

class PatientDischargeDetailController extends Controller
{
    /**
     * Get latest discharge for a patient (for invoice create when type = Discharge).
     */
    public function getByPatient(Request $request)
    {
        $patientId = $request->query('patient_id');
        $branchId = $request->query('branch_id');

        if (!$patientId) {
            return response()->json(['status' => false, 'message' => 'patient_id is required'], 422);
        }

        $query = PatientDischargeDets::where('patient_id', $patientId)->orderBy('discharge_date', 'desc');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        $discharge = $query->first();

        if (!$discharge) {
            return response()->json([
                'status' => true,
                'discharge' => null,
                'message' => 'No discharge found for this patient.',
            ], 200);
        }

        return response()->json([
            'status' => true,
            'discharge' => [
                'id' => $discharge->id,
                'total_bill' => $discharge->total_bill,
                'amount_paid' => $discharge->amount_paid,
                'gst_option' => $discharge->gst_option ?? 'Without GST',
                'product_gst' => $discharge->product_gst,
                'discharge_date' => $discharge->discharge_date,
            ],
        ], 200);
    }

    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $records = PatientDischargeDets::with(['patient', 'ipd', 'ipd.doctor'])
            ->orderBy('discharge_date', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $records->where('branch_id', $branchId);
        }

        $records = $records->get();

        if ($records->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No discharge records found to export.'
            ]);
        }

        $filename = 'patient_discharges_export_' . now()->format('Ymd_His') . '.csv';
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
            'Room Number',
            'Bed Number',
            'Discharge Date',
            'Total Bill',
            'Amount Paid',
            'Payment Status',
            'Discharge Note',
            'GST Option',
            'Product GST',
            'Created At'
        ]);

        $sr = 1;
        foreach ($records as $row) {
            $doctorName = $row->ipd?->doctor?->fullname ?? 'N/A';
            $room = $row->ipd->room_number ?? 'N/A';
            $bed = $row->ipd->bed_number ?? 'N/A';

            fputcsv($file, [
                $sr++,
                $row->patient->fullname ?? '',
                $doctorName,
                $room,
                $bed,
                $row->discharge_date,
                $row->total_bill,
                $row->amount_paid,
                $row->payment_status,
                $row->discharge_note,
                $row->gst_option,
                $row->product_gst ? json_encode($row->product_gst) : '',
                $row->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Discharge records exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }






    // public function index(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'branch_id' => 'nullable|integer|exists:branches,id',
    //         'user_id' => 'nullable|integer',
    //         'page' => 'nullable|integer|min:1',
    //         'per_page' => 'nullable|integer|min:1|max:100',
    //         'search' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $branchId = $request->input('branch_id');
    //     $userId = $request->input('user_id');
    //     $page = (int) $request->input('page', 1);
    //     $perPage = (int) $request->input('per_page', 10);
    //     $searchValue = $request->input('search');

    //     $query = PatientDischargeDets::with(['patient', 'doctor', 'treatment'])
    //         ->orderBy('id', 'desc');

    //     // ✅ filter by branch
    //     if ($branchId) {
    //         $query->where('branch_id', $branchId);
    //     }

    //     // ✅ filter by user_id
    //     if ($userId) {
    //         $query->where('user_id', $userId);
    //     }

    //     $recordsTotal = $query->count();

    //     // Search functionality
    //     if ($searchValue) {
    //         $query->where(function ($q) use ($searchValue) {
    //             $q->whereHas('patient', function ($pq) use ($searchValue) {
    //                 $pq->where('fullname', 'like', "%{$searchValue}%");
    //             })->orWhere('discharge_date', 'like', "%{$searchValue}%")
    //               ->orWhere('payment_status', 'like', "%{$searchValue}%");
    //         });
    //     }

    //     $recordsFiltered = $query->count();
    //     $dischargeDetails = $query->paginate($perPage, ['*'], 'page', $page);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Discharge details fetched successfully',
    //         'dischargeDetails' => $dischargeDetails->items(),
    //         'pagination' => [
    //             'current_page' => $dischargeDetails->currentPage(),
    //             'last_page'    => $dischargeDetails->lastPage(),
    //             'per_page'     => $dischargeDetails->perPage(),
    //             'total'        => $dischargeDetails->total(),
    //         ]
    //     ], 200);
    // }
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'nullable|integer|exists:branches,id',
            'user_id' => 'nullable|integer',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $branchId = $request->input('branch_id');
        $userId = $request->input('user_id');
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);
        $searchValue = $request->input('search');

        $query = PatientDischargeDets::with(['patient', 'doctor', 'treatment'])
            ->orderBy('id', 'desc');

        // Filter by branch
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Filter by user_id
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $recordsTotal = $query->count();

        // Search functionality
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('patient', function ($pq) use ($searchValue) {
                    $pq->where('fullname', 'like', "%{$searchValue}%");
                })->orWhere('discharge_date', 'like', "%{$searchValue}%")
                    ->orWhere('payment_status', 'like', "%{$searchValue}%");
            });
        }

        $recordsFiltered = $query->count();

        // Apply pagination after counting filtered results
        $dischargeDetails = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Discharge details fetched successfully',
            // 'draw' => (int) $request->input('draw', 1),
            // 'recordsTotal' => $recordsTotal,
            // 'recordsFiltered' => $recordsFiltered,
            'dischargeDetails' => $dischargeDetails->items(),
            'pagination' => [
                'current_page' => $dischargeDetails->currentPage(),
                'last_page'    => $dischargeDetails->lastPage(),
                'per_page'     => $dischargeDetails->perPage(),
                'total'        => $dischargeDetails->total(),
            ]
        ], 200);
    }


    public function store(Request $request)
    {
        $loggedInUserId = auth()->id();

        $validator = Validator::make($request->all(), [
            'patient_id'     => 'required|exists:patients,id',
            'discharge_date' => 'required|date',
            'total_bill'     => 'required|numeric',
            'amount_paid'    => 'required|numeric',
            'payment_status' => 'required|in:paid,unpaid',
            'discharge_note' => 'nullable|string',
            'branch_id'      => 'required|exists:branches,id', // ✅ ensure branch exists
            'gst_option'     => 'required|string',
            'product_gst'    => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        // ✅ Get latest IPD admission for this patient
        $ipdAdmission = \App\Models\IpdAdmission::where('patient_id', $request->patient_id)
            ->latest()
            ->first();

        if (!$ipdAdmission) {
            return response()->json([
                'message' => 'No IPD admission found for the selected patient.'
            ], 404);
        }

        // ✅ Store discharge data with branch_id
        $dischargeDetail = \App\Models\PatientDischargeDets::create([
            'patient_id'     => $request->patient_id,
            'ipd_id'         => $ipdAdmission->id,
            'discharge_date' => $request->discharge_date,
            'total_bill'     => $request->total_bill,
            'amount_paid'    => $request->amount_paid,
            'gst_option'     => $request->gst_option,
            'product_gst'    => $request->product_gst,
            'payment_status' => $request->payment_status,
            'discharge_note' => $request->discharge_note,
            'user_id'        => $loggedInUserId,
            'branch_id'      => $request->branch_id, // ✅ store branch
        ]);

        return response()->json([
            'message' => 'Patient discharge details created successfully',
            'patient_discharge_detail' => $dischargeDetail
        ], 200);
    }



    public function show($id)
    {
        try {
            $discharge = PatientDischargeDets::with([
                'patient',
                'ipd.doctor',
                'ipd.treatment'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $discharge
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'discharge not found'], 401);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'discharge went wrong'], 401);
        }
    }



    public function update(Request $request, $id)
    {
        $dischargeDetail = PatientDischargeDets::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'discharge_date' => 'required|date',
            'total_bill' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'gst_option' => 'required|string',
            'product_gst' => 'nullable|array',
            'payment_status' => 'required|in:paid,unpaid',
            'discharge_note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $dischargeDetail->update([
            'patient_id' => $request->patient_id,
            'discharge_date' => $request->discharge_date,
            'total_bill' => $request->total_bill,
            'amount_paid' => $request->amount_paid,
            'gst_option' => $request->gst_option,
            'product_gst' => $request->product_gst,
            'payment_status' => $request->payment_status,
            'discharge_note' => $request->discharge_note,
        ]);

        return response()->json([
            'message' => 'Patient discharge details updated successfully',
            'patient_discharge_detail' => $dischargeDetail
        ], 200);
    }

    /**
     * Delete a patient discharge detail.
     */
    public function destroy($id)
    {
        $dischargeDetail = PatientDischargeDets::findOrFail($id);
        $dischargeDetail->delete();

        return response()->json(['message' => 'Patient discharge detail deleted successfully'], 200);
    }
}
