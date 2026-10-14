<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{


  


     public function notificationView(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->name ?? null;
        $notifications = collect(); // default empty collection

        if ($userRole == 'Admin') {
            // Filter: Exclude notifications meant for patients
            $notifications = Notification::where('receiver_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($userRole == 'Patient') {
            // Fetch patient record using login_patient_id
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();

            if ($patient) {
                // Get notifications where patient_id matches
                $notifications = Notification::where('receiver_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Optional: return or log if patient not found
                return view('notifications.notifications', [
                    'notifications' => collect(),
                    'notificationCount' => 0,
                    'message' => 'No notifications found for this patient.'
                ]);
            }
        } else {
            // For other roles (like Doctor, Receptionist, etc.)
            $notifications = Notification::where('receiver_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $notificationCount = $notifications->count();

        return view('notifications.notifications', compact('notifications', 'notificationCount'));
    }


    public function update(Request $request, string $id)
    {
        $notification = Notification::findOrFail($id);
        // print_r($notification);
        if ($notification->read == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Notification is already marked as read',
            ]);
        }
        if ($notification->receiver_id != Auth::user()->id && Auth::user()->role->name != 'Admin') {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to mark this notification as read',
            ]);
        }



        $notification->update(['is_read' => 1]);
        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }
}
