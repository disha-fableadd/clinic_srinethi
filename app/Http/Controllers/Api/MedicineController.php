<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TaxRate;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $medicines = Medicine::with(['category'])->orderBy('name');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $medicines->where('branch_id', $branchId);
        }

        $medicines = $medicines->get();

        if ($medicines->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No medicines found to export.'
            ]);
        }

        $filename = 'medicines_export_' . now()->format('Ymd_His') . '.csv';
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
            'Medicine Name',
            'Category',
            'Description',
            'Unit',
            'Quantity',
            'Batch No',
            'Manufacture Date',
            'Expiry Date',
            'Image URL',

            'Created At'
        ]);

        $sr = 1;
        foreach ($medicines as $medicine) {
            fputcsv($file, [
                $sr++,
                $medicine->name,
                $medicine->category->name ?? '',
                $medicine->description,
                $medicine->unit,
                $medicine->quantity,
                $medicine->batch_no,
                $medicine->manufacture_date,
                $medicine->expiry_date,
                $medicine->image, // accessor provides full image URL

                $medicine->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Medicines exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }





    public function getmedicine(Request $request)
    {
        $user = Auth::user(); // Get the logged-in user

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Medicine::where('user_id', $user->id);

        // ✅ Filter by branch if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Fetch medicines associated with the logged-in user and order by ID in descending order
        $medicines = $query->orderBy('id', 'desc')->get();

        return response()->json(['medicines' => $medicines]);
    }


   

    public function index(Request $request)
    {
        $query = Medicine::with('category', 'branch'); // eager load relations

        // ✅ Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $mapMedicine = function ($medicine) {
            return [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'description' => $medicine->description,
                'image' => $medicine->image,
                'category_name' => $medicine->category->name ?? 'No category',
                'branch_name' => $medicine->branch->name ?? 'N/A',
                'quantity' => $medicine->quantity ?? 0,
                'unit' => $medicine->unit ?? 'N/A',
                'status' => $medicine->status ?? 'N/A',
                'manufacture_date' => $medicine->manufacture_date ?? null,
                'batch_no' => $medicine->batch_no ?? 'N/A',
                'expiry_date' => $medicine->expiry_date ?? null,
                'created_at' => $medicine->created_at->toDateTimeString(),
            ];
        };

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('batch_no', 'like', '%' . $searchValue . '%')
                        ->orWhere('unit', 'like', '%' . $searchValue . '%')
                        ->orWhere('quantity', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhere('expiry_date', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('category', function ($c) use ($searchValue) {
                            $c->where('name', 'like', '%' . $searchValue . '%');
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

            $allowedOrderColumns = ['id', 'name', 'quantity', 'unit', 'batch_no', 'manufacture_date', 'expiry_date', 'status', 'created_at', 'updated_at'];
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

            $medicines = $filteredQuery->forPage($page, $length)->get()->map($mapMedicine);

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $medicines,
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
                    $q->where('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('batch_no', 'like', '%' . $searchValue . '%')
                        ->orWhere('unit', 'like', '%' . $searchValue . '%')
                        ->orWhere('quantity', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhere('expiry_date', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('category', function ($c) use ($searchValue) {
                            $c->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();
            $medicines = $filteredQuery->orderBy('id', 'desc')->forPage($page, $perPage)->get()->map($mapMedicine);
            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'status' => true,
                'data' => $medicines,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ], 200);
        }

        $medicines = $query->latest()->get()->map($mapMedicine);

        return response()->json([
            'status' => true,
            'data' => $medicines
        ], 200);
    }









    



    public function show($id)
    {
        $medicine = Medicine::with('category')->find($id);

        if (!$medicine) {
            return response()->json(['message' => 'Medicine not found'], 401);
        }

        $medicineName = $medicine->name;
        $invoices = DB::table('invoices')->get();
        $medicineHistory = [];

        foreach ($invoices as $invoice) {
            $details = json_decode($invoice->types_details, true);

            if (is_array($details)) {
                foreach ($details as $item) {
                    if (isset($item['id']) && $item['id'] == $id) {
                        $patient = DB::table('patients')->find($invoice->patient_id);

                        $medicineHistory[] = [
                            'patient_name' => $patient ? $patient->fullname : 'Unknown',
                            'category' => $medicine->category ? $medicine->category->name : 'No Category',
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'date' => $invoice->date,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'medicine' => $medicine,
            'history' => $medicineHistory,
        ]);
    }


  
    public function store(Request $request)
    {
        $loggedInUserId = auth()->id();

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id', // ✅ from local storage/frontend
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'medicine_unit' => 'required|string|max:50',
            'quantity' => 'required|integer|min:0',
            'manufacture_date' => 'required|date',
            'batch_no' => 'required|string|max:100',
            'gst_option' => 'required|string|in:Without GST,With GST',
            'product_gst' => 'nullable|array',
            'product_gst.*' => 'nullable',
            'expiry_date' => 'required|date|after_or_equal:manufacture_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,avif,webp|max:2048',
        ]);

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $destinationPath = public_path('uploads/medicines');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);

                $imagePath = 'uploads/medicines/' . $filename;
            }
            $gstDetails = [];
            $gstOption = str_replace(' ', '_', strtolower($request->gst_option));
            if ($gstOption === 'with_gst' && !empty($request->product_gst)) {
                $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
                $price = (float)str_replace(',', '', $request->unit);

                foreach ($taxItems as $item) {
                    $tax = null;
                    if (is_numeric($item)) {
                        $tax = TaxRate::find($item);
                    } else {
                        if (preg_match('/^(.*?)\s*\((.*?)\s*%\)$/', $item, $matches)) {
                            $name = trim($matches[1]);
                            $rate = trim($matches[2]);
                            $tax = TaxRate::where('tax_name', $name)->where('tax_rate', $rate)->first();
                        }
                    }

                    if ($tax) {
                        $taxAmount = ($price * $tax->tax_rate) / 100;
                        $gstDetails[] = [
                            'tax_name' => $tax->tax_name,
                            'tax_rate' => number_format($tax->tax_rate, 2, '.', ''),
                            'tax_amount' => (float)$taxAmount,
                        ];
                    }
                }
            }
            // ✅ Save with branch_id
            $medicine = Medicine::create([
                'user_id' => $loggedInUserId,
                'branch_id' => $request->branch_id, // ✅ from frontend/local storage
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'unit' => $request->unit,
                'medicine_unit' => $request->medicine_unit,
                'quantity' => $request->quantity,
                'manufacture_date' => $request->manufacture_date,
                'batch_no' => $request->batch_no,
                'gst_option' => $request->gst_option,
                'product_gst' => $gstDetails ? $gstDetails : null,
                'expiry_date' => $request->expiry_date,
                'image' => $imagePath,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Medicine created successfully.',
                'medicine' => $medicine,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create medicine.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Update a medicine
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        // Validate the request
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'unit' => 'required',
            'medicine_unit' => 'required|string|max:50',
            // 'status' => 'required|in:active,inactive',
            // 'description' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'manufacture_date' => 'required|date',
            'batch_no' => 'required|string|max:100',
            'gst_option' => 'required|string|in:Without GST,With GST',
            'product_gst' => 'nullable|array',
            'product_gst.*' => 'nullable',
            'expiry_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,avif,webp|max:2048',
        ]);


        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Define the path where the image should be stored
            $destinationPath = public_path('uploads/medicines');

            // Create directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Generate a unique filename
            $filename = time() . '_' . $image->getClientOriginalName();

            // Move the uploaded file to the destination directory
            $image->move($destinationPath, $filename);

            // Store the relative path for database
            $medicine->image = 'uploads/medicines/' . $filename;
        }
        $gstDetails = [];
        $gstOption = str_replace(' ', '_', strtolower($request->gst_option));
        if ($gstOption === 'with_gst' && !empty($request->product_gst)) {
            $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
            $price = (float)str_replace(',', '', $request->unit ?? $medicine->unit);

            foreach ($taxItems as $item) {
                $tax = null;
                if (is_numeric($item)) {
                    $tax = TaxRate::find($item);
                } else {
                    if (preg_match('/^(.*?)\s*\((.*?)\s*%\)$/', $item, $matches)) {
                        $name = trim($matches[1]);
                        $rate = trim($matches[2]);
                        $tax = TaxRate::where('tax_name', $name)->where('tax_rate', $rate)->first();
                    }
                }

                if ($tax) {
                    $taxAmount = ($price * $tax->tax_rate) / 100;
                    $gstDetails[] = [
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => number_format($tax->tax_rate, 2, '.', ''),
                        'tax_amount' => (float)$taxAmount,
                    ];
                }
            }
        }
        // Update medicine details
        $medicine->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'medicine_unit' => $request->medicine_unit,
            // 'status' => $request->status,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'manufacture_date' => $request->manufacture_date,
            'batch_no' => $request->batch_no,
            'gst_option' => $request->gst_option,
            'product_gst' => $gstDetails ? $gstDetails : null,
            'expiry_date' => $request->expiry_date,
            'image' => $medicine->image,
        ]);

        return response()->json([
            'message' => 'Medicine updated successfully',
            'medicine' => $medicine,
        ], 200);
    }




    public function updateQuantity(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $medicine->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Quantity updated successfully',
            'medicine' => $medicine,
        ], 200);
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        // Check if medicine is associated with any patient_medicine records
        if ($medicine->patientMedicines()->exists()) {
            return response()->json([
                'message' => 'This medicine is assigned to a patient. Please remove it from patient records first.'
            ], 409); // 409 Conflict
        }

        $medicine->delete();

        return response()->json(['message' => 'Medicine deleted successfully'], 200);
    }
}








