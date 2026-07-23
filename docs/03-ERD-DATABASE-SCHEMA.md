# ERD & SKEMA DATABASE
# SIPANDA Web (MySQL 8 / Laravel Migrations)

Versi: 2.0 (Konsolidasi) — menggabungkan skema inti dengan modul Perencanaan & Evaluasi PKPT
dan modul Portal OPD (sebelumnya di dokumen terpisah 06/07).

---

## 1. DIAGRAM RELASI (ringkas, notasi teks)

```
roles (1)───(N) users (N)───(1) irbans
users (N)───(N) penugasan   [via penugasan_tim, dgn kolom peran]
irbans (1)───(N) pkppt
pkppt (1)───(N) penugasan               [nullable: penugasan non-PKPPT tanpa pkppt_id]
penugasan (N)───(N) objek_penugasan     [via penugasan_objek]
penugasan (1)───(N) tindak_lanjut
penugasan (1)───(N) arsip_digital
penugasan (1)───(N) laporan_hasil
penugasan (1)───(N) calendar_events
users (1)───(N) notifikasi
users/penugasan (1)───(N) activity_log

--- Perencanaan & Evaluasi PKPT ---
objek_penugasan (1)───(N) penilaian_risiko   [per tahun_perencanaan]
irbans (1)───(N) kapasitas_sdm               [per tahun_perencanaan]
pkppt (1)───(N) evaluasi_tahunan (opsional, atau dihitung on-the-fly)

--- Portal OPD ---
objek_penugasan (1)───(N) users              [role='opd', via kolom objek_penugasan_id]
tindak_lanjut (1)───(N) bukti_tindak_lanjut
bukti_tindak_lanjut (1)───(N) arsip_digital  [via kolom bukti_tindak_lanjut_id]
```

---

## 2. DAFTAR TABEL

### 2.1 `roles`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar(50) | Admin, Sekretariat, Inspektur, Admin Irban, Irban, Auditor |
| slug | varchar(50) unique | admin, sekretariat, inspektur, admin_irban, irban, auditor |

> **Hierarki hak akses (dari tertinggi):**
> `admin` → `sekretariat` → `inspektur` → `admin_irban` → `irban` → `auditor`
> Untuk route middleware: gunakan `role:admin|sekretariat` (pipe = OR) dengan spatie/laravel-permission.

### 2.2 `irbans`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_irban | varchar(50) | mis. "Irban I", "Irban II", "Irban III" |
| wilayah_keterangan | text nullable | deskripsi cakupan wilayah/OPD |

### 2.3 `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar(150) | Nama lengkap dengan gelar |
| nama_tanpa_gelar | varchar(150) nullable | Nama tanpa gelar, untuk tampilan ringkas |
| nip | varchar(30) nullable unique | |
| email | varchar(150) unique | |
| no_hp | varchar(20) nullable | Nomor HP aktif |
| password | varchar(255) nullable | nullable karena bisa login via Google SSO saja |
| google_id | varchar(100) nullable unique | untuk Socialite |
| jabatan | varchar(150) nullable | mis. "Auditor Ahli Muda", "PPUPD Ahli Madya" |
| pangkat | varchar(100) nullable | mis. "Penata", "Pembina Tingkat I" |
| golongan | varchar(10) nullable | mis. "III/c", "IV/b" |
| role_id | bigint FK → roles.id | |
| irban_id | bigint FK → irbans.id, nullable | null untuk Admin/Inspektur/Sekretariat (lintas Irban); wajib diisi untuk role `irban` dan `admin_irban` |
| is_active | boolean default true | |
| tipe_akun | enum('internal','opd') default 'internal' | pembeda cepat untuk middleware area |
| objek_penugasan_id | bigint FK → objek_penugasan.id, nullable | diisi hanya untuk `role = opd` |
| status_undangan | enum('pending','aktif') default 'aktif' | 'pending' untuk akun OPD baru |
| token_undangan | varchar(100) nullable | token set-password pertama kali (akun OPD) |
| token_kedaluwarsa | timestamp nullable | |
| timestamps | | created_at, updated_at |

### 2.4 `objek_penugasan` (master: OPD/Kecamatan/Desa/Kelurahan target)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar(150) | mis. "Dinas Perindustrian dan Tenaga Kerja", "Kecamatan Suruh" |
| kategori | enum('opd','kecamatan','desa','kelurahan','lainnya') | |
| is_active | boolean default true | |

