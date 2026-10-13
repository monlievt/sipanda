<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Restrukturisasi hak akses role Auditor/PPUPD, Admin Irban, dan Pejabat Irban:
     * - Admin Irban: Input SPT, Input Temuan & Rekomendasi, Menandai Selesai, Input PKPPT.
     * - Irban: Verifikasi (Setujui/Tolak) SPT & telaah, monitoring unitnya.
     * - Auditor/PPUPD: View-only (hanya melihat penugasan timnya, tidak bisa input/edit/selesaikan).
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = [
            'master.view', 'master.create', 'master.edit', 'master.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'pkppt.view', 'pkppt.create', 'pkppt.edit', 'pkppt.delete', 'pkppt.usulkan', 'pkppt.tetapkan',
            'penugasan.view', 'penugasan.create', 'penugasan.edit', 'penugasan.delete', 'penugasan.update_status',
            'tindak_lanjut.view', 'tindak_lanjut.create', 'tindak_lanjut.edit',
            'arsip.view', 'arsip.upload', 'arsip.delete',
            'beban_kerja.view',
            'dashboard.view', 'dashboard.view_all',
            'perencanaan.view', 'perencanaan.hitung_risiko', 'perencanaan.generate_draft', 'perencanaan.kapasitas_sdm',
            'evaluasi.view', 'evaluasi.generate',
            'audit_log.view',
            'opd_users.manage', 'bukti.verifikasi',
        ];

        foreach ($allPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 1. Auditor, PPUPD, dan Staf Sekretariat (View Only)
        $viewOnlyPermissions = [
            'pkppt.view',
            'penugasan.view',
            'tindak_lanjut.view',
            'arsip.view',
            'beban_kerja.view',
            'dashboard.view',
        ];

        $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $auditor->syncPermissions($viewOnlyPermissions);

        $ppupd = Role::firstOrCreate(['name' => 'ppupd', 'guard_name' => 'web']);
        $ppupd->syncPermissions($viewOnlyPermissions);

        $sekretariat = Role::firstOrCreate(['name' => 'sekretariat', 'guard_name' => 'web']);
        $sekretariat->syncPermissions($viewOnlyPermissions);

        // 2. Tim Pelaporan (Subbag Evaluasi & Pelaporan — Penyusunan ILHP & Monitoring Se-Inspektorat)
        $pelaporan = Role::firstOrCreate(['name' => 'pelaporan', 'guard_name' => 'web']);
        $pelaporan->syncPermissions([
            'pkppt.view',
            'penugasan.view',
            'tindak_lanjut.view',
            'arsip.view',
            'beban_kerja.view',
            'dashboard.view', 'dashboard.view_all',
            'evaluasi.view', 'evaluasi.generate',
        ]);

        // 3. Sekretaris (Pimpinan Administratif — Pengawasan Makro, ILHP, Kapasitas SDM & Evaluasi)
        $sekretaris = Role::firstOrCreate(['name' => 'sekretaris', 'guard_name' => 'web']);
        $sekretaris->syncPermissions([
            'pkppt.view', 'pkppt.usulkan',
            'penugasan.view',
            'tindak_lanjut.view',
            'arsip.view',
            'beban_kerja.view',
            'dashboard.view', 'dashboard.view_all',
            'perencanaan.view', 'perencanaan.hitung_risiko', 'perencanaan.kapasitas_sdm',
            'evaluasi.view', 'evaluasi.generate',
            'audit_log.view',
        ]);

        // 4. Admin Irban (Operasional Input Harian Irban)
        $adminIrban = Role::firstOrCreate(['name' => 'admin_irban', 'guard_name' => 'web']);
        $adminIrban->syncPermissions([
            'master.view',
            'pkppt.view', 'pkppt.create', 'pkppt.edit', 'pkppt.delete', 'pkppt.usulkan',
            'penugasan.view', 'penugasan.create', 'penugasan.edit', 'penugasan.update_status',
            'tindak_lanjut.view', 'tindak_lanjut.create', 'tindak_lanjut.edit',
            'arsip.view', 'arsip.upload',
            'beban_kerja.view',
            'dashboard.view',
            'bukti.verifikasi',
        ]);

        // 5. Pejabat Irban (Supervisi & Verifikasi Unit)
        $irban = Role::firstOrCreate(['name' => 'irban', 'guard_name' => 'web']);
        $irban->syncPermissions([
            'master.view',
            'pkppt.view', 'pkppt.edit', 'pkppt.usulkan',
            'penugasan.view', 'penugasan.update_status',
            'tindak_lanjut.view', 'tindak_lanjut.edit',
            'arsip.view',
            'beban_kerja.view',
            'dashboard.view',
            'perencanaan.view',
            'evaluasi.view',
            'bukti.verifikasi',
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
