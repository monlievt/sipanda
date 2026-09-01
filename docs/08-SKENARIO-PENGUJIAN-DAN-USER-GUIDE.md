# PANDUAN PENGUJIAN & SKENARIO UAT LENGKAP
## SISTEM INFORMASI PENGAWASAN TERINTEGRASI (SIPANDA)
### INSPEKTORAT KABUPATEN TRENGGALEK

---

**Versi Dokumen:** 2.0 (Edisi Pemutakhiran Fitur AI & Bank Regulasi)  
**Tanggal Rilis:** 1 September 2026  
**Klasifikasi:** Dokumen Panduan Pengujian & Operasional Pengguna Resmi  
**Target Pembaca:** Inspektur, Sekretaris, Para Irban, Pengendali Teknis, Ketua Tim, Anggota Tim Auditor/PPUPD, Admin Sistem, dan PIC OPD se-Kabupaten Trenggalek.

---

## 🌐 1. INFORMASI ALAMAT PORTAL SIPANDA

* **Portal Internal APIP (Auditor, Irban, Manajemen):** `https://sipanda.inspektorat.trenggalekkab.go.id/login`
* **Portal Eksternal Auditi OPD (Tindak Lanjut & e-Consulting):** `https://sipanda.inspektorat.trenggalekkab.go.id/opd/login`
* **Dashboard Transparansi Publik & Bank FAQ APIP:** `https://sipanda.inspektorat.trenggalekkab.go.id/faq`
* **Pusat Unduhan Regulasi Pengawasan & Juknis:** `https://sipanda.inspektorat.trenggalekkab.go.id/regulasi`

---

## 🔑 2. DAFTAR AKUN & KREDENSIAL PENGUJIAN PER ROLE

> ⚠️ **Catatan Keamanan:** Seluruh akun pengujian menggunakan password default awal. Pengguna dapat mengubah kata sandi mandiri melalui menu **Profil Pengguna**.

| No | Peran / Role | Nama Pengguna | NIP / Jabatan | Email Login | Password Default |
|:---:|---|---|---|---|---|
| **1** | **Administrator Sistem** | Administrator SIPANDA | Pranata Komputer | `admin@inspektorat.trenggalek.go.id` | `Admin@sipanda2025!` |
| **2** | **Inspektur** | Ir. WIJIONO, S.T., M.MKes. | 197308051997031007 (Plt. Inspektur) | `onowiji2@gmail.com` | `sipanda2025` |
| **3** | **Sekretaris / Sekretariat** | NUGRAHENI RAHAYU S, SE,M.Si | 197211141994022001 (Kasubbag Umum & Kepegawaian) | `nugrahenisetya72@gmail.com` | `sipanda2025` |
| **4** | **Irban I** | GATOT SUPRIYANTO, SH. | 197308111993031002 (Irban I) | `gatotsupriyanto.gs@gmail.com` | `sipanda2025` |
| **5** | **Irban II** | SIGIT PRASETYO, S.IP., MAP | 197310211993101001 (Irban II) | `sigit.prasetyo1973@gmail.com` | `sipanda2025` |
| **6** | **Irban III** | SUYATNO, SH | 196906221992021001 (Irban III) | `ytnos69@gmail.com` | `sipanda2025` |
| **7** | **Irban IV** | AGUNG YUDYANA, S.H., M.H | 196805241997031001 (Irban IV) | `yudyanaagung@gmail.com` | `sipanda2025` |
| **8** | **Irban Khusus / Investigasi** | DIDIK AGIT W, SE.MAP | 196612061992031009 (Plt. Irban Khusus) | `diekagita@gmail.com` | `sipanda2025` |
| **9** | **Pengendali Teknis (Daltek)** | DIDIK SUPRIYANTO, S.Sos.M.Si | 196612221992021001 (PPUPD Ahli Madya) | `didieks@yahoo.co.id` | `sipanda2025` |
| **10** | **Ketua Tim Auditor** | Ir. BENNO HERA T. | 196706162006041010 (Auditor Ahli Madya) | `bennohera100@gmail.com` | `sipanda2025` |
| **11** | **Anggota Tim Auditor** | UTARI PRASETYANI, SE | 197101042010012003 (Auditor Ahli Muda) | `utariprasetyani@gmail.com` | `sipanda2025` |
| **12** | **PIC OPD (Dinas Kesehatan)** | PIC Dinas Kesehatan | Perangkat Daerah Dinkes | `pic.dinkes@trenggalek.go.id` | `sipanda2025` |
| **13** | **PIC OPD (Dinas Pendidikan)** | PIC Dinas Pendidikan | Perangkat Daerah Dikpora | `pic.dikpora@trenggalek.go.id` | `sipanda2025` |