### 2.5 `jenis_penugasan` (master)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kategori | enum('assurance','consulting') | |
| nama | varchar(50) | Monitoring, Evaluasi, Monitoring dan Evaluasi, Reviu, Audit, Advisory, Facilitative Role, Training Role |

### 2.6 `sumber_penugasan` (master)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar(50) | Mandatory, Permintaan, Manajemen Risiko, Permintaan APH |

### 2.7 `pkppt` (rencana pengawasan tahunan)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tahun | year | |
| area_pengawasan | varchar(150) | mis. "Pengendalian Inflasi Daerah" |
| jenis_pengawasan | varchar(100) | mis. "Monitoring", "Pengawasan Lainnya" |
| sasaran | varchar(150) | mis. "Sekretariat Daerah", "6 Perangkat Daerah di Wilayah Irban I" |
| rencana_mulai | date | |
| rencana_selesai_laporan | date | |
| jumlah_laporan_rencana | int | |
| irban_id | bigint FK → irbans.id nullable | pelaksana kegiatan (bisa "Semua Irban" = null) |
| status | enum('draft','diusulkan','direviu','ditetapkan') default 'draft' | alur persetujuan Perencanaan PKPT |
| skor_risiko_acuan | decimal(4,2) nullable | skor `penilaian_risiko` saat draf dibuat |
| ditetapkan_oleh | bigint FK → users.id nullable | Inspektur |
| tanggal_ditetapkan | date nullable | |
| versi_revisi | int default 1 | jejak perubahan PKPT yang sudah ditetapkan |
| dibuat_oleh | bigint FK → users.id | |
| timestamps | | |

### 2.8 `penugasan` (inti — SPT, sesuai PKPPT maupun tidak)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| no_spt | varchar(50) unique | mis. "700/02/406.008/2025" |
| pkppt_id | bigint FK → pkppt.id, nullable | null = di luar PKPPT |
| is_sesuai_pkppt | boolean | flag cepat untuk filter (redundan dari pkppt_id agar query dashboard ringan) |
| uraian_penugasan | text | |
| sumber_penugasan_id | bigint FK → sumber_penugasan.id | |
| jenis_penugasan_id | bigint FK → jenis_penugasan.id | |
| tanggal_mulai | date | |
| tanggal_selesai | date | |
| status | enum('belum_berjalan','berjalan','selesai') default 'belum_berjalan' | |
| progres_persen | tinyint default 0 | 0–100 |
| keterangan_hasil | text nullable | ringkasan laporan hasil |
| irban_id | bigint FK → irbans.id | untuk filter akses multi-level |
| dibuat_oleh | bigint FK → users.id | |
| diperbarui_oleh | bigint FK → users.id nullable | |
| timestamps | | |

### 2.9 `penugasan_objek` (pivot N:N)
| Kolom | Tipe |
|---|---|
| penugasan_id | bigint FK → penugasan.id |
| objek_penugasan_id | bigint FK → objek_penugasan.id |

### 2.10 `penugasan_tim` (pivot N:N dengan peran — inti "Susunan Tim")
| Kolom | Tipe | Keterangan |
|---|---|---|
| penugasan_id | bigint FK → penugasan.id | |
| user_id | bigint FK → users.id | |
| peran | enum('wakil_penanggung_jawab','pengendali_teknis','ketua_tim','anggota_tim') | |

> Kombinasi (penugasan_id, user_id, peran) unique — satu orang bisa punya lebih dari satu peran
> berbeda dalam penugasan yang sama bila diperlukan, tapi tidak duplikat pada peran yang sama.

### 2.11 `tindak_lanjut` (modul baru — integrasi yang belum ada di v1)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| penugasan_id | bigint FK → penugasan.id | |
| uraian_temuan | text | |
| rekomendasi | text | |
| status_tindak_lanjut | enum('belum','proses','menunggu_verifikasi','selesai','dikembalikan') default 'belum' | diperluas untuk menampung alur verifikasi bukti dari OPD (lihat §2.20) |
| tanggal_target | date nullable | |
| tanggal_selesai_aktual | date nullable | |
| dibuat_oleh | bigint FK → users.id | |
| timestamps | | |

