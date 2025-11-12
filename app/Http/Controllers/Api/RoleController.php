<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($request->all());

        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions', 'users')->findOrFail($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:roles,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->all());

        return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Assign role to user.
     */
    public function assignToUser(Request $request, string $roleId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $role = Role::findOrFail($roleId);
        $user = User::findOrFail($request->user_id);

        $user->assignRole($role);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Remove role from user.
     */
    public function removeFromUser(Request $request, string $roleId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $role = Role::findOrFail($roleId);
        $user = User::findOrFail($request->user_id);

        $user->removeRole($role);

        return response()->json([
            'message' => 'Role removed successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Assign permission to role.
     */
    public function assignPermission(Request $request, string $roleId)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::findOrFail($roleId);
        $role->permissions()->syncWithoutDetaching($request->permission_id);

        return response()->json([
            'message' => 'Permission assigned to role successfully',
            'role' => $role->load('permissions')
        ]);
    }

    /**
     * Remove permission from role.
     */
    public function removePermission(Request $request, string $roleId)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::findOrFail($roleId);
        $role->permissions()->detach($request->permission_id);

        return response()->json([
            'message' => 'Permission removed from role successfully',
            'role' => $role->load('permissions')
        ]);
    }
}

