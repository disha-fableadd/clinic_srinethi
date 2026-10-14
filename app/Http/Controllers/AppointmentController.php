<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
   

    public function index(Request $request)
    {
        $user = Auth::user(); // Get logged-in user

        $query = Appointments::with(['patient', 'doctor', 'treatment'])
            ->orderBy('id', 'desc');

        // Filter by branch_id if provided
        if ($request->has('branch_id')) {
            $branchId = $request->input('branch_id');
            $query->where('branch_id', $branchId);
        }

        $appointments = $query->get();
        $totalAppointments = $appointments->count();

        return view('appointment.index', [
            'total' => $totalAppointments,
            'appointments' => $appointments
        ]);
    }


    public function create()
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return redirect()->route('appointment.index')->with('error', 'Patients are not allowed to create appointments.');
        }

        return view('appointment.create');
    }
    public function show($id)
    {
        $appointments = Appointments::with(['patient', 'doctor', 'treatment',])->find($id);

        if (!$appointments) {
            return redirect()->route('appointment.index')->with('error', 'Appointment not found.');
        }
        return view('appointment.show', ['appointment_id' => $id, 'appointments' => $appointments]);
    }

    public function edit($id)
    {
        if (optional(Auth::user()?->role)->name === 'Patient') {
            return redirect()->route('appointment.index')->with('error', 'Patients are not allowed to edit appointments.');
        }

        return view('appointment.edit', ['appointment_id' => $id]);
    }




    


    public function exportAppointments(Request $request)
    {
        $branchId = $request->get('branch_id'); // coming from query string

        $query = Appointments::with(['patient', 'doctor', 'treatment']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $appointments = $query->get();

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

        $header = [
            'Appointment ID',
            'Branch ID',  
            'Branch Name',      // ✅ Added branch column
            'Patient Name',
            'Doctor Name',
            'Treatment'
        ];
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Appointment Type';
            $header[] = 'Status';
        }
        $header[] = 'Date';
        $header[] = 'Duration';
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Clinic Location';
            $header[] = 'Followup Update';
        }
        $header[] = 'Created At';
        $header[] = 'Updated At';

        $csvData = [];
        $csvData[] = $header;

        foreach ($appointments as $appointment) {
            $row = [
                $appointment->id,
                $appointment->branch_id ?? 'N/A', // ✅ Include branch_id
                $appointment->branch->name ?? 'N/A',
                $appointment->patient->fullname ?? 'N/A',
                $appointment->doctor->fullname ?? 'N/A',
                $appointment->treatment->name ?? 'N/A'
            ];
            if ($currentProjectTypeId !== 3) {
                $row[] = $appointment->appoint_type ?? 'N/A';
                $row[] = $appointment->status ?? 'N/A';
            }
            $row[] = $appointment->date;
            $row[] = $appointment->duration;
            if ($currentProjectTypeId !== 3) {
                $row[] = $appointment->clinic_location ?? 'N/A';
                $row[] = $appointment->followup_update ?? 'N/A';
            }
            $row[] = $appointment->created_at ? $appointment->created_at->format('Y-m-d H:i:s') : '';
            $row[] = $appointment->updated_at ? $appointment->updated_at->format('Y-m-d H:i:s') : '';
            $csvData[] = $row;
        }

        // Generate CSV
        $filename = 'appointments_export_' . ($branchId ?? 'all') . '_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
