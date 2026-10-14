<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PathologyTest;
use App\Models\RadiologyTest;
use Illuminate\Http\Request;
use App\Models\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class ServiceController extends Controller
{
  public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $records = Services::with(['patient', 'pathelogy_service', 'radiology_service']);

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $records->where('branch_id', $branchId);
    }

    $records = $records->get();

    if ($records->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No services found to export.'
        ]);
    }

    $filename = 'services_export_' . now()->format('Ymd_His') . '.csv';
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
        'Department',
        'Test Name',
        'Description',
        'Cost',
        'Created At'
    ]);

    $sr = 1;
    foreach ($records as $row) {
        $testName = '';

        // ✅ Use only eager loaded relationships
        if ($row->department === 'Pathology') {
            $testName = $row->pathelogy_service->test_name ?? 'Not found';
        } elseif ($row->department === 'Radiology') {
            $testName = $row->radiology_service->test_name ?? 'Not found';
        }

        fputcsv($file, [
            $sr++,
            $row->patient->fullname ?? '',
            ucfirst($row->department),
            $testName,
            $row->description ?? '',
            $row->cost,
            $row->created_at
        ]);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Services exported successfully.',
        'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}




    


    public function getServicesByDepartment(Request $request)
{
    $department = $request->get('department');
    $branchId = $request->get('branch_id'); // Get branch_id from request

    if ($department === 'Pathology') {
        $query = PathologyTest::select('id', 'test_name');
    } elseif ($department === 'Radiology') {
        $query = RadiologyTest::select('id', 'test_name');
    } else {
        return response()->json([]);
    }

    // If branchId is provided, filter by branch_id
    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    $services = $query->get();
    return response()->json($services);
}



    public function getServices()
    {
        $user = Auth::user(); // Get the logged-in user

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user has the role of 'Receptionist'
        if ($user->role->name === 'Receptionist') {
            // Fetch all services for receptionists and order by id in descending order
            $services = Services::orderBy('id', 'desc')->get();
        } else {
            // Fetch services that belong to the logged-in user and order by id in descending order
            $services = Services::where('user_id', $user->id)
                ->orderBy('id', 'desc') // Order by ID in descending order
                ->get();
        }

        return response()->json(['services' => $services]);
    }



    public function index(Request $request)
    {
        $branchId = $request->get('branch_id'); // 👈 branch_id comes from frontend

        $query = Services::with(['patient', 'pathelogy_service', 'radiology_service']);

        if ($branchId) {
            $query->where('branch_id', $branchId); // 👈 filter by branch_id
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('department', 'like', '%' . $searchValue . '%')
                        ->orWhere('description', 'like', '%' . $searchValue . '%')
                        ->orWhere('cost', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('pathelogy_service', function ($p) use ($searchValue) {
                            $p->where('test_name', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('radiology_service', function ($r) use ($searchValue) {
                            $r->where('test_name', 'like', '%' . $searchValue . '%');
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

            $allowedOrderColumns = ['id', 'department', 'description', 'cost', 'created_at', 'updated_at'];
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

            $services = $filteredQuery->forPage($page, $length)->get();

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $services,
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
                    $q->where('department', 'like', '%' . $searchValue . '%')
                        ->orWhere('description', 'like', '%' . $searchValue . '%')
                        ->orWhere('cost', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('pathelogy_service', function ($p) use ($searchValue) {
                            $p->where('test_name', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('radiology_service', function ($r) use ($searchValue) {
                            $r->where('test_name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $services = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get();
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'data' => $services,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $services = $query->orderBy('id', 'desc')->get();

        return response()->json($services, 200);
    }



    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'branch_id'   => 'required|exists:branches,id', // ✅ validate branch_id
            'patient_id'  => 'required|exists:patients,id',
            'department'  => 'required|in:Pathology,Radiology',
            'service_id'  => 'required|integer',
            'description' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Optional: fetch cost based on department + service_id
        $cost = 0;
        if ($request->department === 'Pathology') {
            $pathology = PathologyTest::find($request->service_id);
            $cost = $pathology ? $pathology->cost : 0;
        } elseif ($request->department === 'Radiology') {
            $radiology = RadiologyTest::find($request->service_id);
            $cost = $radiology ? $radiology->cost : 0;
        }

        // Create service entry
        $service = Services::create([
            'branch_id'   => $request->branch_id,   // ✅ store branch id
            'patient_id'  => $request->patient_id,
            'department'  => $request->department,
            'service_id'  => $request->service_id,
            'description' => $request->description,
            'cost'        => $cost,
            'user_id'     => Auth::id() ?? 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Service created successfully',
            'data'    => $service
        ], 201);
    }

    /**
     * Get a single service by ID.
     */
    public function show($id)
    {
        $service = Services::with(['patient', 'pathelogy_service', 'Radiology_service'])->findOrFail($id);

        return response()->json($service, 200);
    }

    /**
     * Update an existing service.
     */
    public function update(Request $request, $id)
    {
        $service = Services::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'description' => 'required|string|min:10',
            'department' => 'required|in:Pathology,Radiology', // adjust values as per your app logic
            'service_id' => 'required|integer'

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service->update([
            'patient_id' => $request->patient_id,
            'description' => $request->description,
            'department' => $request->department,
            'service_id' => $request->service_id,
            'cost' => $request->cost,
            'user_id' => auth()->id() ?? $service->user_id, // optional: track who updated it
        ]);

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service
        ], 200);
    }

    /**
     * Delete a service.
     */
    public function destroy($id)
    {
        $service = Services::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully'], 200);
    }
}