---

## 🏛️ 3. MATRIKS TUPOKSI & HAK AKSES SISTEM

```
┌────────────────────────────────────────────────────────────────────────┐
│                   STRUKTUR PERAN & WEWENANG SIPANDA                    │
├─────────────────┬──────────────────────────────────────────────────────┤
│ Inspektur       │ • Menetapkan PKPT Resmi Tahunan                      │
│                 │ • Memantau Dashboard Strategis & Beban Kerja Auditor │
│                 │ • Evaluasi Kinerja Pengawasan Tahunan                │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Sekretariat     │ • Reviu & Verifikasi Draf PKPT dari Para Irban       │
│                 │ • Kelola Master Data Penugasan, SBM, & Regulasi PDF  │
│                 │ • Import Data Historis CSV & Arsip Digital           │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Irban (I - V)   │ • Susun Draf PKPT Berbasis Risiko & Usulkan ke Sekr. │
│                 │ • Terbitkan Surat Perintah Tugas (SPT) Penugasan     │
│                 │ • Disposisi Permohonan Konsultasi (e-Consulting)     │
│                 │ • Verifikasi Bukti Tindak Lanjut (Diterima/Tolak/TDT)│
│                 │ • Kelola & Publikasikan Bank FAQ Regulasi            │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Tim Auditor     │ • Pelaksanaan Audit/Reviu/Evaluasi Lapangan          │
│ (Daltek/Ketua/  │ • Input Matriks Temuan & Rekomendasi LHP             │
│  Anggota)       │ • Unggah KKP & Laporan Hasil Pengawasan (LHP)        │
│                 │ • Respon Chat Konsultasi & Terbitkan Berita Acara    │
│                 │ • Unggah & Perbarui Referensi Dasar Hukum / Juknis   │
├─────────────────┼──────────────────────────────────────────────────────┤
│ PIC OPD         │ • Unggah Dokumen Bukti Tindak Lanjut TLRHP           │
│ (Auditi)        │ • Ajukan Permohonan Konsultasi Online ke APIP        │
│                 │ • Tanya Asisten AI Regulasi 24/7 & Unduh Perbup      │
└─────────────────┴──────────────────────────────────────────────────────┘
```

---

## 🧪 4. SKENARIO PENGUJIAN USER ACCEPTANCE TEST (UAT) (14 SKENARIO)

---

