<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignedTherapy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Models\Branch;

class AssignedTherapyController extends Controller
{




    public function assignedTherapyPdf(Request $request, $id)
    {
        try {
            // ✅ Load assigned therapy with relations
            $assignedTherapy = \App\Models\AssignedTherapy::with([
                'patient',
                'doctor',
                'therapy'
            ])->findOrFail($id);

            // ✅ Fetch clinic settings
            $settings = \App\Models\Setting::whereIn('key', [
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

            // ✅ Data for Blade
            $data = [
                'date' => now()->format('d-m-Y'),
                'assignedTherapy' => $assignedTherapy,
                'patient' => $assignedTherapy->patient,
                'doctor' => $assignedTherapy->doctor,
                'therapy' => $assignedTherapy->therapy,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone' => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email' => $settings['clinic_email'] ?? 'clinic@example.com',
                'clinic_city' => $settings['clinic_city'] ?? 'xyz',
                'clinic_state' => $settings['clinic_state'] ?? 'xyz',
            ];

            // ✅ Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'assign-therapy.assigned_therapy_pdf',
                $data
            )->setPaper('A4', 'portrait');

            // ✅ File paths
            $folder = 'assigned-therapies/';
            $fileName = "AssignedTherapy_{$assignedTherapy->id}.pdf";
            $path = storage_path($folder . $fileName);

            // Ensure folder exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            // ✅ Save PDF
            $pdf->save($path);

            // ✅ Public URL (requires `php artisan storage:link`)
            // This will point to: public/storage/assigned-therapies/AssignedTherapy_4.pdf
            $fileUrl = asset('storage/' . $folder . $fileName);

            // 👉 If API/Postman request → JSON response
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status' => true,
                    'message' => 'Assigned Therapy PDF generated successfully.',
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                ], 200);
            }

            // 👉 Otherwise → browser direct download
            return response()->download($path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Assigned Therapy PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function exportAssignedTherapiesCsv(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $branchId = $request->input('branch_id'); // ✅ branch filter

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $assignedTherapies = AssignedTherapy::with(['patient', 'therapy', 'doctor'])
            ->where('user_id', $userId);

        // ✅ branch filter
        if ($branchId) {
            $assignedTherapies->where('branch_id', $branchId);
        }


        $assignedTherapies = $assignedTherapies->orderBy('id', 'desc')->get();

        if ($assignedTherapies->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No assigned therapies found to export.'
            ]);
        }

        $filename = 'assigned_therapies_export_' . now()->format('Ymd_His') . '.csv';
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
            'Therapy Name',
            'Doctor Name',
            'Type',
            'Start Date',
            'End Date',
            'Status',
            'Created At'
        ]);

        $sr = 1;
        foreach ($assignedTherapies as $item) {
            fputcsv($file, [
                $sr++,
                $item->patient->fullname ?? 'N/A',
                $item->therapy->name ?? 'N/A',
                $item->doctor->fullname ?? 'N/A',
                $item->type ?? 'N/A',
                $item->start_date ?? 'N/A',
                $item->end_date ?? 'N/A',
                $item->status ?? 'N/A',
                $item->created_at ? $item->created_at->format('d-M-Y h:i A') : 'N/A',
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Assigned therapies exported successfully.',
           'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }




  



    public function index(Request $request)
    {
        $branchId = $request->input('branch_id');

        $query = AssignedTherapy::with(['therapy', 'doctor', 'patient'])
            ->when($branchId, function ($q, $branchId) {
                $q->where('branch_id', $branchId);
            });

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
                    $q->where('type', 'like', '%' . $searchValue . '%')
                        ->orWhere('start_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('end_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('therapy', function ($t) use ($searchValue) {
                            $t->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && $request->input('length') == -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $assignedTherapies = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Assigned therapies fetched successfully',
                'assigned_therapies' => $assignedTherapies,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $data = $query->get();

        return response()->json($data, 200); // ✅ No "data" wrapper
    }



    public function store(Request $request)
    {
        $loggedInUser = auth()->user();

        // ✅ Validate input including branch_id from frontend/local storage
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'therapy_id' => 'required|exists:therapy,id',
            'doctor_id' => 'required|exists:user,id',
            'type' => 'required|in:virtual,inperson',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'branch_id' => 'required|exists:branches,id', // ✅ from frontend/local storage
        ]);

        // ✅ Create assigned therapy record
        $assigned = AssignedTherapy::create([
            'user_id' => $loggedInUser->id,
            'patient_id' => $validated['patient_id'],
            'therapy_id' => $validated['therapy_id'],
            'doctor_id' => $validated['doctor_id'],
            'branch_id' => $validated['branch_id'], // ✅ from frontend/local storage
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Therapy assigned successfully',
            'data' => $assigned
        ], 201);
    }





    public function show($id)
    {
        $assigned = AssignedTherapy::with(['therapy', 'doctor', 'patient'])->findOrFail($id);
        return response()->json($assigned);
    }



    public function update(Request $request, $id)
    {
        $assigned = AssignedTherapy::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'therapy_id' => 'required|exists:therapy,id',
            'doctor_id' => 'required|exists:user,id',
            'type' => 'required|in:virtual,inperson',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);



        $assigned->update([

            'patient_id' => $request->patient_id,
            'therapy_id' => $request->therapy_id,
            'doctor_id' => $request->doctor_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Assigned therapy updated successfully',
            'data' => $assigned
        ]);
    }


    public function destroy($id)
    {
        $assigned = AssignedTherapy::findOrFail($id);
        $assigned->delete();

        return response()->json(['message' => 'Assigned therapy deleted successfully']);
    }
}
