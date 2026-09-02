<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memberikan izin pkppt.delete kepada role irban, admin_irban, dan sekretariat
     * agar dapat menghapus item PKPPT yang berstatus draft atau salah input.
     */
    public function up(): void
    {
        $perm = Permission::firstOrCreate(['name' => 'pkppt.delete', 'guard_name' => 'web']);

        $roles = ['admin', 'sekretariat', 'irban', 'admin_irban', 'inspektur'];
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role && ! $role->hasPermissionTo('pkppt.delete')) {
                $role->givePermissionTo($perm);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
