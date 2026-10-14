<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Patients;
use App\Models\Treatment;
use App\Models\TreatmentBooking;
use App\Models\TreatmentPaymentHistory;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Setting;
use App\Services\FCMService;
use Barryvdh\DomPDF\Facade\Pdf;

class TreatmentBookingController extends Controller
{


    public function treatmentBookingPdf(Request $request, $id)
    {
        try {
            // ✅ Load booking with relations
            $booking = \App\Models\TreatmentBooking::with([
                'patient',
                'treatment',
                'paymentHistory.user'
            ])->findOrFail($id);

            // ✅ Fetch clinic details
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
                'booking' => $booking,
                'patient' => $booking->patient,
                'treatment' => $booking->treatment,
                'history' => $booking->paymentHistory,
                'clinic_logo' => $clinic_logo_path,
                'clinic_name' => $settings['clinic_name'] ?? 'Sunshine Clinic',
                'clinic_address' => $settings['clinic_address'] ?? '123 Health Street',
                'clinic_phone' => $settings['clinic_phone'] ?? '1234567890',
                'clinic_email' => $settings['clinic_email'] ?? 'clinic@example.com',
            ];

            // ✅ Generate PDF
            $pdf = Pdf::loadView('treatment_booking.booking_pdf', $data)
                ->setPaper('A4', 'portrait');

            $folder = public_path('storage/treatment-bookings/');
            $filename = "TreatmentBooking_{$booking->id}.pdf";
            $path = $folder . $filename;

            // ✅ Ensure folder exists
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // ✅ Save PDF directly in /public/storage/treatment-bookings/
            $pdf->save($path);

            // ✅ Public URL (direct)
            $fileUrl = url('public/storage/treatment-bookings/' . $filename);

            // 👉 If Postman/API → return JSON
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'status' => true,
                    'message' => 'Treatment booking PDF generated successfully.',
                    'file_url' => $fileUrl,
                    'file_name' => $filename,
                ], 200);
            }

            // 👉 If Browser → download PDF directly
            return response()->download($path, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Treatment Booking PDF generation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    public function scopeExpired($query)
    {
        return $query->whereDate('payment_date', '<', Carbon::today()); // expired based on payment date

    }






    public function treatmentbookingexportCsv(Request $request)
    {
        $query = TreatmentBooking::with(['patient', 'treatment', 'paymentHistory', 'branch'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $user = Auth::user();
        if ($user && in_array($user->role->name, ['Staff', 'Doctor'])) {
            $query->whereHas('treatment', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }

        $bookings = $query->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No treatment bookings found to export.'
            ]);
        }

        $filename = 'treatment_bookings_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        // CSV Header
        fputcsv($file, [
            'Branch ID',
            'Branch Name',
            'Patient Name',
            'Treatment Name',
            'Plan',
            'Amount',
            'Payment Mode',
            'Paid Type',
            'Cash',
            'Online',
            'Remain Amount',
            'Booking Status',
            'Payment Date',
            'Booking Created At',
            'Booking Updated At'
        ]);

        foreach ($bookings as $booking) {
            if ($booking->paymentHistory->isEmpty()) {

                // If no payment history → one blank row
                fputcsv($file, [
                    $booking->branch_id,
                    $booking->branch->name ?? 'N/A',
                    $booking->patient->fullname ?? 'N/A',
                    $booking->treatment->name ?? 'N/A',
                    $booking->plan ?? 'N/A',
                    '0.00',
                    'N/A',
                    'N/A',
                    '0.00',
                    '0.00',
                    $booking->remain_amount ?? '0.00',
                    $booking->status ?? 'N/A',
                    'N/A',
                    $booking->created_at?->format('d-M-Y h:i A') ?? 'N/A',
                    $booking->updated_at?->format('d-M-Y h:i A') ?? 'N/A',
                ]);
            } else {

                // Multiple payment rows
                foreach ($booking->paymentHistory as $payment) {
                    fputcsv($file, [
                        $booking->branch_id,
                        $booking->branch->name ?? 'N/A',
                        $booking->patient->fullname ?? 'N/A',
                        $booking->treatment->name ?? 'N/A',
                        $booking->plan ?? 'N/A',
                        $payment->amount ?? '0.00',
                        $payment->payment_mode ?? 'N/A',
                        $payment->paid_type ?? 'N/A',
                        $payment->cash ?? '0.00',
                        $payment->online ?? '0.00',
                        $payment->remain_amount ?? '0.00',
                        $booking->status ?? 'N/A',
                        $payment->created_at?->format('d-M-Y h:i A') ?? 'N/A',
                        $booking->created_at?->format('d-M-Y h:i A') ?? 'N/A',
                        $booking->updated_at?->format('d-M-Y h:i A') ?? 'N/A',
                    ]);
                }
            }
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Treatment bookings exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }



    public function treatmentHistory($id)
    {
        $records = TreatmentBooking::with(['patient', 'treatment'])
            ->where('patient_id', $id)
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($item) {
                // Decode machine_ids JSON
                $machineIds = json_decode($item->machine_id, true);

                // Fetch machine names
                $machineNames = \App\Models\Machine::whereIn('id', $machineIds ?? [])->pluck('name')->toArray();

                // Return transformed record
                return [
                    'id' => $item->id,
                    'patient_name' => optional($item->patient)->fullname,
                    'treatment_name' => optional($item->treatment)->name,
                    'machine_names' => $machineNames, // array of machine names
                    'payment_date' => $item->payment_date,
                    'plan' => $item->plan,
                    'status' => $item->status,
                ];
            });

        return response()->json($records);
    }



    public function paymentHistory($id)
    {
        $booking = TreatmentBooking::findOrFail($id);

        $histories = TreatmentPaymentHistory::where('treatment_booking_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedHistory = [];

        foreach ($histories as $history) {
            if ($history->payment_mode === 'cash+online') {
                // Add cash row if > 0
                if ($history->cash > 0) {
                    $formattedHistory[] = [
                        'id' => $history->id,
                        'treatment_booking_id' => $history->treatment_booking_id,
                        'payment_type' => 'Cash',
                        'amount' => (float) $history->cash,      // split amount
                        'total_amount' => (float) $history->amount, // ✅ full DB amount
                        'paid_type' => $history->paid_type,
                        'created_at' => $history->created_at->format('d M Y, h:i a')
                    ];
                }

                // Add online row if > 0
                if ($history->online > 0) {
                    $formattedHistory[] = [
                        'id' => $history->id,
                        'treatment_booking_id' => $history->treatment_booking_id,
                        'payment_type' => 'Online',
                        'amount' => (float) $history->online,    // split amount
                        'total_amount' => (float) $history->amount, // ✅ full DB amount
                        'paid_type' => $history->paid_type,
                        'created_at' => $history->created_at->format('d M Y, h:i a')
                    ];
                }
            } else {
                // Normal case (single mode)
                $formattedHistory[] = [
                    'id' => $history->id,
                    'treatment_booking_id' => $history->treatment_booking_id,
                    'payment_type' => ucfirst($history->payment_mode),
                    'amount' => (float) $history->paid_amount,
                    'total_amount' => (float) $history->amount, // ✅ full DB amount
                    'paid_type' => $history->paid_type,
                    'created_at' => $history->created_at->format('d M Y, h:i a')
                ];
            }
        }

        return response()->json([
            'status' => true,
            'history' => $formattedHistory,
            'remaining' => (float) $booking->remain_amount,
            'payable' => (float) $booking->remain_amount
        ]);
    }


    public function makePayment(Request $request, $id)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:1',
            'payment_mode' => 'required|string|in:cash,online,cash+online',
            'cash' => 'nullable|numeric|min:0',
            'online' => 'nullable|numeric|min:0',
        ]);

        $booking = TreatmentBooking::findOrFail($id);
        $remainingBefore = $booking->remain_amount;

        if ($remainingBefore <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'No pending amount to pay.'
            ], 400);
        }

        // Determine actual paid amounts based on payment_mode
        $cashAmount = 0;
        $onlineAmount = 0;
        $paidAmount = 0;

        switch (strtolower($request->payment_mode)) {
            case 'cash':
                $cashAmount = min($request->paid_amount, $remainingBefore);
                $paidAmount = $cashAmount;
                break;
            case 'online':
                $onlineAmount = min($request->paid_amount, $remainingBefore);
                $paidAmount = $onlineAmount;
                break;
            case 'cash+online':
                $cashAmount = $request->cash ?? 0;
                $onlineAmount = $request->online ?? 0;
                $paidAmount = min($cashAmount + $onlineAmount, $remainingBefore);
                break;
        }

        $remainingAfter = $remainingBefore - $paidAmount;
        $paidType = $remainingAfter == 0 ? 'fully' : 'partial';

        // Record payment history
        TreatmentPaymentHistory::create([
            'user_id' => Auth::id(),
            'treatment_booking_id' => $booking->id,
            'amount' => $booking->treatment->price,
            'payment_mode' => $request->payment_mode,
            'paid_type' => $paidType,
            'paid_amount' => $paidAmount,
            'cash' => $cashAmount,
            'online' => $onlineAmount,
            'remain_amount' => $remainingAfter,
        ]);

        // Update booking
        $booking->remain_amount = $remainingAfter;
        $booking->status = $remainingAfter == 0 ? 'paid' : 'pending';
        $booking->payment_date = now();
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'Payment recorded successfully',
            'remaining' => $remainingAfter,
            'status_updated' => $booking->status
        ], 200);
    }






    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TreatmentBooking::with([
            'patient',
            'treatment',
            'machine',
            'paymentHistory' => function ($q) use ($request) {
                if ($request->filled('branch_id')) {
                    $q->where('branch_id', $request->branch_id);
                }
            }
        ])->orderBy('payment_date', 'desc');

        // Filter by branch_id if provided (from localStorage)
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Doctor/Staff role filter
        if (in_array($user->role->name, ['Staff', 'Doctor'])) {
            $query->whereHas('treatment', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }

        // Additional filters
        if ($request->filled('year')) {
            $query->whereYear('payment_date', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('payment_date', $request->month);
        }
        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

        $transformBooking = function ($item) {
            $totalPaid = $item->paymentHistory->sum('paid_amount');
            $totalPending = $item->paymentHistory->sum('remain_amount');
            $totalAmount = optional($item->paymentHistory->first())->amount ?? 0;
            return [
                'id' => $item->id,
                'patient_id' => $item->patient_id,
                'patient_name' => optional($item->patient)->fullname,
                'treatment_name' => optional($item->treatment)->name,
                'machine_name' => $item->machine_details->pluck('name')->implode(', '),
                'payment_date' => $item->payment_date,
                'plan' => $item->plan,
                'amount' => $totalAmount,
                'received' => $totalPaid,
                'pending' => $item->remain_amount,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'payment_histories' => $item->paymentHistory->map(function ($ph) {
                    return [
                        'id' => $ph->id,
                        'amount' => $ph->amount,
                        'payment_mode' => $ph->payment_mode,
                        'paid_type' => $ph->paid_type,
                        'cash_amount' => $ph->cash,
                        'online_amount' => $ph->online,
                        'remain_amount' => $ph->remain_amount,
                        'paid_amount' => $ph->paid_amount,
                        'created_at' => $ph->created_at,
                    ];
                }),
            ];
        };

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
                    $q->where('payment_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('plan', 'like', '%' . $searchValue . '%')
                        ->orWhere('status', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('patient', function ($p) use ($searchValue) {
                            $p->where('fullname', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('treatment', function ($t) use ($searchValue) {
                            $t->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            if ($hasDataTable && (int) $request->input('length') === -1) {
                $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
            }

            $bookings = $filteredQuery->forPage($page, $perPage)->get();
            $transformed = $bookings->map($transformBooking);

            $summaryBookings = (clone $filteredQuery)->get();
            $summaryTransformed = $summaryBookings->map($transformBooking);
            $summary = [
                'totalAmount' => $summaryTransformed->sum('amount'),
                'totalReceived' => $summaryTransformed->sum('received'),
                'totalPending' => $summaryTransformed->sum('pending'),
            ];

            $lastPage = (int) ceil($recordsFiltered / $perPage);

            return response()->json([
                'data' => $transformed,
                'summary' => $summary,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage > 0 ? $lastPage : 1,
                    'per_page' => $perPage,
                    'total' => $recordsFiltered,
                ],
            ]);
        }

        $bookings = $query->get();

        // Transform data for frontend
        $transformed = $bookings->map($transformBooking);

        $summary = [
            'totalAmount' => $transformed->sum('amount'),
            'totalReceived' => $transformed->sum('received'),
            'totalPending' => $transformed->sum('pending'),
        ];

        return response()->json([
            'data' => $transformed,
            'summary' => $summary,
        ]);
    }








    public function store(Request $request, SmsService $smsService)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            //  'machine_id' => 'nullable|exists:machines,id',
            'machine_id' => 'nullable|array',
            'machine_id.*' => 'exists:machines,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|string|in:cash,online,cash+online',
            'paid_type' => 'required|in:fully,partial',
            'paid_amount' => 'nullable|numeric|min:0',
            'cash' => 'nullable|numeric|min:0',
            'online' => 'nullable|numeric|min:0',
            'plan' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id', // ✅ branch validation
        ]);

        try {
            $totalAmount = $request->amount;
            $cashAmount = 0;
            $onlineAmount = 0;
            $paidAmount = 0;

            // ✅ Payment split logic
            if ($request->paid_type === 'fully') {
                switch ($request->payment_mode) {
                    case 'cash':
                        $cashAmount = $totalAmount;
                        $paidAmount = $cashAmount;
                        break;
                    case 'online':
                        $onlineAmount = $totalAmount;
                        $paidAmount = $onlineAmount;
                        break;
                    case 'cash+online':
                        $cashAmount = $request->cash ?? 0;
                        $onlineAmount = $request->online ?? 0;
                        $paidAmount = $cashAmount + $onlineAmount;
                        break;
                }
            } else { // partial
                $cashAmount = $request->cash ?? 0;
                $onlineAmount = $request->online ?? 0;
                $paidAmount = $request->paid_amount ?? ($cashAmount + $onlineAmount);
            }

            $remainAmount = $totalAmount - $paidAmount;
            $status = $remainAmount > 0 ? 'pending' : 'paid';

            // ✅ Create treatment booking with branch_id
            $booking = TreatmentBooking::create([
                'patient_id' => $request->patient_id,
                'treatment_id' => $request->treatment_id,
                'machine_id'   => $request->machine_id ? json_encode($request->machine_id) : null,
                'plan' => $request->plan,
                'remain_amount' => $remainAmount,
                'status' => $status,
                'payment_date' => $request->payment_date,
                'branch_id' => $request->branch_id, // ✅ store branch
            ]);

            // ✅ Create payment history with branch_id
            $paymentHistory = TreatmentPaymentHistory::create([
                'user_id' => auth()->id(),
                'treatment_booking_id' => $booking->id,
                'branch_id' => $request->branch_id, // ✅ store branch
                'amount' => $totalAmount,
                'payment_mode' => $request->payment_mode,
                'paid_type' => $request->paid_type,
                'paid_amount' => $paidAmount,
                'cash' => $cashAmount,
                'online' => $onlineAmount,
                'remain_amount' => $remainAmount,
            ]);


            $patient = Patients::find($request->patient_id);
            $treatment = Treatment::find($request->treatment_id);
            $patientFullName = $patient->fullname ?? '';
            $treatmentName = $treatment->name ?? 'Treatment';
            $patientUserId = $patient?->login_patient_id;
            $paymentDate = \Carbon\Carbon::parse($request->payment_date)->format('d/m/Y');

            $messagePatient = "Dear {$patientFullName}, your payment of ₹{$paidAmount} for {$treatmentName} has been recorded on {$paymentDate}. Remaining balance: ₹{$remainAmount}.";


            // ------------------ Staff notification for pending payment ------------------
            if ($remainAmount > 0) {
                // Fetch staff for branch (you can filter by role_id if needed)
                $staffUsers = User::where('branch_id', $request->branch_id)
                    ->whereIn('role_id', [2, 3]) // adjust roles for staff/cashier
                    ->get();

                foreach ($staffUsers as $staff) {
                    $staffName = $staff->fullname ?? $staff->name;
                    $messageStaff = "Treatment Booking Payment of ₹{$paidAmount} for patient {$patientFullName} for treatment: {$treatmentName} Collected by:  " . (auth()->user()->fullname ?? auth()->user()->name) . " Remaining balance: ₹{$remainAmount}.";

                    Notification::store($messageStaff, $staff->id, $booking->id, 'payment_pending', auth()->id(), $patient->id);

                    if (!empty($staff->fcm_token)) {
                        $fcm = new FCMService();
                        try {
                            $fcm->sendNotification(
                                $staff->fcm_token,
                                'Treatment Booking Pending Payment',
                                $messageStaff,
                                ['booking_id' => (string) $booking->id]
                            );
                        } catch (\Exception $e) {
                            Log::warning("⚠️ FCM error for Staff ID {$staff->id}: {$e->getMessage()}");
                        }
                    }
                }
            }



            // if (!empty($patient->phone)) {
            //     $smsService->send_sms($patient->phone, $messageText);
            // }

            return response()->json([
                'status' => true,
                'message' => 'Booking and payment saved successfully.',
                'booking' => $booking,
                'payment_history' => $paymentHistory,
            ], 201);
        } catch (\Exception $e) {
            Log::error(" Payment store error: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to save data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }





    public function show($id)
    {
        $booking = TreatmentBooking::with(['patient', 'treatment', 'paymentHistory'])->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Add machine details to the response
        $booking->machine_details = $booking->machine_details; // uses the accessor

        return response()->json([
            'booking' => $booking
        ]);
    }







    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'treatment_id' => 'required|exists:treatments,id',
            'machine_id' => 'nullable|array',                  // must be array for multiple select
            'machine_id.*' => 'exists:machines,id',
            'payment_date' => 'required|date',
            'plan' => 'required|string',

        ]);

        try {
            $booking = TreatmentBooking::findOrFail($id);


            // Update booking
            $booking->update([
                'patient_id' => $request->patient_id,
                'treatment_id' => $request->treatment_id,
                'plan' => $request->plan,
                'payment_date' => $request->payment_date,
                'machine_id' => $request->machine_id ? json_encode($request->machine_id) : null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Treatment booking updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        TreatmentBooking::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function download($id)
    {
        $booking = TreatmentBooking::with(['patient', 'treatment', 'paymentHistory.user'])->findOrFail($id);

        // Fetch settings as key-value pair
        $settings = (new Setting())->getSettings();

        // Generate PDF using Blade template
        $pdf = Pdf::loadView('treatment_booking.pdf', compact('booking', 'settings'));

        return $pdf->download('treatment-booking-' . $booking->id . '.pdf');
    }

    public function getYears()
    {
        // If your table has created_at
        $years = TreatmentBooking::selectRaw('YEAR(payment_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');



        return response()->json($years);
    }

    public function getFilterPatient(Request $request)
    {
        $query = TreatmentBooking::with('patient')
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('patient', function ($sub) use ($request) {
                    $sub->where('fullname', 'like', '%' . $request->search . '%');
                });
            })
            ->select('patient_id')
            ->distinct();

        $patients = $query->get()->map(function ($booking) {
            return [
                'id' => $booking->patient->id ?? null,
                'fullname' => $booking->patient->fullname ?? '',
            ];
        })->filter(fn($p) => $p['id'] !== null);

        return response()->json(['patients' => $patients->values()]);
    }
}
