<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RadiologyTest;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class RadiologyTestController extends Controller
{


  public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $tests = RadiologyTest::orderBy('test_name');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $tests->where('branch_id', $branchId);
    }

    $tests = $tests->get();

    if ($tests->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No radiology tests found to export.'
        ]);
    }

    $filename = 'radiology_tests_export_' . now()->format('Ymd_His') . '.csv';
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
        'Test Name',
        'Test Code',
        'Body Part',
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
            $test->body_part,
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
        'message' => 'Radiology tests exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}



  
    public function index(Request $request)
    {
        $query = RadiologyTest::query();

        // ✅ filter if branch_id is provided in request
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
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
                        ->orWhere('body_part', 'like', '%' . $searchValue . '%')
                        ->orWhere('cost', 'like', '%' . $searchValue . '%')
                        ->orWhere('gst_option', 'like', '%' . $searchValue . '%')
                        ->orWhere('report_format', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && $request->input('length') == -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $radiologyTests = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Radiology tests fetched successfully',
                'radiology' => $radiologyTests,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $radiologyTests = $query->get();

        return response()->json([
            "radiology" => $radiologyTests
        ]);
    }



    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'test_name'     => 'required|string|unique:radiology_tests,test_name',
        'test_code'     => 'required|string|unique:radiology_tests,test_code',
        'body_part'     => 'required|string',
        'cost'          => 'required|numeric',
        'gst_option'    => 'nullable|string',
        'product_gst'   => 'nullable|array',
        'report_format' => 'nullable|string|in:pdf,jpg,jpeg,png,doc,docx,dcm,webp,tiff,bmp,avi,mp4,zip',
        'branch_id'     => 'required|exists:branches,id', // ✅ validate branch
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // ✅ Detailed GST calculation
    if (isset($data['gst_option'])) {
        $gstOption = str_replace(' ', '_', strtolower($data['gst_option']));
        if ($gstOption === 'with_gst' && !empty($data['product_gst'])) {
            $gstDetails = [];
            $taxItems = (array)$data['product_gst'];
            
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
                    $taxAmount = ($data['cost'] * $tax->tax_rate) / 100;
                    $gstDetails[] = [
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

    // ✅ Set default format if not provided
    $data['report_format'] = $data['report_format'] ?? 'dcm';

    $test = RadiologyTest::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Radiology test created successfully.',
        'data' => $test
    ]);
}


    public function show($id)
    {
        $test = RadiologyTest::find($id);

        return response()->json($test);
    }


    public function update(Request $request, $id)
    {
        $test = RadiologyTest::find($id);
        if (!$test) {
            return response()->json(['message' => 'Radiology test not found'], 404);
        }

        $validated = $request->validate([
            'test_name'     => 'sometimes|required|string',
            'test_code'     => 'sometimes|required|string',
            'body_part'     => 'sometimes|required|string',
            'cost'          => 'sometimes|required|numeric',
            'gst_option'    => 'nullable|string',
            'product_gst'   => 'nullable|array',
            'report_format' => 'nullable|string|in:pdf,jpg,jpeg,png,doc,docx,dcm,webp,tiff,bmp,avi,mp4,zip',
        ]);

        $data = $validated;

        // ✅ Detailed GST calculation
        if (isset($data['gst_option'])) {
            $gstDetails = [];
            $gstOption = str_replace(' ', '_', strtolower($data['gst_option']));
            if ($gstOption === 'with_gst' && !empty($data['product_gst'])) {
                $taxItems = (array)$data['product_gst'];
                $cost = $data['cost'] ?? $test->cost;
                
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

        // No file upload logic anymore
        $test->update($data);

        return response()->json([
            'message' => 'Radiology test updated successfully',
            'data' => $test
        ]);
    }

    public function destroy($id)
    {
        $test = RadiologyTest::find($id);

        if (!$test) {
            return response()->json(['message' => 'Radiology test not found'], 404);
        }

        // Check if the test is used in any radiology reports
        $isUsed = \App\Models\RadiologyReport::where('test_id', $id)->exists();

        if ($isUsed) {
            return response()->json([
                'message' => 'Cannot delete test. This radiology test is already associated with one or more reports.'
            ], 400);
        }

        $test->delete();

        return response()->json(['message' => 'Radiology test deleted successfully']);
    }
}
