<?php

namespace App\Http\Controllers;

use App\Models\HomeAdvice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class HomeAdviceController extends Controller
{
    public function index()
    {
        $homeAdvices = HomeAdvice::all();
        return view('homeadvice.index', compact('homeAdvices'));
    }

    public function create()
    {
        return view('homeadvice.create');
    }


  
    public function exportHomeAdvice(Request $request)
    {
        $branchId = $request->get('branch_id');

        $query = HomeAdvice::orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $homeAdvices = $query->get();

        $csvData = [];
        $csvData[] = [
            'HomeAdvice ID',
            'Branch ID',
            'Branch Name',
            'Template Name',
            'Title(s)',
            'Description(s)',
        ];
              $sr = 1;

        foreach ($homeAdvices as $advice) {
            $csvData[] = [
               $sr++,
                $advice->branch_id ?? 'N/A',
                $advice->branch->name ?? 'N/A',
                $advice->template_name ?? 'N/A',
                is_array($advice->title) ? implode(" | ", $advice->title) : $advice->title,
                is_array($advice->description) ? implode(" | ", $advice->description) : $advice->description,
                // is_array($advice->image) ? implode(" | ", $advice->image) : $advice->image,
                // $advice->created_at ? $advice->created_at->format('Y-m-d H:i:s') : '',
                // $advice->updated_at ? $advice->updated_at->format('Y-m-d H:i:s') : '',
            ];
        }

        $filename = 'home_advice_export_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
