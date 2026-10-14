<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Patients;
use App\Models\Treatment;
use App\Models\TreatmentBooking;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TreatmentBookingController extends Controller
{
    //
    public function create()
    {
        $patients = Patients::all();
        $treatments = Treatment::all();
        return view('treatment_booking.create', compact('patients', 'treatments'));
    }
    public function index()
    {
        $bookings = TreatmentBooking::with(['patient', 'treatment'])->latest()->paginate(10);
        return view('treatment_booking.index', compact('bookings'));
    }

   


    public function exportTreatmentBooking(Request $request)
    {
        $branchId = $request->get('branch_id');

        $query = \App\Models\TreatmentBooking::with(['patient', 'treatment', 'paymentHistory', 'branch'])
            ->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $bookings = $query->get();

        $csvData = [];
        // CSV Header
        $csvData[] = [
            'Branch ID',
            'Branch Name',
            'Patient Name',
            'Treatment',
            'Plan',
            'Payment Amount',
            'Payment Mode',
            'Paid Type',
            'Cash',
            'Online',
            'Pending',
            'Payment Status',
            'Payment Date',
            'Created At',
            'Updated At'
        ];

        foreach ($bookings as $booking) {
            if ($booking->paymentHistory->isEmpty()) {
                $csvData[] = [
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
                    $booking->payment_date ? \Carbon\Carbon::parse($booking->payment_date)->format('d-M-Y h:i A') : 'N/A',
                    $booking->created_at?->format('d-M-Y h:i A') ?? 'N/A',
                    $booking->updated_at?->format('d-M-Y h:i A') ?? 'N/A',
                ];
            } else {
                foreach ($booking->paymentHistory as $payment) {
                    $csvData[] = [
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
                    ];
                }
            }
        }

        $branchName = $branchId ? ($bookings->first()->branch->name ?? 'branch') : 'all';
        $filename = 'treatment_booking_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return response($contents, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }
}
