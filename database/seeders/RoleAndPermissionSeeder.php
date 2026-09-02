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
            'view dashboard',
            'manage users',
            'manage settings',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles and assign permissions
        $superAdminRole = Role::findOrCreate('super-admin', 'web');
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->givePermissionTo(['view dashboard', 'view reports', 'manage users']);

        $userRole = Role::findOrCreate('user', 'web');
        $userRole->givePermissionTo(['view dashboard']);

        // Create Default Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole($superAdminRole);
    }
}
