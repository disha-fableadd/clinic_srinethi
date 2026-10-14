<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyData;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\Response;

class DailyDataController extends Controller
{
    public function index()
    {
        return view('daily_data.index');
    }
    public function create()
    {
        return view('daily_data.create');
    }



    public function exportDailyData($date)
    {
        // Get all daily data for that date
        $dailyRecords = DailyData::with(['patient', 'treatment', 'collectedBy'])
            ->whereDate('date', $date)
            ->get();

        $csvData = [];
        $csvData[] = [
            'Patient Name',
            'Treatment',
            'Payment Date',
            'Total Amount',
            'Paid Amount',
            'Remain Amount',
            'Payment Mode',
            'Paid Type',
            'Status',
            'Collected By'





        ];

        foreach ($dailyRecords as $record) {
            // Get payment history for this daily entry
            $payments = PaymentHistory::where('daily_id', $record->id)->get();

            if ($payments->isEmpty()) {
                // If no payment history, just push daily data
                $csvData[] = [
                    $record->patient->fullname ?? 'N/A',
                    $record->treatment->name ?? 'N/A',

                    $record->amount ?? 0,
                    $record->remain_amount ?? 0,
                    ucfirst($record->status),
                    $record->collectedBy->fullname ?? 'N/A',
                    '-',
                    '-',
                    '-',
                    '-',
                    '-'
                ];
            } else {
                foreach ($payments as $payment) {
                    $csvData[] = [
                        $record->patient->fullname ?? 'N/A',
                        $record->treatment->name ?? 'N/A',
                        $payment->created_at ? $payment->created_at->format('d-M-Y h:i A') : 'N/A',
                        $payment->amount ?? 0,
                        $payment->paid_amount ?? 0,
                        $payment->remain_amount ?? 0,
                        $payment->payment_mode ?? 'N/A',
                        $payment->paid_type ?? 'N/A',
                        ucfirst($record->status),
                        $record->collectedBy->fullname ?? 'N/A'

                    ];
                }
            }
        }

        // Prepare CSV
        $filename = 'daily_data_export_' . $date . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return Response::make($contents, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }
}
