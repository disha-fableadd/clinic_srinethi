<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\SendEmail;
use App\Models\DefaultEmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\File;

class EmailTemplateController extends Controller
{


    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $templates = EmailTemplate::with('defaultTemplate')->orderBy('name');

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $templates->where('branch_id', $branchId);
        }

        $templates = $templates->get();

        if ($templates->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No email templates found to export.'
            ]);
        }

        $filename = 'email_templates_export_' . now()->format('Ymd_His') . '.csv';
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
            'Template Name',
            'Default Template',
            'Status',
            'Image 1',
            'Image 2',
            'Image 3',

            'Created At'
        ]);

        $sr = 1;
        foreach ($templates as $template) {
            fputcsv($file, [
                $sr++,
                $template->name,
                $template->defaultTemplate->name ?? '',
                $template->status,
                $template->img1 ? asset(env('IMAGE_PATH') . $template->img1) : '',
                $template->img2 ? asset(env('IMAGE_PATH') . $template->img2) : '',
                $template->img3 ? asset(env('IMAGE_PATH') . $template->img3) : '',

                $template->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Email templates exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }



    public function send_exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id'); // ✅ Branch filter

        $records = SendEmail::with(['user', 'emailtemplate'])->latest();

        // ✅ Apply branch filter if provided
        if ($branchId) {
            $records->where('branch_id', $branchId);
        }

        $records = $records->get();

        if ($records->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No sent emails found to export.'
            ]);
        }

        $filename = 'sent_emails_export_' . now()->format('Ymd_His') . '.csv';
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
            'Sender ID',

            'Created At'
        ]);

        $sr = 1;
        foreach ($records as $row) {
            fputcsv($file, [
                $sr++,
                $row->user->fullname ?? '',
                $row->emailtemplate->name ?? '',
                $row->sender_id,

                $row->created_at
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Sent emails exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }


    public function getTemplates()
    {
        $templates = EmailTemplate::select('id', 'name')->get();
        return response()->json(['data' => $templates]);
    }

    public function getTemplateById($id)
    {
        $template = DefaultEmailTemplate::find($id);

        if ($template) {
            return response()->json([
                'success' => true,
                'data' => $template
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Template not found'
            ], 401);
        }
    }



    public function sendemail(Request $request)
    {
        // Validate request including branch_id
        $request->validate([
            'emailSubject' => 'required|string',
            'users' => 'required|array',
            'users.*' => 'exists:user,id',
            'template_id' => 'required|integer',
            'branch_id' => 'required|integer', // branch_id from local storage
        ]);

        Log::info($request->all());

        $subject = $request->emailSubject;
        $branchId = $request->branch_id;

        // Find Email Template
        $template = EmailTemplate::find($request->template_id);
        if (!$template) {
            return response()->json(['message' => 'Email template not found.'], 404);
        }

        // Find Default Email Template
        $d_template = DefaultEmailTemplate::find($template->template_id);
        if (!$d_template) {
            return response()->json(['message' => 'Default email template not found.'], 404);
        }

        $emailContent = $d_template->content;

        // Replace image placeholders
        $placeholders = [
            '[IMG1]' => url($template->img1 ?? 'placeholder.jpg'),
            '[IMG2]' => url($template->img2 ?? ''),
            '[IMG3]' => url($template->img3 ?? '')
        ];

        // Remove unwanted Googleusercontent prefixes
        $emailContent = preg_replace(
            '/https:\/\/ci3\.googleusercontent\.com\/[^\#]+#/',
            '',
            $emailContent
        );

        foreach ($placeholders as $key => $value) {
            if (!empty($value)) {
                $emailContent = str_replace($key, $value, $emailContent);
            }
        }

        $successCount = 0;
        $failures = [];

        foreach ($request->users as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            try {
                // Send email
                EmailHelper::sendEmail(
                    $user->email,
                    $subject,
                    null,
                    ['content' => $emailContent],
                    $emailContent
                );

                // Store sent email record with branch_id
                SendEmail::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'sender_id' => auth()->id(),
                    'branch_id' => $branchId,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                Log::error('Email sending failed for user ' . $user->id . ': ' . $e->getMessage());
                $failures[] = ['user_id' => $user->id, 'error' => $e->getMessage()];
            }
        }

        if ($successCount > 0) {
            return response()->json([
                'message' => 'Emails sent successfully.',
                'success_count' => $successCount,
                'failures' => $failures
            ], 200);
        }

        return response()->json([
            'message' => 'Email sending failed for all users.',
            'failures' => $failures
        ], 500);
    }


    public function getEmails(Request $request)
    {
        $branchId = $request->branch_id; // get branch_id from request

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch ID is required.'
            ], 400);
        }

        // Fetch emails for this branch only
        $emails = SendEmail::with(['user', 'emailtemplate'])
            ->where('branch_id', $branchId)
            ->get();

        return response()->json($emails);
    }



    public function deleteEmail($id)
    {
        $email = SendEmail::find($id);

        if (!$email) {
            return response()->json(['error' => 'Email record not found!'], 401);
        }

        $email->delete();

        return response()->json(['success' => 'Email record deleted successfully!']);
    }

    public function getAllTemplates()
    {
        $templates = DefaultEmailTemplate::all();
        return response()->json($templates);
    }




    public function index(Request $request)
    {
        // Validate branch_id is sent
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        // Fetch templates for the given branch
        $templates = EmailTemplate::where('branch_id', $request->branch_id)->get();

        return response()->json($templates, 200);
    }






    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'template_id' => 'required|exists:default_email_template,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'branch_id' => 'required|exists:branches,id', // new validation
            'img1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'img2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'img3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        // Handle image uploads
        $img1Path = $request->hasFile('img1') ? $request->file('img1')->store('uploads/images', 'public') : null;
        $img2Path = $request->hasFile('img2') ? $request->file('img2')->store('uploads/images', 'public') : null;
        $img3Path = $request->hasFile('img3') ? $request->file('img3')->store('uploads/images', 'public') : null;

        // Create email template
        $template = EmailTemplate::create([
            'user_id' => $request->user_id,
            'template_id' => $request->template_id,
            'name' => $request->name,
            'status' => $request->status,
            'branch_id' => $request->branch_id, // save branch ID
            'img1' => $img1Path,
            'img2' => $img2Path,
            'img3' => $img3Path,
        ]);

        return response()->json([
            'message' => 'Email template created successfully',
            'template' => $template
        ], 200);
    }



    public function destroy($id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 401);
        }

        $template->delete();

        return response()->json(['message' => 'Template deleted successfully'], 200);
    }

    public function show($id)
    {
        $template = EmailTemplate::with('defaultTemplate')->find($id);

        if (!$template) {
            return response()->json(['message' => 'Template not found'], 401);
        }

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'status' => $template->status,
            'template_id' => $template->template_id,
            'content' => $template->defaultTemplate ? $template->defaultTemplate->content : null,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'template_id' => 'required|exists:default_email_template,id',
            'img1' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'img2' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'img3' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $template = EmailTemplate::findOrFail($id);


        $template->name = $request->input('name'); // Make sure 'name' is not null
        $template->status = $request->input('status');
        $template->template_id = $request->input('template_id');

        if ($request->hasFile('img1')) {
            $template->img1 = $request->file('img1')->store('uploads/images', 'public');
        }
        if ($request->hasFile('img2')) {
            $template->img2 = $request->file('img2')->store('uploads/images', 'public');
        }
        if ($request->hasFile('img3')) {
            $template->img3 = $request->file('img3')->store('uploads/images', 'public');
        }

        $template->save();

        return response()->json(['message' => 'Template updated successfully!']);
    }
}
