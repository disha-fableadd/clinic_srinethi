<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Patients;
use App\Models\TreatmentBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function getPatients(Request $request)
    {
        $branchId = $request->query('branch_id');
        $query = Patients::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $patients = $query->select('id', 'fullname')->get();

        return response()->json($patients);
    }

    public function getData(Request $request)
    {
        $type = $request->query('type'); // appointment, followup, treatment, payment
        $period = $request->query('period'); // today, week, month
        $branchId = $request->query('branch_id');
        $patientId = $request->query('patient_id');

        $user = Auth::user();
        $userId = $user->id;

        $query = $this->getQuery($type);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        // Staff wise filter
        if ($user->role->name == 'Doctor') {
            if ($type == 'treatment') {
                $query->whereHas('treatment', function ($q) use ($userId) {
                    $q->where('doctor_id', $userId);
                });
            } else if ($type != 'payment') {
                $query->where('doctor_id', $userId);
            }
        }

        $this->applyPeriodFilter($query, $period, $type);

        $dateField = ($type == 'treatment') ? 'created_at' : 'date';
        $query->orderBy($dateField, 'desc');

        $data = $query->get();

        return response()->json($this->formatResponse($data, $type));
    }

    private function getQuery($type)
    {
        switch ($type) {
            case 'appointment':
                return Appointments::with(['patient', 'doctor', 'treatment']);
            case 'followup':
                return Followup::with(['patient', 'doctor', 'treatment']);
            case 'treatment':
                return TreatmentBooking::with(['patient', 'treatment']);
            case 'payment':
                return Invoice::with(['patient']);
            default:
                return Appointments::with(['patient', 'doctor', 'treatment']);
        }
    }

    private function applyPeriodFilter($query, $period, $type)
    {
        $dateField = ($type == 'treatment') ? 'created_at' : 'date';

        switch ($period) {
            case 'today':
                $query->whereDate($dateField, Carbon::today());
                break;
            case 'week':
                $query->whereBetween($dateField, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth($dateField, Carbon::now()->month)
                      ->whereYear($dateField, Carbon::now()->year);
                break;
        }
    }

    private function formatResponse($data, $type)
    {
        return $data->map(function ($item) use ($type) {
            $res = [
                'id' => $item->id,
                'patient_name' => $item->patient->fullname ?? 'N/A',
                'date' => ($type == 'treatment') ? ($item->created_at ? $item->created_at->format('Y-m-d') : 'N/A') : ($item->date ?? 'N/A'),
            ];

            if ($type == 'appointment' || $type == 'followup') {
                $res['doctor_name'] = $item->doctor->fullname ?? 'N/A';
                $res['treatment_name'] = $item->treatment->name ?? 'N/A';
            }

            if ($type == 'payment') {
                $res['amount'] = $item->grand_total;
                $res['status'] = $item->payment_type;
            }

            if ($type == 'treatment') {
                $res['treatment_name'] = $item->treatment->name ?? 'N/A';
                $res['plan'] = $item->plan;
            }

            return $res;
        });
    }
}
