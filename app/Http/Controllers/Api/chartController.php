<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patients;
use App\Models\Appointments;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class chartController extends Controller
{
    

    public function getChartData(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $request->user_id;
        $filter = $request->filter ?? 'year';

        $now = now();
        switch ($filter) {
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $groupFormat = 'd M';
                $labelFormat = 'd M';
                $range = collect(range(0, $now->daysInMonth - 1))->map(function ($i) use ($startDate) {
                    return $startDate->copy()->addDays($i)->format('d M');
                });
                break;

            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $groupFormat = 'D';
                $labelFormat = 'D';
                $range = collect(range(0, 6))->map(function ($i) use ($startDate) {
                    return $startDate->copy()->addDays($i)->format('D');
                });
                break;

            default:
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $groupFormat = 'm';
                $labelFormat = 'F';
                $range = collect(range(1, 12))->map(function ($i) {
                    return str_pad($i, 2, '0', STR_PAD_LEFT); // gives "01", "02", ..., "12"
                });
        }

        $patients = $this->getDataByCreatedAt($userId, 'patients', $startDate, $endDate, $groupFormat);
        $doctors = $this->getDataByCreatedAt($userId, 'user', $startDate, $endDate, $groupFormat, 3);
        $receptionists = $this->getDataByCreatedAt($userId, 'user', $startDate, $endDate, $groupFormat, 4);
        $appointments = $this->getDataByCreatedAt($userId, 'appointments', $startDate, $endDate, $groupFormat);
        $inventory = $this->getDataByCreatedAt($userId, 'inventory', $startDate, $endDate, $groupFormat);
        $treatments = $this->getDataByCreatedAt($userId, 'treatments', $startDate, $endDate, $groupFormat);
        // dd($patients);
        $allData = [
            'labels' => $range->toArray(),
            'patients' => $this->fillMissingData($patients, $range),
            'doctors' => $this->fillMissingData($doctors, $range),
            'receptionists' => $this->fillMissingData($receptionists, $range),
            'appointments' => $this->fillMissingData($appointments, $range),
            'inventory' => $this->fillMissingData($inventory, $range),
            'treatments' => $this->fillMissingData($treatments, $range),
        ];
        // dd($allData);
        return response()->json(['chartData' => $allData]);
    }

    private function getDataByCreatedAt($userId, $type, $startDate, $endDate, $groupFormat, $roleId = null)
    {
        $query = DB::table($type);
    
        $loggedInUser = auth()->user();
    
        // Filter based on type
        if ($type === 'patients') {
            $query->where('user_id', $userId);
        } elseif ($type === 'appointments') {
            // Show only doctor's appointments if user is doctor
            if ($loggedInUser->role === 'Doctor' || $loggedInUser->role_id == 3) {
                $query->where('doctor_id', $userId);
            }
            // If admin, no need to filter by doctor_id
            // Optionally add filter for other roles if needed
        }
    
        // Choose date column
        $dateColumn = ($type === 'appointments') ? 'date' : 'created_at';
    
        $query->whereBetween($dateColumn, [$startDate, $endDate]);
    
        if ($roleId) {
            $query->where('role_id', $roleId);
        }
    
        return $query->get()
            ->groupBy(function ($item) use ($groupFormat, $dateColumn) {
                return Carbon::parse($item->$dateColumn)->format($groupFormat);
            })
            ->map(function ($group) {
                return $group->count();
            });
    }
    


    private function fillMissingData($data, $range)
    {
        return $range->map(function ($label) use ($data) {
            return $data[$label] ?? 0;
        })->toArray();
    }

    public function getReportOverview(Request $request)
    {
        $user = auth()->user() ?? auth('api')->user() ?? \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            $user = \App\Models\User::first();
        }

        $branchId = $request->get('branch_id') ?: ($user ? $user->branch_id : null);
        $filter = $request->get('filter', 'month'); // 'today', 'week', 'month', 'year', 'all'

        $now = now();
        switch ($filter) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'all':
                $startDate = Carbon::createFromTimestamp(0);
                $endDate = $now->copy()->endOfDay();
                break;
            case 'month':
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        // Branch filter helper
        $applyBranch = function ($query, $table = null) use ($branchId) {
            if ($branchId) {
                $column = $table ? "{$table}.branch_id" : 'branch_id';
                $query->where($column, $branchId);
            }
            return $query;
        };

        // 1. Metric Count Cards
        $totalPatients = $applyBranch(DB::table('patients'))->count();
        $totalDoctors = $applyBranch(DB::table('user')->where(function ($q) {
            $q->where('role_id', 3)->orWhere('role_id', 2);
        }))->count();
        $totalStaff = $applyBranch(DB::table('user')->whereIn('role_id', [2, 3, 4]))->count();
        $totalTreatments = $applyBranch(DB::table('treatments'))->count();
        $totalRefDoctors = $applyBranch(DB::table('referal_doctors'))->count();

        // Period-specific metrics
        $periodAppointmentsQuery = $applyBranch(DB::table('appointments'))
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
        $periodAppointmentsCount = (clone $periodAppointmentsQuery)->count();
        $completedAppointments = (clone $periodAppointmentsQuery)->where('status', 'Completed')->count();
        $upcomingAppointments = (clone $periodAppointmentsQuery)->whereIn('status', ['Upcoming', 'Confirmed', 'Pending'])->count();
        $cancelledAppointments = (clone $periodAppointmentsQuery)->where('status', 'Cancelled')->count();

        $periodFollowupsQuery = $applyBranch(DB::table('followups'))
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
        $periodFollowupsCount = (clone $periodFollowupsQuery)->count();

        // Invoices / Transactions
        $invoicesQuery = $applyBranch(DB::table('invoices'))
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
        $totalTransactions = (clone $invoicesQuery)->count();
        $totalRevenue = (float) (clone $invoicesQuery)->sum('grand_total');
        $paidRevenue = (float) (clone $invoicesQuery)->where('payment_status', 'Paid')->sum('grand_total');
        $pendingRevenue = (float) (clone $invoicesQuery)->where('payment_status', 'Pending')->sum('grand_total');
        $failedTransactions = (clone $invoicesQuery)->where('payment_status', 'Failed')->count();
        $paidTransactions = (clone $invoicesQuery)->where('payment_status', 'Paid')->count();
        $pendingTransactions = (clone $invoicesQuery)->where('payment_status', 'Pending')->count();

        // 2. Revenue Trend Chart
        $revenueLabels = [];
        $revenueValues = [];
        if ($filter === 'today') {
            for ($h = 8; $h <= 20; $h += 2) {
                $label = sprintf('%02d:00', $h);
                $revenueLabels[] = $label;
                $val = (float) $applyBranch(DB::table('invoices'))
                    ->whereDate('date', $now->toDateString())
                    ->whereTime('created_at', '>=', sprintf('%02d:00:00', $h))
                    ->whereTime('created_at', '<', sprintf('%02d:00:00', $h + 2))
                    ->sum('grand_total');
                $revenueValues[] = $val;
            }
        } elseif ($filter === 'week') {
            for ($d = 0; $d < 7; $d++) {
                $dayDate = $startDate->copy()->addDays($d);
                $revenueLabels[] = $dayDate->format('D, d M');
                $val = (float) $applyBranch(DB::table('invoices'))
                    ->whereDate('date', $dayDate->toDateString())
                    ->sum('grand_total');
                $revenueValues[] = $val;
            }
        } elseif ($filter === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $mDate = Carbon::create($startDate->year, $m, 1);
                $revenueLabels[] = $mDate->format('M');
                $val = (float) $applyBranch(DB::table('invoices'))
                    ->whereYear('date', $startDate->year)
                    ->whereMonth('date', $m)
                    ->sum('grand_total');
                $revenueValues[] = $val;
            }
        } else {
            // Month
            $daysInMonth = $startDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d += ($daysInMonth > 20 ? 2 : 1)) {
                $dayDate = $startDate->copy()->day($d);
                $revenueLabels[] = $dayDate->format('d M');
                $val = (float) $applyBranch(DB::table('invoices'))
                    ->whereDate('date', $dayDate->toDateString())
                    ->sum('grand_total');
                $revenueValues[] = $val;
            }
        }

        // 3. Status Distribution (Appointments & Followups)
        $appointmentDistribution = [
            'completed' => $completedAppointments,
            'upcoming' => $upcomingAppointments,
            'cancelled' => $cancelledAppointments,
        ];

        // 4. Treatment Performance Table (Top Treatments with Counts and Doctor)
        $treatments = \App\Models\Treatment::with('doctor')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $treatmentPerformance = $treatments->map(function ($t) {
            $count = DB::table('appointments')->where('treatment_id', $t->id)->count();
            return [
                'id' => $t->id,
                'name' => $t->name,
                'price' => $t->price,
                'doctor_name' => $t->doctor ? $t->doctor->fullname : 'General',
                'total_appointments' => $count,
            ];
        })->sortByDesc('total_appointments')->values()->take(10);

        // 5. Recent Transaction / Invoice Report Table
        $invoices = \App\Models\Invoice::with('patient')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $recentTransactions = $invoices->map(function ($inv) {
            return [
                'id' => $inv->id,
                'patient_name' => $inv->patient ? $inv->patient->fullname : '--',
                'type' => $inv->type,
                'date' => $inv->date,
                'amount' => $inv->grand_total,
                'method' => $inv->payment_type ?? 'Cash',
                'status' => $inv->payment_status ?? 'Pending',
            ];
        });

        return response()->json([
            'metrics' => [
                'total_patients' => $totalPatients,
                'total_doctors' => $totalDoctors,
                'total_staff' => $totalStaff,
                'total_treatments' => $totalTreatments,
                'total_reference_doctors' => $totalRefDoctors,
                'total_appointments' => $periodAppointmentsCount,
                'total_followups' => $periodFollowupsCount,
                'total_transactions' => $totalTransactions,
                'total_revenue' => $totalRevenue,
                'paid_revenue' => $paidRevenue,
                'pending_revenue' => $pendingRevenue,
                'paid_transactions' => $paidTransactions,
                'pending_transactions' => $pendingTransactions,
                'failed_transactions' => $failedTransactions,
                'completed_appointments' => $completedAppointments,
                'upcoming_appointments' => $upcomingAppointments,
                'cancelled_appointments' => $cancelledAppointments,
            ],
            'revenue_chart' => [
                'labels' => $revenueLabels,
                'values' => $revenueValues,
            ],
            'appointment_distribution' => $appointmentDistribution,
            'treatment_performance' => $treatmentPerformance,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}

