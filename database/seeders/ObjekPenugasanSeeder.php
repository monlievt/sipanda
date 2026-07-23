<?php

namespace Database\Seeders;

use App\Models\ObjekPenugasan;
use Illuminate\Database\Seeder;

class ObjekPenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $opdList = [
            // Dinas / Badan Kabupaten Trenggalek
            ['nama' => 'Sekretariat Daerah', 'kategori' => 'opd'],
            ['nama' => 'Sekretariat DPRD', 'kategori' => 'opd'],
            ['nama' => 'Inspektorat', 'kategori' => 'opd'],
            ['nama' => 'Dinas Pendidikan, Pemuda dan Olahraga', 'kategori' => 'opd'],
            ['nama' => 'Dinas Kesehatan, Pengendalian Penduduk dan Keluarga Berencana', 'kategori' => 'opd'],
            ['nama' => 'Dinas Pekerjaan Umum dan Penataan Ruang', 'kategori' => 'opd'],
            ['nama' => 'Dinas Perumahan, Kawasan Permukiman dan Lingkungan Hidup', 'kategori' => 'opd'],
            ['nama' => 'Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak', 'kategori' => 'opd'],
            ['nama' => 'Dinas Koperasi, Usaha Mikro dan Perdagangan', 'kategori' => 'opd'],
            ['nama' => 'Dinas Perindustrian dan Tenaga Kerja', 'kategori' => 'opd'],
            ['nama' => 'Dinas Pertanian dan Pangan', 'kategori' => 'opd'],
            ['nama' => 'Dinas Perikanan', 'kategori' => 'opd'],
            ['nama' => 'Dinas Perhubungan', 'kategori' => 'opd'],
            ['nama' => 'Dinas Komunikasi dan Informatika', 'kategori' => 'opd'],
            ['nama' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'kategori' => 'opd'],
            ['nama' => 'Dinas Kearsipan dan Perpustakaan', 'kategori' => 'opd'],
            ['nama' => 'Satuan Polisi Pamong Praja dan Pemadam Kebakaran', 'kategori' => 'opd'],
            ['nama' => 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah', 'kategori' => 'opd'],
            ['nama' => 'Badan Keuangan dan Aset Daerah', 'kategori' => 'opd'],
            ['nama' => 'Badan Kepegawaian Daerah', 'kategori' => 'opd'],
            ['nama' => 'Badan Kesatuan Bangsa dan Politik', 'kategori' => 'opd'],
            ['nama' => 'Badan Penanggulangan Bencana Daerah', 'kategori' => 'opd'],
            ['nama' => 'RSUD dr. Soedomo Trenggalek', 'kategori' => 'opd'],

            // Kecamatan di Trenggalek (14 Kecamatan)
            ['nama' => 'Kecamatan Trenggalek', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Pogalan', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Durenan', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Gandusari', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Karangan', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Suruh', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Tugu', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Pule', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Dongko', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Panggul', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Munjungan', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Watulimo', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Kampak', 'kategori' => 'kecamatan'],
            ['nama' => 'Kecamatan Bendungan', 'kategori' => 'kecamatan'],
        ];

        foreach ($opdList as $item) {
            ObjekPenugasan::firstOrCreate(['nama' => $item['nama']], $item);
        }

        $this->command->info('✓ 37 Master Objek Penugasan (OPD & Kecamatan Trenggalek) berhasil dibuat.');
    }
}
