<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::latest()->get();
        return view('plans.planlist', compact('plans'));
    }

    public function create()
    {
        return view('plans.addplan');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);

        // Process features
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        DB::transaction(function () use ($validated, $featuresArray, $request) {
            $plan = Plan::create([
    'name'          => $validated['name'],
    'price'         => $validated['price'],
    'start_date'    => $validated['start_date'],
    'end_date'      => $validated['end_date'],
    'duration'      => $request->duration,
    'total_amount'  => $request->total_amount,
    'subtitle'      => $validated['subtitle'],
    'user_limit'    => $validated['user_limit'],
    'branch_limit'  => $validated['branch_limit'],
    'storage_limit' => $validated['storage_limit'],
    'is_active'     => $validated['is_active'],
    'features'      => $featuresArray,

            ]);
        });

        return redirect()->route('plans.planlist')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('plans.editplan', compact('plan'));
    }

    public function show(Plan $plan)
    {
        return view('plans.viewplan', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $this->validatePlan($request);

        // Process features
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        DB::transaction(function () use ($validated, $featuresArray, $request, $plan) {
            $updateData = [
                'name'          => $validated['name'],
                'price'         => $validated['price'] ?? null,
                'start_date'    => $validated['start_date'] ?? null,
                'end_date'      => $validated['end_date'] ?? null,
                'subtitle'      => $validated['subtitle'] ?? null,
                'user_limit'    => $validated['user_limit'] ?? null,
                'branch_limit'  => $validated['branch_limit'] ?? null,
                'storage_limit' => $validated['storage_limit'] ?? null,
                'is_active'     => $validated['is_active'],
                'features'      => $featuresArray,
            ];
            $plan->update($updateData);
        });

        return redirect()->route('plans.planlist')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        DB::transaction(function () use ($plan) {
            $plan->delete();
        });

        return redirect()->route('plans.planlist')->with('success', 'Plan deleted successfully.');
    }

    public function myplan()
    {
        $user = auth()->user();
        if (!$user->plan_id) {
            $userPlan = null;
        } else {
            $userPlan = Plan::find($user->plan_id);
        }

        return view('plans.myplan', compact('userPlan'));
    }

    // API Methods for AJAX calls
    public function apiList()
    {
        $plans = Plan::latest()->get();
        return response()->json(['plans' => $plans]);
    }

    public function apiShow($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        return response()->json(['plan' => $plan]);
    }

    public function apiStore(Request $request)
    {
        $validated = $this->validatePlan($request);
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        try {
            $plan = DB::transaction(function () use ($validated, $featuresArray, $request) {
                return Plan::create([
    'name'          => $validated['name'],
    'price'         => $validated['price'] ?? null,
    'start_date'    => $validated['start_date'] ?? null,
    'end_date'      => $validated['end_date'] ?? null,
    'duration'      => $request->duration,
    'total_amount'  => $request->total_amount,
    'subtitle'      => $validated['subtitle'] ?? null,
    'user_limit'    => $validated['user_limit'] ?? null,
    'branch_limit'  => $validated['branch_limit'] ?? null,
    'storage_limit' => $validated['storage_limit'] ?? null,
    'is_active'     => $validated['is_active'],
    'features'      => $featuresArray,
]);
            });

            return response()->json([
                'message' => 'Plan created successfully',
                'plan' => $plan
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $validated = $this->validatePlan($request);
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $validated['features']))));
        }

        try {
            DB::transaction(function () use ($plan, $validated, $featuresArray,$request) {
                $updateData = [
    'name'          => $validated['name'],
    'price'         => $validated['price'] ?? null,
    'start_date'    => $validated['start_date'] ?? null,
    'end_date'      => $validated['end_date'] ?? null,
    'duration'      => $request->duration,
    'total_amount'  => $request->total_amount,
    'subtitle'      => $validated['subtitle'] ?? null,
    'user_limit'    => $validated['user_limit'] ?? null,
    'branch_limit'  => $validated['branch_limit'] ?? null,
    'storage_limit' => $validated['storage_limit'] ?? null,
    'is_active'     => $validated['is_active'],
    'features'      => $featuresArray,
];
                $plan->update($updateData);
            });

            return response()->json([
                'message' => 'Plan updated successfully',
                'plan' => $plan
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apiDestroy($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        try {
            DB::transaction(function () use ($plan) {
                $plan->delete();
            });

            return response()->json(['message' => 'Plan deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Validate plan data
     * Removed duration validation as it's calculated automatically from dates
     */
    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'price'          => ['nullable', 'numeric', 'min:0'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'subtitle'       => ['nullable', 'string'],
            'user_limit'     => ['nullable', 'integer', 'min:0'],
            'branch_limit'   => ['nullable', 'integer', 'min:0'],
            'storage_limit'  => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['required', 'boolean'],
            'features'       => ['nullable', 'string'],
        ]);
    }
}