### 📌 SKENARIO 1: Perencanaan PKPT Berbasis Penilaian Risiko (Siklus N-1)
* **Aktor:** Irban → Sekretariat → Inspektur
* **Tujuan:** Menguji siklus penyusunan PKPT dari Draf, Reviu, Penetapan, hingga Pembuatan Versi Revisi.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 1.1 | Login dan buka menu **Perencanaan PKPT** (`/pkppt`). Klik **"+ Tambah PKPT"** atau gunakan tombol **"Hitung Penilaian Risiko Otomatis"**. | Irban (`sigit.prasetyo1973@gmail.com`) | Draf PKPT baru berhasil dibuat dengan status `DRAFT`. | [ ] |
| 1.2 | Pada baris draf PKPT, klik tombol **"Usulkan"** ke Sekretariat. | Irban | Status berubah menjadi `DIUSULKAN`. Notifikasi terkirim ke Sekretaris. | [ ] |
| 1.3 | Login sebagai Sekretariat, buka menu **Perencanaan PKPT**, klik tombol **"Reviu"**, masukkan catatan telaah, lalu simpan. | Sekretariat (`nugrahenisetya72@gmail.com`) | Status berubah menjadi `DIREVIU`. Notifikasi siap penetapan terkirim ke Inspektur. | [ ] |
| 1.4 | Login sebagai Inspektur, buka menu **Perencanaan PKPT**, klik tombol **"Tetapkan PKPT"**. | Inspektur (`onowiji2@gmail.com`) | Status berubah menjadi `DITETAPKAN` (Hijau). PKPT resmi terkunci. | [ ] |
| 1.5 | Uji Fitur Revisi: Klik tombol **"Revisi PKPT"** pada item yang sudah ditetapkan. | Admin / Irban | Sistem mengarsipkan v1 (read-only) dan membuat draf **v2** secara otomatis. | [ ] |

---

### 📌 SKENARIO 2: Penerbitan Surat Perintah Tugas (SPT) Baru & Multi-Irban
* **Aktor:** Irban / Admin Sistem
* **Tujuan:** Menerbitkan SPT pengawasan lengkap dengan alokasi multi-irban, objek sasaran, dan penugasan tim.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 2.1 | Buka menu **Penugasan (SPT)** (`/penugasan`) → klik **"+ Tambah Penugasan"**. | Irban (`ytnos69@gmail.com`) | Formulir pembuatan SPT terbuka. | [ ] |
| 2.2 | Isi Nomor SPT (contoh: `700.1.1/045/406.050/2026`), pilih Jenis Pengawasan (Audit Kinerja), Objek Penugasan (Dinas Kesehatan), Tanggal Pelaksanaan, dan pilih Tim Auditor (Daltek, Ketua, Anggota). | Irban | Validasi form berjalan normal. | [ ] |
| 2.3 | Klik tombol **"Simpan Penugasan"**. | Irban | SPT berhasil diterbitkan. Seluruh anggota tim menerima notifikasi WhatsApp, Email, dan Lonceng In-App. | [ ] |
| 2.4 | Buka detail penugasan (`/penugasan/{id}`). Cek integrasi tab: Ringkasan Tim, Matriks Temuan, Arsip Digital KKP, dan Activity Log. | Tim Auditor (`bennohera100@gmail.com`) | Seluruh tab termuat cepat dengan data akurat. | [ ] |

---

### 📌 SKENARIO 3: Input Matriks Temuan LHP & Rekomendasi TLRHP
* **Aktor:** Ketua Tim Auditor / Daltek
* **Tujuan:** Menginput temuan hasil pemeriksaan, rekomendasi perbaikan, nilai kerugian/setoran, dan batas waktu 60 hari.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 3.1 | Buka menu **Tindak Lanjut (LHP)** (`/tindak-lanjut`) → klik **"+ Tambah Rekomendasi LHP"**. | Ketua Tim (`bennohera100@gmail.com`) | Form input temuan & rekomendasi terbuka. | [ ] |
| 3.2 | Pilih Penugasan terkait, isi No LHP, Judul Temuan, Uraian Rekomendasi, Nilai Rekomendasi Rp (misal: `15.000.000`), Nilai Diawasi Rp (misal: `150.000.000`), dan Tanggal Target Penyelesaian (60 hari). | Ketua Tim | Format Rupiah terformat otomatis. | [ ] |
| 3.3 | Klik **"Simpan Rekomendasi"**. | Ketua Tim | Rekomendasi tersimpan dengan status `BELUM DITINDAKLANJUTI`. | [ ] |

---

