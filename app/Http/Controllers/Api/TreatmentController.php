<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Treatment;
use Illuminate\Support\Facades\File;
use App\Models\Patients;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\TaxRate;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;

class TreatmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $treatments = Treatment::with('doctor', 'patients')
            ->orderBy('created_at', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $treatments->where('branch_id', $branchId);
        }

        $treatments = $treatments->get();

        if ($treatments->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No treatments found to export.'
            ]);
        }

        $filename = 'treatments_export_' . now()->format('Ymd_His') . '.csv';
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
            'Treatment Name',
            'Price',
            'Description',
            'GST Option',
            'Product GST',
            'Doctor Name',
            'Patient Count',
            'Created At'
        ]);

        $sr = 1;
        foreach ($treatments as $treatment) {
            // Format Product GST details into a readable string
            $gstDetailsString = '';
            if (!empty($treatment->product_gst) && is_array($treatment->product_gst)) {
                $details = [];
                foreach ($treatment->product_gst as $gst) {
                    $details[] = ($gst['tax_name'] ?? '') . ' (' . ($gst['tax_rate'] ?? 0) . '%)';
                }
                $gstDetailsString = implode(', ', $details);
            }

            fputcsv($file, [
                $sr++,
                $treatment->name,
                $treatment->price,
                $treatment->description,
                $treatment->gst_option ?? 'N/A',
                $gstDetailsString ?: 'N/A',
                $treatment->doctor->fullname ?? '',
                $treatment->patients->count(),
                $treatment->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Treatments exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }



  

    public function index(Request $request)
    {
        $query = Treatment::with(['doctor', 'branch']); // eager load relations

        // Filter by branch_id if provided
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        // If logged-in user is a doctor, show only their treatments
        $user = Auth::user();
        if ($user && $user->role->name === 'Doctor') {
            $query->where('doctor_id', $user->id);
        }

        $treatments = $query->latest()->get()->map(function ($treatment) {
            return [
                'id'          => $treatment->id,
                'name'        => $treatment->name,
                'price'       => $treatment->price,
                'description' => $treatment->description ?? 'N/A',
                'doctor_name' => $treatment->doctor->fullname ?? 'N/A',
                'branch_name' => $treatment->branch->name ?? 'N/A',
                'created_at'  => $treatment->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status'     => true,
            'treatments' => $treatments,
        ]);
    }











      public function store(Request $request)
    {
        $loggedInUserId = auth()->id();


        $validator = Validator::make($request->all(), [
            'doctor_id' => 'nullable|exists:user,id',
            'name'      => 'nullable|string|max:255',
            'price'     => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'machine' => 'nullable|string|max:255',
            'gst_option' => 'nullable|string|in:Without GST,With GST',
            'product_gst' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $treatmentData = $request->except(['product_gst']);
        $treatmentData['user_id'] = $loggedInUserId;

        // GST Calculation
        $gstDetails = [];
        $gstOption = str_replace(' ', '_', strtolower($request->gst_option ?? 'without_gst'));
        if ($gstOption === 'with_gst' && !empty($request->product_gst)) {
            $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
            $price = (float)str_replace(',', '', $request->price ?? 0);

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
        $treatmentData['product_gst'] = !empty($gstDetails) ? $gstDetails : null;

        $treatment = Treatment::create($treatmentData);

        // if ($treatment) {
        //     Notification::store(
        //         "New treatment '{$treatment->name}' has been assigned to you.",
        //         $request->doctor_id,
        //         $treatment->id,
        //         'treatment'
        //     );
        // }


        $treatment->load('branch');


        // ✅ Get doctor and creator info
        $doctor  = User::find($request->doctor_id);
        $creator = User::find($loggedInUserId);

        if ($doctor && $treatment) {
            $doctorFullName    = $doctor->fullname ?? 'Doctor';
            $treatmentName     = $treatment->name ?? 'Treatment';
            $creatorFullName   = $creator->fullname ?? 'Admin';

            // ✅ Store notification in database
            Notification::store(
                "Dr {$doctorFullName} You have a New treatment '{$treatmentName}' has been assigned to you by {$creatorFullName}.",
                $doctor->id,
                $treatment->id,
                'treatment',
                $loggedInUserId
            );
            Log::info("🔔 Notification stored for Doctor ID: {$doctor->id} (Treatment ID: {$treatment->id})");

            // ✅ Send FCM push notification
            if (!empty($doctor->fcm_token)) {
                try {
                    $fcm = new FCMService();
                    $response = $fcm->sendNotification(
                        $doctor->fcm_token,
                        'New Treatment Assigned',
                        "Dear Dr. {$doctorFullName}, a new treatment '{$treatmentName}' has been assigned to you by {$creatorFullName}.",
                        [
                            'treatment_id' => (string)$treatment->id,
                            'type' => 'treatment'
                        ]
                    );
                    Log::info("FCM sent to Doctor ID {$doctor->id} for Treatment '{$treatmentName}': " . json_encode($response));
                } catch (\Exception $e) {
                    Log::warning("FCM error for Doctor ID {$doctor->id}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Treatment created successfully',
            'treatment' => [
                'id'         => $treatment->id,
                'name'       => $treatment->name,
                'price'      => $treatment->price,
                'doctor_id'  => $treatment->doctor_id,
                //  'machine'     => $treatment->machine,   
                'branch_id'  => $treatment->branch_id,
                'branch_name' => $treatment->branch->name ?? 'N/A',
                'created_at' => $treatment->created_at->toDateTimeString(),
            ]
        ], 200);
    }

    /**
     * Get a single treatment.
     */
    public function show($id)
    {
        $treatment = Treatment::with('doctor')->findOrFail($id);

        $sanitize = function ($value) {
            if ($value === null) {
                return null;
            }

            if (is_array($value)) {
                array_walk_recursive($value, function (&$item) {
                    if (is_string($item)) {
                        $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                    }
                });

                return $value;
            }

            if (is_string($value)) {
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }

            return $value;
        };

        $payload = [
            'id' => $treatment->id,
            'name' => $sanitize($treatment->name),
            'price' => $sanitize($treatment->price),
            'description' => $sanitize($treatment->description),
            'gst_option' => $sanitize($treatment->gst_option),
            'product_gst' => $sanitize($treatment->product_gst),
            'doctor_id' => $treatment->doctor_id,
            'doctor_name' => $sanitize(optional($treatment->doctor)->fullname ?? 'N/A'),
            'branch_id' => $treatment->branch_id,
            'created_at' => optional($treatment->created_at)?->toDateTimeString(),
            'updated_at' => optional($treatment->updated_at)?->toDateTimeString(),
        ];

        return response()->json($payload, 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update a treatment.
     */
    public function update(Request $request, $id)
    {
        $treatment = Treatment::findOrFail($id);

        // Validate input
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:user,id',
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'gst_option' => 'nullable|string|in:Without GST,With GST',
            'product_gst' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $treatmentData = $request->except(['product_gst']);

        // GST Calculation
        $gstDetails = [];
        $gstOption = str_replace(' ', '_', strtolower($request->gst_option ?? 'without_gst'));
        if ($gstOption === 'with_gst' && !empty($request->product_gst)) {
            $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
            $price = (float)str_replace(',', '', $request->price ?? $treatment->price);

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
        $treatmentData['product_gst'] = !empty($gstDetails) ? $gstDetails : null;

        // Update treatment details
        $treatment->update($treatmentData);

        return response()->json([
            'message' => 'Treatment updated successfully',
            'treatment' => $treatment
        ], 200);
    }

    /**
     * Delete a treatment.
     */
   


    public function destroy($id)
    {
        $treatment = Treatment::findOrFail($id);

        // Check if the treatment has related patients
        if ($treatment->patients()->exists()) {
            return response()->json(['message' => 'Cannot delete treatment. Related patients exist.'], 400);
        }

        $treatment->delete();

        return response()->json(['message' => 'Treatment deleted successfully'], 200);
    }






   

    public function getTreatments(Request $request)
    {
        $user = Auth::user(); // Get the logged-in user

        $query = DB::table('treatments')
            ->join('user', 'user.id', '=', 'treatments.doctor_id')
            ->select('treatments.*', 'user.fullname as doctor_name')
            ->orderBy('treatments.id', 'desc');

        // If the user is a doctor, only show their treatments
        if ($user->role->name === 'Doctor') {
            $query->where('treatments.doctor_id', $user->id);
        }

        // Filter by branch_id if provided in the request
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('treatments.branch_id', $request->branch_id);
        }

        $treatments = $query->get();

        return response()->json(['treatments' => $treatments]);
    }
}
