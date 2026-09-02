<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan permission bukti.verifikasi ada dan diberikan ke role auditor
        try {
            $permission = Permission::firstOrCreate(['name' => 'bukti.verifikasi', 'guard_name' => 'web']);
            $auditor = Role::where('name', 'auditor')->where('guard_name', 'web')->first();
            if ($auditor && !$auditor->hasPermissionTo('bukti.verifikasi')) {
                $auditor->givePermissionTo($permission);
            }

            // Bersihkan cache permission Spatie
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Throwable $e) {
            // Abaikan jika tabel belum ada saat inisialisasi awal
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
