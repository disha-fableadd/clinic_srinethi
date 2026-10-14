<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IpdAdmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IpdAdmissionController extends Controller
{
   



   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $admissions = IpdAdmission::with(['patient', 'doctor', 'treatment'])
        ->orderBy('admission_date', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $admissions->where('branch_id', $branchId);
    }

    $admissions = $admissions->get();

    if ($admissions->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No IPD admissions found to export.'
        ]);
    }

    $filename = 'ipd_admissions_export_' . now()->format('Ymd_His') . '.csv';
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
        'Treatment Name',
        'Admission Date',
        'Room Number',
        'Bed Number',
       
        'Created At'
    ]);

    $sr = 1;
    foreach ($admissions as $admission) {
        fputcsv($file, [
            $sr++,
            $admission->patient->fullname ?? '',
            $admission->doctor->fullname ?? '',
            $admission->treatment->name ?? '',
            $admission->admission_date,
            $admission->room_number,
            $admission->bed_number,
          
            $admission->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'IPD admissions exported successfully.',
      'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}









  


    public function index(Request $request)
    {
        $user = auth()->user(); // current user

        $query = IpdAdmission::with(['patient', 'doctor', 'treatment']);

        // ✅ Filter by branch_id if sent from frontend
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // ✅ Doctor restriction
        if ($user && $user->role->name === 'Doctor') {
            $query->where('doctor_id', $user->id);
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
                    $q->where('admission_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('room_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('bed_number', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('doctor', function ($d) use ($searchValue) {
                            $d->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('treatment', function ($t) use ($searchValue) {
                            $t->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && $request->input('length') == -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $ipds = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'message' => 'IPD admissions fetched successfully',
                'ipd_admissions' => $ipds,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $ipds = $query->orderBy('id', 'desc')->get();

        return response()->json(['ipd_admissions' => $ipds]);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'doctor_id'      => 'required|exists:user,id', // ✅ corrected table name to `users`
            'treatment_id'   => 'required|exists:treatments,id',
            'admission_date' => 'required|date',
            'room_number'    => 'required|string|max:255',
            'bed_number'     => 'required|string|max:255',
            'branch_id'      => 'required|exists:branches,id', // ✅ ensure branch exists
        ]);

        $ipd = IpdAdmission::create($validated);

        return response()->json([
            'message' => 'IPD Admission created successfully.',
            'data'    => $ipd
        ]);
    }


    public function show($id)
    {
        $ipd = IpdAdmission::with(['patient', 'doctor', 'treatment'])->findOrFail($id);
        return response()->json($ipd);
    }

    public function update(Request $request, $id)
    {
        $ipd = IpdAdmission::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'doctor_id' => 'sometimes|exists:user,id',
            'treatment_id' => 'sometimes|exists:treatments,id',
            'admission_date' => 'sometimes|date',

            'room_number' => 'sometimes|string|max:255',
            'bed_number' => 'sometimes|string|max:255',

        ]);

        $ipd->update($validated);

        return response()->json(['message' => 'IPD Admission updated successfully.', 'data' => $ipd]);
    }

    public function destroy($id)
    {
        $ipd = IpdAdmission::findOrFail($id);
        $ipd->delete();

        return response()->json(['message' => 'IPD Admission deleted successfully.']);
    }
}
