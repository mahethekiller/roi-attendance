<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'greythr_logs.view',
            'greythr_logs.push',
            'greythr_logs.clear',
        ];

        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);

            $superAdmin = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
            if ($superAdmin && !$superAdmin->hasPermissionTo($permName)) {
                $superAdmin->givePermissionTo($perm);
            }

            $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();
            if ($admin && !$admin->hasPermissionTo($permName)) {
                $admin->givePermissionTo($perm);
            }
        }
    }

    public function down(): void
    {
        $permissions = [
            'greythr_logs.view',
            'greythr_logs.push',
            'greythr_logs.clear',
        ];

        foreach ($permissions as $permName) {
            $permission = Permission::where('name', $permName)->where('guard_name', 'web')->first();
            if ($permission) {
                $permission->delete();
            }
        }
    }
};
