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
            // Admin sees notifications in the system
            $notifications = Notification::orderBy('created_at', 'desc')->get();
        } elseif ($userRole == 'Patient') {
            // Fetch patient record using login_patient_id
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();

            if ($patient) {
                $notifications = Notification::where('receiver_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
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

        $notificationCount = $notifications->where('is_read', false)->count();

        return view('notifications.notifications', compact('notifications', 'notificationCount'));
    }


    public function update(Request $request, string $id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->is_read == 1 || $notification->is_read === true) {
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

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->name ?? null;

        if ($userRole === 'Admin') {
            Notification::where('is_read', false)->update(['is_read' => true]);
        } else {
            Notification::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function destroy(string $id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $user = Auth::user();
            $userRole = $user->role->name ?? null;

            if ($userRole !== 'Admin' && $notification->receiver_id != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to delete this notification',
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'status' => true,
                'message' => 'Notification deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            $user = Auth::user();
            $userRole = $user->role->name ?? null;

            if ($userRole === 'Admin') {
                Notification::query()->delete();
            } else {
                Notification::where('receiver_id', $user->id)->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'All notifications deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete all notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
