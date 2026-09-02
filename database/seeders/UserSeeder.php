<?php

namespace Database\Seeders;

use App\Models\Irban;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Muat data Irban dari DB
        $irbanMap = Irban::pluck('id', 'nama_irban');

        // Pemetaan kolom BIDANG di CSV ke irban_id
        // Nilai dari CSV: "INSPEKTUR PEMBANTU I", "IRBAN I", "SEKRETARIAT", dll.
        $bidangToIrban = [
            'INSPEKTUR PEMBANTU I'   => $irbanMap['Inspektur Pembantu I']  ?? null,
            'INSPEKTUR PEMBANTU II'  => $irbanMap['Inspektur Pembantu II'] ?? null,
            'INSPEKTUR PEMBANTU III' => $irbanMap['Inspektur Pembantu III']?? null,
            'INSPEKTUR PEMBANTU IV'  => $irbanMap['Inspektur Pembantu IV'] ?? null,
            'IRBAN I'                => $irbanMap['Inspektur Pembantu I']  ?? null,
            'IRBAN II'               => $irbanMap['Inspektur Pembantu II'] ?? null,
            'IRBAN III'              => $irbanMap['Inspektur Pembantu III']?? null,
            'IRBAN IV'               => $irbanMap['Inspektur Pembantu IV'] ?? null,
            'plt IRBAN IV / INSPEKTUR PEMBANTU IV' => $irbanMap['Inspektur Pembantu IV'] ?? null,
            'plt IRBAN I / INSPEKTUR PEMBANTU III' => $irbanMap['Inspektur Pembantu I']  ?? null,
            'SEKRETARIAT'            => $irbanMap['Sekretariat']           ?? null,
            'KEPALA SUB BAGIAN UMUM DAN KEPEGAWAIAN' => $irbanMap['Sekretariat'] ?? null,
            'SEKRETARIS'             => $irbanMap['Sekretariat']           ?? null,
        ];

        // Pemetaan JABATAN ke role sistem
        $jabatanToRole = fn(string $jabatan): string => match(true) {
            str_contains($jabatan, 'INSPEKTUR') && !str_contains($jabatan, 'PEMBANTU') => 'inspektur',
            str_contains($jabatan, 'PRANATA KOMPUTER')    => 'admin',
            str_contains($jabatan, 'IRBAN')               => 'irban',
            str_contains($jabatan, 'KASUBBAG')            => 'sekretariat',
            str_contains($jabatan, 'PENELAAH TEKNIS')     => 'sekretariat',
            str_contains($jabatan, 'PENGADMINISTRASI')    => 'sekretariat',
            str_contains($jabatan, 'PENGELOLA UMUM')      => 'sekretariat',
            str_contains($jabatan, 'Plt. IRBAN')          => 'irban',
            str_contains($jabatan, 'Plt. INSPEKTUR')      => 'inspektur',
            default                                        => 'auditor', // PPUPD, Auditor, dll.
        };

        $csvPath = base_path('docs/data-pegawai.csv');
        if (! file_exists($csvPath)) {
            $this->command->warn('File data-pegawai.csv tidak ditemukan. Skip UserSeeder.');
            return;
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // baris pertama = header

        $created = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 12) continue;

            [,$namaTanpaGelar, $nama, $noHp, $nip, , , $pangkat, $bidang, $golongan, $jabatan, $email] = $row;

            // Normalisasi
            $email         = trim($email);
            $nip           = trim($nip);
            $namaTanpaGelar= trim($namaTanpaGelar);
            $nama          = trim($nama);
            $jabatan       = trim($jabatan);
            $bidang        = trim($bidang);
            $pangkat       = trim($pangkat);
            $golongan      = trim($golongan);
            $noHp          = trim($noHp);

            if (empty($email) || empty($nip)) { $skipped++; continue; }

            // Cari irban_id dari bidang
            $irbanId = null;
            if (preg_match('/\b(IV|III|II|I)\b/i', $bidang, $m)) {
                $roman = strtoupper($m[1]);
                $irbanKey = "Inspektur Pembantu {$roman}";
                $irbanId = $irbanMap[$irbanKey] ?? null;
            } elseif (stripos($bidang, 'SEKRETARIAT') !== false || stripos($bidang, 'SEKRETARIS') !== false || stripos($bidang, 'KEPEGAWAIAN') !== false) {
                $irbanId = $irbanMap['Sekretariat'] ?? null;
            }

            $role = $jabatanToRole($jabatan);

            // Admin Irban: tidak bisa di-mapping otomatis dari CSV, default ke auditor/sekretariat
            // akan di-update manual oleh Admin Sistem setelah login

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'nama'            => $nama,
                    'nama_tanpa_gelar'=> $namaTanpaGelar,
                    'nip'             => $nip ?: null,
                    'no_hp'           => $noHp ?: null,
                    'jabatan'         => $jabatan,
                    'pangkat'         => $pangkat ?: null,
                    'golongan'        => $golongan ?: null,
                    'irban_id'        => $irbanId,
                    'password'        => Hash::make('sipanda2025'), // password awal, WAJIB diganti
                    'is_active'       => true,
                    'tipe_akun'       => 'internal',
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->assignRole($role);
                $created++;
            } else {
                $skipped++;
            }
        }

        fclose($handle);

        // ─── Akun Admin default (pastikan ada) ────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@inspektorat.trenggalek.go.id'],
            [
                'nama'            => 'Administrator SIPANDA',
                'nama_tanpa_gelar'=> 'Administrator',
                'password'        => Hash::make('Admin@sipanda2025!'),
                'is_active'       => true,
                'tipe_akun'       => 'internal',
            ]
        );
        if ($adminUser->wasRecentlyCreated || ! $adminUser->hasRole('admin')) {
            $adminUser->syncRoles(['admin']);
        }

        // ─── Akun Sampel OPD untuk Pengujian Portal OPD ───────────
        $dinkes = \App\Models\ObjekPenugasan::where('nama', 'like', '%Kesehatan%')->first();
        if ($dinkes) {
            $opdDinkes = User::firstOrCreate(
                ['email' => 'pic.dinkes@trenggalek.go.id'],
                [
                    'nama'               => 'PIC Dinas Kesehatan',
                    'nama_tanpa_gelar'   => 'PIC Dinkes',
                    'password'           => Hash::make('sipanda2025'),
                    'tipe_akun'          => 'opd',
                    'objek_penugasan_id' => $dinkes->id,
                    'is_active'          => true,
                    'status_undangan'    => 'aktif',
                ]
            );
            $opdDinkes->assignRole('opd');
        }

        $dikpora = \App\Models\ObjekPenugasan::where('nama', 'like', '%Pendidikan%')->first();
        if ($dikpora) {
            $opdDikpora = User::firstOrCreate(
                ['email' => 'pic.dikpora@trenggalek.go.id'],
                [
                    'nama'               => 'PIC Dinas Pendidikan',
                    'nama_tanpa_gelar'   => 'PIC Dikpora',
                    'password'           => Hash::make('sipanda2025'),
                    'tipe_akun'          => 'opd',
                    'objek_penugasan_id' => $dikpora->id,
                    'is_active'          => true,
                    'status_undangan'    => 'aktif',
                ]
            );
            $opdDikpora->assignRole('opd');
        }

        $this->command->info("✓ UserSeeder selesai: {$created} user dibuat, {$skipped} dilewati.");
        $this->command->warn('⚠ Password awal semua pegawai & akun sampel OPD: sipanda2025');
        $this->command->warn('⚠ Admin default: admin@inspektorat.trenggalek.go.id | Admin@sipanda2025!');
        $this->command->warn('⚠ Sampel PIC OPD: pic.dinkes@trenggalek.go.id | sipanda2025');
    }
}
