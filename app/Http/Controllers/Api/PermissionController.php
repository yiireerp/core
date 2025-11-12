<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($request->all());

        return response()->json($permission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::with('roles', 'users')->findOrFail($id);
        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:permissions,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        $permission->update($request->all());

        return response()->json($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully'
        ]);
    }

    /**
     * Assign permission to user directly.
     */
    public function assignToUser(Request $request, string $permissionId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $permission = Permission::findOrFail($permissionId);
        $user = User::findOrFail($request->user_id);

        $user->givePermissionTo($permission);

        return response()->json([
            'message' => 'Permission assigned to user successfully',
            'user' => $user->load('permissions')
        ]);
    }

    /**
     * Remove permission from user.
     */
    public function removeFromUser(Request $request, string $permissionId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $permission = Permission::findOrFail($permissionId);
        $user = User::findOrFail($request->user_id);

        $user->revokePermissionTo($permission);

        return response()->json([
            'message' => 'Permission removed from user successfully',
            'user' => $user->load('permissions')
        ]);
    }
}

