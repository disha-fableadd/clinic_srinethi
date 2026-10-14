<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\DailyData;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Notification;
use App\Models\Patients;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyDataController extends Controller
{
    public function getPendingPayments(Request $request)
    {
        $branchId = $request->branch_id;
        
        $query = DailyData::with(['patient', 'paymentHistories'])
            ->where('status', 'pending');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $pendingPayments = $query->orderBy('date', 'desc') // latest first
            ->take(5) // limit to 5
            ->get()
            ->map(function ($daily) {
                return [
                    'id' => $daily->id,
                    'patient' => $daily->patient->fullname ?? 'Unknown',
                    'profile' => $daily->patient->profile ?? asset('admin/assets/img/img1.png'),
                    'date' => $daily->date,
                    'total' => $daily->paymentHistories->sum('amount'),
                    'pending' => $daily->remain_amount,
                ];
            });

        return response()->json($pendingPayments);
    }

    public function dailyDataPdf(Request $request, $id)
    {
        try {
            // ✅ Load Daily Data with relations
            $daily = DailyData::with([
                'patient',
                'treatment',
                'paymentHistories',
                'collectedBy',
                'branch'
            ])->findOrFail($id);

            // ✅ Fetch clinic settings
            $settings = Setting::whereIn('key', [
                'clinic_logo',
                'clinic_name',
                'clinic_address',
                'clinic_phone',
                'clinic_email',
            ])->pluck('value', 'key');

            $clinic_logo = $settings['clinic_logo'] ?? 'admin/assets/img/cliniclogo.png';
            $clinic_logo_path = public_path($clinic_logo);

            // ✅ Prepare data for blade
            $data = [
                'date' => now()->format('d-m-Y'),
                'daily' => $daily,
                'patient' => $daily->patient,
                'treatment' => $daily->treatment,
                'history' => $daily->paymentHistories,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Clinic Name',
                'clinic_address' => $settings['clinic_address'] ?? '--',
                'clinic_phone' => $settings['clinic_phone'] ?? '--',
                'clinic_email' => $settings['clinic_email'] ?? '--',
            ];

            // ✅ Generate PDF
            $pdf = Pdf::loadView('daily_data.daily-data', $data)
                ->setPaper('A4', 'portrait');

            // ✅ Save path
            $folder = public_path('storage/daily-data/');
            $filename = "DailyData_{$daily->id}.pdf";
            $path = $folder . $filename;

            // ✅ Ensure folder exists
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // ✅ Save PDF
            $pdf->save($path);

            // ✅ Public URL
            $fileUrl = url('public/storage/daily-data/' . $filename);

            // 👉 API response (Postman / AJAX)
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status' => true,
                    'message' => 'Daily Data PDF generated successfully.',
                    'file_url' => $fileUrl,
                    'file_name' => $filename,
                ], 200);
            }

            // 👉 Browser direct download
            return response()->download($path, $filename);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Daily Data PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $dailyData = DailyData::with(['user', 'patient', 'treatment', 'collectedBy', 'paymentHistories'])
            ->orderBy('created_at', 'desc');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $dailyData->where('branch_id', $branchId);
        }

        $dailyData = $dailyData->get();

        if ($dailyData->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No Daily Data records found to export.'
            ]);
        }

        $filename = 'daily_data_export_' . now()->format('Ymd_His') . '.csv';
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
            'User',
            'Patient',
            'Treatment',
            'Date',
            'Remain Amount',
            'Status',
            'Collected By',
            'Comment',
            'Payment History (Amount / Mode / Paid Type / Cash / Online / Paid / Remain)',

            'Created At'
        ]);

        $sr = 1;
        foreach ($dailyData as $item) {
            $paymentDetails = [];
            foreach ($item->paymentHistories as $ph) {
                $paymentDetails[] =
                    ($ph->amount ?? 0) . ' | ' .
                    ($ph->payment_mode ?? 'N/A') . ' | ' .
                    ($ph->paid_type ?? 'N/A') . ' | Cash:' . ($ph->cash_amount ?? 0) .
                    ' | Online:' . ($ph->online_amount ?? 0) .
                    ' | Paid:' . ($ph->paid_amount ?? 0) .
                    ' | Remain:' . ($ph->remain_amount ?? 0);
            }
            $paymentHistoryString = implode(" || ", $paymentDetails);

            fputcsv($file, [
                $sr++,
                $item->user->fullname ?? 'N/A',
                $item->patient->fullname ?? 'N/A',
                $item->treatment->name ?? 'N/A',
                $item->date ?? 'N/A',
                $item->remain_amount ?? 0,
                $item->status ?? 'N/A',
                $item->collectedBy->fullname ?? 'N/A',
                $item->comment ?? 'N/A',
                $paymentHistoryString,

                $item->created_at ? $item->created_at->format('d-M-Y h:i A') : 'N/A',
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Daily Data with payment history exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }

    public function getAvailableYears()
    {
        Log::info('Getting available years for user: ' . auth()->id());

        $userId = auth()->id();

        $years = DailyData::where('user_id', $userId)
            ->select(DB::raw('YEAR(date) as year'))
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return response()->json($years);
    }




    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = DailyData::with(['user', 'patient', 'treatment', 'collectedBy', 'paymentHistories'])
            ->where('user_id', $userId)
            ->orderBy('date', 'desc');

        // ✅ Filter by branch_id if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Additional filters
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $data = $query->get();

        // Transform data for frontend
        $transformed = $data->map(function ($item) {
            $totalPaid = $item->paymentHistories->sum('paid_amount');
            $totalPending = $item->paymentHistories->sum('remain_amount');
            $totalAmount = optional($item->paymentHistories->first())->amount ?? 0;

            return [
                'id' => $item->id,
                'patient_id' => $item->patient_id,
                'patient_name' => optional($item->patient)->fullname,
                'treatment_name' => optional($item->treatment)->name,
                'date' => $item->date,
                'amount' => $totalAmount,
                'received' => $totalPaid,
                'pending' => $item->remain_amount,
                'status' => $item->status,
                'by' => optional($item->collectedBy)->fullname,
                'comment' => $item->comment,
                'created_at' => $item->created_at,
                'payment_histories' => $item->paymentHistories->map(function ($ph) {
                    return [
                        'id' => $ph->id,
                        'amount' => $ph->amount,
                        'payment_mode' => $ph->payment_mode,
                        'paid_type' => $ph->paid_type,
                        'cash_amount' => $ph->cash_amount,
                        'online_amount' => $ph->online_amount,
                        'remain_amount' => $ph->remain_amount,
                        'paid_amount' => $ph->paid_amount,
                        'created_at' => $ph->created_at,
                    ];
                }),
            ];
        });

        // Summary
        $summary = [
            'totalAmount' => $transformed->sum('amount'),
            'totalReceived' => $transformed->sum('received'),
            'totalPending' => $transformed->sum('pending'),
        ];

        return response()->json([
            'data' => $transformed,
            'summary' => $summary
        ]);
    }






    public function paymentHistorydailydata($id)
    {
        // ✅ Load daily data with relations
        $dailyData = DailyData::with([
            'paymentHistories',
            'collectedBy:id,fullname' // load collector name only
        ])->findOrFail($id);

        // ✅ Get payable amount (first payment entry amount)
        $payableAmount = PaymentHistory::where('daily_id', $id)
            ->orderBy('created_at', 'asc')
            ->value('amount') ?? 0;

        // ✅ Payment history list
        $payments = $dailyData->paymentHistories()
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ Calculate remaining
        $paidTotal = $payments->sum('paid_amount');
        $remaining = $payableAmount - $paidTotal;

        return response()->json([
            'payable' => $payableAmount,
            'remaining' => $remaining,

            // 👇 NEW DATA
            'comment' => $dailyData->comment,
            'collected_by' => optional($dailyData->collectedBy)->fullname,

            'history' => $payments
        ]);
    }

    public function paymentHistory($patientId)
    {
        // Get all daily_data IDs for this patient
        $dailyDataIds = DailyData::where('patient_id', $patientId)->pluck('id');

        // Fetch all payments linked to these daily_data IDs, eager-load dailyData
        $payments = PaymentHistory::with('dailyData.collectedBy')
            ->whereIn('daily_id', $dailyDataIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Map payments to include collector's name
        $history = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'daily_id' => $payment->daily_id,
                'amount' => $payment->amount,
                'paid_amount' => $payment->paid_amount,
                'remain_amount' => $payment->remain_amount,
                'payment_mode' => $payment->payment_mode,
                'paid_type' => $payment->paid_type,
                'collected_by' => $payment->dailyData && $payment->dailyData->collectedBy
                    ? $payment->dailyData->collectedBy->fullname
                    : null,
                'created_at' => $payment->created_at->format('Y-m-d'),
            ];
        });

        $totalPayable = $payments->sum('amount');
        $totalPaid = $payments->sum('paid_amount');
        $remaining = $totalPayable - $totalPaid;

        return response()->json([
            'total_payable' => $totalPayable,
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'history' => $history
        ]);
    }





    public function makePayment(Request $request, $id)
    {
        $daily = DailyData::findOrFail($id);

        $firstPayment = PaymentHistory::where('daily_id', $id)->orderBy('id')->first();

        // Base rules
        $rules = [
            'paid_amount' => 'required|numeric|min:1',
            'payment_mode' => 'required|string|in:Cash,Online,Cash+Online',
        ];

        // If no previous payment, require total
        if (!$firstPayment) {
            $rules['total_amount'] = 'required|numeric|min:1';
        }

        // Extra rules only if Cash+Online selected
        if ($request->payment_mode === 'Cash+Online') {
            $rules = array_merge($rules, [
                'cash_amount' => 'nullable|numeric|min:0',
                'online_amount' => 'nullable|numeric|min:0',
            ]);
        }

        // ✅ Validate request
        $validated = $request->validate($rules);

        // Calculate bill & remaining
        $totalBill = $firstPayment ? $firstPayment->amount : $validated['total_amount'];
        $existingPayments = PaymentHistory::where('daily_id', $id)->sum('paid_amount');
        $remaining = $totalBill - $existingPayments;

        // Handle amounts
        $cashAmount = null;
        $onlineAmount = null;

        if ($validated['payment_mode'] === 'Cash') {
            $cashAmount = $validated['paid_amount'];
        } elseif ($validated['payment_mode'] === 'Online') {
            $onlineAmount = $validated['paid_amount'];
        } elseif ($validated['payment_mode'] === 'Cash+Online') {
            $cashAmount = $validated['cash_amount'] ?? 0;
            $onlineAmount = $validated['online_amount'] ?? 0;

            // 🔥 Auto-set paid_amount = cash + online
            $validated['paid_amount'] = $cashAmount + $onlineAmount;
        }

        // Prevent overpayment
        if ($validated['paid_amount'] > $remaining) {
            return response()->json(['message' => 'Paid amount exceeds remaining balance.'], 422);
        }

        $paidType = ($validated['paid_amount'] == $remaining) ? 'fully' : 'partial';

        // Store payment
        PaymentHistory::create([
            'user_id' => auth()->id(),
            'daily_id' => $id,
            'amount' => $totalBill,
            'payment_mode' => $validated['payment_mode'],
            'cash_amount' => $cashAmount,
            'online_amount' => $onlineAmount,
            'paid_type' => $paidType,
            'paid_amount' => $validated['paid_amount'],
            'remain_amount' => $remaining - $validated['paid_amount'],
        ]);

        // Update daily record
        $newRemaining = $remaining - $validated['paid_amount'];
        $daily->update([
            'remain_amount' => $newRemaining,
            'status' => $newRemaining == 0 ? 'paid' : $daily->status
        ]);

        return response()->json(['message' => 'Payment recorded successfully.']);
    }




    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'required|exists:treatments,id',
            'date' => 'required|date',
            'staff_id' => 'required|exists:user,id',
            'branch_id' => 'required|exists:branches,id', // ✅ validate branch
            'payment' => 'required|numeric|min:0',
            'payment_mode' => 'required|string|in:cash,online,cash+online',
            'paid_type' => 'required|string|in:fully,partial',
            'cash_amount' => 'nullable|numeric|min:0',
            'online_amount' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate cash+online fully paid
        if ($request->payment_mode === 'cash+online' && $request->paid_type === 'fully') {
            $sum = ($request->cash_amount ?? 0) + ($request->online_amount ?? 0);
            if ($sum != $request->payment) {
                return response()->json([
                    'status' => false,
                    'message' => "Cash + Online must equal payable amount ({$request->payment})",
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $totalPayment = $request->payment;

            $cashAmount = 0;
            $onlineAmount = 0;
            $paidAmount = 0;
            $remainAmount = 0;
            $status = 'pending';

            if ($request->paid_type === 'fully') {
                $paidAmount = $totalPayment;
                $remainAmount = 0;
                $status = 'paid';

                if ($request->payment_mode === 'cash')
                    $cashAmount = $totalPayment;
                elseif ($request->payment_mode === 'online')
                    $onlineAmount = $totalPayment;
                elseif ($request->payment_mode === 'cash+online') {
                    $cashAmount = $request->cash_amount ?? 0;
                    $onlineAmount = $request->online_amount ?? 0;
                }
            } else { // Partial payment
                if ($request->payment_mode === 'cash')
                    $cashAmount = $request->amount_paid ?? 0;
                elseif ($request->payment_mode === 'online')
                    $onlineAmount = $request->amount_paid ?? 0;
                elseif ($request->payment_mode === 'cash+online') {
                    $cashAmount = $request->cash_amount ?? 0;
                    $onlineAmount = $request->online_amount ?? 0;
                }

                $paidAmount = $cashAmount + $onlineAmount;
                $remainAmount = max($totalPayment - $paidAmount, 0);
                $status = $remainAmount > 0 ? 'pending' : 'paid';
            }

            // Save daily data with branch_id
            $dailyData = DailyData::create([
                'user_id' => auth()->id(),
                'patient_id' => $request->patient_id,
                'treatment_id' => $request->treatment_id,
                'date' => $request->date,
                'branch_id' => $request->branch_id, // ✅ store branch
                'remain_amount' => $remainAmount,
                'status' => $status,
                'collect_by_id' => $request->staff_id,
                'comment' => $request->comment,
            ]);

            // Save payment history
            $paymentHistory = PaymentHistory::create([
                'user_id' => auth()->id(),
                'daily_id' => $dailyData->id,
                'branch_id' => $request->branch_id, // optional
                'amount' => $totalPayment,
                'payment_mode' => $request->payment_mode,
                'paid_type' => $request->paid_type,
                'paid_amount' => $paidAmount,
                'remain_amount' => $remainAmount,
                'cash_amount' => $cashAmount,
                'online_amount' => $onlineAmount,
            ]);
            $staff = User::find($request->staff_id);
            $patient = Patients::find($request->patient_id);
            $creator = User::find(auth()->id());
            if ($staff && $patient) {
                // ✅ Store notification in DB
                Notification::store(
                    "Payment of ₹{$paidAmount} received from patient '{$patient->fullname}' by {$creator->fullname}.",
                    $staff->id,
                    $dailyData->id,
                    'payment',
                    $creator->id,
                    $patient->id
                );

                // ✅ Send FCM Push Notification
                if (!empty($staff->fcm_token)) {
                    $fcm = new FCMService();
                    try {
                        $fcm->sendNotification(
                            $staff->fcm_token,
                            '💰 Payment Received',
                            "Dear {$staff->fullname}, payment of ₹{$paidAmount} has been collected from {$patient->fullname} by {$creator->fullname}.",
                            [
                                'daily_id' => (string) $dailyData->id,
                                'type' => 'payment',
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::warning("⚠️ FCM Push failed for Staff ID {$staff->id}: {$e->getMessage()}");
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Daily data and payment saved successfully',
                'daily_data' => $dailyData,
                'payment_history' => $paymentHistory,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to save data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



























    // 🟢 Show Specific Record
    public function show($id)
    {
        $data = DailyData::with(['user', 'patient', 'treatment', 'collectedBy'])->findOrFail($id);
        return response()->json(['data' => $data]);
    }

    // 🟢 Update
    public function update(Request $request, $id)
    {
        $data = DailyData::findOrFail($id);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'required|exists:treatments,id',
            'date' => 'required|date',

            'collect_by_id' => 'required|exists:user,id',
            'comment' => 'string|max:1000',
        ]);

        $data->update($request->only([
            'patient_id',
            'treatment_id',
            'date',
            'amount',
            'status',
            'collect_by_id',
            'comment',
        ]));

        return response()->json(['message' => 'Daily data updated.', 'data' => $data]);
    }

    // 🟢 Delete
    public function destroy($id)
    {
        $data = DailyData::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Daily data deleted.']);
    }
}
