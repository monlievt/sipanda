# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# SIPANDA Web — Sistem Informasi Pengawasan Terintegrasi
Inspektorat Kabupaten Trenggalek

Versi: 1.0
Sumber: Proposal Inovasi Instansi Pemerintah "SIPANDA" (dasar hukum: Perbup Trenggalek No. 1
Tahun 2024 tentang Kedudukan, Susunan Organisasi, Tugas dan Fungsi, serta Tata Kerja Inspektorat)

---

## 1. LATAR BELAKANG & MASALAH

Inspektorat belum memiliki sistem/media pemantauan pelaksanaan PKPPT (Program Kerja Pengawasan
dan Pembinaan Tahunan) secara simultan. Dampaknya:
- Keterlambatan penyusunan laporan hasil pembinaan dan pengawasan.
- Capaian kinerja yang dilaporkan kurang akurat.

SIPANDA versi awal (Google Spreadsheet) sudah mulai menjawab masalah ini, namun terbatas pada
tiga hal: belum terintegrasi dengan data tindak lanjut, input manual rawan error, dan tidak
ada visibilitas beban kerja personil. **PRD ini mendefinisikan versi web-based** untuk menutup
ketiga celah tersebut sambil mempertahankan seluruh proses bisnis yang sudah berjalan.

## 2. TUJUAN PRODUK

1. Menyediakan media pelaporan & pemantauan kegiatan pengawasan berbasis web yang bisa diakses
   real-time oleh Irban dan Inspektur.
2. Meningkatkan akuntabilitas dan transparansi internal pelaksanaan PKPPT.
3. Mengintegrasikan pengingat jadwal kegiatan (email + opsional Google Calendar).
4. **(Baru)** Mengintegrasikan data tindak lanjut pengawasan dan arsip digital.
5. **(Baru)** Mengotomasi perbandingan PKPPT vs realisasi (bukan rekap manual).
6. **(Baru)** Menampilkan beban penugasan per personil dalam periode tertentu.

## 3. SASARAN PENGGUNA (ROLE)

| Role | Slug | Deskripsi | Kebutuhan Utama |
|---|---|---|---|
| Admin Sistem | `admin` | Pengelola teknis aplikasi (mis. Pranata Komputer) | CRUD penuh, audit log, kelola semua pengguna |
| Sekretariat | `sekretariat` | Staf administrasi & tata kelola data | Kelola master data, PKPPT, pengguna, generate draf PKPT |
| Inspektur | `inspektur` | Pimpinan tertinggi Inspektorat (saat ini Plt.) | Melihat rekap menyeluruh seluruh Irban, tetapkan PKPT |
| Admin Irban | `admin_irban` | Staf operasional di tiap Irban (bukan Irban-nya sendiri) | Input & kelola penugasan wilayah Irban-nya sehari-hari |
| Irban (Inspektur Pembantu) | `irban` | Penanggung jawab wilayah/tim tertentu (Irban I–IV) | Supervisi tim, usulkan penyesuaian PKPT, approve manajerial |
| Auditor / P2UPD | `auditor` | Pelaksana teknis pengawasan | Input data penugasan, update status, unggah laporan |
| OPD (objek pemeriksaan) | `opd` | Pihak eksternal yang diperiksa/dibina | Merespons rekomendasi tindak lanjut dengan bukti, lewat area login terpisah |

## 4. LINGKUP (SCOPE)

### 4.1 Dalam Lingkup (In-Scope) — Termasuk MVP
- Autentikasi (login manual + Google SSO), RBAC **6 role internal** (Admin, Sekretariat, Inspektur, Admin Irban, Irban, Auditor) + 1 role eksternal (OPD).
- CRUD PKPPT tahunan (rencana pengawasan).
- Input & kelola penugasan (SPT), baik sesuai maupun di luar PKPPT.
- Dashboard realtime (status, grafik, rekap realisasi per jenis penugasan).
- Perbandingan otomatis rencana (PKPPT) vs realisasi.
- Modul Tindak Lanjut & Arsip Digital.
- Modul Beban Kerja Personil per periode.
- Notifikasi email H-3 s.d. H-1 sebelum jadwal kegiatan.
- Integrasi opsional Google Calendar per pengguna.
- Import data historis dari spreadsheet SIPANDA lama.
- Audit log perubahan data.
- **Perencanaan PKPT berbasis risiko sederhana (N-1):** universe pengawasan, penilaian risiko
  per objek (aging, anggaran, temuan, tindak lanjut mandek, pengaduan khusus), kapasitas SDM,
  draf PKPT otomatis, alur usul → reviu → tetapkan.
- **Evaluasi Tahunan (N+1):** rekap capaian PKPT & tindak lanjut, umpan balik otomatis ke skor
  risiko siklus perencanaan berikutnya — menutup siklus PKPT secara penuh.
- **Portal OPD (MVP):** akun berjenjang untuk PIC objek pemeriksaan, area login terpisah (`/opd/*`
  dengan guard terpisah), pengajuan bukti tindak lanjut dengan alur verifikasi dua arah oleh
  Auditor/Irban, notifikasi email dua arah, reminder otomatis jika OPD belum merespons >14 hari.
- **Seeder data pegawai nyata:** data dari `docs/data-pegawai.csv` digunakan sebagai seeder
  awal sehingga sistem langsung bisa dicoba dengan nama pegawai sesungguhnya.

### 4.2 Di Luar Lingkup (Out-of-Scope, Fase 1)
- Aplikasi mobile native (cukup web responsif).
- Integrasi dengan sistem kepegawaian (SIMPEG) atau e-office Pemda — dicatat sebagai
  kemungkinan pengembangan lanjutan, bukan kebutuhan wajib saat ini.
