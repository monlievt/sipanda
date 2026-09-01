<?php

namespace Database\Seeders;

use App\Models\UatFeedback;
use App\Models\User;
use Illuminate\Database\Seeder;

class UatFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $auditor = User::role('auditor')->first();

        UatFeedback::firstOrCreate(
            ['judul' => 'Saran penambahan shortcut tombol cetak langsung dari tabel SPT'],
            [
                'user_id'       => $auditor?->id ?? 1,
                'guard_type'    => 'web',
                'nama_pelapor'  => $auditor?->nama ?? 'Benno Hera (Auditor Madya)',
                'email_pelapor' => $auditor?->email ?? 'bennohera100@gmail.com',
                'role_pelapor'  => 'Ketua Tim Auditor',
                'kategori'      => 'saran',
                'urgensi'       => 'sedang',
                'url_halaman'   => url('/penugasan'),
                'deskripsi'     => 'Akan sangat membantu jika pada tabel penugasan ada tombol cepat "Cetak" di samping tombol "Detail", sehingga auditor tidak perlu masuk ke halaman detail terlebih dahulu.',
                'browser_info'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0.0.0 | Screen: 1920x1080',
                'status'        => 'diperbaiki',
                'catatan_admin' => 'Fitur tombol cetak langsung sudah ditambahkan pada kolom aksi tabel penugasan.',
                'ditindaklanjuti_pada' => now(),
            ]
        );

        UatFeedback::firstOrCreate(
            ['judul' => 'Contoh laporan pengujian: Pertanyaan format dokumen pendukung SBM'],
            [
                'guard_type'    => 'opd',
                'nama_pelapor'  => 'Operator Keuangan Dinkes',
                'email_pelapor' => 'pic.dinkes@trenggalek.go.id',
                'role_pelapor'  => 'Auditi OPD (Dinas Kesehatan)',
                'kategori'      => 'pertanyaan',
                'urgensi'       => 'rendah',
                'url_halaman'   => url('/opd/dashboard'),
                'deskripsi'     => 'Apakah untuk bukti perjalanan dinas luar daerah wajib melampirkan foto dokumentasi kegiatan atau cukup SPT dan tiket transportasi?',
                'browser_info'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/121.0.0.0 | Screen: 1366x768',
                'status'        => 'baru',
            ]
        );
    }
}