### 📌 SKENARIO 4: Portal OPD — Respon Temuan & Unggah Bukti Tindak Lanjut
* **Aktor:** PIC OPD (Dinas Kesehatan)
* **Tujuan:** OPD login ke portal khusus dan mengunggah dokumen pertanggungjawaban/setoran STS.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 4.1 | Buka URL Portal OPD: `https://sipanda.inspektorat.trenggalekkab.go.id/opd/login`. Masukkan kredensial `pic.dinkes@trenggalek.go.id` / `sipanda2025`. | PIC OPD (`pic.dinkes@trenggalek.go.id`) | Berhasil masuk ke Dashboard Khusus OPD Dinas Kesehatan. | [ ] |
| 4.2 | Buka menu **Daftar Rekomendasi Temuan**. Klik detail pada temuan yang belum selesai. | PIC OPD | Muncul rincian rekomendasi temuan dari Inspektorat. | [ ] |
| 4.3 | Masukkan uraian tindak lanjut OPD, lampirkan berkas bukti (PDF Surat Tindak Lanjut / Bukti Setor STS Bank Jatim), lalu klik **"Kirim Bukti Tindak Lanjut"**. | PIC OPD | File terunggah aman (UUID). Status berubah menjadi `MENUNGGU VERIFIKASI`. Notifikasi terkirim ke Auditor. | [ ] |

---

### 📌 SKENARIO 5: Verifikasi Bukti Tindak Lanjut oleh Inspektorat
* **Aktor:** Auditor / Irban
* **Tujuan:** Memeriksa dokumen bukti dari OPD dan memberikan keputusan status kepatuhan.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 5.1 | Login ke portal internal, klik lonceng notifikasi *"Bukti Baru dari Dinas Kesehatan"*. | Auditor (`didieks@yahoo.co.id`) | Langsung dialihkan (*deep link*) ke lembar verifikasi bukti terkait. | [ ] |
| 5.2 | Unduh dan periksa berkas lampiran bukti dari OPD. | Auditor | File PDF terbuka dengan baik dan preview dokumen tampil. | [ ] |
| 5.3 | Pilih keputusan verifikasi:  
a. **Diterima (Sesuai):** Status rekomendasi berubah menjadi `SESUAI` (100%).  
b. **Ditolak / Belum Sesuai:** Masukkan catatan revisi, status kembali ke OPD.  
c. **TDT (Tidak Dapat Ditindaklanjuti):** Status menjadi `TDT`. | Auditor / Irban | Keputusan tersimpan, log audit tercatat, dan PIC OPD otomatis menerima notifikasi WhatsApp hasil verifikasi. | [ ] |

---

### 📌 SKENARIO 6: Layanan Konsultasi & Pendampingan APIP (e-Consulting)
* **Aktor:** PIC OPD → Irban → Tim Auditor
* **Tujuan:** Pengajuan konsultasi online/tatap muka, percakapan dua arah, hingga penerbitan Berita Acara.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 6.1 | Login ke Portal OPD → Buka menu **Layanan Konsultasi** → Klik **"Ajukan Konsultasi Baru"**. Isi Topik (misal: *Tata Cara Pengadaan Langsung BLUD*), Kategori, dan Deskripsi. | PIC OPD | Konsultasi terkirim dengan status `MENUNGGU DISPOSISI`. | [ ] |
| 6.2 | Login sebagai Irban, buka menu **e-Consulting** (`/konsultasi`). Klik detail permohonan, pilih metode (*Online/Tatap Muka*), dan tentukan Tim APIP penanggap. Klik **"Tetapkan Disposisi"**. | Irban | Tim Auditor menerima notifikasi disposisi tugas konsultasi. | [ ] |
| 6.3 | Tim Auditor dan PIC OPD saling berbalas pesan dan mengirim berkas lampiran di ruang chat interaktif. | Auditor & PIC OPD | Pesan chat real-time tersimpan rapi dan notifikasi WA masuk ke ponsel pengguna. | [ ] |
| 6.4 | Auditor mengisi **Form Kesimpulan Advis** dan klik **"Terbitkan Berita Acara Konsultasi"**. | Auditor | Status menjadi `SELESAI`. Dokumen Berita Acara (BA) resmi terbit dan dapat diunduh oleh OPD. | [ ] |

