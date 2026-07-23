# PANDUAN PENGUJIAN USER ACCEPTANCE TESTING (UAT) SIPANDA WEB
## Panduan Pengetesan Lengkap untuk Seluruh Peran (Role)
**Inspektorat Kabupaten Trenggalek**

---

## 1. PENDAHULUAN & PENANGGUNG JAWAB UJI COBA

Dokumen ini disusun sebagai panduan pengujian (*User Acceptance Testing / UAT*) menyeluruh untuk aplikasi **SIPANDA Web**. Dokumen ini mencakup skenario pengetesan spesifik untuk **seluruh 6 Role Internal + 1 Role OPD Eksternal**:
1. **Admin Sistem** (Pengelola Teknis & Akses Sistem)
2. **Sekretariat** (Administrasi, Master Data, PKPPT, & Undangan OPD)
3. **Inspektur** (Pimpinan / Penetap PKPT & Reviu Lintas Irban)
4. **Admin Irban** (Staf Operasional Pengawasan Wilayah Irban)
5. **Irban I–IV** (Supervisi Manajerial Wilayah Irban)
6. **Auditor / PPUPD** (Pelaksana Teknis Pengawasan & Input SPT)
7. **Perangkat Daerah (OPD)** (Objek Pengawasan / Pengunggah Bukti TL)

---

## 2. INFORMASI AKSES & KREDENSIAL LOGIN LENGKAP

- **URL Aplikasi Internal**: `http://127.0.0.1:8000/login`
- **URL Portal OPD Eksternal**: `http://127.0.0.1:8000/opd/login`

### 🔑 Tabel Kredensial Login Menurut Peran (Role):

| Peran (Role) | Nama / Pegawai | Email / NIP Login | Kata Sandi (Password) | Cakupan Fitur Utama |
|---|---|---|---|---|
| **Admin Sistem** | Administrator SIPANDA | `admin@inspektorat.trenggalek.go.id` | `Admin@sipanda2025!` | CRUD User, Assign Role, Master Objek, Audit Log |
| **Sekretariat** | Nandito Monliev Passa, S.Kom | `nanditomonlievpassa@gmail.com` | `sipanda2025` | Input PKPPT, Buat Akun OPD, Draf PKPT N-1, Evaluasi N+1 |
| **Inspektur** | Wijiono, S.Sos., M.Si (Plt.) | `onowiji2@gmail.com` | `sipanda2025` | Penetapan PKPT, Rekap Lintas Irban, Audit Log |
| **Admin Irban I** | Subroto, SE | `subroto.irban4@gmail.com` | `sipanda2025` | Operasional SPT & Verifikasi Bukti Irban I |
| **Irban I** | Didik Agit Wahyudianto, SH | `diekagita@gmail.com` | `sipanda2025` | Usul PKPPT, Supervisi SPT Irban I, Verifikasi Bukti |
| **Irban II** | Sigit Prasetyo, S.STP., M.Si | `sigitprasetyo.irban2@gmail.com` | `sipanda2025` | Usul PKPPT, Supervisi SPT Irban II, Verifikasi Bukti |
| **Irban III** | Suyatno, SE | `suyatno.irban3@gmail.com` | `sipanda2025` | Usul PKPPT, Supervisi SPT Irban III, Verifikasi Bukti |
| **Irban IV** | Subroto, SE | `subroto.irban4@gmail.com` | `sipanda2025` | Usul PKPPT, Supervisi SPT Irban IV, Verifikasi Bukti |
| **Auditor / PPUPD** | Didik Supriyanto, SE | `didieks@yahoo.co.id` | `sipanda2025` | Input SPT, Update Progres %, Catat Temuan TL |
| **Portal OPD** | PIC Dinas Pendidikan | `disdik@trenggalek.go.id` | `password123` | Lihat Rekomendasi, Respons Penjelasan, Upload Bukti |

---

## 3. SKENARIO PENGUJIAN KELOMPOK PERAN (ROLE-BASED TEST SCENARIOS)

---

### 🛡️ KELOMPOK A: PENGUJIAN PERAN ADMIN SISTEM & SEKRETARIAT

#### Skenario A1: Kelola Pengguna Internal (69 Pegawai CSV) & Penetapan Role
- **Login**: `admin@inspektorat.trenggalek.go.id`
- **Rute**: `/master/users`
- **Langkah**:
  1. Cari nama pegawai (mis. `DIDIK AGIT`).
  2. Klik **Edit Role** pada baris pengguna.
  3. Ubah Role atau Unit Irban, lalu simpan.
