<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\EmailHelper;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;


class EmailController extends Controller
{
    // public function sendemail(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'emailSubject' => 'required|string',
    //         'selectUser' => 'required|array',
    //         'selectTemplate' => 'required|integer', // Assuming you are passing template ID
    //     ]);

    //     $subject = $request->input('emailSubject');
    //     $templateId = $request->input('selectTemplate');
    //     $userIds = $request->input('selectUser');

    //     // Get selected users
    //     $users = User::whereIn('id', $userIds)->get();

        
    //     $template = EmailTemplate::find($request['selectTemplate']); 
    //     $data = [
    //         'users' => $users, // You can pass user-specific data for the email body here
    //     ];

    //     DD( $users );
    //     // Send email to each user
    //     foreach ($users as $user) {
    //         // EmailHelper::sendEmail($user->email, $subject, $templateId, $data);
    //         dd(EmailHelper::sendEmail($user->email, $subject, $templateId, $data));
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Emails sent successfully.'
    //     ], 200);
    // }
}

