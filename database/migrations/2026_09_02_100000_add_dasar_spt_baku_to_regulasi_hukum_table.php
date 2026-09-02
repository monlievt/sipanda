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
        Schema::table('regulasi_hukum', function (Blueprint $table) {
            $table->boolean('is_dasar_spt_baku')->default(false)->after('visibilitas');
            $table->index('is_dasar_spt_baku');
        });

        // Masukkan / Pastikan 3 Regulasi Baku Dasar SPT Kabupaten Trenggalek tersedia
        $dasarBakuData = [
            [
                'judul'             => 'Kedudukan, Susunan Organisasi, Tugas dan Fungsi serta Tata Kerja Inspektorat Daerah Kabupaten Trenggalek',
                'nomor_regulasi'    => 'Nomor 45 Tahun 2021',
                'tahun'             => 2021,
                'jenis_regulasi'    => 'perbup',
                'kategori'          => 'umum',
                'ringkasan_eksekutif'=> 'Regulasi mandat kedudukan, wewenang, dan fungsi pengawasan Inspektorat Kabupaten Trenggalek.',
                'visibilitas'       => 'publik',
                'is_dasar_spt_baku' => true,
            ],
            [
                'judul'             => 'Pembentukan dan Susunan Perangkat Daerah Kabupaten Trenggalek',
                'nomor_regulasi'    => 'Nomor 10 Tahun 2016',
                'tahun'             => 2016,
                'jenis_regulasi'    => 'perda',
                'kategori'          => 'umum',
                'ringkasan_eksekutif'=> 'Landasan hukum kelembagaan seluruh perangkat daerah di lingkungan Pemkab Trenggalek.',
                'visibilitas'       => 'publik',
                'is_dasar_spt_baku' => true,
            ],
            [
                'judul'             => 'Program Kerja Pengawasan Tahunan (PKPT) Berbasis Risiko Inspektorat Kabupaten Trenggalek Tahun Anggaran 2026',
                'nomor_regulasi'    => 'Nomor 188.45/12/406.008/2026',
                'tahun'             => 2026,
                'jenis_regulasi'    => 'perbup',
                'kategori'          => 'keuangan',
                'ringkasan_eksekutif'=> 'Keputusan penetapan agenda pengawasan tahunan berbasis risiko bagi seluruh Irban.',
                'visibilitas'       => 'internal',
                'is_dasar_spt_baku' => true,
            ],
        ];

        foreach ($dasarBakuData as $item) {
            RegulasiHukum::firstOrCreate(
                ['nomor_regulasi' => $item['nomor_regulasi']],
                $item
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulasi_hukum', function (Blueprint $table) {
            $table->dropIndex(['is_dasar_spt_baku']);
            $table->dropColumn('is_dasar_spt_baku');
        });
    }
};