- **Hasil**: Role & Irban pengguna berhasil diperbarui dan audit log mencatat perubahan tersebut.

#### Skenario A2: Kelola Akun PIC OPD & Token Undangan Aktivasi
- **Login**: `nanditomonlievpassa@gmail.com` (Sekretariat)
- **Rute**: `/master/opd-users`
- **Langkah**:
  1. Klik **Unduh Akun PIC OPD Baru**.
  2. Isi Nama PIC (*mis. PIC Dinas Kesehatan*), Email resmi (`dinkes@trenggalek.go.id`), dan pilih Objek Target (*Dinas Kesehatan*).
  3. Klik **Kirim Undangan Akun**.
  4. Salin link token undangan (mis. `/opd/undangan/{token}`).
- **Hasil**: Akun OPD terbuat dalam status `PENDING`, link undangan aktivasi dihasilkan untuk diberikan ke PIC OPD.

#### Skenario A3: Manajemen PKPPT Tahunan (Rencana Pengawasan)
- **Login**: `nanditomonlievpassa@gmail.com` (Sekretariat)
- **Rute**: `/pkppt`
- **Langkah**:
  1. Klik **Tambah Rencana PKPPT**.
  2. Isi Tahun `2025`, Irban `Inspektur Pembantu I`, Area Pengawasan `Audit Kinerja Keuangan Sekolah`, Target `2 Laporan`.
  3. Klik **Simpan PKPPT**.
- **Hasil**: Baris PKPPT tersimpan dalam status `DRAFT`.

---

### 👔 KELOMPOK B: PENGUJIAN PERAN INSPEKTUR

#### Skenario B1: Reviu Lintas Irban & Penetapan Resmi PKPPT
- **Login**: `onowiji2@gmail.com` (Plt. Inspektur)
- **Rute**: `/pkppt` dan `/perencanaan`
- **Langkah**:
  1. Buka menu **PKPPT Tahunan** (`/pkppt`). Filter Irban untuk memantau rencana seluruh Irban I–IV.
  2. Buka menu **Perencanaan PKPT (N-1)** (`/perencanaan`).
  3. Pada draf PKPPT yang diusulkan Irban, klik **Tetapkan PKPPT**.
- **Hasil**: Status PKPPT berubah menjadi **DITETAPKAN** dan nama Inspektur tercatat sebagai penandatangan penetapan.

#### Skenario B2: Audit Log Viewer (Jejak Pengawasan)
- **Login**: `onowiji2@gmail.com` (Plt. Inspektur)
- **Rute**: `/audit-log`
- **Langkah**:
  1. Buka menu **Audit Log**.
  2. Amati daftar log aktivitas pencatatan, pembaruan, dan penghapusan data seluruh pegawai.
- **Hasil**: Seluruh aktivitas terekam lengkap beserta IP address dan timestamp.

---

### 🏛️ KELOMPOK C: PENGUJIAN PERAN IRBAN (I–IV) & ADMIN IRBAN

#### Skenario C1: Pengusulan PKPPT & Filter Otomatis Wilayah Irban
- **Login**: `diekagita@gmail.com` (Irban I)
- **Rute**: `/pkppt`
- **Langkah**:
  1. Buka menu **PKPPT Tahunan**.
  2. Pada draf PKPPT milik Irban I, klik **Usulkan PKPPT**.
- **Hasil**: Status PKPPT berubah dari `DRAFT` menjadi `DIUSULKAN` ke Inspektur.

#### Skenario C2: Verifikasi Bukti Tindak Lanjut dari OPD (Terima / Tolak)
- **Login**: `diekagita@gmail.com` (Irban I)
- **Rute**: `/tindak-lanjut/verifikasi-bukti`
- **Langkah**:
  1. Amati daftar bukti yang dikirimkan oleh OPD.
  2. Klik nama berkas untuk mengunduh bukti PDF/JPG.
  3. Uji dua tombol:
     - **Terima**: Status rekomendasi berubah menjadi `SELESAI` (🟢 Hijau).
     - **Tolak**: Isi catatan revisi $\rightarrow$ Status berubah menjadi `DIKEMBALIKAN` (🔴 Merah).
- **Hasil**: Verifikasi bukti terproses secara transparan dan notifikasi catatan terkirim ke OPD.

---

### 🔍 KELOMPOK D: PENGUJIAN PERAN AUDITOR / PPUPD

