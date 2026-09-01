<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $inspektur = Role::where('name', 'inspektur')->where('guard_name', 'web')->first();
        if ($inspektur) {
            $permissions = ['pkppt.create', 'pkppt.edit', 'pkppt.usulkan'];
            foreach ($permissions as $permName) {
                $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                $inspektur->givePermissionTo($perm);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
