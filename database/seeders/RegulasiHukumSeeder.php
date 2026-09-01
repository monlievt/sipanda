<?php

namespace Database\Seeders;

use App\Models\RegulasiHukum;
use Illuminate\Database\Seeder;

class RegulasiHukumSeeder extends Seeder
{
    public function run(): void
    {
        $regulasis = [
            [
                'judul'               => 'Standar Biaya Masukan (SBM) dan Pedoman Pelaksanaan Anggaran Pemerintah Kabupaten Trenggalek',
                'nomor_regulasi'      => 'Perbup Trenggalek No. 42 Tahun 2025',
                'tahun'               => 2025,
                'jenis_regulasi'      => 'perbup',
                'kategori'            => 'keuangan',
                'ringkasan_eksekutif' => "Mengatur batas tertinggi honorarium narasumber/moderator/panitia, satuan biaya uang harian perjalanan dinas dalam/luar daerah, biaya penginapan, sewa kendaraan dinas, serta konsumsi rapat kedinasan di lingkungan Pemkab Trenggalek.",
                'visibilitas'         => 'publik',
                'diunduh_count'       => 142,
            ],
            [
                'judul'               => 'Pedoman Pengadaan Barang/Jasa Pemerintah Melalui e-Purchasing dan Pengadaan Langsung',
                'nomor_regulasi'      => 'Perpres No. 12 Tahun 2021 jo. Perpres No. 16/2018',
                'tahun'               => 2021,
                'jenis_regulasi'      => 'perpres',
                'kategori'            => 'pbj',
                'ringkasan_eksekutif' => "Mengatur batas nilai pengadaan langsung barang/pekerjaan konstruksi/jasa lainnya s.d. Rp200.000.000 (dua ratus juta rupiah) dan jasa konsultansi s.d. Rp100.000.000 (seratus juta rupiah). Mewajibkan prioritas produk dalam negeri (PDN) dan e-Katalog Lokal Trenggalek.",
                'visibilitas'         => 'publik',
                'diunduh_count'       => 215,
            ],
            [
                'judul'               => 'Pedoman Teknis Pengelolaan Keuangan Daerah',
                'nomor_regulasi'      => 'Permendagri No. 77 Tahun 2020',
                'tahun'               => 2020,
                'jenis_regulasi'      => 'permendagri',
                'kategori'            => 'keuangan',
                'ringkasan_eksekutif' => "Pedoman komprehensif penatausahaan APBD, tugas dan wewenang PA/KPA, PPTK, Bendahara Pengeluaran, mekanisme UP/GU/TU/LS, serta tata cara pemberian dan pertanggungjawaban Belanja Hibah dan Bantuan Sosial.",
                'visibilitas'         => 'publik',
                'diunduh_count'       => 98,
            ],
            [
                'judul'               => 'Pengelolaan Keuangan Desa dan Akuntabilitas Penatausahaan APBDes',
                'nomor_regulasi'      => 'Permendagri No. 20 Tahun 2018',
                'tahun'               => 2018,
                'jenis_regulasi'      => 'permendagri',
                'kategori'            => 'desa',
                'ringkasan_eksekutif' => "Mengatur asas pengelolaan keuangan desa (transparan, akuntabel, partisipatif), siklus perencanaan, pelaksanaan, penatausahaan melalui Siskeudes, syarat penyaluran Dana Desa/ADD, dan pelaporan pertanggungjawaban Kepala Desa.",
                'visibilitas'         => 'publik',
                'diunduh_count'       => 189,
            ],
            [
                'judul'               => 'Pedoman Tata Cara Pengelolaan dan Penghapusan Barang Milik Daerah (BMD) Kabupaten Trenggalek',
                'nomor_regulasi'      => 'Perda Trenggalek No. 8 Tahun 2023',
                'tahun'               => 2023,
                'jenis_regulasi'      => 'perda',
                'kategori'            => 'aset',
                'ringkasan_eksekutif' => "Mengatur sensus barang milik daerah, inventarisasi KIB A s.d. F, mekanisme pinjam pakai aset antar OPD, hibah aset, serta tata cara lelang dan penghapusan BMD yang rusak berat/hilang.",
                'visibilitas'         => 'publik',
                'diunduh_count'       => 76,
            ],
            [
                'judul'               => 'Disiplin Pegawai Negeri Sipil dan Kode Etik Aparatur Sipil Negara',
                'nomor_regulasi'      => 'PP No. 94 Tahun 2021',
                'tahun'               => 2021,
                'jenis_regulasi'      => 'pp',
                'kategori'            => 'kepegawaian',
                'ringkasan_eksekutif' => "Mengatur kewajiban dan larangan bagi ASN, tingkat dan jenis hukuman disiplin (ringan, sedang, berat), penanganan ketidakhadiran tanpa izin, larangan benturan kepentingan, serta netralitas ASN.",
                'visibilitas'         => 'publik',
                'diunduh_count'       => 112,
            ],
        ];

        foreach ($regulasis as $reg) {
            RegulasiHukum::firstOrCreate(
                ['nomor_regulasi' => $reg['nomor_regulasi']],
                $reg
            );
        }

        $this->command->info('✓ RegulasiHukumSeeder: Berhasil memasukkan 6 regulasi dasar pengawasan.');
    }
}