### 2.12 `arsip_digital` (modul baru)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| penugasan_id | bigint FK → penugasan.id nullable | bisa juga terkait tindak_lanjut_id |
| tindak_lanjut_id | bigint FK → tindak_lanjut.id nullable | |
| bukti_tindak_lanjut_id | bigint FK → bukti_tindak_lanjut.id nullable | diisi jika file berasal dari lampiran OPD (§2.20) |
| nama_file | varchar(255) | |
| path_file | varchar(255) | |
| kategori | varchar(50) nullable | mis. "Surat Tugas", "Laporan Hasil", "Bukti Tindak Lanjut" |
| diunggah_oleh | bigint FK → users.id | bisa akun internal maupun akun OPD |
| timestamps | | |

### 2.13 `laporan_hasil`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| penugasan_id | bigint FK → penugasan.id | |
| nomor_laporan | varchar(50) nullable | |
| judul | varchar(200) | |
| tanggal_laporan | date | |
| path_file | varchar(255) nullable | |
| timestamps | | |

### 2.14 `notifikasi`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id | |
| penugasan_id | bigint FK → penugasan.id | |
| jenis | enum('reminder_h3','reminder_h1','info_lain') | |
| pesan | text | |
| status | enum('pending','terkirim','gagal') default 'pending' | |
| dikirim_pada | timestamp nullable | |
| timestamps | | |

### 2.15 `calendar_events` (integrasi Google Calendar opsional)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| penugasan_id | bigint FK → penugasan.id | |
| user_id | bigint FK → users.id | pemilik kalender tujuan |
| google_event_id | varchar(150) nullable | |
| tanggal_mulai | datetime | |
| tanggal_selesai | datetime | |
| status_sinkron | enum('belum','tersinkron','gagal') default 'belum' | |
| timestamps | | |

### 2.16 `activity_log` (audit trail — mengatasi celah "rawan human error tanpa jejak")
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id | |
| tabel | varchar(50) | nama tabel yang diubah |
| record_id | bigint | |
| aksi | enum('create','update','delete') | |
| data_sebelum | json nullable | |
| data_sesudah | json nullable | |
| created_at | timestamp | |

### 2.17 `penilaian_risiko` *(Modul Perencanaan PKPT)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| objek_penugasan_id | bigint FK → objek_penugasan.id | |
| tahun_perencanaan | year | tahun PKPT yang sedang disusun (N) |
| skor_aging | decimal(3,1) | dihitung sistem dari `MAX(tanggal_selesai)` penugasan objek ini |
| skor_anggaran | decimal(3,1) | input manual Sekretariat |
| skor_temuan | decimal(3,1) | dihitung sistem dari riwayat `tindak_lanjut` |
| skor_tindak_lanjut_mandek | decimal(3,1) | dihitung sistem, % tindak lanjut belum selesai |
| skor_pengaduan_khusus | decimal(3,1) | dihitung/manual dari riwayat sumber "Permintaan"/APH |
| skor_total | decimal(4,2) | agregat berbobot (30/25/20/15/10%), dihitung sistem |
| catatan_penyesuaian_manual | text nullable | justifikasi jika Irban mengubah skor |
| dihitung_pada | timestamp | |

### 2.18 `kapasitas_sdm` *(Modul Perencanaan PKPT)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| irban_id | bigint FK → irbans.id | |
| tahun_perencanaan | year | |
| jumlah_hari_tersedia | int | total hari kerja pengawasan tersedia |
| catatan | text nullable | |

### 2.19 `evaluasi_tahunan` *(Modul Evaluasi Tahunan, opsional — bisa dihitung on-the-fly)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tahun_evaluasi | year | tahun N yang dievaluasi |
| irban_id | bigint FK nullable | null = rekap seluruh instansi |
| persen_objek_terealisasi | decimal(5,2) | |
| persen_laporan_tepat_waktu | decimal(5,2) | |
| persen_tindak_lanjut_selesai | decimal(5,2) | |
| catatan_evaluasi | text nullable | |
| dibuat_oleh | bigint FK → users.id | |
| timestamps | | |

### 2.20 `bukti_tindak_lanjut` *(Modul Portal OPD)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tindak_lanjut_id | bigint FK → tindak_lanjut.id | |
| diunggah_oleh | bigint FK → users.id | akun OPD (`role='opd'`) yang mengunggah |
| catatan_opd | text | penjelasan dari OPD |
| status_verifikasi | enum('menunggu','diterima','ditolak') default 'menunggu' | |
| catatan_verifikasi | text nullable | alasan auditor menerima/menolak |
| diverifikasi_oleh | bigint FK → users.id, nullable | |
| diverifikasi_pada | timestamp nullable | |
| timestamps | | |