---

### 📌 SKENARIO 7: Import Data Historis Spreadsheet / CSV
* **Aktor:** Administrator Sistem / Sekretariat
* **Tujuan:** Memasukkan data massal SPT, Matriks LHP, dan Master OPD lama tanpa input satu per satu.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 7.1 | Buka menu **Master Data & Sistem** → **Import Data CSV** (`/import`). | Admin (`admin@inspektorat.trenggalek.go.id`) | Halaman dashboard importer terbuka. | [ ] |
| 7.2 | Unduh salah satu template CSV (misal: *Template Penugasan SPT*). Buka di Excel/Numbers dan isi 2-3 baris data uji. | Admin | File CSV terunduh dalam format standar delimiter koma/titik-koma. | [ ] |
| 7.3 | Unggah file CSV tersebut pada form upload. | Admin | Halaman **Pratinjau Data (Preview)** menampilkan 10 baris pertama dengan validasi status baris (*Valid / Siap Import*). | [ ] |
| 7.4 | Klik tombol **"Eksekusi Import Data"**. | Admin | Data tersimpan ke database dalam 1 transaksi aman (*DB Transaction*). Notifikasi sukses tampil. | [ ] |

---

### 📌 SKENARIO 8: Kelola Pengguna, Tambah Pegawai Baru & Audit Log
* **Aktor:** Administrator Sistem
* **Tujuan:** Menambah pegawai baru manual, mengatur hak akses role, dan memantau jejak audit log.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 8.1 | Buka menu **Kelola Pengguna Internal** (`/master/users`). Klik tombol **"+ Tambah Pegawai Baru"**. | Admin | Modal form pendaftaran pegawai baru terbuka. | [ ] |
| 8.2 | Isi Nama Lengkap, NIP, Email, Nomor WhatsApp, Jabatan, Golongan, Unit Irban, dan Role Akses. Klik **"Simpan Pegawai"**. | Admin | Pegawai baru berhasil terdaftar di database dan langsung bisa login. | [ ] |
| 8.3 | Klik tombol **Edit** pada baris pegawai untuk memperbarui data / ganti password / ubah unit kerja. | Admin | Data terupdate instan dan Activity Log mencatat perubahan tersebut. | [ ] |
| 8.4 | Buka menu **Master Data & Sistem** → **Log Aktivitas Sistem** (`/master/audit-log`). | Admin | Seluruh riwayat aksi (Create, Update, Delete, Login) tercatat lengkap dengan filter multi-kriteria dan modal **Diff JSON Viewer**. | [ ] |

---

### 📌 SKENARIO 9: Pengujian Otomasi Notifikasi WhatsApp (WAHA)
* **Aktor:** Administrator Sistem (via Terminal / Scheduler)
* **Tujuan:** Memastikan gateway WhatsApp mengirim pesan secara instan.

| No | Langkah Pengujian | Perintah / Aksi | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 9.1 | Uji coba kirim pesan langsung dari CLI ke nomor ponsel penguji. | `php artisan sipanda:test-wa 081234567890 "Uji Notifikasi SIPANDA"` | Pesan uji coba berhasil masuk ke aplikasi WhatsApp ponsel dalam waktu < 3 detik. | [ ] |
| 9.2 | Uji coba scheduler reminder harian (H-3/H-1 penugasan, reminder mandek, dan jatuh tempo 60 hari). | `php artisan sipanda:send-reminders` | Terminal menampilkan ringkasan notifikasi terkirim dan pesan reminder masuk ke WhatsApp tim penugasan. | [ ] |

---

