<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Forget cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove user management permissions from 'admin' role if present
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $userPermissions = [
                'manage users',
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'users.assign-roles',
            ];

            foreach ($userPermissions as $permName) {
                if ($adminRole->hasPermissionTo($permName)) {
                    $adminRole->revokePermissionTo($permName);
                }
            }
        }

        // Ensure super-admin still has all permissions
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'manage users',
                'users.view',
                'users.create',
                'users.edit',
            ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
