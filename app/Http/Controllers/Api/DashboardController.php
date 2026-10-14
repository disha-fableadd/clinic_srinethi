<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Appointments;
use App\Models\Followup;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getPatientCounts(Request $request)
    {
        $branchId = $request->branch_id;
        $today = Carbon::today();

        $homePatientsQuery = Patients::whereDate('created_at', $today)->where('patient_type', 'Home');
        $cosmeticPatientsQuery = Patients::whereDate('created_at', $today)->where('patient_type', 'Cosmetic');
        $ipdPatientsQuery = Patients::whereDate('created_at', $today)->where('patient_type', 'IPD');

        if ($branchId) {
            $homePatientsQuery->where('branch_id', $branchId);
            $cosmeticPatientsQuery->where('branch_id', $branchId);
            $ipdPatientsQuery->where('branch_id', $branchId);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'home_patient'     => $homePatientsQuery->count(),
                'cosmetic_patient' => $cosmeticPatientsQuery->count(),
                'ipd_patient'      => $ipdPatientsQuery->count(),
            ]
        ]);
    }
    public function totalFollowup(Request $request)
    {
        $branchId = $request->branch_id;
        $query = Followup::whereDate('date', Carbon::today());
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return response()->json(['total_followup' => $query->count()]);
    }

    public function allFollowup(Request $request)
    {
        $branchId = $request->branch_id;
        $query = Followup::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return response()->json(['total_followups' => $query->count()]);
    }

    public function totalAppointment(Request $request)
    {
        $branchId = $request->branch_id;
        $query = Appointments::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return response()->json(['total_appointment' => $query->count()]);
    }

    public function totalTodayAppointment(Request $request)
    {
        $branchId = $request->branch_id;
        $query = Appointments::whereDate('date', Carbon::today());
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return response()->json(['total_appointments' => $query->count()]);
    }

    public function todayStats(Request $request)
    {
        $branchId = $request->branch_id;
        $today = Carbon::today();

        // Income - Assuming we get it from treatment_booking or invoices. Let's just use 0 for now as it wasn't specified where it comes from.
        // Or if we check how it's calculated elsewhere? I will use 0.
        // Wait, patient counts for today:
        $patientsQuery = Patients::whereDate('created_at', $today);
        if ($branchId) {
            $patientsQuery->where('branch_id', $branchId);
        }

        $birthdaysQuery = Patients::whereRaw('DATE_FORMAT(birthdate, "%m-%d") = ?', [$today->format('m-d')]);
        if ($branchId) {
            $birthdaysQuery->where('branch_id', $branchId);
        }

        $expensesQuery = Expense::whereDate('date_time', $today);
        if ($branchId) {
            $expensesQuery->where('branch_id', $branchId);
        }

        return response()->json([
            'today_income' => 0,
            'today_patients' => $patientsQuery->count(),
            'today_birthdays' => $birthdaysQuery->count(),
            'today_expense' => $expensesQuery->sum('amount')
        ]);
    }

    public function todayBirthdayUsers(Request $request)
    {
        $branchId = $request->branch_id;
        $today = Carbon::today()->format('m-d');
        
        $query = Patients::whereRaw('DATE_FORMAT(birthdate, "%m-%d") = ?', [$today]);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $users = $query->get()->map(function ($patient) {
            return [
                'fullname' => $patient->fullname,
                'birth_date' => Carbon::parse($patient->birthdate)->format('d M Y')
            ];
        });

        return response()->json([
            'count' => $users->count(),
            'users' => $users
        ]);
    }

    public function totalPatient(Request $request)
    {
        $branchId = $request->branch_id;
        $query = Patients::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return response()->json(['total' => $query->count()]);
    }
}
