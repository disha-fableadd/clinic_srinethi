<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{


    public function index()
    {
        $branches = Branch::all();
        return response()->json([
            'status' => true,
            'data' => $branches
        ]);
    }



    // for setting branch name on button 
    public function setBranch(Request $request)
    {
        $branch = Branch::find($request->branch_id);

        if ($branch) {
            session([
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
            ]);
        }

        return redirect()->back(); // go back to previous page
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:branches,email',
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:255',
            'state'   => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            $planId = $user->plan_id;
            if (!$planId && $user->created_by) {
                $creator = \App\Models\User::find($user->created_by);
                $planId = $creator ? $creator->plan_id : null;
            }

            if ($planId) {
                $plan = \App\Models\Plan::find($planId);
                if ($plan && isset($plan->branch_limit)) {
                    $currentBranchCount = \App\Models\Branch::count();
                    if ($currentBranchCount >= $plan->branch_limit) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Branch limit reached for your plan. Please upgrade your plan to add more branches.',
                        ], 403);
                    }
                }
            }
        }

        try {
            // Create branch
            $branch = Branch::create($request->only(
                'name',
                'email',
                'address',
                'city',
                'state',
                'country'
            ));

            return response()->json([
                'status'  => true,
                'message' => 'Branch created successfully.',
                'data'    => $branch
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create branch.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json($branch);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:branches,email,' . $id,
            'address'  => 'nullable|string|max:500',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'country'  => 'nullable|string|max:255',
        ]);

        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'status'  => false,
                'message' => 'Branch not found'
            ], 404);
        }

        try {
            $branch->update([
                'name'    => $request->name,
                'email'   => $request->email,
                'address' => $request->address,
                'city'    => $request->city,
                'state'   => $request->state,
                'country' => $request->country,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Branch updated successfully.',
                'data'    => $branch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update branch.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // prevent deleting branch with id = 1
        if ($id == 1) {
            return response()->json([
                'message' => 'Default branch cannot be deleted'
            ], 403); // 403 = forbidden
        }

        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully']);
    }
}
