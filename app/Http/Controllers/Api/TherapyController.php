<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapy;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TherapyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // <-- This causes login redirect if unauthenticated
    }

    public function exportTherapyCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $therapies = Therapy::orderBy('created_at', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $therapies->where('branch_id', $branchId);
        }

        $therapies = $therapies->get();

        if ($therapies->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No therapies found to export.'
            ]);
        }

        $filename = 'therapies_export_' . now()->format('Ymd_His') . '.csv';
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
            'Therapy Name',
            'Description',
            'Duration (Minutes)',
            'Cost',
            'GST Option',
            'Product GST',
            'Status',
            'Created At'
        ]);

        $sr = 1;
        foreach ($therapies as $therapy) {
            $productGstText = '';
            if ($therapy->gst_option === 'With GST' && is_array($therapy->product_gst)) {
                $parts = [];
                foreach ($therapy->product_gst as $gst) {
                    $rate = isset($gst['tax_rate']) ? number_format((float)$gst['tax_rate'], 2, '.', '') : '0.00';
                    $amount = isset($gst['tax_amount']) ? number_format((float)$gst['tax_amount'], 2, '.', '') : '0.00';
                    $name = $gst['tax_name'] ?? '';
                    $parts[] = "{$name} ({$rate}%) = {$amount}";
                }
                $productGstText = implode(', ', $parts);
            }
            fputcsv($file, [
                $sr++,
                $therapy->name,
                $therapy->description,
                $therapy->duration_minutes,
                $therapy->cost,
                $therapy->gst_option ?? 'Without GST',
                $productGstText,
                $therapy->status ? 'Active' : 'Inactive',
                $therapy->created_at ? $therapy->created_at->format('d-M-Y h:i A') : 'N/A',
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Therapies exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }


    public function api_index(Request $request)
    {
        $branchId = session('branch_id'); // ✅ get from session

        if (!$branchId) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not selected.'
            ], 400);
        }

        $therapies = Therapy::where('branch_id', $branchId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data' => $therapies
        ]);
    }


    public function index(Request $request)
    {
        // If no pagination/search params, keep legacy response (array)
        if (!$request->hasAny(['page', 'per_page', 'search'])) {
            $branchId = $request->get('branch_id'); // from frontend
            $therapies = Therapy::where('branch_id', $branchId)->get();
            return response()->json($therapies);
        }

        $validator = \Validator::make($request->all(), [
            'branch_id' => 'nullable|integer|exists:branches,id',
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
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);
        $searchValue = $request->input('search');

        $query = Therapy::query()->orderBy('id', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%")
                    ->orWhere('duration_minutes', 'like', "%{$searchValue}%")
                    ->orWhere('cost', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%");
            });
        }

        $therapies = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Therapies fetched successfully',
            'therapies' => $therapies->items(),
            'pagination' => [
                'current_page' => $therapies->currentPage(),
                'last_page'    => $therapies->lastPage(),
                'per_page'     => $therapies->perPage(),
                'total'        => $therapies->total(),
            ]
        ], 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer',
            'cost'             => 'required|numeric',
            'gst_option'       => 'required|in:With GST,Without GST',
            'product_gst'      => 'nullable|array|required_if:gst_option,With GST',
            'product_gst.*.tax_name'   => 'required_with:product_gst|string|max:255',
            'product_gst.*.tax_rate'   => 'required_with:product_gst|numeric',
            'product_gst.*.tax_amount' => 'required_with:product_gst|numeric',
            'status'           => 'required|in:active,inactive',
            'branch_id'        => 'required|exists:branches,id', // validate branch_id
        ]);
        
        $data = $validated;
        if ($data['gst_option'] !== 'With GST') {
            $data['product_gst'] = null;
        }

        $therapy = Therapy::create($data);

        return response()->json([
            'message' => 'Therapy created successfully',
            'data'    => $therapy
        ]);
    }


    public function show($id)
    {
        $therapy = Therapy::findOrFail($id);
        return response()->json($therapy);
    }

    public function update(Request $request, $id)
    {
        $therapy = Therapy::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer',
            'cost' => 'required|numeric',
            'gst_option'       => 'required|in:With GST,Without GST',
            'product_gst'      => 'nullable|array|required_if:gst_option,With GST',
            'product_gst.*.tax_name'   => 'required_with:product_gst|string|max:255',
            'product_gst.*.tax_rate'   => 'required_with:product_gst|numeric',
            'product_gst.*.tax_amount' => 'required_with:product_gst|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $validated;
        if ($data['gst_option'] !== 'With GST') {
            $data['product_gst'] = null;
        }

        $therapy->update($data);

        return response()->json(['message' => 'Therapy updated successfully', 'data' => $therapy]);
    }

    public function destroy($id)
    {
        $therapy = Therapy::findOrFail($id);
        $therapy->delete();

        return response()->json(['message' => 'Therapy deleted successfully']);
    }


    public function getPatientTherapies($patientId)
    {
        $therapies = DB::table('assigned_therapies')
            ->join('therapy', 'assigned_therapies.therapy_id', '=', 'therapy.id')
            ->where('assigned_therapies.patient_id', $patientId)
            ->select(
                'therapy.name',
                'therapy.description',
                'therapy.duration_minutes as duration',
                'therapy.cost',
                'assigned_therapies.status'
            )
            ->get();

        return response()->json($therapies);
    }
}
