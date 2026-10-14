<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class EmailController extends Controller
{
    public function index()
    {
        return view('email.index');
    }
    public function create()
    {
        return view('email.create');
    }


   





  
    
        public function exportTemplates(Request $request)
{
    $branchId = $request->query('branch_id'); // Get branch_id from query string

    // Load templates, filter by branch if branch_id is provided
    $templates = EmailTemplate::with('defaultTemplate')
        ->when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
        ->get();

    $csvData = [];
    $csvData[] = ['ID', 'User ID', 'Template Name', 'Default Template Name', 'Status', 'Image 1', 'Image 2', 'Image 3', 'Created At', 'Updated At'];
          $sr = 1;

    foreach ($templates as $template) {
        $csvData[] = [
          $sr++,
            $template->user_id,
            $template->name,
            $template->defaultTemplate->name ?? 'N/A',
            $template->status,
            $template->img1 ?? '',
            $template->img2 ?? '',
            $template->img3 ?? '',
            $template->created_at ? $template->created_at->format('Y-m-d H:i:s') : 'N/A',
            $template->updated_at ? $template->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    $filename = 'email_templates_' . now()->format('Ymd_His') . '.csv';
    $handle = fopen('php://temp', 'r+');

    // UTF-8 BOM
    fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

    foreach ($csvData as $line) {
        fputcsv($handle, $line);
    }

    rewind($handle);
    $contents = stream_get_contents($handle);
    fclose($handle);

    return Response::make($contents, 200, [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => "attachment; filename=$filename",
    ]);
}





  
    public function exportSendEmails(Request $request)
{
    $branchId = $request->query('branch_id'); // Get branch_id from query param

    $query = SendEmail::with(['user', 'emailtemplate']);

    if ($branchId) {
        $query->where('branch_id', $branchId);
    }

    $logs = $query->get();

    $csvData = [];
    $csvData[] = ['Log ID', 'User Name', 'Send Date', 'Template Name', 'Created At'];

    foreach ($logs as $log) {
        $csvData[] = [
            $log->id,
            $log->user->fullname ?? 'N/A',
            $log->created_at ? $log->created_at->format('d-M-Y h:i A') : 'N/A',
            $log->emailtemplate->name ?? 'N/A',
            $log->created_at ? $log->created_at->format('d-M-Y h:i A') : 'N/A',
        ];
    }

    $filename = 'send_email_logs_' . now()->format('Ymd_His') . '.csv';
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
