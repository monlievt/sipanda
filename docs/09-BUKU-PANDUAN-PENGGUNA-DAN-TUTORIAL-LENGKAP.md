# BUKU PANDUAN PENGGUNA & TUTORIAL OPERASIONAL LENGKAP
## SISTEM INFORMASI PENGAWASAN TERINTEGRASI (SIPANDA)
### INSPEKTORAT DAERAH KABUPATEN TRENGGALEK

---

**Edisi:** 2026 / Versi 2.0 (Edisi Komprehensif Seluruh Modul & Fitur AI)  
**Penyusun:** Tim Pengembang SIPANDA & Tim TI Inspektorat Daerah Kabupaten Trenggalek  
**Klasifikasi:** Buku Pedoman Resmi Operasional Aplikasi (Master User Manual & SOP Teknis)

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

# DAFTAR ISI LENGKAP

1. [BAB I: PENDAHULUAN & ARSITEKTUR SISTEM](#bab-i-pendahuluan--arsitektur-sistem)
2. [BAB II: DAFTAR AKUN, HAK AKSES & KREDENSIAL PENGGUNA](#bab-ii-daftar-akun-hak-akses--kredensial-pengguna)
3. [BAB III: PANDUAN MODUL PERENCANAAN PKPT (SIKLUS N-1)](#bab-iii-panduan-modul-perencanaan-pkpt-siklus-n-1)
4. [BAB IV: PANDUAN MODUL PELAKSANAAN PENUGASAN & SPT (SIKLUS N)](#bab-iv-panduan-modul-pelaksanaan-penugasan--spt-siklus-n)
5. [BAB V: PANDUAN MODUL TINDAK LANJUT HASIL PENGAWASAN (TLRHP / LHP)](#bab-v-panduan-modul-tindak-lanjut-hasil-pengawasan-tlrhp--lhp)
6. [BAB VI: PANDUAN MODUL LAYANAN E-CONSULTING & ADVISORY APIP](#bab-vi-panduan-modul-layanan-e-consulting--advisory-apip)
7. [BAB VII: PANDUAN PUSAT REGULASI, BANK FAQ & ASISTEN AI PENASIHAT APIP 24/7](#bab-vii-panduan-pusat-regulasi-bank-faq--asisten-ai-penasihat-apip-247)
8. [BAB VIII: PANDUAN MODUL EVALUASI KINERJA TAHUNAN (SIKLUS N+1)](#bab-viii-panduan-modul-evaluasi-kinerja-tahunan-siklus-n1)
9. [BAB IX: PANDUAN MODUL ARSIP DIGITAL & KERTAS KERJA (KKP)](#bab-ix-panduan-modul-arsip-digital--kertas-kerja-kkp)
10. [BAB X: PANDUAN MODUL MASTER DATA, PENGGUNA, & IMPORTER CSV](#bab-x-panduan-modul-master-data-pengguna--importer-csv)
11. [BAB XI: PANDUAN PORTAL KHUSUS AUDITI / PERANGKAT DAERAH (OPD)](#bab-xi-panduan-portal-khusus-auditi--perangkat-daerah-opd)
12. [BAB XII: PANDUAN ADMINISTRASI SISTEM, AUDIT LOG & OTOMASI WHATSAPP (WAHA)](#bab-xii-panduan-administrasi-sistem-audit-log--otomasi-whatsapp-waha)
13. [BAB XIII: PANDUAN PUSAT MASUKAN, SARAN & LAPORAN BUG UAT (FEEDBACK HUB)](#bab-xiii-panduan-pusat-masukan-saran--laporan-bug-uat-feedback-hub)
14. [BAB XIV: PEMECAHAN MASALAH (TROUBLESHOOTING) & PANDUAN CETAK PDF](#bab-xiv-pemecahan-masalah-troubleshooting--panduan-cetak-pdf)

---

# BAB I: PENDAHULUAN & ARSITEKTUR SISTEM

### 1.1 Visi & Tujuan Digitalisasi Pengawasan
SIPANDA (*Sistem Informasi Pengawasan Terintegrasi*) dibangun untuk mewujudkan tata kelola pengawasan intern pemerintah Kabupaten Trenggalek yang **cepat, akuntabel, transparan, dan terintegrasi dari hulu ke hilir**. 

Aplikasi ini menghubungkan 3 pilar siklus pengawasan APIP:
```
┌──────────────────┐       ┌──────────────────────┐       ┌──────────────────┐
│   SIKLUS N-1     │       │       SIKLUS N       │       │    SIKLUS N+1    │
│  Perencanaan     │ ────> │  Pelaksanaan & TLRHP │ ────> │ Evaluasi Kinerja │
│   (PKPT Tahunan) │       │ (SPT, KKP, Advis, WA)│       │  & Penyelamatan  │
└──────────────────┘       └──────────────────────┘       └──────────────────┘
```

### 1.2 Peta URL & Akses Portal Resmi
* **Portal Internal APIP (Auditor, Irban, Sekretariat, Inspektur):**  
  👉 `https://sipanda.inspektorat.trenggalekkab.go.id/login`
* **Portal Eksternal Auditi / Perangkat Daerah (OPD, Kecamatan, Desa):**  
  👉 `https://sipanda.inspektorat.trenggalekkab.go.id/opd/login`
* **Bank FAQ, QnA Publik & Asisten AI Penasihat APIP 24/7:**  
  👉 `https://sipanda.inspektorat.trenggalekkab.go.id/faq`
* **Pusat Unduhan Regulasi Pengawasan Daerah & Juknis:**  
  👉 `https://sipanda.inspektorat.trenggalekkab.go.id/regulasi`

---

# BAB II: DAFTAR AKUN, HAK AKSES & KREDENSIAL PENGGUNA

Aplikasi menggunakan sistem otorisasi peran berbasis **Spatie Laravel Permission** dengan pemisahan guard internal (`web`) dan guard auditi (`opd`).

| No | Peran / Role | Akun Sampel | Jabatan / Unit | Hak Akses Utama |
|:---:|---|---|---|---|
| **1** | **Administrator** | `admin@inspektorat.trenggalek.go.id` | Pranata Komputer | Kelola user, audit log, WhatsApp gateway, backup DB. |
| **2** | **Inspektur** | `onowiji2@gmail.com` | Plt. Inspektur | Penetapan PKPT, evaluasi tahunan, monitor seluruh audit. |
| **3** | **Sekretariat** | `nugrahenisetya72@gmail.com` | Kasubbag Umum | Reviu usulan PKPT, kelola master data, import data CSV. |
| **4** | **Irban I** | `gatotsupriyanto.gs@gmail.com` | Irban I (Pemerintahan) | Susun PKPT, terbitkan SPT, verifikasi bukti tindak lanjut. |
| **5** | **Irban II** | `sigit.prasetyo1973@gmail.com` | Irban II (Perekonomian) | Susun PKPT, terbitkan SPT, disposisi e-Consulting. |
| **6** | **Irban III** | `ytnos69@gmail.com` | Irban III (Sosial Budaya) | Susun PKPT, terbitkan SPT, verifikasi bukti tindak lanjut. |
| **7** | **Irban IV** | `yudyanaagung@gmail.com` | Irban IV (Pembangunan/Desa)| Susun PKPT, terbitkan SPT, verifikasi bukti tindak lanjut. |
| **8** | **Irban Khusus** | `diekagita@gmail.com` | Plt. Irban Khusus (Investigasi)| Penugasan investigasi, telaah aduan, verifikasi kasus. |
| **9** | **Pengendali Teknis** | `didieks@yahoo.co.id` | PPUPD Ahli Madya | Pengendalian teknis audit, reviu KKP, telaah matriks LHP. |
| **10** | **Ketua Tim** | `bennohera100@gmail.com` | Auditor Ahli Madya | Input matriks temuan LHP, chat konsultasi, cetak BA. |
| **11** | **Anggota Tim** | `utariprasetyani@gmail.com` | Auditor Ahli Muda | Pelaksanaan audit lapangan, upload KKP, telaah bukti. |
| **12** | **PIC OPD Dinkes** | `pic.dinkes@trenggalek.go.id` | Auditi Dinas Kesehatan | Upload bukti tindak lanjut, ajukan konsultasi, tanya AI. |
| **13** | **PIC OPD Dikpora** | `pic.dikpora@trenggalek.go.id` | Auditi Dinas Pendidikan | Upload bukti tindak lanjut, ajukan konsultasi, tanya AI. |

> 🔑 **Kata Sandi Default:** Pegawai internal & PIC OPD menggunakan kata sandi awal `sipanda2025` (Admin Sistem: `Admin@sipanda2025!`).

---

# BAB III: PANDUAN MODUL PERENCANAAN PKPT (SIKLUS N-1)

Modul **Perencanaan PKPT** memfasilitasi siklus penyusunan Program Kerja Pengawasan Tahunan berbasis manajemen risiko, alokasi hari pengawasan (HP/Mandays), dan rantai persetujuan berjenjang.

```
                  RANTAI PERSETUJUAN PKPT
  ┌─────────┐      ┌────────────┐      ┌───────────┐      ┌─────────────┐
  │  DRAFT  │ ───> │ DIUSULKAN  │ ───> │  DIREVIU  │ ───> │ DITETAPKAN  │
  │ (Irban) │      │  (Irban)   │      │(Sekretaris)      │ (Inspektur) │
  └─────────┘      └────────────┘      └───────────┘      └─────────────┘
```

### 3.1 Langkah Pembuatan Usulan PKPT Baru
1. Login sebagai **Irban** &rarr; Buka menu **Pengawasan (PKPPT)** &rarr; **PKPPT Tahunan** (`/pkppt`).
2. Klik tombol **"+ Tambah PKPT"**.
3. Isi data formulir:
   - **Tahun Pengawasan:** Default tahun berjalan / tahun depan.
   - **Area Pengawasan:** Objek/tema pemeriksaan (misal: *Pengelolaan Dana Bantuan Operasional Kesehatan / BOK Puskesmas*).
   - **Jenis Pengawasan:** Audit Kinerja, Audit Kepatuhan, Reviu, Evaluasi, atau Monitoring.
   - **Sasaran & Objek:** Pilih Perangkat Daerah sasaran.
   - **Rencana Waktu:** Tentukan rencana tanggal mulai dan rencana tanggal selesai.
   - **Jumlah Laporan Rencana:** Target output dokumen laporan (default: 1).
   - **Skor Risiko Acuan:** Masukkan skor penilaian risiko (1.00 s.d. 5.00) atau gunakan tombol **"Hitung Penilaian Risiko Otomatis"**.
4. Klik **"Simpan Draf PKPT"**. Status awal PKPT adalah `DRAFT`.

### 3.2 Mengusulkan Draf PKPT ke Sekretariat
1. Pada tabel PKPT, cari baris draf yang ingin diajukan.
2. Klik tombol biru **"Usulkan"**.
3. Status berubah menjadi `DIUSULKAN`. Sekretariat akan menerima notifikasi in-app bahwa usulan siap ditelaah.

### 3.3 Reviu oleh Sekretariat
1. Login sebagai **Sekretariat** (`/pkppt`).
2. Klik tombol **"Reviu Usulan"** pada baris usulan berstatus `DIUSULKAN`.
3. Masukkan catatan telaah kesesuaian anggaran, ketersediaan mandays, dan prioritas daerah.
4. Klik **"Simpan Reviu"**. Status berubah menjadi `DIREVIU`.

### 3.4 Penetapan Resmi oleh Inspektur
1. Login sebagai **Inspektur** (`/pkppt`).
2. Klik tombol hijau **"Tetapkan PKPT"**.
3. Status berubah menjadi `DITETAPKAN` (Hijau). PKPT resmi terkunci dan menjadi dasar hukum penerbitan Surat Perintah Tugas (SPT).

### 3.5 Prosedur Revisi PKPT (Adendum Perubahan)
1. Jika terjadi perubahan kebijakan/anggaran di tengah tahun, Admin atau Irban dapat mengklik tombol **"Revisi PKPT"** pada item yang sudah ditetapkan.
2. Sistem otomatis mengarsipkan data lama sebagai **Versi 1 (Arsip Read-Only)** dan membuat draf aktif **Versi 2** untuk disesuaikan kembali melalui alur persetujuan yang sama.

---

# BAB IV: PANDUAN MODUL PELAKSANAAN PENUGASAN & SPT (SIKLUS N)

Modul **Penugasan (SPT)** adalah pusat operasional pengawasan yang mengatur penerbitan surat tugas, alokasi tim auditor lintas Irban, monitoring progres harian, dan pencetakan naskah dinas resmi.

### 4.1 Menerbitkan Surat Perintah Tugas (SPT) Baru
1. Buka menu **Input Penugasan (SPT)** (`/penugasan/create`).
2. Lengkapi isian formulir:
   - **Nomor SPT:** Masukkan nomor resmi naskah dinas (contoh: `700.1.1/045/406.050/2026`).
   - **Kesesuaian PKPT:** Centang opsi *"Sesuai PKPT"* jika bersumber dari PKPT yang telah ditetapkan, atau pilih *"Non-PKPT"* jika merupakan penugasan khusus Bupati.
   - **Jenis & Sumber Penugasan:** Pilih jenis pengawasan (Assurance / Consulting) dan sumber mandat (PKPT / Permintaan Bupati / Pengaduan Masyarakat).
   - **Irban & Multi-Irban:** Pilih Irban Penanggung Jawab Utama. Jika penugasan melibatkan kolaborasi lintas bidang, centang Irban pendukung pada pilihan multi-irban.
   - **Objek Sasaran Penugasan:** Pilih satu atau beberapa OPD / Kecamatan / Desa sasaran.
   - **Waktu Pelaksanaan:** Tentukan tanggal mulai dan selesai.
   - **Susunan Tim Pengawasan:**
     - **Penanggung Jawab:** Inspektur / Irban Terkait
     - **Pengendali Teknis (Daltek):** Pejabat Fungsional Madya (Auditor / PPUPD)
     - **Ketua Tim:** Auditor / PPUPD yang memimpin tim lapangan
     - **Anggota Tim:** Auditor / PPUPD pelaksana teknis (dapat memilih banyak personil).
3. Klik **"Simpan Penugasan"**.
4. **Otomasi Sistem:** Seluruh anggota tim yang terdaftar otomatis menerima notifikasi penugasan baru via **WhatsApp, Email, dan Lonceng Notifikasi Web**.

### 4.2 Menerbitkan Surat Tugas Perpanjangan (Adendum Waktu)
1. Pada form penugasan baru, centang opsi **"Merupakan ST Perpanjangan"**.
2. Pilih Nomor SPT Induk yang diperpanjang.
3. Masukkan tanggal perpanjangan baru. Sistem akan menghubungkan kedua surat tugas tersebut dalam satu pohon penugasan (*parent-child hierarchy*).

### 4.3 📚 Pengelolaan Dasar Penugasan Terintegrasi Bank Regulasi & Disposisi
Pada form pembuatan atau pengeditan SPT, sistem menyediakan komponen pemilih dasar penugasan interaktif:
1. **Regulasi Standar Baku Pemkab Trenggalek:** 3 regulasi dasar (Perda Struktur Perangkat Daerah, Perbup Tugas dan Fungsi Inspektorat, serta SK PKPT Berbasis Risiko Tahun Berjalan) telah terpasang secara otomatis bernomor urut 1, 2, 3.
2. **+ Bank Regulasi:** Klik tombol ini untuk membuka katalog regulasi hukum APIP, cari peraturan berdasarkan nomor atau kata kunci, lalu klik untuk menyisipkannya langsung ke naskah SPT.
3. **+ Surat / Disposisi:** Jika penugasan didasari oleh surat masuk atau disposisi pimpinan (misal: Disposisi Bupati untuk audit khusus), klik tombol ini, isi Nomor Surat, Tanggal, dan Perihal. Sistem otomatis merangkainya menjadi klausul hukum resmi.
4. **Sunting Langsung & Reset:** Teks pada kotak pratinjau tetap dapat diedit secara bebas. Klik tombol **"🔄 Reset Baku"** jika ingin mengembalikan klausul ke 3 regulasi standar.

### 4.4 🖨️ Mencetak Naskah Dinas SPT Resmi Format Pemkab Trenggalek
1. Buka menu **Data Penugasan** (`/penugasan`) &rarr; Klik tombol **"🖨️ Cetak"** pada baris penugasan yang diinginkan, atau buka **Detail Penugasan** (`/penugasan/{id}`) dan klik **"Cetak Surat Tugas (SPT)"**.
2. Sistem akan membuka lembar naskah dinas resmi lengkap dengan:
   - Kop Resmi Inspektorat Daerah Kabupaten Trenggalek
   - Konsiderans Dasar Hukum (Perda, Perbup, dan PKPT)
   - Tabel Susunan Tim (Nama, NIP, Golongan, Jabatan Dinas, dan Kedudukan dalam Tim)
   - Uraian Tugas, Objek Sasaran, dan Durasi Hari Kerja
   - Kolom Tanda Tangan Plt. Inspektur Daerah dan Tembusan Resmi.
3. Klik tombol **"Cetak / Simpan PDF Resmi"** untuk mencetak langsung ke kertas A4 atau menyimpannya sebagai file PDF.

### 4.5 Monitoring Beban Kerja Personil Auditor
1. Buka menu **Beban Kerja Personil** (`/beban-kerja`).
2. Sistem menyajikan matriks beban kerja:
   - Jumlah penugasan aktif berjalan per auditor.
   - Akumulasi hari kerja pengawasan (mandays).
   - Status ketersediaan personil (*Tersedia, Optimal, atau Padat Tugas*).

---

# BAB V: PANDUAN MODUL TINDAK LANJUT HASIL PENGAWASAN (TLRHP / LHP)

Modul **Tindak Lanjut Result** mengatur siklus pengawasan pasca-audit, penatausahaan temuan LHP, pengawalan kepatuhan batas waktu 60 hari, serta rekonsiliasi setoran kas daerah.

```
               SIKLUS STATUS REKOMENDASI TEMUAN
  ┌─────────┐      ┌─────────────────────┐      ┌─────────────────────────┐
  │  BELUM  │ ───> │ MENUNGGU VERIFIKASI │ ───> │ SESUAI (100% SELESAI)   │
  │ (Draft) │      │  (OPD Upload Bukti) │      │ Diterima oleh Auditor   │
  │         │      │  + Setor Kasda      │      │                         │
  └─────────┘      └─────────────────────┘      └─────────────────────────┘
                                                           │ (Jika Bukti Kurang)
                                                           ▼
                                                ┌─────────────────────────┐
                                                │   DITOLAK / PERBAIKAN   │
                                                │ Dikembalikan ke OPD     │
                                                └─────────────────────────┘
```

### 5.1 Menginput Matriks Temuan & Rekomendasi LHP Baru
1. Buka menu **Tindak Lanjut Result** (`/tindak-lanjut`) &rarr; Klik **"+ Tambah Rekomendasi LHP"**.
2. Hubungkan dengan Nomor SPT Penugasan terkait.
3. Isi rincian temuan:
   - **Nomor LHP & Tanggal LHP:** Nomor laporan hasil audit resmi.
   - **Judul LHP:** Judul pengawasan (misal: *LHP Kinerja Pelayanan Publik RSUD dr. Soedomo*).
   - **Uraian Temuan Pemeriksaan:** Ringkasan kondisi ketidaksesuaian fakta lapangan.
   - **Uraian Rekomendasi Wajib:** Langkah perbaikan yang wajib dilaksanakan auditi.
   - **Nilai Target Rekomendasi (Rp):** Jumlah kewajiban penyetoran ke Kas Daerah / pengembalian belanja (kosongkan jika rekomendasi administratif non-rupiah).
   - **Nilai Anggaran Diawasi (Rp):** Total pagu anggaran program/kegiatan yang diaudit.
   - **Tanggal Target Penyelesaian:** Otomatis dihitung maksimal 60 hari kalender sejak tanggal LHP sesuai ketentuan perundang-undangan.
4. Klik **"Simpan Rekomendasi"**.

### 5.2 Memverifikasi Bukti Tindak Lanjut dari OPD
1. Buka menu **Tindak Lanjut Result** (`/tindak-lanjut`) atau menu **Verifikasi Bukti OPD** (`/tindak-lanjut/verifikasi-bukti`).
2. Klik baris temuan yang memiliki badge status `MENUNGGU VERIFIKASI`.
3. Tinjau dokumen lampiran yang diunggah oleh OPD (Surat Tanggapan, SK Perbaikan, SOP, atau Bukti Setor STS Bank Jatim).
4. Klik tombol **"Verifikasi Bukti"** dan pilih keputusan:
   - ✅ **Diterima (Sesuai):** Status rekomendasi otomatis berubah menjadi `SESUAI` (100%).
   - ❌ **Ditolak / Belum Sesuai:** Masukkan catatan evaluasi kekurangan dokumen. Status dikembalikan ke OPD untuk diperbaiki.
   - ⏸️ **TDT (Tidak Dapat Ditindaklanjuti):** Ditetapkan jika terdapat alasan hukum sah (misal: auditi meninggal dunia/perusahaan pailit berkekuatan hukum tetap).
5. Klik **"Simpan Keputusan Verifikasi"**. PIC OPD otomatis menerima notifikasi hasil verifikasi via WhatsApp.

### 5.3 Mencatat Setoran Kas Daerah (NTPN / STS Bank Jatim)
1. Pada lembar verifikasi bukti finansial, buka tab **"Pencatatan Penyetoran Kas Daerah"**.
2. Masukkan Nominal Setor (Rp) *(otomatis terformat pemisah ribuan)*, Nama Bank Penyetor (misal: *Bank Jatim / Kasda*), Nomor Referensi NTPN / No STS Bank Jatim, dan Tanggal Setor.
3. Sistem otomatis mengakumulasi saldo setoran terhadap total kewajiban rekomendasi.

### 5.4 📑 Mencetak Naskah Dinas Berita Acara Rekonsiliasi Tindak Lanjut
Saat pelaksanaan rekonsiliasi hasil tindak lanjut berkala atau forum Gelar Pengawasan antara APIP dan OPD:
1. Buka dokumen LHP di `/tindak-lanjut/{id}` (pada sisi Inspektorat) atau `/opd/lhp/{id}` (pada portal OPD).
2. Klik tombol **"📑 Berita Acara Rekonsiliasi (PDF)"**.
3. Sistem menyusun naskah dinas resmi:
   - Kop Resmi Inspektorat Daerah Kabupaten Trenggalek.
   - Identitas Pihak Pertama (Tim Evaluator APIP / Irban) dan Pihak Kedua (Kepala OPD).
   - Tabel Rekapitulasi Status Standar BPKP (SS, BS, BTL, TDT dan % Penyelesaian).
   - Tabel Rekapitulasi Pemulihan Keuangan Daerah (Target, Telah Disetor, Sisa Kurang Setor, dan % Recovery).
   - Matriks rincian per butir rekomendasi dan catatan kesepakatan rekonsiliasi.
   - Kolom tanda tangan para pihak berdampingan.
4. Klik tombol **"Cetak / Simpan PDF Berita Acara"** untuk dicetak pada kertas A4 atau disimpan dalam format PDF.

### 5.5 📊 Mengunduh Laporan Kompilasi Daerah (Standar Evaluasi BPKP / Kemendagri)
Untuk kebutuhan pelaporan eksekutif kepada Bupati Trenggalek atau pemantauan evaluasi oleh BPKP Perwakilan Provinsi Jawa Timur:
1. Buka menu **Tindak Lanjut Result** (`/tindak-lanjut`).
2. Klik tombol **"📊 Kompilasi BPKP (Excel)"**.
3. Sistem mengunduh lembar kerja Excel/CSV berstandar BPKP yang mengelompokkan data seluruh OPD se-Kabupaten Trenggalek per Irban (Irban I, II, III, IV, dan Investigasi), dilengkapi sub-total per Irban serta Grand Total Pemulihan Keuangan Daerah se-Kabupaten Trenggalek.

---

# BAB VI: PANDUAN MODUL LAYANAN E-CONSULTING & ADVISORY APIP

Modul **E-Consulting APIP** memfasilitasi pendampingan dan konsultasi regulasi secara daring maupun tatap muka antara Perangkat Daerah dengan Tim APIP, dilengkapi alur **Disposisi Bertingkat (Inspektur &rarr; Irban &rarr; Auditor)** serta notifikasi multi-channel (*Web, WhatsApp, dan Email*).

```
                      ALUR DISPOSISI E-CONSULTING BERTINGKAT
  ┌─────────────────┐       ┌────────────────────────┐       ┌────────────────────────┐
  │ OPD AJUKAN      │ ────> │ 1. DISPOSISI INSPEKTUR │ ────> │ 2. DISPOSISI IRBAN     │
  │ KONSULTASI BARU │       │    • Pilih Irban       │       │    • Tunjuk Tim APIP   │
  │ (Notif Masuk)   │       │    • Isi Arahan        │       │    • Pilih Chat/Offline│
  └─────────────────┘       └────────────────────────┘       └────────────────────────┘
                                                                         │
                                                                         ▼
                                                             ┌────────────────────────┐
                                                             │ 3. DISKUSI CHAT/OFFLINE│
                                                             │    & TERBITKAN BA (PDF)│
                                                             └────────────────────────┘
```

### 6.1 Disposisi Tingkat 1 oleh Inspektur Daerah (Sederhana & Cepat)
1. Setiap kali OPD mengajukan konsultasi baru, Inspektur Daerah otomatis menerima notifikasi Web (lonceng), WhatsApp, dan Email.
2. Buka menu **E-Consulting APIP (QnA)** (`/konsultasi`) &rarr; Klik tiket berstatus `Menunggu Arahan Inspektur`.
3. Klik tombol **"✍️ Disposisi Inspektur ke Irban"**.
4. Lengkapi formulir ringkas:
   - **Diteruskan Kepada Irban:** Pilih Irban pembina wilayah yang relevan (Irban I s.d. IV / Investigasi).
   - **Catatan / Petunjuk Arahan Inspektur:** Tuliskan arahan singkat pimpinan (contoh: *"Pelajari regulasi PBJ dan dampingi OPD terkait perbaikan dokumen"*).
5. Klik **"Kirim Disposisi ke Irban"**. Pejabat Irban yang dituju otomatis menerima notifikasi Web, Email, dan WhatsApp.

### 6.2 Disposisi Tingkat 2 oleh Irban (Penugasan Tim Teknis APIP)
1. Pejabat Irban membuka tiket konsultasi yang telah diarahkan oleh Inspektur.
2. Tinjau lembar disposisi dan arahan Inspektur di bagian atas dokumen.
3. Klik tombol **"👥 Penugasan Tim APIP (Irban)"**.
4. Lengkapi penetapan teknis:
   - **Metode Konsultasi:** Pilih **Online Chat** (percakapan daring) atau **Tatap Muka** (pertemuan langsung di kantor Inspektorat dilengkapi tanggal, jam, dan ruangan).
   - **Susunan Tim APIP:** Tentukan Penanggung Jawab, Pengendali Teknis (Daltek), Ketua Tim, dan Anggota Tim (Auditor/PPUPD).
5. Klik **"Simpan & Mulai Konsultasi"**.
6. **Otomasi Notifikasi:**
   - Seluruh Auditor/PPUPD yang ditunjuk otomatis menerima notifikasi penugasan via Web, Email, dan WhatsApp.
   - Pihak OPD menerima konfirmasi bahwa tim APIP telah siap melayani konsultasi.

### 6.3 Ruang Obrolan Interaktif & Notifikasi Real-Time
1. Buka tiket konsultasi di `/konsultasi/{id}` (APIP) atau `/opd/konsultasi/{id}` (OPD).
2. Tim Auditor dan PIC OPD dapat saling berbalas pesan teks dan mengunggah dokumen regulasi/bukti permasalahan.
3. **Notifikasi Pesan Baru:** Setiap kali ada balasan chat, sistem otomatis mengirimkan notifikasi Web dan WhatsApp ke pihak lawan bicara.
4. **Inbound WhatsApp:** Jika auditor atau OPD membalas langsung pesan notifikasi WhatsApp dari ponsel, pesan tersebut otomatis terhubung dan masuk ke dalam riwayat ruang obrolan web aplikasi.

### 6.4 Penerbitan Berita Acara (BA) Konsultasi Resmi
1. Setelah konsultasi selesai, Auditor/Irban mengisi formulir **"Formulasi Advis & Terbitkan Berita Acara"**.
2. Masukkan poin-poin kesimpulan, solusi normatif, dan arahan regulasi resmi Inspektorat.
3. Klik **"Terbitkan Berita Acara PDF"**. Status tiket otomatis berubah menjadi `SELESAI`.
4. Klik **"🖨️ Cetak Berita Acara (PDF)"** untuk mengunduh naskah dinas resmi format A4 Pemkab Trenggalek yang siap ditandatangani.
5. (Opsional) Klik **"➕ Jadikan Artikel FAQ Publik"** jika substansi konsultasi ini bernilai edukasi tinggi agar dapat dipelajari oleh seluruh perangkat daerah lain.

---

# BAB VII: PANDUAN PUSAT REGULASI, BANK FAQ & ASISTEN AI PENASIHAT APIP 24/7

Modul **Knowledge Base & AI Advisory** adalah pusat dokumentasi hukum pengawasan dan konsultasi cerdas berbasis kecerdasan buatan (*Retrieval-Augmented Generation / RAG*).

```
┌─────────────────────────────────────────────────────────────┐
│             ARSITEKTUR ASISTEN AI PENASIHAT APIP            │
├─────────────────────────────────────────────────────────────┤
│ 1. Admin/Auditor Unggah PDF Perbup SBM & Juknis Pengawasan  │
│ 2. Sistem Ekstrak Teks & Mengindeks Pasal-Pasal Kunci       │
│ 3. OPD/Publik Bertanya via Chatbox AI                       │
│ 4. AI Memindai Database & Menjawab Faktual + Kutipan Pasal  │
│ 5. AI Menyertakan Tombol Download PDF Rujukan Resmi         │
└─────────────────────────────────────────────────────────────┘
```

### 7.1 Mengunggah Dokumen Regulasi Baru (Admin / Auditor)
1. Buka menu **Master Data & Sistem** &rarr; **Bank Regulasi & Juknis** (`/master/regulasi`).
2. Klik tombol **"Unggah Dokumen Regulasi"**.
3. Isi data:
   - **Judul Regulasi:** misal *Standar Biaya Masukan (SBM) Kabupaten Trenggalek*.
   - **Nomor & Tahun:** contoh *Perbup Trenggalek No. 42 Tahun 2025*.
   - **Kategori:** Keuangan, PBJ, Dana Desa, Aset/BMD, Disiplin ASN, atau Investigasi.
   - **Ringkasan Eksekutif:** Poin-poin pasal penting dan batasan nominal biaya.
   - **Unggah PDF:** Lampirkan berkas PDF resmi (maksimal 25MB).
   - **Visibilitas:** *Publik & OPD* (bisa diunduh bebas) atau *Internal APIP Saja*.
4. Klik **"Simpan & Indeks Regulasi"**. Dokumen otomatis tayang di Pusat Unduhan Publik (`/regulasi`) dan intisari pasalnya siap dibaca oleh Asisten AI.

### 7.2 Mengelola Bank Artikel FAQ Resmi APIP
1. Buka menu **Master Data & Sistem** &rarr; **Kelola FAQ APIP** (`/master/faq`).
2. Klik **"Tambah Artikel FAQ"**.
3. Masukkan Pertanyaan, Kategori, Tautkan ke Dokumen Regulasi terkait, Dasar Hukum Pasal Rujukan, dan Uraian Advis Resmi APIP.
4. Klik **"Simpan Artikel FAQ"**.

### 7.3 Menggunakan Asisten AI Penasihat APIP (24/7)
1. Buka halaman `https://sipanda.inspektorat.trenggalekkab.go.id/faq`.
2. Klik tombol mengambang hijau **"Tanya AI APIP"** di pojok kanan bawah.
3. Ketik pertanyaan permasalahan regulasi Anda (contoh: *"Berapa batas nilai pengadaan langsung barang?"* atau *"Bagaimana syarat pencairan Dana Desa tahap 2?"*).
4. Asisten AI akan memberikan jawaban telaah normatif, menyebutkan pasal dasar hukum rujukan, serta menampilkan tombol unduh PDF peraturan terkait.
5. **Strict Grounding:** AI dijamin tidak mengarang jawaban (*anti-halusinasi*) karena hanya menjawab fakta yang tercantum dalam basis data regulasi SIPANDA.

---

# BAB VIII: PANDUAN MODUL EVALUASI KINERJA TAHUNAN (SIKLUS N+1)

Modul **Evaluasi Tahunan** menghitung capaian kinerja APIP, efektivitas pengawasan, dan penyelamatan keuangan daerah secara otomatis.

1. Buka menu **Evaluasi Tahunan (N+1)** (`/evaluasi`).
2. Pilih Tahun Anggaran Evaluasi.
3. Tinjau 4 Indikator Utama:
   - **Persentase Realisasi PKPT:** Rasio penugasan selesai terhadap target laporan PKPT.
   - **Tingkat Kepatuhan Tindak Lanjut (TLRHP):** Persentase rekomendasi berstatus `Sesuai` (Target nasional: minimal 75%).
   - **Penyelamatan Keuangan Daerah:** Total akumulasi setoran kas daerah (STS Bank Jatim).
   - **Cakupan Pengawasan Wilayah:** Distribusi audit pada seluruh Perangkat Daerah dan Desa.
4. Klik tombol **"Generate Laporan Evaluasi Tahunan"** untuk mengunduh dokumen laporan evaluasi tahunan resmi.

---

# BAB IX: PANDUAN MODUL ARSIP DIGITAL & KERTAS KERJA (KKP)

Modul **Arsip Digital** (`/arsip`) adalah repositori dokumen terpusat untuk menyimpan:
1. Berkas Kertas Kerja Pemeriksaan (KKP) format Excel/PDF.
2. Naskah Laporan Hasil Pengawasan (LHP) final bertanda tangan.
3. Berkas Berita Acara Konsultasi dan Lampiran Bukti Tindak Lanjut.

Setiap berkas disimpan dengan enkripsi nama unik (UUID) pada direktori penyimpanan aman (*private disk*) sehingga terlindungi dari akses publik tanpa otorisasi.

---

# BAB X: PANDUAN MODUL MASTER DATA, PENGGUNA, & IMPORTER CSV

### 10.1 Menambah Pegawai Baru Manual
1. Buka menu **Master Data & Sistem** &rarr; **Kelola Pengguna** (`/master/users`).
2. Klik tombol **"+ Tambah Pegawai Baru"**.
3. Lengkapi formulir: Nama Lengkap, Gelar, NIP, Email, Nomor WhatsApp, Jabatan, Golongan, Unit Irban, dan Role Spatie.
4. Klik **"Simpan Pegawai"**.

### 10.2 Mengundang PIC Perangkat Daerah (OPD)
1. Buka menu **Kelola Pengguna OPD** (`/master/opd-users`).
2. Klik **"Undang PIC OPD Baru"**.
3. Pilih Instansi OPD sasaran, masukkan Nama PIC, Email, dan Nomor WhatsApp.
4. Sistem otomatis mengirimkan tautan undangan aktivasi akun via WhatsApp & Email kepada PIC bersangkutan.

### 10.3 Mengimpor Data Historis dari Spreadsheet / CSV
1. Buka menu **Import Data CSV** (`/import`).
2. Unduh template CSV resmi:
   - 📄 *Template Penugasan SPT*
   - 📄 *Template Matriks LHP & Tindak Lanjut*
   - 📄 *Template Master Objek Penugasan (OPD/Desa)*
3. Isi data pada Microsoft Excel / Google Sheets, lalu simpan sebagai file `.csv`.
4. Unggah berkas pada formulir importer. Tinjau halaman **Pratinjau Data (Preview)** untuk memastikan tidak ada kesalahan format baris.
5. Klik **"Eksekusi Import Data"**. Sistem mengeksekusi penyimpanan massal dalam satu transaksi database aman (*Database Transaction Rollback-Safe*).

---

# BAB XI: PANDUAN PORTAL KHUSUS AUDITI / PERANGKAT DAERAH (OPD)

Portal OPD (`/opd/login`) adalah portal khusus yang diperuntukkan bagi Kepala OPD, Camat, Kepala Desa, dan operator penatausahaan.

### 11.1 Mengunggah Dokumen Bukti Tindak Lanjut
1. Login ke `https://sipanda.inspektorat.trenggalekkab.go.id/opd/login`.
2. Buka menu **Daftar Rekomendasi Temuan**.
3. Klik tombol **"Tindak Lanjut / Kirim Bukti"** pada temuan yang belum tuntas.
4. Tuliskan uraian perbaikan yang telah dilaksanakan oleh instansi Anda.
5. Lampirkan berkas bukti resmi (Surat Pengantar Kepala OPD, SOP, SK, atau Slip Setoran STS Bank Jatim).
6. Klik **"Kirim Bukti Tindak Lanjut"**. Status berubah menjadi `MENUNGGU VERIFIKASI`.

### 11.2 Mengajukan Permohonan Konsultasi Daring
1. Buka menu **Layanan Konsultasi** &rarr; Klik **"Ajukan Konsultasi Baru"**.
2. Pilih Kategori Pengawasan, isi Topik Kasus, Uraian Permasalahan, dan lampirkan dokumen telaah.
3. Klik **"Kirim Permohonan"**. Tim Irban akan mendisposisikan auditor untuk memberikan bimbingan teknis.

---

# BAB XII: PANDUAN ADMINISTRASI SISTEM, AUDIT LOG & OTOMASI WHATSAPP (WAHA)

### 12.1 Audit Log Trail & Diff JSON Viewer
1. Buka menu **Audit Log** (`/master/audit-log`).
2. Seluruh aksi (*Create, Update, Delete, Login*) terekam otomatis beserta IP Address, User Agent, dan User ID.
3. Klik tombol **"Lihat Perubahan (Diff)"** untuk melihat perbandingan data sebelum (*old value*) dan sesudah (*new value*) dalam format JSON yang rapi.

### 12.2 Perintah Operasional CLI Server (Terminal)
```bash
# 1. Menguji pengiriman notifikasi WhatsApp langsung ke ponsel
php artisan sipanda:test-wa 081234567890 "Pesan Uji Coba Gateway SIPANDA"

# 2. Menjalankan scheduler pengingat otomatis (H-3, H-1, Mandek >14 hari, Jatuh Tempo 60 hari)
php artisan sipanda:send-reminders

# 3. Menjalankan pencadangan (backup) database otomatis
php artisan sipanda:backup-db

# 4. Membersihkan seluruh cache sistem
php artisan optimize:clear
```

---

# BAB XIII: PANDUAN PUSAT MASUKAN, SARAN & LAPORAN BUG UAT (FEEDBACK HUB)

Fitur **UAT Feedback & Bug Tracking Hub** disediakan untuk memfasilitasi pengujian interaktif (*User Acceptance Testing*). Setiap pengguna yang menemukan kendala, kebingungan alur, atau ide perbaikan dapat langsung mengirimkannya secara instan dari halaman manapun.

```
┌─────────────────────────────────────────────────────────────┐
│           ALUR PENGIRIMAN & TINDAK LANJUT FEEDBACK          │
├─────────────────────────────────────────────────────────────┤
│ 1. Pengguna klik Tombol Melayang "Kritik, Saran & Bug UAT"  │
│ 2. Form otomatis mendeteksi URL halaman & Browser pengguna  │
│ 3. Pengguna mengisi uraian + Paste Screenshot (Ctrl + V)    │
│ 4. Laporan tersimpan ke database & muncul di Panel Admin    │
│ 5. Admin meninjau screenshot & update status: Diperbaiki    │
└─────────────────────────────────────────────────────────────┘
```

### 13.1 Cara Mengirim Masukan dari Halaman Apapun (Pengguna)
1. Klik tombol melayang berwarna oranye di **pojok kiri bawah**: **`Kritik, Saran & Bug UAT`**.
2. Pilih **Jenis Masukan**:
   - 🐞 *Bug / Kendala Error*
   - 💡 *Ide & Saran Perbaikan*
   - ❓ *Pertanyaan Alur / Bingung*
   - ⭐ *Apresiasi / Ulasan UX*
3. Tentukan **Tingkat Urgensi** (*Rendah, Sedang, Tinggi, atau Kritis*).
4. Tuliskan **Judul Singkat** dan **Uraian Kronologi Masalah**.
5. **Menyertakan Tangkapan Layar (Screenshot):**
   - 💡 **Cara Tercepat (Paste Clipboard):** Ambil screenshot layar menggunakan tombol `PrintScreen` / *Snipping Tool*, lalu langsung tekan **`Ctrl + V`** (atau `Cmd + V`) di dalam formulir modal. Gambar otomatis tertempel dan pratinjau muncul instan!
   - Atau klik kotak lampiran untuk memilih file gambar dari komputer Anda.
6. Klik tombol **"Kirim Masukan"**. Laporan Anda langsung tercatat di database tim pengembang.

### 13.2 Pengelolaan & Tindak Lanjut oleh Administrator
1. Buka menu **Master Data & Sistem** &rarr; **Kotak Saran & Bug UAT** (`/master/feedback`).
2. Periksa kartu statistik ringkasan:
   - **Total Masukan Masuk**
   - **Perlu Ditelaah (Baru)**
   - **Bug & Kendala Kritis**
   - **Sudah Diperbaiki**
3. Klik thumbnail screenshot untuk membuka **Lightbox Preview Full-Size** (melihat detail screenshot beresolusi tinggi).
4. Klik tombol **"⚙️ Tindak Lanjut"** pada baris laporan:
   - Ubah status: `🆕 Baru Masuk` &rarr; `🔍 Sedang Ditelaah` &rarr; `✅ Sudah Diperbaiki` &rarr; `📁 Ditutup / Arsip`.
   - Masukkan **Catatan Respon / Solusi Admin** (misal: *"Bug tombol cetak telah diperbaiki pada commit v2.1"*).
5. Klik **"Simpan Tindak Lanjut"**.

---

# BAB XIV: PEMECAHAN MASALAH (TROUBLESHOOTING) & PANDUAN CETAK PDF

| Gejala Permasalahan | Kemungkinan Akar Masalah | Solusi Cepat |
|---|---|---|
| **Lupa Password Akun** | Kata sandi salah / lupa | Klik link *"Lupa kata sandi?"* di halaman login untuk menerima tautan reset instan 1-klik di WhatsApp. |
| **Notifikasi WhatsApp Tidak Terkirim** | Nomor WA tidak aktif / container WAHA mati | Pastikan nomor berformat `08...` atau `628...`. Admin dapat mengecek status service WAHA di port 3000. |
| **Gagal Upload Berkas PDF** | Ukuran file > 25MB | Kompres dokumen PDF sebelum diunggah ke portal. |
| **Halaman Error 500 / Tampilan Usang** | Cache konfigurasi belum dibersihkan | Jalankan `php artisan optimize:clear` di terminal VPS. |

### 🖨️ Petunjuk Mengonversi Dokumen Ini ke PDF Resmi:
1. Buka tautan file di browser: [docs/09-BUKU-PANDUAN-PENGGUNA-DAN-TUTORIAL-LENGKAP.md](https://github.com/monlievt/sipanda/blob/main/docs/09-BUKU-PANDUAN-PENGGUNA-DAN-TUTORIAL-LENGKAP.md).
2. Tekan shortcut **`Ctrl + P`** (Windows) atau **`Cmd + P`** (Mac).
3. Pilih **Destination:** *Save as PDF*.
4. Centang **Background graphics** agar seluruh kotak tabel, diagram arsitektur, dan badge tercetak sempurna!

---
*Dokumen ini merupakan panduan operasional resmi Sistem Informasi Pengawasan Terintegrasi (SIPANDA) Inspektorat Daerah Kabupaten Trenggalek.*
