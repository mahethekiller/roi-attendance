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

            // Attendances & Overrides
            'attendances.view',
            'attendances.sync',
            'attendances.export',
            'attendance.overrides.manage',

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
        
        // Seed default initial override rule if table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('attendance_overrides')) {
            \App\Models\AttendanceOverride::firstOrCreate(
                ['employee_id' => 'I2K2-0340', 'card_no' => '1234'],
                [
                    'employee_name' => 'Target Employee (Default Override)',
                    'check_in_window_start' => '10:00:00',
                    'check_in_window_end' => '10:20:00',
                    'adjusted_in_min_minute' => 20,
                    'adjusted_in_max_minute' => 35,
                    'min_duration_hours' => 9.00,
                    'is_active' => true,
                    'notes' => 'Pre-configured biometric sync override rule for HRSale alignment.',
                    'created_by' => $admin->id,
                ]
            );
        }
    }
}
