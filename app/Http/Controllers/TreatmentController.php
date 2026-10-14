<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class TreatmentController extends Controller
{
    public function index()
    {
        return view('treatment.index');
    }
    public function create()
    {
        return view('treatment.create');
    }






   


    public function exportTreatments(Request $request)
    {
        $branchId = $request->get('branch_id');

        $query = Treatment::with(['doctor', 'branch']); // ✅ make sure Treatment has branch() relation

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $treatments = $query->get();

        $csvData = [];
        $csvData[] = [
            'Branch ID',
            'Branch Name',
            'Doctor ID',
            'Doctor Name',
            'Treatment Name',
            'Price',
            'Description',
            'GST Option',
            'Product GST',
            'Created At',
            'Updated At'
        ];

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

            $csvData[] = [
                $treatment->branch_id,
                $treatment->branch->name ?? 'N/A',
                $treatment->doctor_id,
                $treatment->doctor->fullname ?? 'N/A',
                $treatment->name,
                $treatment->price,
                $treatment->description,
                $treatment->gst_option ?? 'N/A',
                $gstDetailsString ?: 'N/A',
                $treatment->created_at ? $treatment->created_at->format('d-M-Y h:i A') : 'N/A',
                $treatment->updated_at ? $treatment->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        // ✅ filename with branch name if filter applied
        $branchName = $branchId ? ($treatments->first()->branch->name ?? 'branch') : 'all';
        $filename = 'treatments_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
