<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Branch;

class CategoryController extends Controller
{




    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Get branch filter

        $categories = Categories::orderBy('name');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $categories->where('branch_id', $branchId);
        }

        $categories = $categories->get();

        if ($categories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No categories found to export.'
            ]);
        }

        $filename = 'categories_export_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        // CSV Header
        fputcsv($file, ['ID', 'Name', 'Description',  'Created At']);

        $sr = 1;
        foreach ($categories as $cat) {
            fputcsv($file, [
                $sr++,
                $cat->name,
                $cat->description,

                $cat->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Categories exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }


    public function index(Request $request)
    {
        $query = Categories::with('branch'); // eager load branch

        // Filter by branch_id if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $categories = $query->orderBy('id', 'desc')->get();

        // Map the response
        $categories = $categories->map(function ($category) {
            return [
                'id'          => $category->id,
                'name'        => $category->name,
                'description' => $category->description ?? 'N/A',
                'branch_id'   => $category->branch_id,
                'branch_name' => $category->branch->name ?? 'N/A',
                'created_at'  => $category->created_at->format('Y-m-d'), // format date
            ];
        });

        return response()->json([
            'status'     => true,
            'categories' => $categories,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id'   => 'required|exists:branches,id', // ✅ get branch_id from frontend/local storage
        ]);

        try {
            // ✅ Use branch_id from frontend
            $branchId = $request->branch_id;

            // Create category with branch_id
            $category = Categories::create([
                'name'        => $request->name,
                'description' => $request->description,
                'branch_id'   => $branchId, // ✅ from frontend/local storage
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'Category created successfully.',
                'category' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create category.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 401);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }



    public function show($id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 401);
        }
        return response()->json(['category' => $category], 200);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $category->update($request->only(['name', 'description']));
        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    }
}
