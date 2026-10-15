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
    // ✅ Validate input (branch_id is optional and defaults to 1)
    $validator = Validator::make($request->all(), [
        'appointment_id' => 'required|exists:appointments,id',
        'date'           => 'required|date',
        'time'           => 'required',
        'status'         => 'required|in:upcoming,confirmed,completed,cancelled,follow-up',
        'comment'        => 'nullable|string|max:255',
        'branch_id'      => 'nullable',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        // ✅ Ensure branch_id column exists in appointment_history table
        if (!\Illuminate\Support\Facades\Schema::hasColumn('appointment_history', 'branch_id')) {
            try {
                \Illuminate\Support\Facades\Schema::table('appointment_history', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->default(1)->nullable()->after('appointment_id');
                });
            } catch (\Throwable $ex) {
                // Ignore if already added concurrently or database user lacks alter permission
            }
        }

        $branchId = $request->input('branch_id') ?: 1;

        $historyData = [
            'appointment_id' => $request->appointment_id,
            'date'           => $request->date,
            'time'           => $request->time,
            'status'         => $request->status,
            'comment'        => $request->comment,
            'created_by'     => auth()->id(),
        ];

        // Only include branch_id if column exists
        if (\Illuminate\Support\Facades\Schema::hasColumn('appointment_history', 'branch_id')) {
            $historyData['branch_id'] = $branchId;
        }

        // ✅ Create appointment history with default branch_id 1
        $history = AppointmentHistory::create($historyData);

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