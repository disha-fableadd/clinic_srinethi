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

}
