<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PlanController extends Controller
{
    /* ==========================================================
     | 1️⃣  LIST — GET /api/getAllPlans
     ========================================================== */
    public function getAllPlans(Request $request)
    {
        $perPage       = (int) $request->input('per_page', 10);
        $page          = (int) $request->input('page', 1);
        $search        = trim((string) $request->input('search', ''));
        $shouldPaginate = $request->has('page') || $request->has('per_page') || $request->filled('search');

        $subBranchId = $request->input('sub_branch_id');

        $query = Plan::query()
            ->when($subBranchId, fn($q) => $q->where('sub_branch_id', $subBranchId))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('subtitle', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->filled('duration'),  fn($q) => $q->where('duration', $request->duration));

        $pagination = null;

        if ($shouldPaginate) {
            $paginated  = $query->latest('id')->paginate($perPage, ['*'], 'page', $page);
            $plans      = $paginated->items();
            $pagination = [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'next_page_url' => $paginated->nextPageUrl(),
                'prev_page_url' => $paginated->previousPageUrl(),
            ];
        } else {
            $plans = $query->latest('id')->get();
        }

        return response()->json([
            'status'     => true,
            'data'       => $plans,
            'pagination' => $pagination,
        ]);
    }

    /* ==========================================================
     | 2️⃣  SHOW — GET /api/getPlanById/{id}
     ========================================================== */
    public function getPlanById($id)
    {
        $plan = Plan::find($id);

        if (! $plan) {
            return response()->json(['status' => false, 'error' => 'Plan not found'], 404);
        }

        return response()->json(['status' => true, 'plan' => $plan], 200);
    }

    /* ==========================================================
     | 3️⃣  CREATE — POST /api/createPlan
     ========================================================== */
    public function createPlan(Request $request)
    {
        /* -------------------------------------------------
         | Validation
         -------------------------------------------------*/
        $rules = [
            'name'          => 'required|string|max:255|unique:plans,name',
            'price'         => 'nullable|numeric|min:0',
            'total_amount'  => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'duration'   => 'nullable|string',
            'subtitle'      => 'nullable|string',
            'user_limit'    => 'nullable|integer|min:0',
            'branch_limit'  => 'nullable',
            'storage_limit' => 'nullable|integer|min:0',
            'is_active'     => 'required|boolean',
            'features'      => 'nullable|string',
            'sub_branch_id' => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            'is_active'     => 'status',
            'user_limit'    => 'user limit',
            'branch_limit'  => 'branch limit',
            'storage_limit' => 'storage limit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        $years = $start->diffInYears($end);
        $months = $start->copy()->addYears($years)->diffInMonths($end);
        $days = $start->copy()->addYears($years)->addMonths($months)->diffInDays($end);

        $duration = '';

        if ($years > 0) {
            $duration .= $years . ' Year ';
        }

        if ($months > 0) {
            $duration .= $months . ' Month ';
        }

        if ($days > 0) {
            $duration .= $days . ' Day';
        }

        $duration = trim($duration);

        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        /* -------------------------------------------------
         | Create Plan + Features (atomic)
         -------------------------------------------------*/
        DB::transaction(function () use ($validated, &$plan , $duration, $featuresArray) {
            $plan = Plan::create([
                'name'          => $validated['name'],
                'price'         => $validated['price']         ?? null,
                'total_amount'  => $validated['total_amount']  ?? null,
                'duration'   => $duration,
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'],
                'subtitle'      => $validated['subtitle']      ?? null,
                'user_limit'    => $validated['user_limit']    ?? null,
                'branch_limit'  => $validated['branch_limit']  ?? null,
                'storage_limit' => $validated['storage_limit'] ?? null,
                'is_active'     => $validated['is_active'],
                'sub_branch_id' => $validated['sub_branch_id'] ?? null,
                'features'      => $featuresArray,
            ]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Plan created successfully',
            'plan'    => $plan,
        ], 200);
    }

    /* ==========================================================
     | 4️⃣  UPDATE — POST /api/updatePlan
     ========================================================== */
    public function updatePlan(Request $request)
    {
        /* -------------------------------------------------
         | Validation
         -------------------------------------------------*/
        $rules = [
            'plan_id'       => 'required|exists:plans,id',
            'name'          => 'required|string|max:255|unique:plans,name,' . $request->plan_id,
            'price'         => 'nullable|numeric|min:0',
            'total_amount'  => 'nullable|numeric|min:0',
'start_date' => 'required|date',
'end_date'   => 'required|date|after:start_date',
'duration'   => 'nullable|string',            'subtitle'      => 'nullable|string',
            'user_limit'    => 'nullable|integer|min:0',
            'branch_limit'  => 'nullable',
            'storage_limit' => 'nullable|integer|min:0',
            'is_active'     => 'required|boolean',
            'features'      => 'nullable|string',
            'sub_branch_id' => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules, [], [
            'plan_id'       => 'plan',
            'is_active'     => 'status',
            'user_limit'    => 'user limit',
            'branch_limit'  => 'branch limit',
            'storage_limit' => 'storage limit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
$start = Carbon::parse($validated['start_date']);
$end = Carbon::parse($validated['end_date']);

$years = $start->diffInYears($end);
$months = $start->copy()->addYears($years)->diffInMonths($end);
$days = $start->copy()->addYears($years)->addMonths($months)->diffInDays($end);

$duration = '';

if ($years > 0) {
    $duration .= $years . ' Year ';
}

if ($months > 0) {
    $duration .= $months . ' Month ';
}

if ($days > 0) {
    $duration .= $days . ' Day';
}

$duration = trim($duration);
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        /* -------------------------------------------------
         | Update Plan + re-sync Features (atomic)
         -------------------------------------------------*/
        $plan = Plan::findOrFail($validated['plan_id']);

        DB::transaction(function () use ($plan, $validated, $duration, $featuresArray) {
            $plan->update([
                'name'          => $validated['name'],
                'price'         => $validated['price']         ?? null,
                'total_amount'  => $validated['total_amount']  ?? null,
                'duration'   => $duration,
'start_date' => $validated['start_date'],
'end_date'   => $validated['end_date'],
                'subtitle'      => $validated['subtitle']      ?? null,
                'user_limit'    => $validated['user_limit']    ?? null,
                'branch_limit'  => $validated['branch_limit']  ?? null,
                'storage_limit' => $validated['storage_limit'] ?? null,
                'is_active'     => $validated['is_active'],
                'sub_branch_id' => $validated['sub_branch_id'] ?? null,
                'features'      => $featuresArray,
            ]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Plan updated successfully',
            'plan'    => $plan->fresh(),
        ], 200);
    }

    /* ==========================================================
     | 5️⃣  DELETE — POST /api/deletePlan/{id}
     ========================================================== */
    public function deletePlan($id)
    {
        $plan = Plan::find($id);

        if (! $plan) {
            return response()->json(['status' => false, 'error' => 'Plan not found'], 404);
        }

        DB::transaction(function () use ($plan) {
            $plan->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Plan deleted successfully',
        ], 200);
    }

    /* ==========================================================
     | 6️⃣  TOGGLE STATUS — POST /api/togglePlanStatus/{id}
     ========================================================== */
    public function togglePlanStatus($id)
    {
        $plan = Plan::find($id);

        if (! $plan) {
            return response()->json(['status' => false, 'error' => 'Plan not found'], 404);
        }

        $plan->is_active = ! $plan->is_active;
        $plan->save();

        return response()->json([
            'status'    => true,
            'message'   => 'Plan status updated successfully',
            'is_active' => $plan->is_active,
        ], 200);
    }

    /* ==========================================================
     | 7️⃣  ASSIGN PLAN TO USER — POST /api/assignPlanToUser
     ========================================================== */
    public function assignPlanToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = \App\Models\User::find($request->user_id);
            $plan = Plan::find($request->plan_id);

            $user->update(['plan_id' => $plan->id]);

            return response()->json([
                'status'  => true,
                'message' => 'Plan assigned to user successfully',
                'user'    => $user,
                'plan'    => $plan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'error'   => 'Failed to assign plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ==========================================================
     | 8️⃣  GET USER PLAN — GET /api/getUserPlan
     ========================================================== */
    public function getUserPlan(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            if (!$user->plan_id) {
                return response()->json([
                    'status'  => false,
                    'has_plan' => false,
                    'message' => 'No plan assigned to this user',
                ], 200);
            }

            $plan = $user->plan;

            return response()->json([
                'status'    => true,
                'has_plan'  => true,
                'plan'      => $plan,
                'user'      => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'error'   => 'Failed to retrieve plan: ' . $e->getMessage(),
            ], 500);
        }
    }

}