> File fisik bukti disimpan lewat tabel `arsip_digital` (§2.12), ditautkan lewat kolom
> `bukti_tindak_lanjut_id` — satu pengajuan bukti bisa punya lebih dari satu file lampiran.

---

## 3. QUERY KUNCI (acuan implementasi fitur)

**Dashboard % status PKPPT tahun berjalan:**
```sql
SELECT status, COUNT(*) AS jumlah,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM penugasan
             WHERE YEAR(tanggal_mulai) = :tahun AND is_sesuai_pkppt = 1), 2) AS persen
FROM penugasan
WHERE YEAR(tanggal_mulai) = :tahun AND is_sesuai_pkppt = 1
GROUP BY status;
```

**Rekap realisasi per jenis penugasan (Consulting/Assurance, Selesai/Dalam Proses/Total):**
```sql
SELECT jp.kategori, jp.nama,
       SUM(p.status = 'selesai') AS selesai,
       SUM(p.status != 'selesai') AS dalam_proses,
       COUNT(*) AS total
FROM penugasan p
JOIN jenis_penugasan jp ON jp.id = p.jenis_penugasan_id
WHERE YEAR(p.tanggal_mulai) = :tahun
GROUP BY jp.kategori, jp.nama;
```

**Beban kerja personil per periode (fitur baru F10):**
```sql
SELECT u.nama, pt.peran, COUNT(DISTINCT pt.penugasan_id) AS jumlah_penugasan
FROM penugasan_tim pt
JOIN penugasan p ON p.id = pt.penugasan_id
JOIN users u ON u.id = pt.user_id
WHERE p.tanggal_mulai BETWEEN :tgl_awal AND :tgl_akhir
GROUP BY u.id, pt.peran
ORDER BY jumlah_penugasan DESC;
```

**Perbandingan PKPPT vs realisasi (modul Kegiatan Pengawasan, F6):**
```sql
SELECT k.area_pengawasan, k.rencana_mulai, k.rencana_selesai_laporan,
       k.jumlah_laporan_rencana,
       COUNT(p.id) AS jumlah_realisasi,
       SUM(p.status = 'selesai') AS realisasi_selesai
FROM pkppt k
LEFT JOIN penugasan p ON p.pkppt_id = k.id
WHERE k.tahun = :tahun
GROUP BY k.id;
```

**Urutan prioritas objek untuk generate draf PKPT (F14):**
```sql
SELECT op.id, op.nama, pr.skor_total
FROM penilaian_risiko pr
JOIN objek_penugasan op ON op.id = pr.objek_penugasan_id
WHERE pr.tahun_perencanaan = :tahun_rencana
ORDER BY pr.skor_total DESC;
-- diproses di layer aplikasi: akumulasi estimasi hari sampai kapasitas_sdm.irban_id habis
```

**Rekap bukti tindak lanjut menunggu verifikasi per Irban (F17, dashboard internal):**
```sql
SELECT i.nama_irban, COUNT(*) AS jumlah_menunggu
FROM bukti_tindak_lanjut btl
JOIN tindak_lanjut tl ON tl.id = btl.tindak_lanjut_id
JOIN penugasan p ON p.id = tl.penugasan_id
JOIN irbans i ON i.id = p.irban_id
WHERE btl.status_verifikasi = 'menunggu'
GROUP BY i.id;
```

---

## 4. CATATAN MIGRASI DATA LAMA

Saat mengimpor data historis dari Google Spreadsheet SIPANDA v1 (Laravel Excel):
- Kolom multi-nama (Wakil Penanggung Jawab, Pengendali Teknis, Ketua Tim, Anggota Tim) di
  spreadsheet lama dipisah koma → parse ke baris-baris `penugasan_tim` dengan `peran` sesuai
  kolom asal.
- Kolom Objek Penugasan yang berisi banyak baris teks → parse ke beberapa baris
  `penugasan_objek`, cocokkan/insert ke master `objek_penugasan`.
- Validasi `no_spt` unik sebelum insert; log baris yang gagal/duplikat untuk ditinjau manual.

---

## 5. SOFT DELETE POLICY

