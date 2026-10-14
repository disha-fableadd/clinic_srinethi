<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicineUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MedicineUnitController extends Controller
{

public function export(Request $request)
{
    $branch_id = $request->query('branch_id') ?? session('branch_id');

    $query = MedicineUnit::query();

    if ($branch_id) {
        $query->where('branch_id', $branch_id);
    }

    $units = $query->orderBy('created_at', 'desc')->get();

    if ($units->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No medicine units found to export.'
        ]);
    }

    $filename = 'medicine_units_export_' . now()->format('Ymd_His') . '.csv';
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
        'Unit',
        'Branch ID',
        'Created At'
    ]);

    $sr = 1;

    foreach ($units as $unit) {
        fputcsv($file, [
            $sr++,
            $unit->unit,
            $unit->branch_id,
            $unit->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Medicine units exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}

    public function index(Request $request)
    {
        $branch_id = $request->query('branch_id') ?? session('branch_id');
        $query = MedicineUnit::query();

        if ($branch_id) {
            $query->where('branch_id', $branch_id);
        }

        $units = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $units
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (!isset($data['branch_id'])) {
            $data['branch_id'] = session('branch_id');
        }

        $validator = Validator::make($data, [
            'unit' => 'required|string|max:255',
            'branch_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $unit = MedicineUnit::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Medicine unit created successfully',
            'data' => $unit
        ]);
    }

    public function show($id)
    {
        $unit = MedicineUnit::find($id);

        if (!$unit) {
            return response()->json([
                'status' => false,
                'message' => 'Medicine unit not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $unit
        ]);
    }

    public function update(Request $request, $id)
    {
        $unit = MedicineUnit::find($id);

        if (!$unit) {
            return response()->json([
                'status' => false,
                'message' => 'Medicine unit not found'
            ], 404);
        }

        $data = $request->all();
        if (!isset($data['branch_id'])) {
            $data['branch_id'] = session('branch_id');
        }

        $validator = Validator::make($data, [
            'unit' => 'required|string|max:255',
            'branch_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $unit->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Medicine unit updated successfully',
            'data' => $unit
        ]);
    }

    public function destroy($id)
    {
        $unit = MedicineUnit::find($id);

        if (!$unit) {
            return response()->json([
                'status' => false,
                'message' => 'Medicine unit not found'
            ], 404);
        }

        $unit->delete();

        return response()->json([
            'status' => true,
            'message' => 'Medicine unit deleted successfully'
        ]);
    }
}
