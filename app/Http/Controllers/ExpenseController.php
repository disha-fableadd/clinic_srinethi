<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    //
    public function index()
    {
        return view('expense.index');
    }
    public function create()
    {
        return view('expense.create');
    }

   
    public function export(Request $request)
    {
        $branchId = $request->get('branch_id');

        $query = Expense::with(['user', 'branch'])
            ->orderBy('date_time', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $expenses = $query->get();

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

        $csvData = [];
        // CSV Header
        $header = [
            'Branch ID',
            'Branch Name',
            'User Name',
            'Date & Time',
            'Amount',
            'Service',
        ];
        
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Comment';
        }

        $header = array_merge($header, ['Created At', 'Updated At']);
        $csvData[] = $header;

        foreach ($expenses as $expense) {
            $row = [
                $expense->branch_id,
                $expense->branch->name ?? 'N/A',
                $expense->user->fullname ?? 'N/A',
                $expense->date_time ? \Carbon\Carbon::parse($expense->date_time)->format('d-M-Y h:i A') : 'N/A',
                number_format($expense->amount, 2),
                $expense->service ?? 'N/A',
            ];
            
            if ($currentProjectTypeId !== 3) {
                $row[] = $expense->comment ?? 'N/A';
            }

            $row[] = $expense->created_at?->format('d-M-Y h:i A') ?? 'N/A';
            $row[] = $expense->updated_at?->format('d-M-Y h:i A') ?? 'N/A';

            $csvData[] = $row;
        }

        $branchName = $branchId ? ($expenses->first()->branch->name ?? 'branch') : 'all';
        $filename = 'expenses_export_' . str_replace(' ', '_', strtolower($branchName)) . '_' . now()->format('Ymd_His') . '.csv';

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