#### Skenario D1: Input Penugasan Baru (Surat Perintah Tugas / SPT)
- **Login**: `didieks@yahoo.co.id` (Auditor)
- **Rute**: `/penugasan/create`
- **Langkah**:
  1. Isi **No. SPT** (contoh: `700/99/406.008/2025`).
  2. Centang beberapa Objek Target (*Dinas Pendidikan, Kecamatan Trenggalek*).
  3. Multi-select Susunan Tim (*Wakil PJ, Daltek, Ketua Tim, Anggota Tim*).
  4. Tandai **Sesuai PKPPT** dan hubungkan ke baris PKPPT terkait.
  5. Klik **Simpan Penugasan (SPT)**.
- **Hasil**: SPT tersimpan, relasi tim dan objek terhubung, dan activity log terekam.

#### Skenario D2: Update Status Execution & Progres %
- **Login**: `didieks@yahoo.co.id` (Auditor)
- **Rute**: `/penugasan`
- **Langkah**:
  1. Cari SPT milik timnya.
  2. Klik **Update** $\rightarrow$ ubah status ke `Selesai`, progres `100%`, dan isi Keterangan Hasil.
- **Hasil**: Status di tabel berubah menjadi `SELESAI` dan progres bar mencapai 100%.

#### Skenario D3: Pencatatan Temuan & Rekomendasi
- **Login**: `didieks@yahoo.co.id` (Auditor)
- **Rute**: `/tindak-lanjut`
- **Langkah**:
  1. Klik **Tambah Temuan / Rekomendasi**.
  2. Pilih No. SPT, isi Uraian Temuan dan Rekomendasi Wajib untuk OPD.
- **Hasil**: Temuan tersimpan dan otomatis muncul di Portal OPD terkait.

---

### 🏢 KELOMPOK E: PENGUJIAN PERAN PORTAL OPD (EKSTERNAL)

#### Skenario E1: Aktivasi Akun OPD via Token Undangan
- **Akses**: `http://127.0.0.1:8000/opd/undangan/{token}`
- **Langkah**:
  1. Buka link token undangan yang diberikan Sekretariat.
  2. Isi kata sandi baru (minimal 8 karakter) dan konfirmasi.
  3. Klik **Aktifkan & Masuk**.
- **Hasil**: Akun OPD aktif dan otomatis masuk ke Dashboard Portal OPD.

#### Skenario E2: Respons Rekomendasi & Unggah Bukti Perbaikan
- **Login**: `disdik@trenggalek.go.id` | Pass: `password123`
- **Rute**: `/opd/dashboard`
- **Langkah**:
  1. Pada tabel rekomendasi, klik **Respons & Bukti**.
  2. Isi **Penjelasan Tindak Lanjut OPD** dan unggah berkas lampiran bukti.
  3. Klik **Kirim Bukti ke Inspektorat**.
- **Hasil**: Status rekomendasi berubah menjadi `MENUNGGU VERIFIKASI` dan berkas tersimpan di Arsip Digital.

---

### 📊 KELOMPOK F: PENGUJIAN FITUR SISTEM LINTAS PERAN

#### Skenario F1: Monitoring Kegiatan Pengawasan (Indikator Warna 🟢🟡🔴🔵)
- **Rute**: `/kegiatan-pengawasan`
- **Hasil**: Indikator warna dihitung otomatis (🟢 Selesai, 🟡 Terlambat, 🔴 Belum Dimulai, 🔵 Dalam Jadwal).

#### Skenario F2: Ekspor Data ke Microsoft Excel (CSV)
- **Rute**: `/penugasan` dan `/pkppt`
- **Langkah**: Klik tombol **Export CSV** di bagian kanan atas.
- **Hasil**: Berkas CSV terunduh tervalidasi UTF-8 BOM dan rapi dibuka di Excel.

#### Skenario F3: Automasi Notifikasi Reminder
- **Terminal**: `php artisan sipanda:send-reminders`
- **Hasil**: Notifikasi reminder H-3, H-1, dan peringatan OPD mandek >14 hari ter-generate di sistem.

---

## 4. LEMBAR UMPAN BALIK PENGUJIAN (UAT FEEDBACK SHEET)

Mohon isi lembar masukan ini jika menemukan kendala atau usulan perbaikan selama uji coba:

| No | Peran Penguji | Modul / Rute | Catatan Masukan / Bug / Usulan | Tingkat Prioritas (Tinggi/Sedang/Rendah) |
|---|---|---|---|---|
| 1 | Contoh: Irban I | Input SPT (`/penugasan/create`) | Tambahkan kolom nomor HP ketua tim | Sedang |
| 2 | | | | |
| 3 | | | | |

---

*Dokumen ini merupakan panduan resmi UAT SIPANDA Web untuk seluruh jajaran Inspektorat Kabupaten Trenggalek.*
