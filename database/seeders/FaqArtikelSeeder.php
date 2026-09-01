<?php

namespace Database\Seeders;

use App\Models\FaqArtikel;
use App\Models\RegulasiHukum;
use Illuminate\Database\Seeder;

class FaqArtikelSeeder extends Seeder
{
    public function run(): void
    {
        $regSbm = RegulasiHukum::where('nomor_regulasi', 'like', '%Perbup Trenggalek No. 42%')->first();
        $regPbj = RegulasiHukum::where('nomor_regulasi', 'like', '%Perpres No. 12%')->first();
        $regKeu = RegulasiHukum::where('nomor_regulasi', 'like', '%Permendagri No. 77%')->first();
        $regDesa = RegulasiHukum::where('nomor_regulasi', 'like', '%Permendagri No. 20%')->first();
        $regAset = RegulasiHukum::where('nomor_regulasi', 'like', '%Perda Trenggalek No. 8%')->first();

        $faqs = [
            // ─── PBJ ───────────────────────────────────────────
            [
                'pertanyaan'          => 'Berapa batas nilai nominal pengadaan langsung barang, jasa lainnya, dan jasa konsultansi?',
                'jawaban'             => "Berdasarkan Perpres PBJ No. 12/2021 Pasal 38 ayat (1) dan (2):\n1. Pengadaan Langsung Barang/Pekerjaan Konstruksi/Jasa Lainnya dilaksanakan untuk pengadaan yang bernilai paling tinggi Rp200.000.000,00 (dua ratus juta rupiah).\n2. Pengadaan Langsung Jasa Konsultansi dilaksanakan untuk pekerjaan yang bernilai paling tinggi Rp100.000.000,00 (seratus juta rupiah).\n3. Pelaksanaan dilakukan melalui Pejabat Pengadaan (PP) dengan bukti transaksi berupa bukti pembelian/kuitansi/SPK sesuai jenjang nilai.",
                'kategori'            => 'pbj',
                'regulasi_hukum_id'   => $regPbj?->id,
                'dasar_hukum_rujukan' => 'Perpres No. 12 Tahun 2021 Pasal 38',
                'urutan'              => 1,
            ],
            [
                'pertanyaan'          => 'Apakah OPD wajib membeli barang/jasa melalui e-Katalog Lokal Trenggalek?',
                'jawaban'             => "Ya, KPA/PPK wajib memprioritaskan pemanfaatan e-Purchasing (Katalog Elektronik Nasional, Sektoral, dan Lokal Trenggalek) serta Toko Daring untuk barang/jasa yang sudah tayang, khususnya yang memiliki Tingkat Komponen Dalam Negeri (TKDN) minimal 25% atau produk UMK/Koperasi lokal.",
                'kategori'            => 'pbj',
                'regulasi_hukum_id'   => $regPbj?->id,
                'dasar_hukum_rujukan' => 'Inpres No. 2 Tahun 2022 & Perpres No. 12/2021',
                'urutan'              => 2,
            ],
            [
                'pertanyaan'          => 'Bolehkah melakukan pemecahan paket pengadaan untuk menghindari tender/seleksi?',
                'jawaban'             => "DILARANG. Pasal 20 ayat (2) Perpres No. 16/2018 jo. Perpres 12/2021 secara tegas melarang PPK/PA menyatukan atau memecah pengadaan barang/jasa yang menurut sifat dan jenis pekerjaannya harus menjadi satu kesatuan demi menghindari tender atau seleksi. Pemecahan paket yang disengaja merupakan temuan audit kepatuhan.",
                'kategori'            => 'pbj',
                'regulasi_hukum_id'   => $regPbj?->id,
                'dasar_hukum_rujukan' => 'Perpres No. 12 Tahun 2021 Pasal 20 ayat (2)',
                'urutan'              => 3,
            ],

            // ─── KEUANGAN & SBM ─────────────────────────────────
            [
                'pertanyaan'          => 'Bagaimana ketentuan pembayaran honorarium narasumber PNS dari instansi penyelenggara sendiri?',
                'jawaban'             => "Sesuai Standar Biaya Masukan (SBM) Perbup Trenggalek No. 42/2025:\n1. Narasumber yang berasal dari internal OPD penyelenggara pada prinsipnya TIDAK diberikan honorarium apabila materi yang disampaikan merupakan tugas pokok dan fungsinya (tupoksi).\n2. Honorarium narasumber hanya dapat diberikan apabila narasumber berasal dari luar OPD penyelenggara atau memiliki keahlian/sertifikasi khusus di luar jam kerja kedinasan reguler dengan persetujuan KPA.",
                'kategori'            => 'keuangan',
                'regulasi_hukum_id'   => $regSbm?->id,
                'dasar_hukum_rujukan' => 'Perbup Trenggalek SBM No. 42/2025 Lampiran I',
                'urutan'              => 4,
            ],
            [
                'pertanyaan'          => 'Apa saja dokumen bukti pertanggungjawaban wajib untuk biaya perjalanan dinas luar daerah?',
                'jawaban'             => "Pertanggungjawaban perjalanan dinas luar daerah wajib melampirkan:\n1. Surat Perintah Tugas (SPT) yang ditandatangani pejabat berwenang.\n2. Surat Perjalanan Dinas (SPD) lembar 1 dan lembar 2 yang telah distempel/ditandatangani pejabat instansi tujuan.\n3. Rincian Biaya Perjalanan Dinas (kuitansi riil/at cost untuk tiket transportasi, boarding pass, dan bill hotel resmi).\n4. Laporan Hasil Perjalanan Dinas yang memuat agenda dan output kegiatan.",
                'kategori'            => 'keuangan',
                'regulasi_hukum_id'   => $regSbm?->id,
                'dasar_hukum_rujukan' => 'Perbup Trenggalek No. 42/2025 & Permendagri No. 77/2020',
                'urutan'              => 5,
            ],
            [
                'pertanyaan'          => 'Berapa batas waktu penerbitan dan penyelesaian SPJ Uang Persediaan (UP/GU) oleh Bendahara Pengeluaran?',
                'jawaban'             => "Pengajuan ganti uang persediaan (SPP-GU) dapat dilakukan apabila sisa UP telah digunakan minimal 50% atau sesuai ketentuan kas daerah. Pertanggungjawaban belanja LS/GU wajib diselesaikan paling lambat pada akhir bulan berkenaan atau tanggal 10 bulan berikutnya.",
                'kategori'            => 'keuangan',
                'regulasi_hukum_id'   => $regKeu?->id,
                'dasar_hukum_rujukan' => 'Permendagri No. 77 Tahun 2020 Bab V',
                'urutan'              => 6,
            ],

            // ─── DANA DESA ─────────────────────────────────────
            [
                'pertanyaan'          => 'Apa syarat utama penyaluran Dana Desa (DD) Tahap II ke Rekening Kas Desa (RKD)?',
                'jawaban'             => "Syarat penyaluran Dana Desa Tahap II meliputi:\n1. Laporan realisasi penyerapan Dana Desa Tahap I minimal mencapai 50%.\n2. Laporan capaian keluaran (output) kegiatan fisik/non-fisik minimal mencapai 35%.\n3. Rekonsiliasi sisa kas tahun anggaran sebelumnya dan input lengkap pada aplikasi OMSPAN / Siskeudes.",
                'kategori'            => 'desa',
                'regulasi_hukum_id'   => $regDesa?->id,
                'dasar_hukum_rujukan' => 'PMK Pengelolaan Dana Desa & Permendagri No. 20/2018',
                'urutan'              => 7,
            ],
            [
                'pertanyaan'          => 'Bolehkah Kepala Desa merangkap jabatan sebagai pelaksana pengadaan atau bendahara desa?',
                'jawaban'             => "DILARANG. Berdasarkan Permendagri No. 20/2018, Kepala Desa adalah Pemegang Kekuasaan Pengelolaan Keuangan Desa (PKPKD). Kepala Desa melimpahkan sebagian kewenangannya kepada PPKD (Sekretaris Desa, Kaur/Kasi, dan Bendahara Desa). Kepala Desa tidak boleh merangkap sebagai bendahara atau pelaksana operasional pengadaan guna mencegah benturan kepentingan (*segregation of duties*).",
                'kategori'            => 'desa',
                'regulasi_hukum_id'   => $regDesa?->id,
                'dasar_hukum_rujukan' => 'Permendagri No. 20 Tahun 2018 Pasal 3 & 4',
                'urutan'              => 8,
            ],
            [
                'pertanyaan'          => 'Bagaimana mekanisme pengelolaan Sisa Lebih Perhitungan Anggaran (SiLPA) Dana Desa pada tahun berikutnya?',
                'jawaban'             => "SiLPA Dana Desa tahun sebelumnya wajib dianggarkan kembali dalam APBDes tahun anggaran berjalan pada pos Penerimaan Pembiayaan (Akun SiLPA). Penggunaannya harus disesuaikan dengan jenis kegiatan yang belum selesai (*carry over*) atau diprioritaskan untuk kegiatan yang telah disepakati dalam Musdes RKPDes.",
                'kategori'            => 'desa',
                'regulasi_hukum_id'   => $regDesa?->id,
                'dasar_hukum_rujukan' => 'Permendagri No. 20 Tahun 2018 Pasal 56',
                'urutan'              => 9,
            ],

            // ─── ASET & BMD ─────────────────────────────────────
            [
                'pertanyaan'          => 'Apa syarat penghapusan Barang Milik Daerah (BMD) yang mengalami rusak berat atau hilang?',
                'jawaban'             => "Penghapusan BMD memerlukan:\n1. Berita Acara Pemeriksaan Fisik oleh Pengurus Barang Pengguna dan Pejabat Penatausahaan BMD.\n2. Dokumen rekomendasi teknis dari dinas terkait (misal PUPR untuk kendaraan/gedung).\n3. Surat Keputusan (SK) Penghapusan yang ditandatangani oleh Pengelola Barang (Sekretaris Daerah) atas persetujuan Bupati Trenggalek.",
                'kategori'            => 'aset',
                'regulasi_hukum_id'   => $regAset?->id,
                'dasar_hukum_rujukan' => 'Perda Trenggalek No. 8 Tahun 2023 Pasal 45',
                'urutan'              => 10,
            ],
            [
                'pertanyaan'          => 'Bolehkah kendaraan dinas operasional dipinjam-pakaikan kepada instansi vertikal atau yayasan sosial?',
                'jawaban'             => "Pinjam pakai BMD diperbolehkan dengan ketentuan:\n1. Jangka waktu paling lama 5 (lima) tahun dan dapat diperpanjang.\n2. Dituangkan dalam Naskah Perjanjian Pinjam Pakai (NPPP) yang disetujui Bupati/Sekda.\n3. Biaya pemeliharaan dan operasional selama masa pinjam pakai menjadi beban penuh pihak peminjam.",
                'kategori'            => 'aset',
                'regulasi_hukum_id'   => $regAset?->id,
                'dasar_hukum_rujukan' => 'Permendagri No. 19/2016 jo. Perda No. 8/2023',
                'urutan'              => 11,
            ],

            // ─── DISIPLIN ASN ───────────────────────────────────
            [
                'pertanyaan'          => 'Bagaimana sanksi bagi ASN yang tidak masuk kerja tanpa alasan sah secara kumulatif?',
                'jawaban'             => "Berdasarkan PP No. 94 Tahun 2021:\n- Tidak masuk kerja 3 s.d. 10 hari: Teguran Lisan / Tertulis (Hukuman Ringan).\n- Tidak masuk kerja 11 s.d. 20 hari: Pemotongan Tukin / TPP 25% selama 6-12 bulan (Hukuman Sedang).\n- Tidak masuk kerja 21 s.d. 27 hari: Penurunan jabatan setingkat lebih rendah (Hukuman Berat).\n- Tidak masuk kerja 28 hari kerja atau lebih secara kumulatif, atau 10 hari kerja terus menerus: Diberhentikan dengan hormat tidak atas permintaan sendiri sebagai PNS.",
                'kategori'            => 'kepegawaian',
                'regulasi_hukum_id'   => null,
                'dasar_hukum_rujukan' => 'PP No. 94 Tahun 2021 Pasal 11',
                'urutan'              => 12,
            ],
        ];

        foreach ($faqs as $f) {
            FaqArtikel::firstOrCreate(
                ['pertanyaan' => $f['pertanyaan']],
                [
                    'jawaban'              => $f['jawaban'],
                    'kategori'             => $f['kategori'],
                    'regulasi_hukum_id'    => $f['regulasi_hukum_id'],
                    'dasar_hukum_rujukan'  => $f['dasar_hukum_rujukan'],
                    'is_published'         => true,
                    'urutan'               => $f['urutan'],
                ]
            );
        }

        $this->command->info('✓ FaqArtikelSeeder: Berhasil memasukkan 12 artikel tanya-jawab resmi APIP.');
    }
}
