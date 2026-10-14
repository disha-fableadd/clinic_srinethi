<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function index()
    {
        
    
        return view('category.index', );
    }
    
    
    public function create()
    {
        return view('category.create');
    }






public function export(Request $request)
{
    $branchId = $request->query('branch_id'); // get branch_id from query

    // Load categories branch-wise
    $categories = Categories::when($branchId, function ($query) use ($branchId) {
        return $query->where('branch_id', $branchId);
    })->get();

    $filename = "categories_export_" . date('Y-m-d_H-i-s') . ".csv";

    $headers = [
        "Content-type" => "text/csv; charset=utf-8",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['ID', 'Name', 'Description', 'Created At'];

    $callback = function () use ($categories, $columns) {
        $file = fopen('php://output', 'w');

        // Ensure UTF-8 encoding
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, $columns);
          $sr = 1;

        foreach ($categories as $category) {
            fputcsv($file, [
               $sr++,
                $category->name,
                $category->description,
                $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : 'N/A',
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}



}
