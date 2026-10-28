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
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        $permissions = [
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',
        ];

        foreach ($permissions as $perm) {
            $p = Permission::where('name', $perm)->first();
            if ($p) {
                $p->delete();
            }
        }
    }
};
