<?php

namespace Database\Seeders;

use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\PenugasanTim;
use App\Models\Pkppt;
use App\Models\SumberPenugasan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $irbanList = Irban::where('nama_irban', '!=', 'Sekretariat')->get();
        $jenisList = JenisPenugasan::all();
        $sumberList = SumberPenugasan::all();
        $objekList = ObjekPenugasan::all();
        $usersList = User::where('tipe_akun', 'internal')->get();
        $adminUser = User::first();

        if ($irbanList->isEmpty() || $usersList->isEmpty()) return;

        // 1. Buat 5 Rencana PKPPT Tahun 2025
        $pkpptData = [
            [
                'tahun' => 2025,
                'area_pengawasan' => 'Audit Kinerja Pengelolaan Keuangan Daerah',
                'jenis_pengawasan' => 'Audit',
                'sasaran' => 'Sekretariat Daerah & BKAD',
                'rencana_mulai' => '2025-02-01',
                'rencana_selesai_laporan' => '2025-03-31',
                'jumlah_laporan_rencana' => 2,
                'irban_id' => $irbanList[0]->id,
                'status' => 'ditetapkan',
                'dibuat_oleh' => $adminUser->id,
            ],
            [
                'tahun' => 2025,
                'area_pengawasan' => 'Monitoring & Evaluasi Pengadaan Barang dan Jasa',
                'jenis_pengawasan' => 'Monitoring dan Evaluasi',
                'sasaran' => 'Dinas PUPR & RSUD dr. Soedomo',
                'rencana_mulai' => '2025-04-01',
                'rencana_selesai_laporan' => '2025-05-30',
                'jumlah_laporan_rencana' => 2,
                'irban_id' => $irbanList[1]->id,
                'status' => 'ditetapkan',
                'dibuat_oleh' => $adminUser->id,
            ],
            [
                'tahun' => 2025,
                'area_pengawasan' => 'Reviu Laporan Keuangan Pemerintah Daerah (LKPD)',
                'jenis_pengawasan' => 'Reviu',
                'sasaran' => 'Seluruh Perangkat Daerah (OPD)',
                'rencana_mulai' => '2025-01-15',
                'rencana_selesai_laporan' => '2025-02-28',
                'jumlah_laporan_rencana' => 1,
                'irban_id' => $irbanList[2]->id,
                'status' => 'ditetapkan',
                'dibuat_oleh' => $adminUser->id,
            ],
            [
                'tahun' => 2025,
                'area_pengawasan' => 'Audit Investigatif Khusus',
                'jenis_pengawasan' => 'Audit',
                'sasaran' => 'Kecamatan Trenggalek & Karangan',
                'rencana_mulai' => '2025-06-01',
                'rencana_selesai_laporan' => '2025-07-31',
                'jumlah_laporan_rencana' => 1,
                'irban_id' => $irbanList[3]->id ?? $irbanList[0]->id,
                'status' => 'ditetapkan',
                'dibuat_oleh' => $adminUser->id,
            ],
        ];

        $createdPkppt = [];
        foreach ($pkpptData as $data) {
            $createdPkppt[] = Pkppt::create($data);
        }

        // 2. Buat Penugasan (SPT) Sampel
        $sptSamples = [
            [
                'no_spt' => '700/01/406.008/2025',
                'pkppt_id' => $createdPkppt[2]->id,
                'is_sesuai_pkppt' => true,
                'uraian_penugasan' => 'Reviu Laporan Keuangan Pemerintah Daerah (LKPD) Kabupaten Trenggalek Tahun Anggaran 2024',
                'sumber_penugasan_id' => $sumberList->where('nama', 'Mandatory')->first()->id ?? 1,
                'jenis_penugasan_id' => $jenisList->where('nama', 'Reviu')->first()->id ?? 1,
                'tanggal_mulai' => '2025-01-15',
                'tanggal_selesai' => '2025-02-25',
                'status' => 'selesai',
                'progres_persen' => 100,
                'keterangan_hasil' => 'Laporan reviu LKPD telah diselesaikan dan diserahkan kepada Sekretaris Daerah.',
                'irban_id' => $createdPkppt[2]->irban_id,
                'dibuat_oleh' => $adminUser->id,
            ],
            [
                'no_spt' => '700/02/406.008/2025',
                'pkppt_id' => $createdPkppt[0]->id,
                'is_sesuai_pkppt' => true,
                'uraian_penugasan' => 'Audit Kinerja Pengelolaan Keuangan dan Aset Daerah pada BKAD Kabupaten Trenggalek',
                'sumber_penugasan_id' => $sumberList->where('nama', 'Manajemen Risiko')->first()->id ?? 1,
                'jenis_penugasan_id' => $jenisList->where('nama', 'Audit')->first()->id ?? 1,
                'tanggal_mulai' => '2025-02-01',
                'tanggal_selesai' => '2025-03-20',
                'status' => 'berjalan',
                'progres_persen' => 65,
                'keterangan_hasil' => null,
                'irban_id' => $createdPkppt[0]->irban_id,
                'dibuat_oleh' => $adminUser->id,
            ],
            [
                'no_spt' => '700/03/406.008/2025',
                'pkppt_id' => null,
                'is_sesuai_pkppt' => false,
                'uraian_penugasan' => 'Pendampingan dan Advisory Audit Pengadaan Alkes RSUD dr. Soedomo Trenggalek',
                'sumber_penugasan_id' => $sumberList->where('nama', 'Permintaan')->first()->id ?? 1,
                'jenis_penugasan_id' => $jenisList->where('nama', 'Advisory')->first()->id ?? 1,
                'tanggal_mulai' => '2025-02-10',
                'tanggal_selesai' => '2025-03-10',
                'status' => 'berjalan',
                'progres_persen' => 40,
                'keterangan_hasil' => null,
                'irban_id' => $irbanList[1]->id,
                'dibuat_oleh' => $adminUser->id,
            ],
        ];

        foreach ($sptSamples as $spt) {
            $penugasan = Penugasan::create($spt);

            // Attach 2 random objek
            $objekIds = $objekList->random(2)->pluck('id')->toArray();
            $penugasan->objekPenugasan()->sync($objekIds);

            // Attach susunan tim
            $randomUsers = $usersList->random(min(4, $usersList->count()))->pluck('id')->toArray();
            PenugasanTim::create(['penugasan_id' => $penugasan->id, 'user_id' => $randomUsers[0], 'peran' => 'wakil_penanggung_jawab']);
            if (isset($randomUsers[1])) PenugasanTim::create(['penugasan_id' => $penugasan->id, 'user_id' => $randomUsers[1], 'peran' => 'pengendali_teknis']);
            if (isset($randomUsers[2])) PenugasanTim::create(['penugasan_id' => $penugasan->id, 'user_id' => $randomUsers[2], 'peran' => 'ketua_tim']);
            if (isset($randomUsers[3])) PenugasanTim::create(['penugasan_id' => $penugasan->id, 'user_id' => $randomUsers[3], 'peran' => 'anggota_tim']);
        }

        $this->command->info('✓ 4 Rencana PKPPT dan 3 Penugasan Sampel berhasil dibuat.');
    }
}
