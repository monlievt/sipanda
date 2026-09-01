# BUKU PANDUAN PENGGUNA & TUTORIAL OPERASIONAL LENGKAP
## SISTEM INFORMASI PENGAWASAN TERINTEGRASI (SIPANDA)
### INSPEKTORAT DAERAH KABUPATEN TRENGGALEK

---

**Edisi:** 2026 / Versi 2.0  
**Penyusun:** Tim Pengembang SIPANDA & Tim TI Inspektorat Kabupaten Trenggalek  
**Klasifikasi:** Buku Pedoman Resmi Operasional Aplikasi (Master User Manual)

---

```
  ███████╗██╗██████╗  █████╗ ███╗   ██╗██████╗  █████╗ 
  ██╔════╝██║██╔══██╗██╔══██╗████╗  ██║██╔══██╗██╔══██╗
  ███████╗██║██████╔╝███████║██╔██╗ ██║██║  ██║███████║
  ╚════██║██║██╔═══╝ ██╔══██║██║╚██╗██║██║  ██║██╔══██║
  ███████║██║██║     ██║  ██║██║ ╚████║██████╔╝██║  ██║
  ╚══════╝╚═╝╚═╝     ╚═╝  ╚═╝╚═╝  ╚═══╝╚═════╝ ╚═╝  ╚═╝
  Sistem Informasi Pengawasan Terintegrasi Kabupaten Trenggalek
```

---

# DAFTAR ISI