- Workflow approval berjenjang formal (digital signature) — proses saat ini masih manual di
  luar sistem; SIPANDA fokus pada pemantauan, bukan persetujuan surat tugas.
- Analitik prediktif/AI scoring risiko pengawasan — bisa jadi roadmap masa depan.

## 5. KEBUTUHAN FUNGSIONAL (RINGKASAN — detail di dokumen 04)

| # | Modul | Kebutuhan |
|---|---|---|
| F1 | Autentikasi | Login manual & Google SSO; role otomatis ditentukan admin |
| F2 | Master Data | Kelola Irban, Objek Penugasan (OPD/Kecamatan/Desa), Jenis Penugasan, Sumber Penugasan, Pengguna |
| F3 | PKPPT | Input rencana tahunan: area pengawasan, jenis, sasaran, jadwal rencana, jumlah laporan, pelaksana (Irban) |
| F4 | Input Penugasan | Form: No. SPT, uraian, objek (multi), sumber, tanggal mulai/selesai, wakil PJ/pengendali teknis/ketua tim/anggota tim (multi), jenis penugasan, status, progres %, keterangan hasil |
| F5 | Data Penugasan | Tabel gabungan PKPPT & non-PKPPT dengan filter, pencarian, ekspor |
| F6 | Kegiatan Pengawasan | Perbandingan otomatis rencana vs realisasi per area pengawasan |
| F7 | Dashboard Realtime | Ringkasan % selesai/berjalan/belum, rekap per jenis (Assurance/Consulting), filter Irban & periode |
| F8 | Tindak Lanjut | Catat temuan, rekomendasi, status tindak lanjut, tautkan ke penugasan |
| F9 | Arsip Digital | Unggah & kelola file laporan/dokumen terkait penugasan |
| F10 | Beban Kerja Personil | Rekap jumlah & daftar SPT per personil pada rentang tanggal pilihan |
| F11 | Notifikasi | Reminder email otomatis H-3 s.d. H-1 ke personil terkait |
| F12 | Kalender | Sinkron opsional penugasan ke Google Calendar pribadi pengguna |
| F13 | Audit Log | Catat siapa mengubah apa dan kapan |
| F14 | Perencanaan PKPT | Universe pengawasan, penilaian risiko per objek, kapasitas SDM per Irban, generate draf PKPT otomatis, alur usul→reviu→tetapkan |
| F15 | Evaluasi Tahunan | Rekap capaian PKPT & tindak lanjut per tahun, umpan balik skor risiko ke siklus perencanaan berikutnya |
| F16 | Portal OPD | Akun PIC OPD (undangan token), lihat rekomendasi tindak lanjut objeknya, unggah bukti |
| F17 | Verifikasi Bukti Tindak Lanjut | Auditor/Irban terima atau tolak bukti dari OPD dengan catatan, notifikasi dua arah |

## 6. KEBUTUHAN DATA (field wajib, mengacu tangkapan layar SIPANDA v1)

Data yang tersedia di SIPANDA (sesuai dokumen sumber):
1. Nama kegiatan
2. Nama pelaksana/tim
3. Jenis pengawasan (audit/reviu/dll.)
4. Status kegiatan (belum berjalan, berjalan, selesai)
5. Tanggal mulai dan target selesai
6. Persentase progres kegiatan
7. Keterangan/laporan hasil

Field form Input Data (detail dari tangkapan layar):
- No. SPT, Uraian Penugasan, Objek Penugasan (multi, dari master), Sumber Penugasan
  (Mandatory / Permintaan / Manajemen Risiko / Permintaan APH), Tanggal Mulai, Tanggal Selesai,
  Wakil Penanggung Jawab (multi), Pengendali Teknis (multi), Ketua Tim (multi), Anggota Tim
  (multi), Jenis Penugasan.

Jenis Penugasan (nilai yang teramati di data): kategori **Assurance** (Monitoring, Evaluasi,
Monitoring dan Evaluasi, Reviu) dan kategori **Consulting** (Advisory, Facilitative Role,
Training Role).

## 7. METRIK KEBERHASILAN

- 100% penugasan tahun berjalan tercatat di sistem (tidak ada lagi input di luar sistem).
- Waktu penyusunan laporan bulanan berkurang dibanding proses manual sebelumnya.
- Tidak ada duplikasi No. SPT (validasi unik otomatis oleh sistem).
- Setiap Irban dapat melihat beban kerja timnya tanpa rekap manual.
- Reminder terkirim otomatis untuk >95% kegiatan terjadwal.

## 8. RISIKO & MITIGASI

| Risiko | Mitigasi |
|---|---|
| Resistensi pengguna beralih dari spreadsheet yang sudah familiar | UI form dibuat semirip mungkin alurnya dengan Input Data lama; sediakan panduan singkat |
| Data historis di Google Spreadsheet perlu dipindah | Modul import Excel (Fase 3 pada Blueprint) |
| Koneksi internet terbatas untuk Google SSO/Calendar di jaringan internal | Login manual selalu tersedia sebagai fallback |
| Ketergantungan pada satu server on-premise | Backup DB harian terjadwal |

## 9. DEPENDENSI EKSTERNAL

- Google OAuth Client ID/Secret (untuk SSO & Calendar) — perlu didaftarkan di Google Cloud
  Console oleh Diskominfo/Inspektorat.
- SMTP server internal atau relay email untuk notifikasi.
- Server internal Diskominfo (PHP 8.3, MySQL 8, akses jaringan intra-Pemda).

## 10. LAMPIRAN

Referensi visual asli (tangkapan layar SIPANDA v1) tersedia di dokumen sumber:
"Penjelasan Inovasi SIPANDA...docx" — Halaman Depan, Dashboard Realtime, Input Data, Hasil
Input Non PKPPT, Kegiatan Pengawasan.
