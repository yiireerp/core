<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants for authenticated user.
     */
    public function index(Request $request)
    {
        $tenants = $request->user()->tenants()->with('roles', 'permissions')->get();
        return response()->json($tenants);
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants',
            'domain' => 'nullable|string|unique:tenants',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        $tenant = Tenant::create($request->all());

        // Add creator as first member with admin role
        $request->user()->joinTenant($tenant);

        return response()->json($tenant, 201);
    }

    /**
     * Display the specified tenant.
     */
    public function show(Request $request, string $id)
    {
        $tenant = Tenant::with('users', 'roles', 'permissions')->findOrFail($id);

        // Check if user has access to this tenant
        if (!$request->user()->belongsToTenant($tenant)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($tenant);
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Check if user has access to this tenant
        if (!$request->user()->belongsToTenant($tenant)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:tenants,slug,' . $id,
            'domain' => 'nullable|string|unique:tenants,domain,' . $id,
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $tenant->update($request->all());

        return response()->json($tenant);
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Check if user has access to this tenant
        if (!$request->user()->belongsToTenant($tenant)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tenant->delete();

        return response()->json(['message' => 'Tenant deleted successfully']);
    }

    /**
     * Add user to tenant.
     */
    public function addUser(Request $request, string $tenantId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $tenant = Tenant::findOrFail($tenantId);
        $user = User::findOrFail($request->user_id);

        // Check if requester has access to this tenant
        if (!$request->user()->belongsToTenant($tenant)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->joinTenant($tenant);

        return response()->json([
            'message' => 'User added to tenant successfully',
            'tenant' => $tenant->load('users')
        ]);
    }

    /**
     * Remove user from tenant.
     */
    public function removeUser(Request $request, string $tenantId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $tenant = Tenant::findOrFail($tenantId);
        $user = User::findOrFail($request->user_id);

        // Check if requester has access to this tenant
        if (!$request->user()->belongsToTenant($tenant)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->leaveTenant($tenant);

        return response()->json([
            'message' => 'User removed from tenant successfully'
        ]);
    }

    /**
     * Get current tenant context.
     */
    public function current(Request $request)
    {
        $tenant = $request->attributes->get('tenant') ?? app('tenant', null);

        if (!$tenant) {
            return response()->json(['message' => 'No tenant context set'], 404);
        }

        return response()->json($tenant->load('roles', 'permissions'));
    }

    /**
     * Get user's roles and permissions in a specific tenant.
     */
    public function userContext(Request $request, string $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        if (!$request->user()->belongsToTenant($tenant)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $roles = $request->user()->rolesInTenant($tenant)->get();
        $permissions = $request->user()->getAllPermissionsInTenant($tenant);

        return response()->json([
            'tenant' => $tenant,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}

