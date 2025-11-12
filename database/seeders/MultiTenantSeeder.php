<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MultiTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample tenants/companies
        $tenant1 = Tenant::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme',
            'domain' => 'acme.example.com',
            'description' => 'Leading provider of innovative solutions',
            'is_active' => true,
        ]);

        $tenant2 = Tenant::create([
            'name' => 'TechStart Inc',
            'slug' => 'techstart',
            'domain' => 'techstart.example.com',
            'description' => 'Technology startup company',
            'is_active' => true,
        ]);

        // Create permissions for each tenant
        $permissionsData = [
            ['name' => 'View Users', 'slug' => 'view-users'],
            ['name' => 'Create Users', 'slug' => 'create-users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users'],
            ['name' => 'View Posts', 'slug' => 'view-posts'],
            ['name' => 'Create Posts', 'slug' => 'create-posts'],
            ['name' => 'Edit Posts', 'slug' => 'edit-posts'],
            ['name' => 'Delete Posts', 'slug' => 'delete-posts'],
            ['name' => 'View Roles', 'slug' => 'view-roles'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles'],
            ['name' => 'Assign Roles', 'slug' => 'assign-roles'],
        ];

        foreach ([$tenant1, $tenant2] as $tenant) {
            foreach ($permissionsData as $perm) {
                Permission::create([
                    'tenant_id' => $tenant->id,
                    'name' => $perm['name'],
                    'slug' => $perm['slug'],
                    'description' => $perm['name'] . ' permission for ' . $tenant->name,
                ]);
            }
        }

        // Create roles for each tenant
        foreach ([$tenant1, $tenant2] as $tenant) {
            $adminRole = Role::create([
                'tenant_id' => $tenant->id,
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full access administrator for ' . $tenant->name,
            ]);

            $moderatorRole = Role::create([
                'tenant_id' => $tenant->id,
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Content moderator for ' . $tenant->name,
            ]);

            $userRole = Role::create([
                'tenant_id' => $tenant->id,
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user for ' . $tenant->name,
            ]);

            // Assign all permissions to admin
            $tenantPermissions = Permission::where('tenant_id', $tenant->id)->get();
            $adminRole->permissions()->sync($tenantPermissions);

            // Assign specific permissions to moderator
            $moderatorPermissions = Permission::where('tenant_id', $tenant->id)
                ->whereIn('slug', ['view-users', 'view-posts', 'create-posts', 'edit-posts', 'delete-posts'])
                ->get();
            $moderatorRole->permissions()->sync($moderatorPermissions);

            // Assign basic permissions to user
            $userPermissions = Permission::where('tenant_id', $tenant->id)
                ->whereIn('slug', ['view-posts', 'create-posts'])
                ->get();
            $userRole->permissions()->sync($userPermissions);
        }

        // Create sample users
        $user1 = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-0101',
            'date_of_birth' => '1990-05-15',
            'gender' => 'male',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'timezone' => 'America/New_York',
            'language' => 'en',
            'bio' => 'Software Engineer with 10+ years of experience',
        ]);

        $user2 = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-0102',
            'date_of_birth' => '1992-08-22',
            'gender' => 'female',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'USA',
            'timezone' => 'America/Los_Angeles',
            'language' => 'en',
            'bio' => 'Product Manager passionate about user experience',
        ]);

        $user3 = User::create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-0103',
            'date_of_birth' => '1988-03-10',
            'gender' => 'male',
            'city' => 'Austin',
            'state' => 'TX',
            'country' => 'USA',
            'timezone' => 'America/Chicago',
            'language' => 'en',
            'bio' => 'Tech entrepreneur and startup founder',
        ]);

        // Add users to tenants with roles
        // User 1: Admin in Acme, User in TechStart
        $user1->joinTenant($tenant1);
        $user1->assignRoleInTenant('admin', $tenant1);
        
        $user1->joinTenant($tenant2);
        $user1->assignRoleInTenant('user', $tenant2);

        // User 2: Moderator in both tenants
        $user2->joinTenant($tenant1);
        $user2->assignRoleInTenant('moderator', $tenant1);
        
        $user2->joinTenant($tenant2);
        $user2->assignRoleInTenant('moderator', $tenant2);

        // User 3: Admin in TechStart only
        $user3->joinTenant($tenant2);
        $user3->assignRoleInTenant('admin', $tenant2);

        $this->command->info('Multi-tenant data seeded successfully!');
        $this->command->info('');
        $this->command->info('Tenants created:');
        $this->command->info('1. Acme Corporation (slug: acme)');
        $this->command->info('2. TechStart Inc (slug: techstart)');
        $this->command->info('');
        $this->command->info('Users created:');
        $this->command->info('- john@example.com (Admin in Acme, User in TechStart)');
        $this->command->info('- jane@example.com (Moderator in both)');
        $this->command->info('- bob@example.com (Admin in TechStart only)');
        $this->command->info('');
        $this->command->info('Password for all users: password');
    }
}

