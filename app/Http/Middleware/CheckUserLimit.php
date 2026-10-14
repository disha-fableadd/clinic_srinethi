<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class CheckUserLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Step 1: Get current plan via plan_id
        $planId = $user->plan_id;
        
        // If user is a staff member, get their creator's plan
        if (!$planId && $user->created_by) {
            $creator = User::find($user->created_by);
            $planId = $creator ? $creator->plan_id : null;
        }

        $plan = Plan::find($planId);

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'No subscription plan found for this user.'
            ], 403);
        }

        // Step 2: Check expiration based on end_date
        if ($plan->end_date) {
            $expirationDate = \Carbon\Carbon::parse($plan->end_date)->endOfDay();
            
            if (now()->greaterThan($expirationDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your subscription plan has expired. Please renew.'
                ], 403);
            }
        }

        // Step 3: Enforce staff limit (using user_limit from plans table)
        $creatorId = $user->created_by ?: $user->id;
        $staffLimit = $plan->user_limit ?? 0;
        
        $currentStaffCount = User::where('created_by', $creatorId)->count();

        if ($currentStaffCount >= $staffLimit) {
            return response()->json([
                'status' => false,
                'message' => 'Staff limit reached for your plan.'
            ], 403);
        }

        return $next($request);
    }
}