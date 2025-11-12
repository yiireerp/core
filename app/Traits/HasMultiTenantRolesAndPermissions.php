<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasMultiTenantRolesAndPermissions
{
    /**
     * The tenants that the user belongs to.
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot('is_active', 'joined_at')
            ->withTimestamps();
    }

    /**
     * The roles that belong to the user (with tenant context).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    /**
     * The permissions that belong to the user (direct permissions).
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Get roles for a specific tenant.
     */
    public function rolesInTenant(Tenant|int $tenant): BelongsToMany
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        return $this->roles()->wherePivot('tenant_id', $tenantId);
    }

    /**
     * Assign role to user in specific tenant.
     */
    public function assignRoleInTenant(Role|string $role, Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        if (is_string($role)) {
            $role = Role::where('slug', $role)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([
            $role->id => ['tenant_id' => $tenantId]
        ]);

        return $this;
    }

    /**
     * Remove role from user in specific tenant.
     */
    public function removeRoleInTenant(Role|string $role, Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        if (is_string($role)) {
            $role = Role::where('slug', $role)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();
        }

        $this->roles()->wherePivot('tenant_id', $tenantId)->detach($role);

        return $this;
    }

    /**
     * Sync roles for user in specific tenant.
     */
    public function syncRolesInTenant(array $roles, Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        // Remove all roles for this tenant
        $this->roles()->wherePivot('tenant_id', $tenantId)->detach();
        
        // Add new roles
        foreach ($roles as $role) {
            $this->assignRoleInTenant($role, $tenantId);
        }

        return $this;
    }

    /**
     * Check if user has role in specific tenant.
     */
    public function hasRoleInTenant(Role|string|array $role, Tenant|int $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        if (is_array($role)) {
            return $this->roles()
                ->wherePivot('tenant_id', $tenantId)
                ->whereIn('slug', $role)
                ->exists();
        }

        if (is_string($role)) {
            return $this->roles()
                ->wherePivot('tenant_id', $tenantId)
                ->where('slug', $role)
                ->exists();
        }

        return $this->roles()
            ->wherePivot('tenant_id', $tenantId)
            ->where('roles.id', $role->id)
            ->exists();
    }

    /**
     * Check if user has any of the given roles in tenant.
     */
    public function hasAnyRoleInTenant(array $roles, Tenant|int $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        return $this->roles()
            ->wherePivot('tenant_id', $tenantId)
            ->whereIn('slug', $roles)
            ->exists();
    }

    /**
     * Check if user has all of the given roles in tenant.
     */
    public function hasAllRolesInTenant(array $roles, Tenant|int $tenant): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRoleInTenant($role, $tenant)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Give permission to user (direct permission, not tenant-specific).
     */
    public function givePermissionTo(Permission|string $permission, Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);

        return $this;
    }

    /**
     * Revoke permission from user.
     */
    public function revokePermissionTo(Permission|string $permission, Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();
        }

        $this->permissions()->detach($permission);

        return $this;
    }

    /**
     * Check if user has permission in specific tenant.
     */
    public function hasPermissionInTenant(Permission|string $permission, Tenant|int $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        // Check direct permissions
        if (is_string($permission)) {
            if ($this->permissions()
                ->where('slug', $permission)
                ->where('tenant_id', $tenantId)
                ->exists()) {
                return true;
            }
        } else {
            if ($this->permissions()->where('id', $permission->id)->exists()) {
                return true;
            }
        }

        // Check role permissions
        $roles = $this->rolesInTenant($tenantId)->get();
        
        foreach ($roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions in tenant.
     */
    public function hasAnyPermissionInTenant(array $permissions, Tenant|int $tenant): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermissionInTenant($permission, $tenant)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions in tenant.
     */
    public function hasAllPermissionsInTenant(array $permissions, Tenant|int $tenant): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermissionInTenant($permission, $tenant)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for user in specific tenant (direct + from roles).
     */
    public function getAllPermissionsInTenant(Tenant|int $tenant)
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        $directPermissions = $this->permissions()
            ->where('tenant_id', $tenantId)
            ->get();

        $roles = $this->rolesInTenant($tenantId)->get();
        
        $rolePermissions = $roles->map(function ($role) {
            return $role->permissions;
        })->flatten();

        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Add user to tenant.
     */
    public function joinTenant(Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        $this->tenants()->syncWithoutDetaching($tenantId);

        return $this;
    }

    /**
     * Remove user from tenant.
     */
    public function leaveTenant(Tenant|int $tenant): self
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        // Remove all roles in this tenant
        $this->roles()->wherePivot('tenant_id', $tenantId)->detach();
        
        // Remove tenant membership
        $this->tenants()->detach($tenantId);

        return $this;
    }

    /**
     * Check if user belongs to tenant.
     */
    public function belongsToTenant(Tenant|int $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
        
        return $this->tenants()->where('tenant_id', $tenantId)->exists();
    }

    /**
     * Get all active tenants for user.
     */
    public function getActiveTenants()
    {
        return $this->tenants()->wherePivot('is_active', true)->get();
    }
}