### 📌 SKENARIO 10: Evaluasi Kinerja & Pelaporan Tahunan (Siklus N+1)
* **Aktor:** Inspektur / Sekretaris
* **Tujuan:** Menghitung otomatis capaian kinerja pengawasan, efektivitas TLRHP, dan penyelamatan keuangan daerah.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 10.1 | Buka menu **Evaluasi Tahunan** (`/evaluasi`). Pilih tahun evaluasi. | Inspektur (`onowiji2@gmail.com`) | Ringkasan persentase capaian PKPT, total temuan, dan total setoran kas daerah tampil. | [ ] |
| 10.2 | Klik tombol **"Generate Laporan Evaluasi Tahunan"**. | Inspektur | Sistem menyusun matriks capaian kinerja tahunan dan siap dicetak/diekspor. | [ ] |

---

### 📌 SKENARIO 11: Sinkronisasi Google Calendar Jadwal Pengawasan
* **Aktor:** Auditor / Irban
* **Tujuan:** Menghubungkan jadwal penugasan pengawasan ke Google Calendar ponsel.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 11.1 | Buka menu **Profil Pengguna** (`/profile`). Lihat kartu **Google Calendar Integration**. | Auditor (`bennohera100@gmail.com`) | Tampil tombol **"Hubungkan Google Calendar"**. | [ ] |
| 11.2 | Klik tombol hubungkan, login dengan akun Google pribadi/kedinasan, lalu berikan izin akses kalender. | Auditor | Akun Google terhubung. Jadwal penugasan otomatis tersinkronisasi ke Google Calendar. | [ ] |

---

### 📌 SKENARIO 12: Pemulihan Kata Sandi Mandiri (Lupa Password OPD)
* **Aktor:** PIC OPD
* **Tujuan:** Mereset kata sandi tanpa bantuan manual admin melalui tautan 1-klik di WhatsApp/Email.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 12.1 | Buka halaman login OPD `/opd/login`, klik **"Lupa kata sandi?"**. Masukkan email `pic.dinkes@trenggalek.go.id`. | PIC OPD | Pesan sukses reset terkirim tampil. | [ ] |
| 12.2 | Buka pesan WhatsApp / Email yang diterima, klik tombol/link **"Atur Ulang Kata Sandi"**. | PIC OPD | Halaman form kata sandi baru terbuka dengan token valid. | [ ] |
| 12.3 | Masukkan kata sandi baru dan konfirmasi. Klik **"Simpan Kata Sandi Baru"**. | PIC OPD | Kata sandi berhasil diperbarui dan pengguna langsung dialihkan ke dashboard OPD. | [ ] |

---

### 📌 SKENARIO 13: Pengelolaan Bank Regulasi PDF & Pusat Unduhan Publik
* **Aktor:** Admin / Auditor → OPD / Publik
* **Tujuan:** Mengunggah berkas PDF Perbup/Juknis pengawasan baru dan memastikan publik dapat mengunduhnya.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 13.1 | Login portal internal → buka menu **Bank Regulasi & Juknis** (`/master/regulasi`) → klik **"Unggah Dokumen Regulasi"**. | Admin (`admin@inspektorat.trenggalek.go.id`) | Modal form unggah dokumen terbuka. | [ ] |
| 13.2 | Isi Judul (*Standar Biaya Masukan 2026*), Nomor (*Perbup No. 50/2026*), Tahun, Kategori (*Keuangan*), Poin-poin pasal penting, Visibilitas (*Publik*), dan lampirkan berkas PDF uji coba. Klik **"Simpan & Indeks Regulasi"**. | Admin | Dokumen tersimpan ke database & storage, counter unduh mulai dari 0, dan ringkasan terindeks untuk AI. | [ ] |
| 13.3 | Buka browser baru (tanpa login) ke **Pusat Regulasi Publik** (`https://sipanda.inspektorat.trenggalekkab.go.id/regulasi`). | Publik / OPD | Kartu regulasi baru langsung tampil di grid dengan badge kategori. | [ ] |
| 13.4 | Klik tombol **"Unduh PDF"** pada kartu regulasi tersebut. | Publik / OPD | File PDF terunduh sempurna ke komputer dan counter unduh bertambah 1x. | [ ] |

