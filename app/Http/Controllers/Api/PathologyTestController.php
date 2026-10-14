<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PathologyTest;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PathologyTestController extends Controller
{




   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $tests = PathologyTest::orderBy('test_name', 'asc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $tests->where('branch_id', $branchId);
    }

    $tests = $tests->get();

    if ($tests->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No pathology tests found to export.'
        ]);
    }

    $filename = 'pathology_tests_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    // CSV header
    fputcsv($file, [
        'ID',
        'Test Name',
        'Test Code',
        'Sample Type',
        'Normal Range',
        'Cost',
        'GST Option',
        'Product GST',
        'Report Format',
        'Created At'
    ]);

    $sr = 1;
    foreach ($tests as $test) {
        fputcsv($file, [
            $sr++,
            $test->test_name,
            $test->test_code,
            $test->sample_type,
            $test->normal_range,
            $test->cost,
            $test->gst_option,
            json_encode($test->product_gst),
            $test->report_format,
            $test->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Pathology tests exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}



    public function index(Request $request)
    {
        // Validate that branch_id is present (optional)
        $request->validate([
            // 'branch_id' => 'required|exists:branches,id',
        ]);

        $branchId = $request->branch_id;
        $query = PathologyTest::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

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
                    $q->where('test_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('test_code', 'like', '%' . $searchValue . '%')
                        ->orWhere('sample_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('normal_range', 'like', '%' . $searchValue . '%')
                        ->orWhere('cost', 'like', '%' . $searchValue . '%')
                        ->orWhere('gst_option', 'like', '%' . $searchValue . '%')
                        ->orWhere('report_format', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && $request->input('length') == -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $tests = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Pathology tests fetched successfully',
                'pathology' => $tests,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $tests = $query->get();

        return response()->json($tests);
    }



    public function store(Request $request)
    {
        // Validate incoming request including branch_id
        $request->validate([
            'test_name'    => 'required|string',
            'test_code'    => 'required|string|unique:pathology_tests',
            'sample_type'  => 'required|string',
            'normal_range' => 'required|string',
            'cost'         => 'required|numeric',
            'gst_option'   => 'nullable|string',
            'product_gst'  => 'nullable|array',
            'report_format' => 'nullable|string',
            // 'branch_id'    => 'required|exists:branches,id', // Ensure branch exists
        ]);

        $data = $request->all();

        // Detailed GST calculation
        $gstDetails = [];
        $gstOption = str_replace(' ', '_', strtolower($request->gst_option));
        if ($gstOption === 'with_gst' && !empty($request->product_gst)) {
            $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
            
            foreach ($taxItems as $item) {
                $tax = null;
                if (is_numeric($item)) {
                    $tax = TaxRate::find($item);
                } else {
                    // Handle case where frontend sends "Name (Rate%)" string
                    if (preg_match('/^(.*?)\s*\((.*?)\s*%\)$/', $item, $matches)) {
                        $name = trim($matches[1]);
                        $rate = trim($matches[2]);
                        $tax = TaxRate::where('tax_name', $name)->where('tax_rate', $rate)->first();
                    }
                }

                if ($tax) {
                    $taxAmount = ($request->cost * $tax->tax_rate) / 100;
                    $gstDetails[] = [
                        // 'tax_id' => $tax->id,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => number_format($tax->tax_rate, 2, '.', ''),
                        'tax_amount' => (float)$taxAmount,
                    ];
                }
            }
            $data['product_gst'] = $gstDetails;
        } else {
            $data['product_gst'] = null;
        }

        // Create new pathology test including branch_id
        $test = PathologyTest::create($data);

        // Return JSON response
        return response()->json([
            'message' => 'Pathology test created successfully',
            'data'    => $test
        ]);
    }

    // Show a single test
    public function show($id)
    {
        $test = PathologyTest::findOrFail($id);
        return response()->json($test);
    }

    // Update a test
    public function update(Request $request, $id)
    {
        $test = PathologyTest::findOrFail($id);

        $request->validate([
            'test_name' => 'sometimes|required|string',
            'test_code' => 'sometimes|required|string|unique:pathology_tests,test_code,' . $id,
            'sample_type' => 'sometimes|required|string',
            'normal_range' => 'sometimes|required|string',
            'cost' => 'sometimes|required|numeric',
            'gst_option' => 'nullable|string',
            'product_gst' => 'nullable|array',
            'report_format' => 'nullable|string',
        ]);

        $data = $request->all();

        // Detailed GST calculation
        if ($request->has('gst_option')) {
            $gstDetails = [];
            $gstOption = strtolower($request->gst_option);
            if ($gstOption === 'with_gst' && !empty($request->product_gst)) {
                $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
                $cost = $request->cost ?? $test->cost;
                
                foreach ($taxItems as $item) {
                    $tax = null;
                    if (is_numeric($item)) {
                        $tax = TaxRate::find($item);
                    } else {
                        // Handle case where frontend sends "Name (Rate%)" string
                        if (preg_match('/^(.*?)\s*\((.*?)\s*%\)$/', $item, $matches)) {
                            $name = trim($matches[1]);
                            $rate = trim($matches[2]);
                            $tax = TaxRate::where('tax_name', $name)->where('tax_rate', $rate)->first();
                        }
                    }

                    if ($tax) {
                        $taxAmount = ($cost * $tax->tax_rate) / 100;
                        $gstDetails[] = [
                            'tax_id' => $tax->id,
                            'tax_name' => $tax->tax_name,
                            'tax_rate' => number_format($tax->tax_rate, 2, '.', ''),
                            'tax_amount' => (float)$taxAmount,
                        ];
                    }
                }
                $data['product_gst'] = $gstDetails;
            } else {
                $data['product_gst'] = null;
            }
        }

        $test->update($data);

        return response()->json([
            'message' => 'Pathology test updated successfully',
            'data' => $test
        ]);
    }

    // Delete a test
    public function destroy($id)
    {
        $test = PathologyTest::findOrFail($id);

        // Check if the test is used in any pathology reports
        $isUsed = \App\Models\PathologyReport::where('test_id', $id)->exists();

        if ($isUsed) {
            return response()->json([
                'message' => 'Cannot delete test. This test is already associated with one or more reports.'
            ], 400); // 400 Bad Request
        }

        $test->delete();

        return response()->json(['message' => 'Pathology test deleted successfully']);
    }
}
