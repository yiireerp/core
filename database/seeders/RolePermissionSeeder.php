<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view users list'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users'],
            
            ['name' => 'View Posts', 'slug' => 'view-posts', 'description' => 'Can view posts'],
            ['name' => 'Create Posts', 'slug' => 'create-posts', 'description' => 'Can create posts'],
            ['name' => 'Edit Posts', 'slug' => 'edit-posts', 'description' => 'Can edit posts'],
            ['name' => 'Delete Posts', 'slug' => 'delete-posts', 'description' => 'Can delete posts'],
            
            ['name' => 'View Roles', 'slug' => 'view-roles', 'description' => 'Can view roles'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'description' => 'Can create roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'description' => 'Can edit roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'description' => 'Can delete roles'],
            
            ['name' => 'Assign Roles', 'slug' => 'assign-roles', 'description' => 'Can assign roles to users'],
            ['name' => 'Assign Permissions', 'slug' => 'assign-permissions', 'description' => 'Can assign permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Has full access to the system'
            ]
        );

        $moderatorRole = Role::firstOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Moderator',
                'description' => 'Can moderate content'
            ]
        );

        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'description' => 'Regular user with basic permissions'
            ]
        );

        // Assign all permissions to admin
        $adminRole->permissions()->sync(Permission::all());

        // Assign specific permissions to moderator
        $moderatorPermissions = Permission::whereIn('slug', [
            'view-users',
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
        ])->get();
        $moderatorRole->permissions()->sync($moderatorPermissions);

        // Assign basic permissions to user
        $userPermissions = Permission::whereIn('slug', [
            'view-posts',
            'create-posts',
        ])->get();
        $userRole->permissions()->sync($userPermissions);

        $this->command->info('Roles and Permissions seeded successfully!');
    }
}

