<?php

use App\Models\RegulasiHukum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Nonaktifkan baku untuk Perbup 45/2021 lama jika ada
        RegulasiHukum::where('nomor_regulasi', 'like', '%45%2021%')
            ->where('judul', 'like', '%Kedudukan, Susunan Organisasi%')
            ->update(['is_dasar_spt_baku' => false]);

        // 2. Tambah / update Perbup No. 36 Tahun 2025 sebagai regulasi baku
        RegulasiHukum::updateOrCreate(
            ['nomor_regulasi' => 'Nomor 36 Tahun 2025'],
            [
                'judul'               => 'Kedudukan, Susunan Organisasi, Tugas dan Fungsi Serta Tata Kerja Inspektorat',
                'tahun'               => 2025,
                'jenis_regulasi'      => 'perkada',
                'kategori'            => 'umum',
                'ringkasan_eksekutif' => 'Regulasi mandat kedudukan, wewenang, susunan organisasi, dan fungsi pengawasan Inspektorat Kabupaten Trenggalek.',
                'visibilitas'         => 'publik',
                'is_dasar_spt_baku'   => true,
            ]
        );

        // 3. Pastikan Perda No. 10 Tahun 2016 terupdate jenis_regulasi = 'perda'
        RegulasiHukum::updateOrCreate(
            ['nomor_regulasi' => 'Nomor 10 Tahun 2016'],
            [
                'judul'               => 'Pembentukan dan Susunan Perangkat Daerah Kabupaten Trenggalek',
                'tahun'               => 2016,
                'jenis_regulasi'      => 'perda',
                'kategori'            => 'umum',
                'ringkasan_eksekutif' => 'Landasan hukum kelembagaan seluruh perangkat daerah di lingkungan Pemkab Trenggalek.',
                'visibilitas'         => 'publik',
                'is_dasar_spt_baku'   => true,
            ]
        );

        // 4. Pastikan SK PKPT 2026 terupdate jenis_regulasi = 'sk_kepala_daerah'
        RegulasiHukum::updateOrCreate(
            ['nomor_regulasi' => 'Nomor 188.45/12/406.008/2026'],
            [
                'judul'               => 'Program Kerja Pengawasan Tahunan (PKPT) Berbasis Risiko Inspektorat Kabupaten Trenggalek Tahun Anggaran 2026',
                'tahun'               => 2026,
                'jenis_regulasi'      => 'sk_kepala_daerah',
                'kategori'            => 'keuangan',
                'ringkasan_eksekutif' => 'Keputusan penetapan agenda pengawasan tahunan berbasis risiko bagi seluruh Irban.',
                'visibilitas'         => 'internal',
                'is_dasar_spt_baku'   => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
