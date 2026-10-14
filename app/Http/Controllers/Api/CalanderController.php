<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointments;
use Carbon\Carbon;

class CalanderController extends Controller
{
    public function getcalander(Request $request)
    {
        $query = Appointments::query();

        // Filter by status
        if ($request->has('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('filter')) {
            $today = Carbon::today();
            if ($request->filter == 'Today') {
                $query->whereDate('date', $today);
            } elseif ($request->filter == 'Upcoming') {
                $query->whereDate('date', '>', $today);
            }
        }

        $appointments = $query->get()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => ucfirst($appointment->title) . ' (' . ucfirst($appointment->status) . ')',
                'start' => $appointment->date,
                'status' => $appointment->status,
                'backgroundColor' => $this->getEventColor($appointment->status),
                'borderColor' => $this->getEventColor($appointment->status),
            ];
        });

        return response()->json($appointments);
    }

    public function getStatuses()
    {
        // Dynamically fetch statuses
        $statuses = ['All', 'scheduled', 'confirmed', 'completed', 'cancelled', 'no-show'];
        return response()->json($statuses);
    }

    private function getEventColor($status)
    {
        $colors = [
            'scheduled' => '#3498db',  // Blue
            'confirmed' => '#2ecc71',  // Green
            'completed' => '#1abc9c',  // Teal
            'cancelled' => '#e74c3c',  // Red
            'no-show' => '#f39c12',    // Orange
        ];
        return $colors[$status] ?? '#7f8c8d';
    }
}
