<?php

namespace App\Http\Controllers;

use App\Models\PatientDischargeDets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DischargeController extends Controller
{
    public function index()
    {
        return view('discharge.index');
    }
    public function create()
    {
        return view('discharge.create');
    }

    public function exportDischarges(Request $request)
    {
        $branchId = $request->query('branch_id'); // get branch id from query string

        $query = PatientDischargeDets::with(['patient', 'doctor', 'treatment']);

        if ($branchId) {
            $query->where('branch_id', $branchId); // filter branch wise
        }

        $discharges = $query->get();

        $csvData = [];
        $csvData[] = [
            'Patient Name',
            'Doctor Name',
            'Treatment Name',
            'Room Number',
            'Bed Number',
            'Admit Date',
            'Discharge Date',
            'Total Bill',
            'Amount Paid',
            'Payment Status',
            'Discharge Note',
            'GST Option',
            'Product GST',
            'Created At',
            'Updated At'
        ];

        foreach ($discharges as $discharge) {
            $csvData[] = [
                $discharge->patient->fullname ?? 'N/A',
                $discharge->doctor->fullname ?? 'N/A',
                $discharge->treatment->name ?? 'N/A',
                $discharge->room_number,
                $discharge->bed_number,
                $discharge->admit_date,
                $discharge->discharge_date,
                $discharge->total_bill,
                $discharge->amount_paid,
                $discharge->payment_status,
                $discharge->discharge_note,
                $discharge->gst_option,
                $discharge->product_gst ? json_encode($discharge->product_gst) : '',
                $discharge->created_at ? $discharge->created_at->format('d-M-Y h:i A') : 'N/A',
                $discharge->updated_at ? $discharge->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $filename = 'discharge_details_export_' . now()->format('Ymd_His') . '.csv';
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
