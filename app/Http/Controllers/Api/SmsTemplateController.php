<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SendSms;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SmsTemplateController extends Controller
{


    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $templates = SmsTemplate::with('user')->orderBy('name');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $templates->where('branch_id', $branchId);
        }

        $templates = $templates->get();

        if ($templates->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No SMS templates found to export.'
            ]);
        }

        $filename = 'sms_templates_export_' . now()->format('Ymd_His') . '.csv';
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
            'User Name',
            'Template Name',
            'Content',
            'Status',
            'Created At'
        ]);

        $sr = 1;
        foreach ($templates as $template) {
            fputcsv($file, [
                $sr++,
                $template->user->fullname ?? '',
                $template->name,
                $template->content,
                $template->status,
                $template->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'SMS templates exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }

    public function send_sms_exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $records = SendSms::with(['user', 'template'])->latest();

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $records->where('branch_id', $branchId);
        }

        $records = $records->get();

        if ($records->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No SMS records found to export.'
            ]);
        }

        $filename = 'sent_sms_export_' . now()->format('Ymd_His') . '.csv';
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
            'User Name',
            'SMS Template',
            'Service',
            'Sender ID',
            'Created At'
        ]);

        $sr = 1;
        foreach ($records as $row) {
            fputcsv($file, [
                $sr++,
                $row->user->fullname ?? '',
                $row->template->name ?? '',
                $row->service ?? '',
                $row->sender_id ?? '',
                $row->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Sent SMS exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }





    public function getTemplates(Request $request)
    {
        $branchId = $request->query('branch_id');

        $templates = SmsTemplate::select('id', 'name')
            ->where('status', 'active')
            ->when($branchId, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get();

        return response()->json(['data' => $templates]);
    }

    public function show_details($id)
    {
        $template = SmsTemplate::select('id', 'name', 'content', 'status')
            ->where('id', $id)
            ->firstOrFail();

        return response()->json(['data' => $template]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'content'   => 'required|string',
            'status'    => 'required|in:active,inactive',
            'branch_id' => 'required|integer',
        ]);

        SmsTemplate::create([
            'user_id'   => Auth::id(),
            'branch_id' => $request->branch_id,
            'name'      => $request->name,
            'content'   => $request->content,
            'status'    => $request->status,
        ]);

        return response()->json(['success' => 'Template created successfully']);
    }






    public function index(Request $request)
    {
        $branchId = $request->query('branch_id'); // 👈 branch_id comes from frontend

        $templates = SmsTemplate::where('user_id', Auth::id())
            ->when($branchId, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get();

        return response()->json($templates);
    }


    public function destroy($id)
    {
        $template = SmsTemplate::where('id', $id)->where('user_id', Auth::id())->first();

        if ($template) {
            $template->delete();
            return response()->json(['success' => 'Template deleted successfully']);
        } else {
            return response()->json(['error' => 'Template not found or unauthorized'], 401);
        }
    }
    public function show($id)
    {
        $template = SmsTemplate::find($id);
        if ($template) {
            return response()->json($template, 200);
        } else {
            return response()->json(['error' => 'Template not found'], 401);
        }
    }
    public function update(Request $request, $id)
    {
        $template = SmsTemplate::find($id);
        if ($template) {
            $template->name = $request->name;
            $template->content = $request->content;
            $template->status = $request->status;
            $template->save();

            return response()->json(['success' => 'Template updated successfully'], 200);
        } else {
            return response()->json(['error' => 'Template not found'], 401);
        }
    }
}
