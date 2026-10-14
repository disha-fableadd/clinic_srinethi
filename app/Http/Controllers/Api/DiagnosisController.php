<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DiagnosisController extends Controller
{
    
    public function index(Request $request)
    {
        $branchId = $request->input('branch_id');
        $page = $request->input('page');
        $perPage = (int) $request->input('per_page', 10);
        $searchValue = $request->input('search');

        $query = Diagnosis::with('branch');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%");
            });
        }

        $query->orderBy('id', 'desc');

        // Backward compatibility: If no page is provided, return all
        if (!$page) {
            $diagnoses = $query->get();
            return response()->json([
                'status' => true,
                'diagnoses' => $diagnoses
            ], 200);
        }

        // Paginated response for DataTables
        $diagnoses = $query->paginate($perPage, ['*'], 'page', (int)$page);

        return response()->json([
            'status' => true,
            'diagnoses' => $diagnoses->items(),
            'pagination' => [
                'current_page' => $diagnoses->currentPage(),
                'last_page'    => $diagnoses->lastPage(),
                'per_page'     => $diagnoses->perPage(),
                'total'        => $diagnoses->total(),
            ]
        ], 200);
    }



    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            'branch_id' => 'required|integer|exists:branches,id', // ✅ add branch validation
        ]);

        $diagnosis = Diagnosis::create([
            'name' => $request->name,
            'description' => $request->description,
            'branch_id' => $request->branch_id, // ✅ save branch
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Diagnosis created successfully',
            'diagnosis' => $diagnosis
        ], 201);
    }


    // 🔹 Show single diagnosis
    public function show($id)
    {
        $diagnosis = Diagnosis::find($id);

        if (!$diagnosis) {
            return response()->json([
                'status' => false,
                'message' => 'Diagnosis not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'diagnosis' => $diagnosis
        ]);
    }

    public function update(Request $request, $id)
    {
        $diagnosis = Diagnosis::find($id);

        if (!$diagnosis) {
            return response()->json([
                'status' => false,
                'message' => 'Diagnosis not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            // 'description' => 'required|string',
        ]);

        $diagnosis->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Diagnosis updated successfully',
            'diagnosis' => $diagnosis
        ]);
    }


    // 🔹 Delete a diagnosis
    public function destroy($id)
    {
        $diagnosis = Diagnosis::find($id);

        if (!$diagnosis) {
            return response()->json([
                'status' => false,
                'message' => 'Diagnosis not found'
            ], 404);
        }

        $diagnosis->delete();

        return response()->json([
            'status' => true,
            'message' => 'Diagnosis deleted successfully'
        ], 200);
    }


    public function getDiagnoses(Request $request)
    {
        $user = Auth::user(); // get logged-in user

        $query = Diagnosis::query()->orderBy('id', 'desc');

        // Filter by branch_id if provided
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        $diagnoses = $query->get();

        return response()->json([
            'status' => true,
            'diagnoses' => $diagnoses
        ]);
    }


    public function exportCsv(Request $request)
    {
        $diagnoses = Diagnosis::with('branch')
            ->orderBy('name', 'desc')
            ->get();

        if ($diagnoses->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No diagnosis found to export.'
            ]);
        }

        $filename = 'diagnosis_export_' . now()->format('Ymd_His') . '.csv';
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
            'Diagnosis Name',
            'Description',
            'Branch Name',
            'Created At'
        ]);

        $sr = 1;
        foreach ($diagnoses as $diagnosis) {
            fputcsv($file, [
                $sr++,
                $diagnosis->name,
                $diagnosis->description,
                $diagnosis->branch->name ?? '',
                $diagnosis->created_at ?? '',
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Diagnosis exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }
}
