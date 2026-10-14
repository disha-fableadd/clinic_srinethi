<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Protect all methods with authentication
    }

  
    public function index()
{
    return response()->json(
        Role::whereNotIn('name', ['SuperAdmin', 'patient','Admin'])->get(),
        200
    );
}


    // Create a new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:role,name',
            'description' => 'required|string',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($role, 201);
    }

    // Get a single role by ID
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        return response()->json($role);
    }

    // Update a role
    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $request->validate([
                'name' => 'required|string|unique:role,name,' . $role->id,
                'description' => 'required|string',
            ]);

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json($role, 200);

        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 401);
        }
    }

    // Delete a role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
