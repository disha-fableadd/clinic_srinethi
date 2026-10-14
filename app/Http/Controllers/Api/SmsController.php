<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\SendSms;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Helpers\SmsHelper;
use Illuminate\Support\Facades\Log;


class SmsController extends Controller
{
    public function index()
    {
        return view('sms.index');
    }






    public function sendSms(Request $request)
    {
        try {
            // 🔹 STEP 1: Check global SMS status first
            $smsStatus = Setting::where('key', 'sms_status')->value('value');
            if ($smsStatus !== 'on') {
                return response()->json([
                    'message' => 'SMS service is currently OFF. Please enable it to send messages.'
                ], 403);
            }

            // 🔹 STEP 2: Validate request
            $request->validate([
                'user_ids'   => 'required|array',
                'user_ids.*' => 'integer',
                'template_id' => 'required|integer',
                'sender_id'  => 'required|integer',
                'service'    => 'required|string',
                'branch_id'  => 'nullable|integer',
            ]);

            // 🔹 STEP 3: Loop through users and send SMS
            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if (!$user) {
                    Log::error("User not found for ID: $userId");
                    continue;
                }

                $to_number = $user->phone;
                $template = SmsTemplate::find($request->template_id);

                if (!$template) {
                    Log::error("Template not found for ID: {$request->template_id}");
                    continue;
                }

                $message = $template->content;
                $send_sms = new SmsHelper();
                $smsSent = $send_sms->send_sms($to_number, $message);

                // ✅ Save with branch_id
                SendSms::create([
                    'user_id'    => $userId,
                    'template_id' => $request->template_id,
                    'sender_id'  => $request->sender_id,
                    'service'    => $request->service,
                    'branch_id'  => $request->branch_id ?? auth()->user()->branch_id, // <-- FIX
                    'status'     => $smsSent ? 'sent' : 'failed',
                ]);
            }

            return response()->json(['message' => 'SMS sent successfully.']);
        } catch (\Exception $e) {
            Log::error("Error sending SMS: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send SMS.'], 500);
        }
    }









    public function getSmsData(Request $request)
    {
        $branchId = $request->query('branch_id');

        $sms = SendSms::with(['user', 'template'])
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        return response()->json(['data' => $sms]);
    }


    public function show($id)
    {
        $smsData = SendSms::with('user', 'template')
            ->orderBy('created_at', 'desc')
            ->get();


        if (!$smsData) {
            return response()->json(['message' => 'SMS not found'], 401);
        }

        return response()->json(['data' => $smsData]);
    }


    public function destroy($id)
    {
        try {
            $sms = SendSms::findOrFail($id); // Find the SMS by ID or fail
            $sms->delete(); // Delete the SMS entry

            return response()->json(['message' => 'SMS deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete SMS', 'error' => $e->getMessage()], 400);
        }
    }
}
