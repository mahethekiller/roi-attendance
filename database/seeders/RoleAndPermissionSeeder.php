<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Legacy aliases
            'view dashboard',
            'manage users',
            'manage settings',
            'view reports',

            // Dashboard
            'dashboard.view',

            // Employees
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'employees.import',

            // Attendances
            'attendances.view',
            'attendances.sync',
            'attendances.export',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.assign-roles',

            // API & Developer
            'api.docs.view',
            'api.tokens.manage',
            'api.logs.view',

            // Sync History Logs
            'sync-logs.view',

            // Reports & Analytics
            'reports.view',

            // Roles & System Settings
            'roles.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles and assign permissions
        $superAdminRole = Role::findOrCreate('super-admin', 'web');
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->givePermissionTo([
            'view dashboard',
            'view reports',
            'manage users',
            'dashboard.view',
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.import',
            'attendances.view',
            'attendances.sync',
            'attendances.export',
            'users.view',
            'users.create',
            'users.edit',
            'api.docs.view',
            'sync-logs.view',
        ]);

        $managerRole = Role::findOrCreate('manager', 'web');
        $managerRole->givePermissionTo([
            'view dashboard',
            'dashboard.view',
            'employees.view',
            'attendances.view',
            'attendances.export',
        ]);

        $userRole = Role::findOrCreate('user', 'web');
        $userRole->givePermissionTo([
            'view dashboard',
            'dashboard.view',
            'attendances.view',
        ]);

        // Create Default Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles([$superAdminRole]);
    }
}