---

### 📌 SKENARIO 14: Konsultasi Cerdas 24/7 dengan "Asisten AI Penasihat APIP"
* **Aktor:** PIC OPD / Auditor / Publik
* **Tujuan:** Menguji asisten virtual AI dalam menjawab pertanyaan hukum/regulasi pengawasan strictly berbasis dokumen.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 14.1 | Buka halaman FAQ publik (`https://sipanda.inspektorat.trenggalekkab.go.id/faq`). Klik tombol mengambang hijau **"Tanya AI APIP"** di pojok kanan bawah. | PIC OPD / Publik | Kotak interaktif Asisten Virtual APIP terbuka di layar. | [ ] |
| 14.2 | Klik salah satu pertanyaan cepat: *"Berapa batas nilai pengadaan langsung barang dan jasa pemerintah?"*. | PIC OPD / Publik | Indikator *loading* memindai dokumen tampil, lalu AI menjawab rujukan **Perpres No. 12/2021 Pasal 38 (Rp200 Juta)** lengkap dengan tombol unduh dokumen terkait. | [ ] |
| 14.3 | Ketik pertanyaan kasus lain: *"Bolehkah PNS menjadi narasumber di kantornya sendiri dan menerima honor?"*. | PIC OPD / Publik | AI menjawab merujuk pada **Perbup SBM No. 42/2025 Lampiran I** bahwa tidak diperbolehkan jika merupakan tupoksi reguler internal. | [ ] |
| 14.4 | Ketik pertanyaan di luar regulasi yang ada (misal: *"Resep membuat masakan khas Trenggalek"*). | PIC OPD / Publik | AI secara sopan menolak menjawab hal di luar regulasi pengawasan (*Strict Document Guardrail*). | [ ] |

---

### 📌 SKENARIO 15: Generator Cetak Surat Perintah Tugas (SPT) Format Pemkab Trenggalek
* **Aktor:** Auditor / Irban / Sekretariat
* **Tujuan:** Menguji generator naskah dinas resmi SPT siap cetak A4/PDF bertanda tangan Inspektur.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 15.1 | Buka menu **Data Penugasan** (`/penugasan`) atau **Detail Penugasan** (`/penugasan/1`). | Ketua Tim Auditor (`bennohera100@gmail.com`) | Tabel penugasan / detail penugasan tampil dengan tombol **"🖨️ Cetak"**. | [ ] |
| 15.2 | Klik tombol **"🖨️ Cetak"** / **"Cetak Surat Tugas (SPT)"**. | Ketua Tim Auditor | Halaman naskah dinas A4 resmi terbuka di tab baru lengkap dengan Kop Pemkab Trenggalek, Dasar PKPT, Tabel Susunan Tim, Uraian Tugas, Tanda Tangan Plt. Inspektur, dan Tembusan. | [ ] |
| 15.3 | Klik tombol **"Cetak / Simpan PDF Resmi"** di toolbar atas. | Ketua Tim Auditor | Dialog print browser terbuka, layout dokumen rapi tanpa terpotong (*page margin A4*), siap disimpan sebagai PDF atau dicetak ke printer. | [ ] |

---

