<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentHistory;
use App\Models\Appointments;
use App\Models\Notification;
use App\Models\Patients;
use App\Models\User;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;

class AppointmentHistoryController extends Controller
{

   
    public function store(Request $request)
{
    // ✅ Validate input
    $validator = Validator::make($request->all(), [
        'appointment_id' => 'required|exists:appointments,id',
        'date'           => 'required|date',
        'time'           => 'required',
        'status'         => 'required|in:upcoming,confirmed,completed,cancelled,follow-up',
        'comment'        => 'nullable|string|max:255',
        'branch_id'      => 'required|exists:branches,id', // ✅ from frontend/local storage
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        // ✅ Create appointment history with branch_id from request
        $history = AppointmentHistory::create([
            'appointment_id' => $request->appointment_id,
            'branch_id'      => $request->branch_id, // ✅ from frontend/local storage
            'date'           => $request->date,
            'time'           => $request->time,
            'status'         => $request->status,
            'comment'        => $request->comment,
            'created_by'     => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Appointment history created successfully.',
            'data'    => $history
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Failed to create appointment history.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}