| Tabel | Strategi | Keterangan |
|---|---|---|
| `penugasan` | **Soft delete** (`deleted_at`) | Tidak pernah dihapus permanen; terhubung ke banyak tabel lain |
| `tindak_lanjut` | **Soft delete** (`deleted_at`) | Terhubung ke `bukti_tindak_lanjut`; tidak boleh hilang dari riwayat |
| `arsip_digital` | **Hard delete** | File fisik ikut dihapus; hanya boleh jika belum ada verifikasi terkait |
| `bukti_tindak_lanjut` | **Tidak bisa dihapus** | Setelah diverifikasi, record bersifat immutable |
| `pkppt` | **Tidak bisa dihapus** jika ada penugasan terkait | Cukup set `status='draft'` untuk membatalkan |
| `users` | **Nonaktifkan saja** (`is_active = false`) | Tidak pernah dihapus; audit trail harus tetap valid |
| `objek_penugasan` | **Nonaktifkan saja** (`is_active = false`) | Sudah ada mekanismenya |
| `activity_log` | **Tidak bisa dihapus** dari UI | Hanya bisa dihapus via backup/archiving oleh Admin Sistem di DB langsung |

---

## 6. INDEX DATABASE (untuk performa query multi-tahun)

Tambahkan index berikut di migration atau setelah tabel dibuat:

```sql
-- Tabel penugasan (paling sering di-query untuk dashboard)
ALTER TABLE penugasan
  ADD INDEX idx_penugasan_tahun_irban (tanggal_mulai, irban_id),
  ADD INDEX idx_penugasan_pkppt_status (pkppt_id, status),
  ADD INDEX idx_penugasan_flag (is_sesuai_pkppt, tanggal_mulai),
  ADD INDEX idx_penugasan_dibuat_oleh (dibuat_oleh);

-- Tabel penugasan_tim (lookup susunan tim)
ALTER TABLE penugasan_tim
  ADD INDEX idx_tim_user (user_id),
  ADD INDEX idx_tim_penugasan_peran (penugasan_id, peran);

-- Tabel penilaian_risiko (query per tahun perencanaan)
ALTER TABLE penilaian_risiko
  ADD INDEX idx_risiko_tahun_objek (tahun_perencanaan, objek_penugasan_id),
  ADD INDEX idx_risiko_skor (tahun_perencanaan, skor_total);

-- Tabel activity_log (audit lookup)
ALTER TABLE activity_log
  ADD INDEX idx_log_tabel_record (tabel, record_id),
  ADD INDEX idx_log_user_waktu (user_id, created_at);

-- Tabel notifikasi (scheduler harian)
ALTER TABLE notifikasi
  ADD INDEX idx_notif_status (status, dikirim_pada);

-- Tabel bukti_tindak_lanjut (dashboard verifikasi)
ALTER TABLE bukti_tindak_lanjut
  ADD INDEX idx_bukti_status (status_verifikasi);
```

---

## 7. DATA PEGAWAI (Seeder)

Data pegawai resmi Inspektorat Trenggalek tersedia di `docs/data-pegawai.csv` (69 baris).
Gunakan file ini sebagai sumber seeder `UserSeeder` dan `IrbanSeeder`.

### Pemetaan Irban dari data CSV:
| Irban | Keterangan |
|---|---|
| **Irban I** | Inspektur Pembantu I |
| **Irban II** | Inspektur Pembantu II |
| **Irban III** | Inspektur Pembantu III |
| **Irban IV** | Inspektur Pembantu IV (saat ini dijabat Plt.) |
| **Sekretariat** | Bukan Irban; bidang tersendiri (Admin, Kepegawaian, dll.) |

### Pemetaan Role dari kolom JABATAN di CSV:
| Jabatan (CSV) | Role Sistem |
|---|---|
| Plt. INSPEKTUR / INSPEKTUR | `inspektur` |
| IRBAN I / II / III / IV | `irban` |
| PPUPD AHLI MADYA/MUDA/PERTAMA | `auditor` |
| AUDITOR AHLI MADYA/MUDA/PERTAMA/PENYELIA/TERAMPIL | `auditor` |
| KASUBBAG. / Penelaah Teknis Kebijakan di Sekretariat | `sekretariat` |
| PRANATA KOMPUTER TERAMPIL | `admin` (Admin Sistem) |
| PENGADMINISTRASI PERKANTORAN | `sekretariat` |
| PENGELOLA UMUM OPERASIONAL | `sekretariat` |

> **Catatan:** Role `admin_irban` tidak bisa di-map otomatis dari jabatan di CSV.
> Perlu dikonfirmasi manual: siapa staf di tiap Irban yang bertugas sebagai admin operasional
> (input data harian) untuk wilayah masing-masing Irban.