1. [BAB I: PENDAHULUAN & ARSITEKTUR APLIKASI](#bab-i-pendahuluan--arsitektur-aplikasi)
2. [BAB II: PANDUAN UNTUK INSPEKTUR (PIMPINAN)](#bab-ii-panduan-untuk-inspektur-pimpinan)
3. [BAB III: PANDUAN UNTUK SEKRETARIAT](#bab-iii-panduan-untuk-sekretariat)
4. [BAB IV: PANDUAN UNTUK INSPEKTUR PEMBANTU (IRBAN I - V)](#bab-iv-panduan-untuk-inspektur-pembantu-irban-i---v)
5. [BAB V: PANDUAN UNTUK TIM AUDITOR & PPUPD](#bab-v-panduan-untuk-tim-auditor--ppupd)
6. [BAB VI: PANDUAN UNTUK AUDITI / PERANGKAT DAERAH (PORTAL OPD)](#bab-vi-panduan-untuk-auditi--perangkat-daerah-portal-opd)
7. [BAB VII: PANDUAN UNTUK ADMINISTRATOR SISTEM](#bab-vii-panduan-untuk-administrator-sistem)
8. [BAB VIII: FITUR PUSAT REGULASI & ASISTEN AI PENASIHAT APIP 24/7](#bab-viii-fitur-pusat-regulasi--asisten-ai-penasihat-apip-247)
9. [BAB IX: PEMECAHAN MASALAH (TROUBLESHOOTING)](#bab-ix-pemecahan-masalah-troubleshooting)

---

# BAB I: PENDAHULUAN & ARSITEKTUR APLIKASI

### 1.1 Latar Belakang
SIPANDA (*Sistem Informasi Pengawasan Terintegrasi*) adalah platform digital terintegrasi yang dirancang khusus untuk memodernisasi seluruh siklus pengawasan Aparat Pengawasan Intern Pemerintah (APIP) di lingkungan Inspektorat Daerah Kabupaten Trenggalek. 

Aplikasi ini mengintegrasikan:
1. **Siklus N-1 (Perencanaan):** Penyusunan Program Kerja Pengawasan Tahunan (PKPT) berbasis penilaian risiko dinamis.
2. **Siklus N (Pelaksanaan & Pemantauan):** Penerbitan Surat Perintah Tugas (SPT), kolaborasi multi-irban, manajemen kertas kerja pemeriksaan (KKP), pemantauan tindak lanjut rekomendasi (TLRHP), notifikasi otomatis WhatsApp/Email, dan layanan konsultasi daring (*e-Consulting*).
3. **Siklus N+1 (Evaluasi):** Evaluasi kinerja tahunan, persentase capaian PKPT, dan rekapitulasi penyelamatan keuangan daerah (STS Bank Jatim).

### 1.2 Alamat Akses (URL Resmi)
* **Portal Internal APIP:** `https://sipanda.inspektorat.trenggalekkab.go.id/login`
* **Portal Eksternal Auditi OPD:** `https://sipanda.inspektorat.trenggalekkab.go.id/opd/login`
* **Bank FAQ & Asisten AI:** `https://sipanda.inspektorat.trenggalekkab.go.id/faq`
* **Pusat Regulasi & Juknis:** `https://sipanda.inspektorat.trenggalekkab.go.id/regulasi`

---

# BAB II: PANDUAN UNTUK INSPEKTUR (PIMPINAN)

Peran **Inspektur** memiliki wewenang eksekutif tertinggi dalam memantau kinerja seluruh jajaran APIP, menetapkan PKPT, dan mengevaluasi hasil pengawasan.

```
┌─────────────────────────────────────────────────────────────┐
│                    WEWENANG UTAMA INSPEKTUR                 │
├─────────────────────────────────────────────────────────────┤
│ 1. Memantau Dashboard Realtime Eksekutif                    │
│ 2. Menyetujui & Menetapkan PKPT Resmi Tahunan               │
│ 3. Memantau Distribusi Beban Kerja Personil Auditor         │
│ 4. Mengesahkan Laporan Evaluasi Tahunan                     │
└─────────────────────────────────────────────────────────────┘
```

### 2.1 Menetapkan PKPT Resmi Tahunan
1. Login dengan akun Inspektur (`onowiji2@gmail.com`).
2. Buka menu **Pengawasan (PKPPT)** &rarr; **PKPPT Tahunan** (`/pkppt`).
3. Periksa daftar usulan PKPT yang telah berstatus `DIREVIU` oleh Sekretariat.
4. Klik tombol hijau **"Tetapkan PKPT"**.
5. Status PKPT resmi berubah menjadi `DITETAPKAN` dan terkunci sebagai dasar penerbitan SPT operasional.

### 2.2 Memantau Beban Kerja & Mandays Auditor
1. Buka menu **Analisis & Siklus PKPT** &rarr; **Beban Kerja Personil** (`/beban-kerja`).
2. Tinjau grafik distribusi penugasan untuk mencegah ketimpangan beban kerja antar personil auditor.

### 2.3 Evaluasi Kinerja Pengawasan Tahunan
1. Buka menu **Evaluasi Tahunan (N+1)** (`/evaluasi`).
2. Pilih tahun pengawasan, periksa rasio temuan rekomendasi yang berhasil ditindaklanjuti dan total rupiah penyelamatan keuangan daerah.
3. Klik tombol **"Generate Laporan Evaluasi Tahunan"** untuk mengunduh laporan resmi.

---

# BAB III: PANDUAN UNTUK SEKRETARIAT

Peran **Sekretariat** bertanggung jawab atas administrasi perencanaan, telaah PKPT, pengelolaan master data pengawasan, dan arsip dokumen.

### 3.1 Mereviu Usulan Draf PKPT dari Para Irban
1. Login dengan akun Sekretariat (`nugrahenisetya72@gmail.com`).
2. Buka menu **PKPPT Tahunan** (`/pkppt`).
3. Pada baris usulan yang berstatus `DIUSULKAN`, klik tombol **"Reviu Usulan"**.
4. Masukkan catatan telaah kesesuaian anggaran/mandays dan klik **"Simpan Reviu"**. Status akan berubah menjadi `DIREVIU` dan siap ditetapkan oleh Inspektur.

### 3.2 Mengelola Objek Penugasan (Master OPD & Desa)
1. Buka menu **Master Data & Sistem** &rarr; **Objek Penugasan (OPD)** (`/master/objek-penugasan`).
2. Klik **"+ Tambah Objek Penugasan"** untuk mendaftarkan Perangkat Daerah, Kecamatan, Puskesmas, atau Desa baru.
3. Pilih Kategori (*OPD, Kecamatan, Desa, Sekolah, Puskesmas, BUMD*).

### 3.3 Mengimpor Data Historis dari File CSV / Spreadsheet
1. Buka menu **Master Data & Sistem** &rarr; **Import Data CSV** (`/import`).
2. Unduh template CSV (*Penugasan SPT, Rekomendasi LHP, atau Master OPD*).
3. Isi data pada file CSV tersebut melalui Microsoft Excel atau Google Sheets.
4. Unggah berkas CSV pada form upload, periksa halaman **Pratinjau Data (Preview)**.
5. Jika validasi baris berstatus hijau (Valid), klik tombol **"Eksekusi Import Data"**.

---

# BAB IV: PANDUAN UNTUK INSPEKTUR PEMBANTU (IRBAN I - V)

Para **Inspektur Pembantu (Irban)** memegang kendali manajerial wilayah pengawasan, pembagian tim penugasan, verifikasi bukti tindak lanjut, dan disposisi konsultasi.

### 4.1 Menyusun Draf PKPT Berbasis Penilaian Risiko
1. Buka menu **PKPPT Tahunan** (`/pkppt`).
2. Klik **"+ Tambah PKPT"** atau klik **"Hitung Penilaian Risiko Otomatis"**.
3. Tentukan Objek Sasaran, Jenis Pengawasan, Rencana Hari Pengawasan (HP), dan Tim yang ditugaskan.
4. Klik tombol **"Usulkan ke Sekretariat"** agar draf diteruskan ke rantai telaah.

### 4.2 Menerbitkan Surat Perintah Tugas (SPT) Baru
1. Buka menu **Input Penugasan (SPT)** (`/penugasan/create`).
2. Isi rincian:
   - **Nomor SPT:** contoh `700.1.1/045/406.050/2026`
   - **Jenis Pengawasan:** Audit Kinerja, Reviu LKPD, Evaluasi SAKIP, Monitoring Dana Desa, dll.
   - **Objek Sasaran:** Pilih dari daftar OPD / Desa.
   - **Tanggal Pelaksanaan:** Tentukan tanggal mulai dan selesai.
   - **Alokasi Tim:** Tentukan Penanggung Jawab, Pengendali Teknis (Daltek), Ketua Tim, dan Anggota Tim.
   - **Multi-Irban:** Centang Irban pendukung jika penugasan melibatkan kolaborasi lintas wilayah kerja.
3. Klik **"Simpan Penugasan"**. Seluruh anggota tim otomatis menerima notifikasi WhatsApp & Email.

### 4.3 Memverifikasi Bukti Tindak Lanjut dari OPD
1. Buka menu **Tindak Lanjut Result** (`/tindak-lanjut`).
2. Klik baris temuan yang memiliki badge kuning `MENUNGGU VERIFIKASI`.
3. Periksa berkas lampiran yang diunggah oleh OPD (Surat Bukti / Slip Setoran STS).
4. Tentukan Keputusan:
   - ✅ **Diterima (Sesuai):** Status rekomendasi menjadi `SESUAI` (100%).
   - ❌ **Ditolak (Belum Sesuai):** Masukkan catatan alasan penolakan dan instruksi perbaikan.
   - ⏸️ **TDT (Tidak Dapat Ditindaklanjuti):** Digunakan untuk kondisi force majeure/alasan yuridis sah.
5. Klik **"Simpan Verifikasi"**. PIC OPD otomatis menerima notifikasi WhatsApp hasil telaah.

### 4.4 Disposisi Layanan e-Consulting APIP
1. Buka menu **E-Consulting APIP** (`/konsultasi`).
2. Klik pada tiket konsultasi baru yang berstatus `MENUNGGU DISPOSISI`.
3. Tentukan Metode: **Online (Chat Web)** atau **Tatap Muka (Offline)**.
4. Pilih Auditor yang ditugaskan untuk menanggapi permohonan tersebut, lalu klik **"Tetapkan Disposisi"**.

---

# BAB V: PANDUAN UNTUK TIM AUDITOR & PPUPD

Tim Auditor bertindak sebagai pelaksana teknis pengawasan lapangan, penyusun matriks temuan, penelaah bukti, dan konsultan pendamping OPD.

### 5.1 Mengisi Matriks Temuan LHP & Rekomendasi
1. Buka menu **Tindak Lanjut Result** (`/tindak-lanjut`) &rarr; Klik **"+ Tambah Rekomendasi LHP"**.
2. Hubungkan ke Nomor SPT Penugasan terkait.
3. Masukkan:
   - **Nomor LHP & Tanggal LHP**
   - **Judul & Uraian Temuan Pemeriksaan**
   - **Rekomendasi Tindak Lanjut**
   - **Nilai Rekomendasi Rp:** (contoh: kewajiban setor ke Kas Daerah `Rp25.000.000`)
   - **Nilai Anggaran Diawasi Rp:** (nilai total pagu program/kegiatan yang diaudit)
   - **Tanggal Target Penyelesaian:** (Batas waktu regulasi maksimal 60 hari kalender).
4. Klik **"Simpan Rekomendasi"**.

### 5.2 Berinteraksi di Ruang Chat e-Consulting & Terbitkan Berita Acara
1. Buka detail tiket konsultasi di `/konsultasi/{id}`.
2. Ketik telaah normatif pada kotak obrolan interaktif dan lampirkan berkas pedoman (jika ada).
3. Setelah konsultasi tuntas, isi **Form Kesimpulan Advis** di bagian bawah halaman.
4. Klik **"Terbitkan Berita Acara Konsultasi"**.
5. Klik tombol **"Cetak Berita Acara (PDF)"** untuk mencetak dokumen bertanda tangan resmi.

### 5.3 Menghubungkan Google Calendar Jadwal Pengawasan
1. Klik nama pengguna di pojok kanan atas &rarr; Buka **Profil Pengguna** (`/profile`).
2. Pada kartu *Google Calendar Integration*, klik **"Hubungkan Google Calendar"**.
3. Berikan izin akses pada akun Google Anda. Jadwal penugasan SPT otomatis tersinkronisasi ke aplikasi kalender di ponsel Anda.

---

# BAB VI: PANDUAN UNTUK AUDITI / PERANGKAT DAERAH (PORTAL OPD)

Portal OPD adalah antarmuka khusus yang aman bagi Kepala Perangkat Daerah, Camat, Kepala Desa, dan PIC Penatausahaan untuk menyelesaikan kewajiban tindak lanjut.

```
┌─────────────────────────────────────────────────────────────┐
│                    ALUR KERJA PIC OPD                       │
├─────────────────────────────────────────────────────────────┤
│ 1. Login ke Portal: /opd/login                              │
│ 2. Lihat Daftar Temuan & Rekomendasi Belum Selesai          │
│ 3. Unggah Berkas Jawaban & Bukti Setor STS Bank Jatim       │
│ 4. Pantau Status Verifikasi dari Auditor (WhatsApp Realtime)│
│ 5. Ajukan Konsultasi / Pendampingan Regulasi jika Dibutuhkan│
└─────────────────────────────────────────────────────────────┘
```

### 6.1 Login & Reset Kata Sandi Mandiri
1. Buka tautan: `https://sipanda.inspektorat.trenggalekkab.go.id/opd/login`.
2. Masukkan email kedinasan dan password Anda.
3. **Jika Lupa Password:** Klik tautan *"Lupa kata sandi?"*, masukkan email, dan klik tombol reset yang dikirim instan ke WhatsApp Anda.

### 6.2 Mengunggah Dokumen Bukti Tindak Lanjut
1. Masuk ke menu **Daftar Rekomendasi Temuan**.
2. Pilih temuan yang memiliki status `BELUM DITINDAKLANJUTI` atau `DITOLAK / REVISI`.
3. Klik tombol **"Tindak Lanjut / Kirim Bukti"**.
4. Masukkan uraian perbaikan yang telah dilakukan instansi Anda.
5. Lampirkan berkas bukti resmi:
   - 📄 **Bukti Administratif:** Surat Tanggapan Kepala OPD, SK Tim, SOP Baru (Format PDF).
   - 🏦 **Bukti Finansial:** Surat Tanda Setor (STS) Bank Jatim yang telah divalidasi kas daerah.
6. Klik **"Kirim Bukti Tindak Lanjut"**. Status akan berubah menjadi `MENUNGGU VERIFIKASI`.

### 6.3 Mengajukan Permohonan Konsultasi Online (e-Consulting)
1. Buka menu **Layanan Konsultasi** &rarr; Klik **"Ajukan Konsultasi Baru"**.
2. Pilih Kategori (*PBJ, Keuangan, Dana Desa, Aset, Kepegawaian*).
3. Tuliskan permasalahan secara jelas beserta dokumen pendukung yang ingin ditelaah.
4. Klik **"Kirim Permohonan"**. Permohonan akan segera didisposisikan oleh Irban ke tim auditor terkait.

---

# BAB VII: PANDUAN UNTUK ADMINISTRATOR SISTEM

Administrator bertugas menjaga kelancaran teknis, manajemen akun pegawai, keamanan audit log, dan integrasi server.

### 7.1 Menambah Pegawai Baru & Mengatur Hak Akses
1. Buka menu **Master Data & Sistem** &rarr; **Kelola Pengguna** (`/master/users`).
2. Klik tombol **"+ Tambah Pegawai Baru"**.
3. Masukkan Nama Lengkap, Gelar, NIP, Email, Nomor WhatsApp, Jabatan, Golongan, Unit Irban, dan Role Akses Spatie (*Admin, Inspektur, Sekretariat, Irban, Auditor*).
4. Klik **"Simpan Pegawai"**.

### 7.2 Memantau Jejak Audit Log (Audit Trail)
1. Buka menu **Audit Log** (`/master/audit-log`).
2. Gunakan filter multi-kriteria untuk melacak aktivitas penambahan, perubahan, atau penghapusan data.
3. Klik tombol **"Lihat Perubahan (Diff)"** pada setiap baris log untuk melihat rekaman data `Sebelum` (*old value*) dan `Sesudah` (*new value*) dalam format JSON Viewer.

### 7.3 Menguji Otomasi Notifikasi WhatsApp (WAHA)
Jalankan perintah ini di terminal server untuk menguji gateway:
```bash
# Uji coba kirim pesan instan
php artisan sipanda:test-wa 081234567890 "Pesan Uji Coba Gateway SIPANDA"

# Menjalankan scheduler pengingat harian
php artisan sipanda:send-reminders

# Menjalankan backup database harian otomatis
php artisan sipanda:backup-db
```

---

# BAB VIII: FITUR PUSAT REGULASI & ASISTEN AI PENASIHAT APIP 24/7

SIPANDA dilengkapi dengan fitur mutakhir **Bank Regulasi PDF** dan **Asisten AI Penasihat APIP Grounded** yang dirancang untuk menjawab pertanyaan kepatuhan hukum pengawasan secara mandiri 24 jam.

```
┌─────────────────────────────────────────────────────────────┐
│             CARA KERJA ASISTEN AI PENASIHAT APIP            │
├─────────────────────────────────────────────────────────────┤
│ User Bertanya ──> Memindai Basis Data Regulasi & FAQ SIPANDA│
│                                  │                          │
│                                  ▼                          │
│ Jawaban Presisi 24/7 + Kutipan Pasal Rujukan + Unduh PDF    │
└─────────────────────────────────────────────────────────────┘
```

### 8.1 Mengunggah Dokumen Regulasi Baru (Admin / Auditor)
1. Buka menu **Master Data & Sistem** &rarr; **Bank Regulasi & Juknis** (`/master/regulasi`).
2. Klik **"Unggah Dokumen Regulasi"**.
3. Isi Judul, Nomor Regulasi, Tahun, Kategori, Poin-poin pasal penting (*Ringkasan Eksekutif*), dan lampirkan berkas PDF.
4. Klik **"Simpan & Indeks Regulasi"**. Berkas PDF otomatis tersedia di pusat unduhan publik dan intisari pasal langsung diindeks oleh AI.

### 8.2 Mengelola Bank FAQ Resmi APIP
1. Buka menu **Master Data & Sistem** &rarr; **Kelola FAQ APIP** (`/master/faq`).
2. Klik **"Tambah Artikel FAQ"** untuk menambahkan tanya-jawab tematik baru dan menautkannya ke dokumen Perbup terkait.

### 8.3 Cara Bertanya ke Asisten AI Penasihat APIP
1. Buka halaman publik: `https://sipanda.inspektorat.trenggalekkab.go.id/faq`.
2. Klik tombol hijau mengambang **"Tanya AI APIP"** di pojok kanan bawah.
3. Ketik pertanyaan kasus (contoh: *"Berapa batas pengadaan langsung jasa konsultansi?"* atau *"Bagaimana syarat pencairan Dana Desa tahap 2?"*).
4. Asisten AI akan langsung memberikan jawaban telaah normatif, menyebutkan pasal dasar hukum rujukan, serta menampilkan tombol unduh PDF peraturan terkait.

---

# BAB IX: PEMECAHAN MASALAH (TROUBLESHOOTING)

| Gejala / Permasalahan | Kemungkinan Penyebab | Langkah Solusi Cepat |
|---|---|---|
| **Lupa Kata Sandi Akun** | Kata sandi salah / lupa | Gunakan fitur *"Lupa kata sandi?"* di halaman login untuk menerima tautan reset instan di WhatsApp / Email. |
| **Notifikasi WhatsApp Tidak Masuk** | Nomor WA salah format / Gateway WAHA terputus | Pastikan nomor telepon berformat diawali `08...` atau `628...`. Admin dapat mengecek status container WAHA di server. |
| **Gagal Mengunggah Berkas Bukti PDF** | Ukuran file melebihi batas (maks 25MB) | Kompres file PDF menggunakan kompresor PDF online sebelum diunggah ke portal. |
| **Tampilan Halaman Error 500 / Cache Usang** | Cache konfigurasi server belum diperbarui | Jalankan perintah terminal: `php artisan optimize:clear` di direktori aplikasi. |

---

### 📞 KONTAK BANTUAN & DUKUNGAN TEKNIS
* **Inspektorat Daerah Kabupaten Trenggalek**
* **Alamat:** Jl. Brigjen Soetran No. 9, Sumbergedong, Trenggalek, Jawa Timur
* **Telepon:** (0355) 791407 | **Email Helpdesk:** `admin@inspektorat.trenggalek.go.id`

---
*Dokumen Buku Panduan Pengguna ini disusun sebagai acuan operasional resmi demi terwujudnya tata kelola pengawasan intern pemerintah yang transparan, akuntabel, dan profesional di Kabupaten Trenggalek.*
