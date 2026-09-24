<?php

namespace Database\Seeders;

use App\Models\KelompokPengawasan;
use Illuminate\Database\Seeder;

class KelompokPengawasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clusters = [
            [
                'nama_kelompok' => 'Pengawasan Proyek Strategis Daerah (PSD)',
                'kode_kelompok' => 'PSD',
                'deskripsi_singkat' => 'Pengawasan yang dilakukan atas Proyek yang tercantum secara resmi dalam Keputusan Bupati / Peraturan Kepala Daerah tentang Proyek Strategis Daerah.',
                'bentuk_pengawasan' => "• Reviu DAK dan/atau DAU yang mendanai Proyek PSD.\n• Monitoring Proyek Strategis Daerah.\n• Audit dengan Ruang Lingkup Proyek Strategis Daerah.\n• Pengawasan lain yang terkait dengan Proyek Strategis Daerah (PSD).",
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'nama_kelompok' => 'Pengawasan Keuangan dan Aset Daerah',
                'kode_kelompok' => 'KEU_ASET',
                'deskripsi_singkat' => 'Pengawasan atas Keuangan dan Aset Daerah serta BUMD Kabupaten Trenggalek.',
                'bentuk_pengawasan' => "• Reviu DAK Reguler/Non-Fisik dan/atau DAU yang mendanai kegiatan OPD.\n• Monitoring DAK, Monitoring BOS, dan monitoring pengelolaan keuangan lainnya.\n• Reviu LKPD, Reviu KUA-PPAS dan Reviu KUPA-PPAS.\n• Audit / Pemeriksaan dengan ruang lingkup pengelolaan keuangan dan/atau pengelolaan aset pada Perangkat Daerah atau BUMD Kabupaten Trenggalek.\n• Audit / Pemeriksaan dengan ruang lingkup pengadaan barang/jasa pada Perangkat Daerah atau BUMD Kabupaten Trenggalek.\n• Pengawasan lain yang terkait dengan Pengawasan Keuangan atau Pengelolaan Aset Daerah.",
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'nama_kelompok' => 'Pengawasan Keuangan Desa',
                'kode_kelompok' => 'KEU_DESA',
                'deskripsi_singkat' => 'Pengawasan yang dilakukan terhadap tata kelola pemerintahan dan pengelolaan keuangan pada Pemerintah Desa di Kabupaten Trenggalek.',
                'bentuk_pengawasan' => "• Monitoring APBDes dan kegiatan keuangan desa lainnya.\n• Audit / Pemeriksaan Umum yang ruang lingkupnya adalah pengelolaan keuangan pada Pemerintah Desa di Kabupaten Trenggalek.\n• Pengawasan lain yang terkait dengan tata kelola keuangan Pemerintah Desa.",
                'urutan' => 3,
                'is_active' => true,
            ],
            [
                'nama_kelompok' => 'Pengawasan Kinerja',
                'kode_kelompok' => 'KINERJA',
                'deskripsi_singkat' => 'Pengawasan terhadap pencapaian target kinerja, efisiensi, dan efektivitas Perangkat Daerah serta BUMD Kabupaten Trenggalek.',
                'bentuk_pengawasan' => "• Evaluasi AKIP.\n• Reviu LPPD.\n• Reviu RPJMD.\n• Reviu RKPD.\n• Audit Kinerja pada Perangkat Daerah atau BUMD.\n• Pengawasan lain yang terkait dengan pengelolaan kinerja perangkat daerah atau BUMD.",
                'urutan' => 4,
                'is_active' => true,
            ],
            [
                'nama_kelompok' => 'Pengawasan Khusus',
                'kode_kelompok' => 'KHUSUS',
                'deskripsi_singkat' => 'Pengawasan yang dilaksanakan berdasarkan penugasan khusus, pengaduan masyarakat, atau indikasi penyimpangan sesuai kewenangan Inspektorat.',
                'bentuk_pengawasan' => "• Telaah atas Pengaduan.\n• Audit / Pemeriksaan Dengan Tujuan Tertentu atas Pengaduan Masyarakat (langsung / pelimpahan APH).\n• Audit / Pemeriksaan Investigatif.\n• Audit / Pemeriksaan Perhitungan Kerugian Negara (PKKN).",
                'urutan' => 5,
                'is_active' => true,
            ],
            [
                'nama_kelompok' => 'Pengawasan dan Pembinaan Lainnya',
                'kode_kelompok' => 'LAINNYA',
                'deskripsi_singkat' => 'Kelompok pengawasan dan pembinaan yang tidak termasuk dalam lima pengelompokkan di atas.',
                'bentuk_pengawasan' => "• Narasumber atas Permintaan Instansi lain sesuai dengan kewenangan Inspektorat.\n• Pembinaan / Pendampingan / Asistensi sesuai dengan kewenangan Inspektorat.",
                'urutan' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($clusters as $c) {
            KelompokPengawasan::updateOrCreate(
                ['kode_kelompok' => $c['kode_kelompok']],
                $c
            );
        }
    }
}
