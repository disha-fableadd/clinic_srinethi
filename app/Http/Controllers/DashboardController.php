<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Branch;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\TreatmentBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medicine;
use App\Models\Plan;
use App\Models\Patients;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        // 1. Get branch_id from URL, or session, or localStorage
        $branchId = $request->query('branch_id', session('branch_id'));

        // 2. If still empty, maybe get user's default branch
        if (!$branchId && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        // 3. If we have a branchId but it's missing from URL → redirect with it
        if ($branchId && !$request->has('branch_id')) {
            return redirect()->route('dashboard', ['branch_id' => $branchId]);
        }

        // 4. Save into session for dropdown
        if ($branchId) {
            session(['branch_id' => $branchId]);
            $branchName = Branch::find($branchId)->name ?? 'Unknown';
            session(['branch_name' => $branchName]);
        }

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
        $viewName = $currentProjectTypeId === 3 ? 'physio-dashboard' : 'dashboard';

        $planExpirationWarning = $this->getPlanExpirationWarning($user);

        if (!$branchId) {
            return view($viewName, [
                'medicineCount' => 0,
                'appointmentsToday' => collect(),
                'followupsToday' => collect(),
                'expiredPlans' => collect(),
                'planExpirationWarning' => $planExpirationWarning,
            ]);
        }
        // Medicine count branch wise
        $medicineCount = Medicine::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->count();

        // Base queries with branch filter
        $appointmentsQuery = Appointments::with(['patient', 'doctor', 'treatment'])
            ->whereDate('date', Carbon::today())
            ->where('branch_id', $branchId);

        $followupsQuery = Followup::with(['patient', 'doctor', 'treatment'])
            ->whereDate('date', Carbon::today())
            ->where('branch_id', $branchId);

        $expiredPlansQuery = TreatmentBooking::with(['patient', 'treatment'])
            ->where('branch_id', $branchId);

        // Role-based filters
        if ($user->role->name == 'Doctor') {
            $appointmentsQuery->where('doctor_id', $userId);
            $followupsQuery->where('doctor_id', $userId);
            $expiredPlansQuery->whereHas('treatment', function ($q) use ($userId) {
                $q->where('doctor_id', $userId);
            });
        } elseif ($user->role->name == 'Patient') {
            $patient = Patients::where('login_patient_id', $userId)->first();
            if ($patient) {
                $appointmentsQuery->where('patient_id', $patient->id);
                $followupsQuery->where('patient_id', $patient->id);
                $expiredPlansQuery->where('patient_id', $patient->id);
            } else {
                return view($viewName, [
                    'medicineCount' => $medicineCount,
                    'appointmentsToday' => collect(),
                    'followupsToday' => collect(),
                    'expiredPlans' => collect()
                ]);
            }
        }

        // Fetch data
        $appointmentsToday = $appointmentsQuery->get();
        $followupsToday = $followupsQuery->get();

        // Expired treatment plans filter
        $expiredPlans = $expiredPlansQuery->get()->filter(function ($plan) {
            $createdAt = Carbon::parse($plan->created_at);
            $today = Carbon::today();

            if ($plan->plan === 'monthly') {
                return $createdAt->addMonth()->isSameDay($today);
            }

            if ($plan->plan === 'weekly') {
                return $createdAt->addWeek()->isSameDay($today);
            }

            return false;
        });

        return view($viewName, compact(
            'medicineCount',
            'appointmentsToday',
            'followupsToday',
            'expiredPlans',
            'planExpirationWarning'
        ));
    }

    private function getPlanExpirationWarning($user)
    {
        if (!$user || !$user->plan_id) {
            return null;
        }

        $plan = Plan::find($user->plan_id);
        if (!$plan || !$plan->end_date) {
            return null;
        }

        $today = Carbon::today();
        $expiryDate = Carbon::parse($plan->end_date);

        $daysRemaining = $today->diffInDays($expiryDate, false);

        if ($daysRemaining < 0 || $daysRemaining > 30) {
            return null;
        }

        return [
            'plan_name' => $plan->name,
            'expiry_date' => $expiryDate->format('d M Y'),
            'days_remaining' => $daysRemaining,
            'plan_duration' => $plan->duration,
            'price' => $plan->price,
        ];
    }

    public function export(Request $request)
    {
        $branchId = $request->query('branch_id'); // get branch_id from query string

        // fetch invoices by branch_id if provided
        $invoices = Invoice::with('patient')
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get();

        $csvData = [];
        $csvData[] = [
            'Invoice ID',
            'Patient Name',
            'Type',
            'Type Details',
            'Total Amount (Rs)',
            'Tax (%)',
            'Discount Type',
            'Discount',
            'Payment Type',
            'Date',
            'Instruction',
            'Created At',
            'Updated At'
        ];

        foreach ($invoices as $invoice) {
            $csvData[] = [
                $invoice->id,
                $invoice->patient->fullname ?? 'N/A',
                $invoice->type,
                collect($invoice->types_details)->pluck('name')->join(', '),
                number_format($invoice->grand_total, 2),
                number_format($invoice->tax, 2),
                $invoice->discount_type,
                number_format($invoice->discount, 2),
                $invoice->payment_type,
                $invoice->date,
                $invoice->instruction,
                $invoice->created_at ? $invoice->created_at->format('d-M-Y h:i A') : 'N/A',
                $invoice->updated_at ? $invoice->updated_at->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        $filename = 'invoices_export_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return response($contents, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }




   public function exportStaffs(Request $request)
{
    $branchId = $request->query('branch_id');
    $branchName = $request->query('branch_name');

    if (!$branchId) {
        return redirect()->back()->with('error', 'Branch not selected');
    }

    // Load relationships
    $staffs = User::with(['role', 'details', 'creator', 'branch'])
        ->where('branch_id', $branchId)
        ->get();

    $filename = $branchName . '_staffs_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($staffs) {

        $file = fopen('php://output', 'w');

        // CSV Headers
        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

        $header = [
            'ID',
            'Full Name',
            'Email',
            'Phone'
        ];
        if ($currentProjectTypeId !== 3) {
            $header[] = 'Role';
        }
        $header = array_merge($header, [
            'Branch',
            'Created By',
            'Profile URL',
            'Gender',
            'Birth Date',
            'Address',
            'City',
            'State',
            'Shift',
            'Salary',
            'Created At'
        ]);
        fputcsv($file, $header);
  $sr = 1;

        foreach ($staffs as $staff) {
            $row = [
               $sr++,
                $staff->fullname,
                $staff->email,
                $staff->phone
            ];
            if ($currentProjectTypeId !== 3) {
                $row[] = $staff->role->name ?? '';
            }
            $row = array_merge($row, [
                $staff->branch->branch_name ?? '',
                $staff->creator->fullname ?? '',
                $staff->profile,
                $staff->details->gender ?? '',
                $staff->details->birth_date ?? '',
                $staff->details->address ?? '',
                $staff->details->city ?? '',
                $staff->details->state ?? '',
                $staff->details->shift ?? '',
                $staff->details->salary ?? '',
                $staff->created_at
            ]);
            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
