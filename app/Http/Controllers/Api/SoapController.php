<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Soap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;


use Carbon\Carbon;


class SoapController extends Controller
{

    


    public function soapPdf(Request $request, $id)
    {
        try {
            // ✅ Load SOAP with patient relation
            $soap = Soap::with('patient')->findOrFail($id);

            // ✅ Clinic details
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

            // ✅ Prepare data for Blade
            $soapData = [
                'date'          => \Carbon\Carbon::parse($soap->date)->format('d-m-Y'),
                'patient'       => $soap->patient,
                'clinic_logo'   => $clinic_logo_path,
                'clinic_name'   => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone'  => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email'  => $settings['clinic_email'] ?? 'clinic@example.com',
                'clinic_city'   => $settings['clinic_city'] ?? 'xyz',
                'clinic_state'  => $settings['clinic_state'] ?? 'xyz',

                'subjective' => [
                    'title'       => $soap->subjective['title'] ?? 'N/A',
                    'description' => $soap->subjective['description'] ?? 'N/A',
                ],
                'objective' => [
                    'title'       => $soap->objective['title'] ?? 'N/A',
                    'description' => $soap->objective['description'] ?? 'N/A',
                ],
                'assessment' => [
                    'title'       => $soap->assessment['title'] ?? 'N/A',
                    'description' => $soap->assessment['description'] ?? 'N/A',
                ],
                'plan' => [
                    'title'       => $soap->plan['title'] ?? 'N/A',
                    'description' => $soap->plan['description'] ?? 'N/A',
                ],
            ];

            // ✅ Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('soap.pdf', $soapData)
                ->setPaper('A4', 'portrait');

            // ✅ File path
            $folder   = 'soaps/';
            $fileName = "SOAP_{$soap->id}.pdf";
            $path     = storage_path('app/public/' . $folder . $fileName);

            // Ensure folder exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            // Save PDF
            $pdf->save($path);

            // ✅ Public URL (after `php artisan storage:link`)
            $fileUrl = url('public/storage/' . $folder . $fileName);

            // 👉 If API request (Postman)
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status'    => true,
                    'message'   => 'SOAP PDF generated successfully.',
                    'file_url'  => $fileUrl,
                    'file_name' => $fileName,
                ], 200);
            }

            // 👉 Otherwise → browser download
            return response()->download($path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'SOAP PDF generation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


  public function SoapexportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $soaps = Soap::with(['user', 'patient'])
        ->orderBy('created_at', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $soaps->where('branch_id', $branchId);
    }

    $soaps = $soaps->get();

    if ($soaps->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No SOAP records found to export.'
        ]);
    }

    $filename = 'soap_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    // Create folder if not exists
    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    // CSV Header
    fputcsv($file, [
        'ID',
        'User',
        'Patient',
        'Date',
        'Subjective',
        'Objective',
        'Assessment',
        'Plan',
        'Created At'
    ]);

    $sr = 1;
    foreach ($soaps as $item) {
        fputcsv($file, [
            $sr++,
            $item->user->name ?? 'N/A',
            $item->patient->fullname ?? 'N/A',
            $item->date ?? 'N/A',
            is_array($item->subjective) ? json_encode($item->subjective) : $item->subjective,
            is_array($item->objective) ? json_encode($item->objective) : $item->objective,
            is_array($item->assessment) ? json_encode($item->assessment) : $item->assessment,
            is_array($item->plan) ? json_encode($item->plan) : $item->plan,
            $item->created_at ? $item->created_at->format('d-M-Y h:i A') : 'N/A',
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'SOAP records exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}



   
      public function index(Request $request)
    {
        $query = Soap::with('patient') // eager load patient
            ->where('user_id', Auth::id());

        // ✅ Filter by branch_id if sent
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        // DataTables server-side request
        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('date', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($q2) use ($searchValue) {
                            $q2->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhere('subjective', 'like', '%' . $searchValue . '%')
                        ->orWhere('objective', 'like', '%' . $searchValue . '%')
                        ->orWhere('assessment', 'like', '%' . $searchValue . '%')
                        ->orWhere('plan', 'like', '%' . $searchValue . '%');
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

            $allowedOrderColumns = ['id', 'date', 'patient_id', 'created_at', 'updated_at'];
            if ($orderColumn && in_array($orderColumn, $allowedOrderColumns, true)) {
                if ($orderColumn === 'patient_id') {
                    $filteredQuery->orderBy('patient_id', $orderDir === 'desc' ? 'desc' : 'asc');
                } else {
                    $filteredQuery->orderBy($orderColumn, $orderDir === 'desc' ? 'desc' : 'asc');
                }
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

            $soaps = $filteredQuery->forPage($page, $length)->get();

            // Format the data for DataTables
            $formattedData = $soaps->map(function ($soap) {
                $subjective = is_string($soap->subjective) ? json_decode($soap->subjective, true) : $soap->subjective;
                $objective = is_string($soap->objective) ? json_decode($soap->objective, true) : $soap->objective;
                $assessment = is_string($soap->assessment) ? json_decode($soap->assessment, true) : $soap->assessment;
                $plan = is_string($soap->plan) ? json_decode($soap->plan, true) : $soap->plan;

                return [
                    'id' => $soap->id,
                    'date' => $soap->date,
                    'patient_id' => $soap->patient_id,
                    'patient_name' => $soap->patient->fullname ?? 'N/A',
                    'subjective_title' => $subjective['title'] ?? '',
                    'subjective_description' => $subjective['description'] ?? '',
                    'objective_title' => $objective['title'] ?? '',
                    'objective_description' => $objective['description'] ?? '',
                    'assessment_title' => $assessment['title'] ?? '',
                    'assessment_description' => $assessment['description'] ?? '',
                    'plan_title' => $plan['title'] ?? '',
                    'plan_description' => $plan['description'] ?? '',
                    'created_at' => $soap->created_at,
                    'updated_at' => $soap->updated_at
                ];
            });

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData,
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
                    $q->where('date', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($q2) use ($searchValue) {
                            $q2->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhere('subjective', 'like', '%' . $searchValue . '%')
                        ->orWhere('objective', 'like', '%' . $searchValue . '%')
                        ->orWhere('assessment', 'like', '%' . $searchValue . '%')
                        ->orWhere('plan', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $soaps = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            // Format the data
            $formattedData = $soaps->map(function ($soap) {
                $subjective = is_string($soap->subjective) ? json_decode($soap->subjective, true) : $soap->subjective;
                $objective = is_string($soap->objective) ? json_decode($soap->objective, true) : $soap->objective;
                $assessment = is_string($soap->assessment) ? json_decode($soap->assessment, true) : $soap->assessment;
                $plan = is_string($soap->plan) ? json_decode($soap->plan, true) : $soap->plan;

                return [
                    'id' => $soap->id,
                    'date' => $soap->date,
                    'patient' => $soap->patient,
                    'subjective' => $subjective,
                    'objective' => $objective,
                    'assessment' => $assessment,
                    'plan' => $plan,
                    'created_at' => $soap->created_at,
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        // Return plain array for existing ajax requests
        $soaps = $query->orderBy('id', 'desc')->get();
        return response()->json($soaps, 200);
    }

  



   



    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',

            'subjective_title' => 'required|string',
            'subjective_description' => 'required|string',

            'objective_title' => 'required|string',
            'objective_description' => 'required|string',

            'assessment_title' => 'required|string',
            'assessment_description' => 'required|string',

            'plan_title' => 'required|string',
            'plan_description' => 'required|string',

            'branch_id' => 'required|exists:branches,id', // ✅ validate branch
        ]);

        $soap = Soap::create([
            'user_id' => Auth::id(),
            'patient_id' => $request->patient_id,
            'branch_id' => $request->branch_id, // ✅ save branch
            'date' => $request->date,
            'subjective' => [
                'title' => $request->subjective_title,
                'description' => $request->subjective_description,
            ],
            'objective' => [
                'title' => $request->objective_title,
                'description' => $request->objective_description,
            ],
            'assessment' => [
                'title' => $request->assessment_title,
                'description' => $request->assessment_description,
            ],
            'plan' => [
                'title' => $request->plan_title,
                'description' => $request->plan_description,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOAP record created successfully',
            'data' => $soap->load('patient')
        ], 201);
    }







    /**
     * Show a single SOAP record
     */
    public function show($id)
    {
        $soap = Soap::with('patient')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($soap);
    }

    /**
     * Update an existing SOAP record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id', // 🔹
            'date' => 'required|date',

            'subjective_title' => 'nullable|string',
            'subjective_description' => 'nullable|string',

            'objective_title' => 'required|string',
            'objective_description' => 'required|string',

            'assessment_title' => 'required|string',
            'assessment_description' => 'required|string',

            'plan_title' => 'required|string',
            'plan_description' => 'required|string',
        ]);

        $soap = Soap::where('user_id', Auth::id())->findOrFail($id);

        $soap->update([
            'patient_id' => $request->patient_id, // 🔹 update patient
            'date' => $request->date,
            'subjective' => [
                'title' => $request->subjective_title,
                'description' => $request->subjective_description,
            ],
            'objective' => [
                'title' => $request->objective_title,
                'description' => $request->objective_description,
            ],
            'assessment' => [
                'title' => $request->assessment_title,
                'description' => $request->assessment_description,
            ],
            'plan' => [
                'title' => $request->plan_title,
                'description' => $request->plan_description,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOAP record updated successfully',
            'data' => $soap->load('patient')
        ]);
    }

    /**
     * Delete a SOAP record
     */
    public function destroy($id)
    {
        $soap = Soap::where('user_id', Auth::id())->findOrFail($id);
        $soap->delete();

        return response()->json([
            'success' => true,
            'message' => 'SOAP record deleted successfully'
        ]);
    }

    public function getPatientSoap($patientId)
    {
        $soaps = Soap::with('patient')
            ->where('patient_id', $patientId)
            ->get();

        $formatted = $soaps->map(function ($soap) {

            // Decode JSON if it is stored as JSON string
            $subjective = is_string($soap->subjective) ? json_decode($soap->subjective, true) : $soap->subjective;
            $objective = is_string($soap->objective) ? json_decode($soap->objective, true) : $soap->objective;
            $assessment = is_string($soap->assessment) ? json_decode($soap->assessment, true) : $soap->assessment;
            $plan = is_string($soap->plan) ? json_decode($soap->plan, true) : $soap->plan;

            return [
                'patient_name' => $soap->patient ? $soap->patient->fullname : 'N/A',
                'date' => $soap->date,

                'subjective' => [
                    'title' => $subjective['title'] ?? $subjective[0] ?? '',
                    'description' => $subjective['description'] ?? $subjective[1] ?? $subjective ?? '',
                ],
                'objective' => [
                    'title' => $objective['title'] ?? $objective[0] ?? '',
                    'description' => $objective['description'] ?? $objective[1] ?? $objective ?? '',
                ],
                'assessment' => [
                    'title' => $assessment['title'] ?? $assessment[0] ?? '',
                    'description' => $assessment['description'] ?? $assessment[1] ?? $assessment ?? '',
                ],
                'plan' => [
                    'title' => $plan['title'] ?? $plan[0] ?? '',
                    'description' => $plan['description'] ?? $plan[1] ?? $plan ?? '',
                ],
            ];
        });

        return response()->json($formatted);
    }

    public function exportCsv(Request $request)
    {
        // Fetch all SOAP records with patient relation
        $soaps = Soap::with('patient')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($soaps->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No SOAP records found to export.'
            ]);
        }

        $filename = 'soap_export_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        $File = new \Illuminate\Support\Facades\File;

        if (!$File::exists($publicPath)) {
            $File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        // CSV Header
        fputcsv($file, [
            'ID',
            'Date',
            'Patient Name',
            'Subjective Titles',
            'Subjective Descriptions',
            'Objective Titles',
            'Objective Descriptions',
            'Assessment Titles',
            'Assessment Descriptions',
            'Plan Titles',
            'Plan Descriptions'
        ]);

        $sr = 1;
        foreach ($soaps as $soap) {
            $subjective = $soap->subjective ?? [];
            $objective = $soap->objective ?? [];
            $assessment = $soap->assessment ?? [];
            $plan = $soap->plan ?? [];

            $subjectiveTitles = is_array($subjective['title'] ?? null) ? implode(' | ', $subjective['title']) : ($subjective['title'] ?? '');
            $subjectiveDescriptions = is_array($subjective['description'] ?? null) ? implode(' | ', $subjective['description']) : ($subjective['description'] ?? '');

            $objectiveTitles = is_array($objective['title'] ?? null) ? implode(' | ', $objective['title']) : ($objective['title'] ?? '');
            $objectiveDescriptions = is_array($objective['description'] ?? null) ? implode(' | ', $objective['description']) : ($objective['description'] ?? '');

            $assessmentTitles = is_array($assessment['title'] ?? null) ? implode(' | ', $assessment['title']) : ($assessment['title'] ?? '');
            $assessmentDescriptions = is_array($assessment['description'] ?? null) ? implode(' | ', $assessment['description']) : ($assessment['description'] ?? '');

            $planTitles = is_array($plan['title'] ?? null) ? implode(' | ', $plan['title']) : ($plan['title'] ?? '');
            $planDescriptions = is_array($plan['description'] ?? null) ? implode(' | ', $plan['description']) : ($plan['description'] ?? '');

            fputcsv($file, [
                $sr++,
                $soap->date ?? '',
                $soap->patient->name ?? 'N/A',
                $subjectiveTitles,
                $subjectiveDescriptions,
                $objectiveTitles,
                $objectiveDescriptions,
                $assessmentTitles,
                $assessmentDescriptions,
                $planTitles,
                $planDescriptions,
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'SOAP records exported successfully.',
            'file_url' => url($folder . $filename),
            'file_name' => $filename
        ]);
    }
}