### 📌 SKENARIO 16: Pengiriman Kritik, Saran & Laporan Bug UAT via Floating Widget
* **Aktor:** Seluruh Pengguna (Auditor, Irban, Sekretariat, PIC OPD)
* **Tujuan:** Menguji pengiriman masukan langsung dari halaman manapun dengan fitur cerdas paste tangkapan layar (*Ctrl+V*).

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 16.1 | Login ke portal internal atau portal OPD. Perhatikan tombol melayang di pojok kiri bawah: **"Kritik, Saran & Bug UAT"**. | Semua Role | Tombol melayang kuning-oranye tampil stabil di seluruh halaman dengan animasi pulse. | [ ] |
| 16.2 | Klik tombol **"Kritik, Saran & Bug UAT"**. | Semua Role | Modal popup form feedback terbuka, URL halaman saat ini dan info browser/layar otomatis terdeteksi. | [ ] |
| 16.3 | Pilih Jenis Masukan (*🐞 Bug / Kendala Error*), Tingkat Urgensi (*⚠️ Tinggi*), isi Judul dan Deskripsi kronologi masalah. | Semua Role | Kolom terisi lengkap dan valid. | [ ] |
| 16.4 | Lakukan screenshot layar menggunakan tombol *PrintScreen* atau *Snipping Tool*, lalu tekan **`Ctrl + V`** (atau `Cmd + V`) di dalam form modal. | Semua Role | Gambar screenshot otomatis tertempel ke dropzone dan menampilkan pratinjau thumbnail instan. | [ ] |
| 16.5 | Klik tombol **"Kirim Masukan"**. | Semua Role | Animasi loading tampil sebentar, muncul pesan konfirmasi sukses, dan formulir otomatis ter-reset. | [ ] |

---

### 📌 SKENARIO 17: Pengelolaan Kotak Saran & Laporan Bug UAT oleh Administrator
* **Aktor:** Administrator Sistem
* **Tujuan:** Memantau laporan kendala pengujian dari para pengguna, melihat screenshot, dan memperbarui status perbaikan.

| No | Langkah Pengujian | Akun Penguji | Hasil yang Diharapkan | Status |
|:---:|---|---|---|:---:|
| 17.1 | Login sebagai Admin (`admin@inspektorat.trenggalek.go.id`), periksa sidebar menu **"Kotak Saran & Bug UAT"** (`/master/feedback`). | Administrator | Badge counter menampilkan jumlah feedback baru yang belum ditinjau. | [ ] |
| 17.2 | Buka menu `/master/feedback`. Tinjau 4 kartu ringkasan (Total Masukan, Perlu Ditelaah, Bug Kritis, Sudah Diperbaiki). | Administrator | Seluruh data statistik feedback terakumulasi akurat. | [ ] |
| 17.3 | Klik thumbnail screenshot pada salah satu baris feedback. | Administrator | Modal *Lightbox* terbuka menampilkan screenshot ukuran penuh (*full-size*) untuk analisis teknis. | [ ] |
| 17.4 | Klik tombol **"⚙️ Tindak Lanjut"** pada baris laporan. Ubah status menjadi **"Sudah Diperbaiki"** dan masukkan Catatan Solusi Admin. Klik **"Simpan Tindak Lanjut"**. | Administrator | Status feedback berubah menjadi hijau (`Sudah Diperbaiki`), catatan admin tersimpan, dan counter laporan baru otomatis berkurang. | [ ] |

---

## 🖨️ 5. PETUNJUK KONVERSI KE FORMAT PDF (CETAK RESMI)

Dokumen ini dirancang dengan standar **GitHub Flavored Markdown** yang siap dicetak/dikonversi ke PDF:

1. **Menggunakan Browser Chrome / Edge / Safari:**
   - Buka tautan file di GitHub: [docs/08-SKENARIO-PENGUJIAN-DAN-USER-GUIDE.md](https://github.com/monlievt/sipanda/blob/main/docs/08-SKENARIO-PENGUJIAN-DAN-USER-GUIDE.md)
   - Tekan `Ctrl + P` (Windows) atau `Cmd + P` (Mac).
   - Pada opsi printer tujuan, pilih **Save as PDF** (Simpan sebagai PDF).
   - Pastikan mencentang opsi **Background graphics** agar warna tabel dan badge tercetak jelas.
2. **Menggunakan Node.js Tool CLI `md-to-pdf`:**
   ```bash
   npx md-to-pdf docs/08-SKENARIO-PENGUJIAN-DAN-USER-GUIDE.md
   ```

---
*Dokumen ini disusun untuk menjamin seluruh modul SIPANDA teruji secara fungsional, andal, dan siap mendukung efektivitas pengawasan Inspektorat Kabupaten Trenggalek.*
