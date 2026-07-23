<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Definisi Permission per Modul ─────────────────────────
        $permissions = [
            // Master Data
            'master.view', 'master.create', 'master.edit', 'master.delete',
            // Pengguna
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // PKPPT
            'pkppt.view', 'pkppt.create', 'pkppt.edit', 'pkppt.delete',
            'pkppt.usulkan', 'pkppt.tetapkan',
            // Penugasan
            'penugasan.view', 'penugasan.create', 'penugasan.edit',
            'penugasan.delete', 'penugasan.update_status',
            // Tindak Lanjut
            'tindak_lanjut.view', 'tindak_lanjut.create', 'tindak_lanjut.edit',
            // Arsip Digital
            'arsip.view', 'arsip.upload', 'arsip.delete',
            // Beban Kerja
            'beban_kerja.view',
            // Dashboard
            'dashboard.view', 'dashboard.view_all',
            // Perencanaan PKPT
            'perencanaan.view', 'perencanaan.hitung_risiko', 'perencanaan.generate_draft',
            'perencanaan.kapasitas_sdm',
            // Evaluasi Tahunan
            'evaluasi.view', 'evaluasi.generate',
            // Audit Log
            'audit_log.view',
            // Portal OPD (sisi internal)
            'opd_users.manage', 'bukti.verifikasi',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ─── 6 Role Internal ───────────────────────────────────────

        // 1. Admin Sistem — akses penuh
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

        // 2. Sekretariat — kelola data & PKPPT, tidak bisa hapus penugasan
        $sekretariat = Role::firstOrCreate(['name' => 'sekretariat', 'guard_name' => 'web']);
        $sekretariat->syncPermissions([
            'master.view','master.create','master.edit',
            'users.view','users.create','users.edit',
            'pkppt.view','pkppt.create','pkppt.edit',
            'penugasan.view','penugasan.create','penugasan.edit','penugasan.update_status',
            'tindak_lanjut.view','tindak_lanjut.create','tindak_lanjut.edit',
            'arsip.view','arsip.upload',
            'beban_kerja.view',
            'dashboard.view','dashboard.view_all',
            'perencanaan.view','perencanaan.hitung_risiko','perencanaan.generate_draft','perencanaan.kapasitas_sdm',
            'evaluasi.view','evaluasi.generate',
            'opd_users.manage',
        ]);

        // 3. Inspektur — baca semua, tetapkan PKPT
        $inspektur = Role::firstOrCreate(['name' => 'inspektur', 'guard_name' => 'web']);
        $inspektur->syncPermissions([
            'master.view',
            'users.view',
            'pkppt.view','pkppt.tetapkan',
            'penugasan.view',
            'tindak_lanjut.view',
            'arsip.view',
            'beban_kerja.view',
            'dashboard.view','dashboard.view_all',
            'perencanaan.view',
            'evaluasi.view','evaluasi.generate',
            'audit_log.view',
            'bukti.verifikasi',
        ]);

        // 4. Admin Irban — operasional harian wilayah Irban-nya
        $adminIrban = Role::firstOrCreate(['name' => 'admin_irban', 'guard_name' => 'web']);
        $adminIrban->syncPermissions([
            'master.view',
            'pkppt.view',
            'penugasan.view','penugasan.create','penugasan.edit','penugasan.update_status',
            'tindak_lanjut.view','tindak_lanjut.create','tindak_lanjut.edit',
            'arsip.view','arsip.upload',
            'beban_kerja.view',
            'dashboard.view',
            'bukti.verifikasi',
        ]);

        // 5. Irban — supervisi manajerial wilayah
        $irban = Role::firstOrCreate(['name' => 'irban', 'guard_name' => 'web']);
        $irban->syncPermissions([
            'master.view',
            'pkppt.view','pkppt.usulkan',
            'penugasan.view','penugasan.create','penugasan.edit','penugasan.update_status',
            'tindak_lanjut.view','tindak_lanjut.create','tindak_lanjut.edit',
            'arsip.view','arsip.upload',
            'beban_kerja.view',
            'dashboard.view',
            'perencanaan.view',
            'evaluasi.view',
            'bukti.verifikasi',
        ]);

        // 6. Auditor/P2UPD — pelaksana teknis
        $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $auditor->syncPermissions([
            'pkppt.view',
            'penugasan.view','penugasan.create','penugasan.edit','penugasan.update_status',
            'tindak_lanjut.view','tindak_lanjut.create',
            'arsip.view','arsip.upload',
            'beban_kerja.view',
            'dashboard.view',
        ]);

        // ─── 1 Role Eksternal (guard opd) ─────────────────────────
        Permission::firstOrCreate(['name' => 'opd.view_rekomendasi', 'guard_name' => 'opd']);
        Permission::firstOrCreate(['name' => 'opd.upload_bukti',     'guard_name' => 'opd']);

        $opdRole = Role::firstOrCreate(['name' => 'opd', 'guard_name' => 'opd']);
        $opdRole->syncPermissions(['opd.view_rekomendasi', 'opd.upload_bukti']);

        $this->command->info('✓ 6 role internal + 1 role eksternal OPD berhasil dibuat.');
    }
}
