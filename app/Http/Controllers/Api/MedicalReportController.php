<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalReport;
use App\Models\Services;
use App\Models\Setting;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MedicalReportController extends Controller
{


    public function medicalReportPdf($id)
    {
        try {
            // Load medical report with patient
            $report = MedicalReport::with('patient')->findOrFail($id);

            // Fetch clinic details
            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
                'clinic_city',
                'clinic_state',
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo);

            // Data for Blade
            $data = [
                'date' => now()->format('d-m-Y'),
                'patient' => $report->patient,
                'report' => $report,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone' => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email' => $settings['clinic_email'] ?? 'clinic@example.com',
            ];

            $pdf = \Pdf::loadView('report.report_pdf', $data)
                ->setPaper('A4', 'portrait');

            return $pdf->download("MedicalReport_{$report->id}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Medical Report PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function patientReports($id)
    {
        $reports = MedicalReport::where('patient_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($reports);
    }



   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $reports = MedicalReport::with(['patient', 'doctor'])
        ->orderBy('created_at', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $reports->where('branch_id', $branchId);
    }

    $reports = $reports->get();

    if ($reports->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No medical reports found to export.'
        ]);
    }

    $filename = 'medical_reports_export_' . now()->format('Ymd_His') . '.csv';
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
        'Report Type',
        'Description',
        'File URL',
        'Report Date',
      
        'Created At'
    ]);

    $sr = 1;
    foreach ($reports as $report) {
        fputcsv($file, [
            $sr++,
            $report->patient->fullname ?? '',
            $report->doctor->fullname ?? '',
            ucfirst($report->report_type ?? ''),
            $report->description ?? '',
            $report->file_path, // assuming accessor returns full URL
            $report->date ?? '',
           
            $report->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Medical reports exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}


    // public function index()
    // {
    //     try {
    //         $user = Auth::user(); // get the authenticated user

    //         // Base query
    //         $query = MedicalReport::with(['patient'])->orderBy('id', 'desc');

    //         // If the user is a doctor, filter by their ID
    //         if ($user->role->name === 'Doctor') {
    //             $query->where('doctor_id', $user->id);
    //         }

    //         $reports = $query->get();




    //         return response()->json($reports, 200);
    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching medical reports: ' . $e->getMessage());
    //         return response()->json(['error' => 'Something went wrong!'], 401);
    //     }
    // }

    public function index(Request $request)
    {
        try {
            $user = Auth::user(); // authenticated user

            // Base query
            $query = MedicalReport::with(['patient', 'branch']);

            // Filter by doctor if role is Doctor
            if ($user->role->name === 'Doctor') {
                $query->where('doctor_id', $user->id);
            }

            // Filter by branch_id from request (sent from localStorage)
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

            // If DataTables server-side request
            if ($hasDataTable) {
                $searchValue = $request->input('search.value', $request->input('search'));
                $filteredQuery = clone $query;

                if (!empty($searchValue)) {
                    $filteredQuery->where(function ($q) use ($searchValue) {
                        $q->where('report_type', 'like', '%' . $searchValue . '%')
                            ->orWhere('date', 'like', '%' . $searchValue . '%')
                            ->orWhereHas('patient', function ($p) use ($searchValue) {
                                $p->where('fullname', 'like', '%' . $searchValue . '%');
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

                $allowedOrderColumns = ['id', 'report_type', 'date', 'created_at', 'updated_at'];
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

                $reports = $filteredQuery->forPage($page, $length)->get();

                $reports = $reports->map(function ($report) {
                    return [
                        'id'          => $report->id,
                        'patient'     => ['fullname' => $report->patient->fullname ?? 'N/A'],
                        'report_type' => $report->report_type ?? 'N/A',
                        'date'        => $report->date ?? 'N/A',
                        'file_path'   => $report->file_path ?? '',
                    ];
                });

                return response()->json([
                    'status' => true,
                    'draw' => (int) $request->input('draw'),
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $reports,
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
                        $q->where('report_type', 'like', '%' . $searchValue . '%')
                            ->orWhere('date', 'like', '%' . $searchValue . '%')
                            ->orWhereHas('patient', function ($p) use ($searchValue) {
                                $p->where('fullname', 'like', '%' . $searchValue . '%');
                            });
                    });
                }

                $recordsFiltered = (clone $filteredQuery)->count();
                $reports = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();

                $reports = $reports->map(function ($report) {
                    return [
                        'id'          => $report->id,
                        'patient'     => ['fullname' => $report->patient->fullname ?? 'N/A'],
                        'report_type' => $report->report_type ?? 'N/A',
                        'date'        => $report->date ?? 'N/A',
                        'file_path'   => $report->file_path ?? '',
                    ];
                });

                $lastPage = (int) ceil($recordsFiltered / $perPage);

                return response()->json([
                    'status' => true,
                    'data'   => $reports,
                    'pagination' => [
                        'current_page' => $page,
                        'last_page' => $lastPage > 0 ? $lastPage : 1,
                        'per_page' => $perPage,
                        'total' => $recordsFiltered,
                    ],
                ], 200);
            }

            // Default (existing) response for non-DataTables requests
            $reports = $query->orderBy('id', 'desc')->get();

            $reports = $reports->map(function ($report) {
                return [
                    'id'          => $report->id,
                    'patient'     => ['fullname' => $report->patient->fullname ?? 'N/A'], // object for JS
                    'report_type' => $report->report_type ?? 'N/A',
                    'date'        => $report->date ?? 'N/A',
                    'file_path'   => $report->file_path ?? '',
                ];
            });

            return response()->json([
                'status' => true,
                'data'   => $reports
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching medical reports: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'error'  => 'Something went wrong!'
            ], 500);
        }
    }





    // public function store(Request $request)
    // {
    //     $loggedInUserId = auth()->id();
    //     try {
    //         $request->validate([
    //             'patient_id' => 'required|exists:patients,id',
    //             // 'doctor_id' => 'required|exists:user,id',
    //             // 'service_id' => 'required|exists:services,id',
    //             // 'description' => 'required|string',
    //             'file_path' => 'required|file|mimes:pdf,jpg,png,doc,docx,webp,jfif|max:2048',
    //             'date' => 'required|date',
    //             'report_type' => 'required|string',

    //         ]);

    //         if ($request->hasFile('file_path')) {
    //             $file = $request->file('file_path');

    //             // Define the destination path in `public/uploads/report`
    //             $destinationPath = public_path('uploads/report');

    //             // Ensure the directory exists
    //             if (!File::exists($destinationPath)) {
    //                 File::makeDirectory($destinationPath, 0755, true);
    //             }

    //             // Generate a unique filename
    //             $filename = time() . '_' . $file->getClientOriginalName();

    //             // Move the uploaded file to the destination directory
    //             $file->move($destinationPath, $filename);

    //             // Store the relative path for database
    //             $imagePath = 'uploads/report/' . $filename;
    //         }

    //         // Create Medical Report
    //         $report = MedicalReport::create([
    //             'patient_id' => $request->patient_id,
    //             // 'doctor_id' => $request->doctor_id,
    //             // 'service_id' => $request->service_id,
    //             'description' => $request->description,
    //             'file_path' => $imagePath, // Store the file path
    //             'user_id' => $loggedInUserId,
    //             'date' => $request->date,
    //             'report_type' => $request->report_type,
    //             // 'type' => $request->type, // 'service' or 'treatment'
    //             // 'type_id' => $request->type_id, // ID from services or treatments

    //         ]);
    //         return response()->json($report, 200);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json(['error' => $e->errors()], 401);
    //     }
    // }


    // public function store(Request $request)
    // {
    //     $loggedInUserId = auth()->id();

    //     // ✅ Validate request
    //     $validator = Validator::make($request->all(), [
    //         'patient_id'  => 'required|exists:patients,id',
    //         'branch_id'   => 'required|exists:branches,id', // ✅ validate branch
    //         'description' => 'nullable|string',
    //         'file_path'   => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,webp,jfif|max:2048',
    //         'date'        => 'required|date',
    //         'report_type' => 'required|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $imagePath = null;

    //         // ✅ Handle file upload
    //         if ($request->hasFile('file_path')) {
    //             $file = $request->file('file_path');

    //             // Ensure upload directory exists
    //             $destinationPath = public_path('uploads/report');
    //             if (!File::exists($destinationPath)) {
    //                 File::makeDirectory($destinationPath, 0755, true);
    //             }

    //             // Generate unique filename
    //             $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    //             // Move file
    //             $file->move($destinationPath, $filename);

    //             // Store relative path
    //             $imagePath = 'uploads/report/' . $filename;
    //         }

    //         // ✅ Create Medical Report
    //         $report = MedicalReport::create([
    //             'patient_id'  => $request->patient_id,
    //             'branch_id'   => $request->branch_id, // ✅ store branch
    //             'description' => $request->description,
    //             'file_path'   => $imagePath,
    //             'user_id'     => $loggedInUserId,
    //             'date'        => $request->date,
    //             'report_type' => $request->report_type,
    //         ]);

    //         return response()->json([
    //             'message' => 'Medical report created successfully',
    //             'data'    => $report,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         \Log::error('Error creating medical report: ' . $e->getMessage());
    //         return response()->json(['error' => 'Something went wrong!'], 500);
    //     }
    // }


    public function store(Request $request)
    {
        $loggedInUserId = auth()->id();

        // ✅ Get branch_id from request (frontend sends localStorage value)
        $branchId = $request->branch_id;

        // ✅ Validate request
        $validator = Validator::make(array_merge($request->all(), ['branch_id' => $branchId]), [
            'patient_id'  => 'required|exists:patients,id',
            'branch_id'   => 'required|exists:branches,id', // branch comes from localStorage
            'description' => 'nullable|string',
            'file_path'   => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,webp,jfif|max:2048',
            'date'        => 'required|date',
            'report_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $imagePath = null;

            // ✅ Handle file upload
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');

                $destinationPath = public_path('uploads/report');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                $imagePath = 'uploads/report/' . $filename;
            }

            // ✅ Create Medical Report
            $report = MedicalReport::create([
                'patient_id'  => $request->patient_id,
                'branch_id'   => $branchId, // branch_id from localStorage
                'description' => $request->description,
                'file_path'   => $imagePath,
                'user_id'     => $loggedInUserId,
                'date'        => $request->date,
                'report_type' => $request->report_type,
            ]);

            return response()->json([
                'message' => 'Medical report created successfully',
                'data'    => $report,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating medical report: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }



    public function show($id)
    {
        try {
            $medicalReport = MedicalReport::with(['patient'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $medicalReport
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Medical report not found'], 401);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 401);
        }
    }







    public function update(Request $request, $id)
    {
        try {
            $loggedInUserId = auth()->id();

            // Validate incoming request data
            $validatedData = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'date' => 'required|date',
                'description' => 'required|string',
                'report_type' => 'required|string',

                'file_path' => 'sometimes|file|mimes:pdf,jpg,png,doc,docx,webp,|max:2048',
            ]);

            // Find the medical report by ID
            $medicalReport = MedicalReport::findOrFail($id);

            // Handle file upload if present
            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');

                // Define the destination path in `public/uploads/report`
                $destinationPath = public_path('uploads/report');

                // Ensure the directory exists
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                // Generate a unique filename
                $filename = time() . '_' . $file->getClientOriginalName();

                // Move the uploaded file to the destination directory
                $file->move($destinationPath, $filename);

                // Store the relative path for database
                $validatedData['file_path'] = 'uploads/report/' . $filename;

                // Delete old file if it exists
                if ($medicalReport->file_path && file_exists(public_path($medicalReport->file_path))) {
                    unlink(public_path($medicalReport->file_path));
                }
            }

            // Update the medical report
            $medicalReport->update([
                'patient_id' => $validatedData['patient_id'],
                'date' => $validatedData['date'],
                'description' => $validatedData['description'],
                'file_path' => $validatedData['file_path'] ?? $medicalReport->file_path,
                'report_type' => $validatedData['report_type'],

                'user_id' => $loggedInUserId,
            ]);

            return response()->json([
                'message' => 'Medical report updated successfully',
                'data' => $medicalReport
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Medical report not found'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 401);
        }
    }
    public function update_old(Request $request, $id)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'doctor_id' => 'required|exists:user,id',
                'service_id' => 'required|exists:services,id',
                // 'description' => 'required|string',
            ]);

            // Find the medical report by ID
            $medicalReport = MedicalReport::findOrFail($id);

            if ($request->hasFile('file_path')) {
                $file = $request->file('file_path');

                // Store the file in 'storage/app/public/report'
                $imagePath = $file->store('report', 'public');

                // Store file path in database
                $validatedData['file_path'] = $imagePath;
            }

            // Update the medical report
            $medicalReport->update($validatedData);

            return response()->json(['message' => 'Medical report updated successfully', 'data' => $medicalReport], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Medical report not found'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 401);
        }
    }



    public function destroy($id)
    {
        try {
            $medicalReport = MedicalReport::findOrFail($id);
            $medicalReport->delete();

            return response()->json([
                'message' => 'Medical report deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Medical report not found'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete medical report'], 401);
        }
    }
}
