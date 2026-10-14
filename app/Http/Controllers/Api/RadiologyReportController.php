<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RadiologyReport;
use App\Models\Patients;
use App\Models\RadiologyTest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RadiologyReportController extends Controller
{


      public function exportCsv()
    {
        $reports = RadiologyReport::with(['patient', 'test'])->orderBy('report_date', 'desc')->get();

        if ($reports->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No radiology reports found to export.'
            ]);
        }

        $filename = 'radiology_reports_export_' . now()->format('Ymd_His') . '.csv';
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
            'Test Name',
            'Report Date',
            'Report File URL',
            'Converted Image URL',
            'Created At'
        ]);
        $sr = 1;
        foreach ($reports as $report) {
            fputcsv($file, [
               $sr++,
                $report->patient->fullname ?? '',
                $report->test->test_name ?? '',
                $report->report_date,
                $report->report_file ? asset(env('IMAGE_PATH') . $report->report_file) : '',
                $report->converted_image ? asset(env('IMAGE_PATH') . $report->converted_image) : '',
                $report->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Radiology reports exported successfully.',
            'file_url' => url($folder . $filename),
            'file_name' => $filename
        ]);
    }
    public function index(Request $request)
    {
        $query = RadiologyReport::with(['patient', 'test']);

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
                    $q->where('report_date', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('test', function ($t) use ($searchValue) {
                            $t->where('test_name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && $request->input('length') == -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $reports = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Radiology reports fetched successfully',
                'radiology_report' => $reports,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $reports = RadiologyReport::with(['patient', 'test'])->get();
        return response()->json(["radiology_report" => $reports]);
    }



   public function store(Request $request)
{
    try {
        // Step 1: Validate basic fields
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'test_id' => 'required|exists:radiology_tests,id',
            'report_date' => 'required|date|before_or_equal:today',
            'report_file' => 'required|file',
        ], [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'test_id.required' => 'Please select a test.',
            'test_id.exists' => 'The selected test does not exist.',
            'report_date.required' => 'Please provide the report date.',
            'report_date.date' => 'Report date must be a valid date.',
            'report_date.before_or_equal' => 'Report date cannot be in the future.',
            'report_file.required' => 'Please upload a report file.',
            'report_file.file' => 'The uploaded file must be valid.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 2: Get test format
        $test = \App\Models\RadiologyTest::find($request->test_id);

        if (!$test || empty($test->report_format)) {
            return response()->json(['error' => 'Invalid or missing report format for this test.'], 422);
        }

        $format = strtolower(trim($test->report_format));

        // Supported MIME types map
        $mimeTypesMap = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'tiff' => 'image/tiff',
            'bmp' => 'image/bmp',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'avi' => 'video/x-msvideo',
            'mp4' => 'video/mp4',
            'zip' => 'application/zip',
            'dcm' => 'application/dicom',
        ];

        // Build allowed types
        if ($format === 'dcm') {
            $allowedMimes = ['application/dicom', 'application/octet-stream'];
            $allowedExtensions = ['dcm'];
        } else {
            $allowedMimes = [$mimeTypesMap[$format] ?? ''];
            $allowedExtensions = [$format];
        }

        // Step 3: Validate the file type strictly
        $request->validate([
            'report_file' => [
                'required',
                'file',
                'max:5120', // 5 MB
                function ($attribute, $value, $fail) use ($allowedMimes, $allowedExtensions) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $mime = $value->getMimeType();

                    if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimes)) {
                        $fail("The uploaded file must be of type: " . implode(', ', $allowedExtensions));
                    }
                }
            ]
        ]);

        // Step 4: Handle file upload
        $filePath = null;
        $jpgPath = null;

        if ($request->hasFile('report_file')) {
            $file = $request->file('report_file');
            $originalExt = strtolower($file->getClientOriginalExtension());

            if (in_array($file->getMimeType(), ['application/dicom', 'application/octet-stream'])) {
                $originalExt = 'dcm';
            }

            $uniqueName = uniqid('report_') . '_' . time();
            $fileName = $uniqueName . '.' . $originalExt;

            $uploadPath = public_path('uploads/patient_radiology_reports');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $fileName);
            $filePath = 'uploads/patient_radiology_reports/' . $fileName;

            // Step 5: Convert DICOM to JPG
            if ($originalExt === 'dcm') {
                $jpgName = $uniqueName . '.jpg';
                $jpgFullPath = $uploadPath . '/' . $jpgName;

                $cmd = 'dcm2img +oj ' . escapeshellarg($uploadPath . '/' . $fileName) . ' ' . escapeshellarg($jpgFullPath);
                exec($cmd, $output, $returnCode);

                Log::info('DCM Convert CMD: ' . $cmd);
                Log::info('DCM CMD Output: ' . implode("\n", $output));
                Log::info('DCM CMD Return: ' . $returnCode);

                if ($returnCode !== 0 || !file_exists($jpgFullPath)) {
                    return response()->json(['error' => 'Failed to convert DICOM to JPG.'], 500);
                }

                $jpgPath = 'uploads/patient_radiology_reports/' . $jpgName;
            } else {
                $jpgPath = $filePath;
            }
        }

        // Step 6: Save to database
        $report = RadiologyReport::create([
            'patient_id' => $request['patient_id'],
            'test_id' => $request['test_id'],
            'report_date' => $request['report_date'],
            'report_file' => $filePath,
            'converted_image' => $jpgPath,
        ]);

        return response()->json([
             'status' => 'success',
            'message' => 'Radiology report uploaded and converted successfully!',
            'data' => $report,
        ], 200);

    } catch (\Exception $e) {
        Log::error('RadiologyReport Store Error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}





    public function show($id)
    {
        $report = RadiologyReport::with(['patient', 'test'])->find($id);

        return response()->json($report);
    }
    public function destroy($id)
    {
        $test = RadiologyReport::find($id);
        if (!$test) {
            return response()->json(['message' => 'Radiology report not found'], 404);
        }
        $test->delete();
        return response()->json(['message' => 'Radiology report deleted successfully']);
    }


    
    public function update(Request $request)
    {
        try {
          



            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'test_id' => 'required|exists:radiology_tests,id',
                'report_date' => 'required|date|before_or_equal:today',
                'report_file' => 'file',
            ], [
                'patient_id.required' => 'Please select a patient.',
                'patient_id.exists' => 'The selected patient does not exist.',
                'test_id.required' => 'Please select a test.',
                'test_id.exists' => 'The selected test does not exist.',
                'report_date.required' => 'Please provide the report date.',
                'report_date.date' => 'Report date must be a valid date.',
                'report_date.before_or_equal' => 'Report date cannot be in the future.',
                'report_file.file' => 'The uploaded file must be valid.',
                'report_file.mimes' => 'The report file must be a PDF, JPG, JPEG, or PNG.',
                'report_file.max' => 'The report file must not exceed 2MB.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Step 2: Get report format
            $test = \App\Models\RadiologyTest::find($request->test_id);

            if (!$test || empty($test->report_format)) {
                return response()->json(['error' => 'Invalid or missing report format for this test.'], 422);
            }

            $format = strtolower($test->report_format);

            $mimeTypesMap = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'tiff' => 'image/tiff',
                'bmp' => 'image/bmp',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'avi' => 'video/x-msvideo',
                'mp4' => 'video/mp4',
                'zip' => 'application/zip',
                'dcm' => 'application/dicom',
            ];

            $mime = $mimeTypesMap[$format] ?? null;

            if ($format === 'dcm') {
                $allowedMimes = ['application/dicom', 'application/octet-stream'];
                $allowedExtensions = ['dcm'];
            } else {
                $allowedMimes = [$mime];
                $allowedExtensions = [$format];
            }

            // Step 3: Validate file if present
            if ($request->hasFile('report_file')) {
                $request->validate([
                    'report_file' => [
                        'file',
                        'max:5120',
                        function ($attribute, $value, $fail) use ($allowedMimes, $allowedExtensions) {
                            $ext = strtolower($value->getClientOriginalExtension());
                            $mime = $value->getMimeType();

                            if (!in_array($mime, $allowedMimes) || !in_array($ext, $allowedExtensions)) {
                                $fail("The file must be a valid " . implode(', ', $allowedExtensions) . " file.");
                            }
                        },
                    ],
                ]);
            }

            // Step 4: Find the report
            $report = RadiologyReport::findOrFail($request['id']);

            $filePath = $report->report_file;
            $jpgPath = $report->converted_image;

            // Step 5: Handle new file upload
            if ($request->hasFile('report_file')) {
                // Delete old files
                if ($report->report_file && file_exists(public_path($report->report_file))) {
                    unlink(public_path($report->report_file));
                }
                if ($report->converted_image && file_exists(public_path($report->converted_image))) {
                    unlink(public_path($report->converted_image));
                }

                $file = $request->file('report_file');
                $originalExt = strtolower($file->getClientOriginalExtension());

                if (in_array($file->getMimeType(), ['application/dicom', 'application/octet-stream'])) {
                    $originalExt = 'dcm';
                }

                $uniqueName = uniqid('report_') . '_' . time();
                $fileName = $uniqueName . '.' . $originalExt;

                $uploadPath = public_path('uploads/patient_radiology_reports');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $fileName);
                $filePath = 'uploads/patient_radiology_reports/' . $fileName;

                if ($originalExt === 'dcm') {
                    $jpgName = $uniqueName . '.jpg';
                    $jpgFullPath = $uploadPath . '/' . $jpgName;

                    $cmd = 'dcm2img +oj ' . escapeshellarg($uploadPath . '/' . $fileName) . ' ' . escapeshellarg($jpgFullPath);
                    exec($cmd, $output, $returnCode);

                    Log::info('DCM Convert CMD: ' . $cmd);
                    Log::info('DCM CMD Output: ' . implode("\n", $output));
                    Log::info('DCM CMD Return: ' . $returnCode);

                    if ($returnCode !== 0 || !file_exists($jpgFullPath)) {
                        return response()->json(['error' => 'Failed to convert DICOM to JPG.'], 500);
                    }

                    $jpgPath = 'uploads/patient_radiology_reports/' . $jpgName;
                } else {
                    // For non-DICOM (e.g., jpg, png), use the same path
                    $jpgPath = $filePath;
                }

            }

            // Step 6: Update database
            $report->patient_id = $request['patient_id'];
            $report->test_id = $request['test_id'];
            $report->report_date = $request['report_date'];
            $report->report_file = $filePath;
            $report->converted_image = $jpgPath;
            $report->save();

            return response()->json([
                 'status' => 'success',
                'message' => 'Radiology report updated successfully!',
                'data' => $report
            ],200);

        } catch (\Exception $e) {
            Log::error('RadiologyReport Update Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




}
