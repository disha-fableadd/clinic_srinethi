<?php

namespace App\Http\Controllers\Api;

use App\Models\UserPermission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserPermissionController extends Controller
{


    public function getPermissions($userId)
    {
        // Fetch permissions for the user
        $permissions = UserPermission::where('user_id', $userId)
            ->with('module')  // Assuming you have a relationship with the modules table
            ->get();

        return response()->json($permissions);
    }



    public function getUserPermissions(Request $request)
    {
        $user = auth()->user();
    
        $permissions = $user->permissions()->get()->mapWithKeys(function ($permission) {
            return [
                $permission->module->name => [
                    'create' => (int) $permission->create,
                    'view' => (int) $permission->view,
                    'update' => (int) $permission->update,
                    'delete' => (int) $permission->delete,
                ]
            ];
        });
    
        return response()->json(['permissions' => $permissions]);
    }
    






    public function index(): JsonResponse
    {
        try {
            $permissions = UserPermission::with(['user', 'module'])->get();
            return response()->json($permissions);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 401);
        }
    }

    /**
     * Get a specific permission
     */
    public function show($id): JsonResponse
    {
        try {
            $permission = UserPermission::with(['user', 'module'])->findOrFail($id);
            return response()->json($permission);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not found'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 401);
        }
    }

    /**
     * Create a new permission
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:user,id',
                'module_id' => 'required|exists:modules,id',
                'create' => 'boolean',
                'view' => 'boolean',
                'update' => 'boolean',
                'delete' => 'boolean',
            ]);

            $permission = UserPermission::create($validated);

            return response()->json(['message' => 'Permission created', 'data' => $permission], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 401);
        }
    }

    /**
     * Update an existing permission
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $permission = UserPermission::findOrFail($id);

            $validated = $request->validate([
                'user_id' => 'sometimes|exists:user,id',
                'module_id' => 'sometimes|exists:modules,id',
                'create' => 'boolean',
                'view' => 'boolean',
                'update' => 'boolean',
                'delete' => 'boolean',
            ]);

            $permission->update($validated);

            return response()->json(['message' => 'Permission updated', 'data' => $permission]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not found'], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 401);
        }
    }

    /**
     * Delete a permission
     */
    public function destroy($id): JsonResponse
    {
        try {
            $permission = UserPermission::findOrFail($id);
            $permission->delete();
            return response()->json(['message' => 'Permission deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not found'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 401);
        }
    }
}
