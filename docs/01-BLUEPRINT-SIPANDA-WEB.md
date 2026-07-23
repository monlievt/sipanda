# BLUEPRINT TEKNIS
# SIPANDA Web — Sistem Informasi Pengawasan Terintegrasi
Inspektorat Kabupaten Trenggalek

Versi: 2.0 (Konsolidasi) | Basis: Migrasi dari SIPANDA v1 (Google Spreadsheet) ke aplikasi web-based
Dokumen ini adalah panduan implementasi teknis untuk AI coding agent / tim developer.

> **Catatan versi:** Versi 2.0 menggabungkan modul Perencanaan & Evaluasi PKPT dan modul
> Portal OPD (sebelumnya dokumen terpisah `06` dan `07`) ke dalam satu paket dokumen 01–05
> yang utuh. Dokumen 06/07 kini superseded oleh dokumen ini beserta 03 dan 05.

---

## 1. LATAR BELAKANG SINGKAT

SIPANDA v1 berjalan di atas Google Spreadsheet dengan 5 halaman (Halaman Depan, Dashboard
Realtime, Input Data, Hasil Input Non PKPPT, Kegiatan Pengawasan) dan pengingat via Google
Calendar. Tiga keterbatasan utama yang mendorong migrasi:

1. Fitur terbatas, tidak terintegrasi dengan data tindak lanjut pengawasan & arsip digital.
2. Input manual → rawan *human error*.
3. Tidak ada tampilan beban penugasan per personil dalam periode tertentu.

Blueprint ini merancang aplikasi web pengganti yang mempertahankan seluruh proses bisnis
existing, sekaligus menutup ketiga celah di atas.

---

## 2. TECH STACK

| Layer | Pilihan | Alasan |
|---|---|---|
| Backend framework | **Laravel 11 (PHP 8.3)** | Rapid development, ekosistem matang, cocok untuk deployment on-premise Pemda |
| Database | **MySQL 8** | Standar de-facto hosting/server internal Pemda/Diskominfo |
| Frontend | **Blade + Livewire 3** | Menghindari kompleksitas SPA terpisah; dipilih karena sistem banyak form kompleks + tabel filter + dashboard yang lebih mudah dikelola di PHP. Alpine.js tetap dipakai untuk interaksi UI mikro (toggle, dropdown lokal) yang tidak butuh server roundtrip. |
| Styling | **Tailwind CSS** | Cepat membangun UI dashboard/tabel yang rapi |
| Chart/Dashboard | **Chart.js** (via CDN) | Ringan, cukup untuk pie/bar chart realisasi PKPPT |
| Autentikasi | **Laravel Breeze/Fortify** (email+password) **+ Laravel Socialite** (Google OAuth) | Memenuhi kebutuhan login manual & Google SSO sekaligus |
| Queue & Notifikasi | **Laravel Queue (database driver)** + **Laravel Notification (Mail channel)** | Reminder H-3 s.d. H-1 otomatis via email, tanpa perlu Redis di server internal sederhana |
| Kalender | **Google Calendar API** (opsional per user, via OAuth token yang sama dengan SSO) | Meneruskan fitur integrasi kalender dari versi lama |
| File storage | **Local disk storage** (`storage/app/arsip`) dengan opsi symlink ke NAS internal | Arsip digital laporan/tindak lanjut, sesuai lingkungan on-premise |
| Excel import/export | **Laravel Excel (maatwebsite/excel)** | Untuk migrasi data lama dari Google Spreadsheet & ekspor laporan |
| Web server | **Nginx + PHP-FPM** di server internal Diskominfo | Sesuai keputusan hosting on-premise |
| Environment | **Docker Compose** (app, mysql, nginx) — opsional, memudahkan deployment berulang di server Diskominfo | Portabilitas & konsistensi environment |

> Catatan untuk AI agent: gunakan Laravel terbaru yang stabil saat implementasi (cek versi rilis
> saat itu), dan sesuaikan minor version PHP/MySQL dengan yang tersedia di server Diskominfo.

---

## 3. ARSITEKTUR APLIKASI

```
[ Browser Pengguna ]
        |
        v
[ Nginx (reverse proxy, TLS internal) ]
        |
        v
[ Laravel App (PHP-FPM) ]
   |-- Web routes (Blade/Livewire) --> Dashboard, Input Data, Kegiatan Pengawasan, dst.
   |-- Auth: Breeze (manual) + Socialite (Google SSO)
   |-- Queue Worker (scheduler: reminder H-3..H-1, sinkron Google Calendar)
   |-- Storage: arsip digital (laporan, tindak lanjut)
        |
        v
[ MySQL 8 ] <--- single source of truth (menggantikan Google Spreadsheet)
        |
        v
[ Google Calendar API ] (opsional, per-user OAuth token, push event penugasan)
[ Mail Server (SMTP internal / relay) ] (notifikasi email)
```

**Prinsip desain:**
- Single source of truth di MySQL — tidak ada lagi input paralel di banyak sheet.
- Role-based access control (RBAC) menggantikan "siapa saja bisa edit sheet".
- Semua perubahan data tercatat di `activity_log` (audit trail) — mengatasi risiko human error
  tanpa jejak yang ada di spreadsheet.
- Perbandingan PKPPT vs realisasi dihitung otomatis dari relasi tabel `pkppt` ↔ `penugasan`,
  bukan rekap manual.

---

## 4. STRUKTUR MODUL (mengacu peta halaman SIPANDA v1)

| Modul Web Baru | Padanan Halaman Lama | Perubahan Kunci |
|---|---|---|
| **Landing / Home** | Halaman Depan | Menu dinamis sesuai role login |
| **Dashboard Realtime** | Dashboard Realtime | Data live dari DB, filter per Irban/periode/tahun |
| **Input Penugasan** | Input Data | Form tervalidasi, dropdown master data, no input manual bebas |
| **Data Penugasan (PKPPT & Non-PKPPT)** | Hasil Input Non PKPPT | Satu tabel terpadu dengan flag `sesuai_pkppt`, filter & pencarian |
| **Kegiatan Pengawasan (Monitoring PKPPT)** | Kegiatan Pengawasan | Perbandingan rencana vs realisasi otomatis (bukan manual) |
| **Tindak Lanjut & Arsip Digital** *(baru)* | — | Modul baru sesuai arahan pengembangan dokumen sumber |
| **Beban Kerja Personil** *(baru)* | — | Rekap penugasan per personil per periode |
| **Master Data** *(baru, admin)* | — | Kelola Objek Penugasan, Jenis Penugasan, Irban, Pengguna |
| **Manajemen Pengguna & Role** *(baru)* | — | RBAC: Inspektur, Irban, Auditor/P2UPD, Sekretariat, OPD |
| **Notifikasi & Kalender** | Integrasi Google Calendar | Reminder H-3 s.d. H-1 otomatis via email + sinkron kalender opsional |
| **Perencanaan PKPT (N-1)** *(baru)* | — | Universe pengawasan, penilaian risiko sederhana, kapasitas SDM, draf PKPT otomatis, alur usul→reviu→tetapkan |
| **Evaluasi Tahunan (N+1)** *(baru)* | — | Evaluasi capaian PKPT & tindak lanjut, umpan balik otomatis ke skor risiko siklus berikutnya |
| **Portal OPD** *(baru, area akses terpisah)* | — | Login khusus objek pemeriksaan, respons rekomendasi tindak lanjut dengan bukti, verifikasi dua arah |

---

## 5. HAK AKSES (ROLE) — Akses Multi-Level

Berdasarkan struktur organisasi Inspektorat Trenggalek, terdapat **6 role internal** dan **1 role eksternal**:

### Role Internal (guard `web`)

| Slug | Nama Role | Ruang Lingkup | Hak Utama |
|---|---|---|---|
| `admin` | **Admin Sistem** | Seluruh data | CRUD penuh semua modul, hapus data, lihat & ekspor audit log, kelola semua pengguna |
| `sekretariat` | **Sekretariat** | Seluruh data | Kelola master data, input PKPPT, kelola pengguna (kecuali hapus permanen), generate draf PKPT, tidak bisa hapus data penugasan |
| `inspektur` | **Inspektur** | Seluruh data (lintas Irban) | Lihat semua dashboard & laporan, tetapkan PKPT final (approve), read-mostly |
| `admin_irban` | **Admin Irban** | Data wilayah/Irban tertentu saja | Kelola data penugasan wilayah Irban-nya, verifikasi bukti OPD — staf tersendiri di tiap Irban, bukan Irban itu sendiri |
| `irban` | **Irban (Inspektur Pembantu)** | Data wilayah/Irban miliknya | Lihat & supervisi penugasan timnya, usulkan penyesuaian draf PKPT, approve/reject secara manajerial |
| `auditor` | **Auditor / P2UPD** | Penugasan yang melibatkan dirinya | Input & update status penugasan, unggah laporan hasil, catat tindak lanjut |

### Role Eksternal (guard `opd`, area `/opd/*` terpisah)

| Slug | Nama Role | Ruang Lingkup | Hak Utama |
|---|---|---|---|
| `opd` | **OPD** | Hanya rekomendasi tindak lanjut objeknya sendiri | Lihat rekomendasi, unggah bukti tindak lanjut — tidak ada akses ke modul internal apa pun |

> **Catatan implementasi:** `admin_irban` berada di bawah Irban tertentu (memiliki `irban_id`),
> sehingga semua query otomatis di-scope ke `irban_id` tersebut — sama seperti role `irban`.
> Perbedaannya: `irban` adalah pejabat struktural, `admin_irban` adalah staf operasional yang
> menjalankan input/kelola data harian untuk wilayah tersebut.

Role **OPD** memakai guard autentikasi terpisah (`opd`, bukan `web`) — lihat detail isolasi akses di Bagian 6, Fase 2.5.

---

## 6. ROADMAP IMPLEMENTASI (untuk AI agent, bertahap)

**Fase 0 — Fondasi**
1. Setup project Laravel, autentikasi (Breeze + Socialite Google), RBAC (spatie/laravel-permission).
2. Migrasi & seeder skema database (lihat `03-ERD-DATABASE-SCHEMA.md`).
3. Master data: Irban, Objek Penugasan, Jenis Penugasan, Sumber Penugasan.

**Fase 1 — Core proses bisnis (paritas dengan SIPANDA v1)**
4. Modul Input Penugasan (form + validasi + relasi tim & objek penugasan multi-select).
5. Modul Data Penugasan (list, filter, sesuai/tidak sesuai PKPPT).
6. Modul PKPPT (input rencana tahunan oleh Sekretariat).
7. Modul Kegiatan Pengawasan — perbandingan rencana (PKPPT) vs realisasi otomatis.
8. Dashboard Realtime — rekap status (selesai/berjalan/belum) + grafik, filter per Irban/tahun.

**Fase 1.5 — Perencanaan & Evaluasi PKPT (siklus tahunan N-1 → N+1)**
9. Tabel `penilaian_risiko`, `kapasitas_sdm`, kolom tambahan pada `pkppt` (status, skor acuan,
   penetapan, versi revisi) — lihat detail di `03-ERD-DATABASE-SCHEMA.md` §2.17–2.19.
10. Job penghitungan skor risiko per objek (aging, anggaran, temuan, tindak lanjut mandek,
    pengaduan khusus), dipicu manual oleh Sekretariat saat musim perencanaan dimulai.
11. Algoritma generate draf PKPT otomatis (skor risiko tertinggi, dibatasi kapasitas SDM Irban).
12. Alur status PKPT: draft → diusulkan → direviu → ditetapkan, dengan histori versi.
13. Modul Evaluasi Tahunan: dashboard capaian PKPT + tindak lanjut, job umpan balik skor risiko
    otomatis ke siklus perencanaan berikutnya.

**Fase 2 — Fitur peningkatan (arahan pengembangan dari dokumen sumber)**
14. Modul Tindak Lanjut & Arsip Digital, terhubung ke `penugasan`.
15. Modul Beban Kerja Personil (rekap jumlah/daftar SPT per personil per rentang tanggal).
16. Notifikasi email otomatis H-3 s.d. H-1 (scheduler + queue).
17. Integrasi Google Calendar opsional per pengguna (push event penugasan ke kalender pribadi).

**Fase 2.5 — Portal OPD (interaksi tindak lanjut dua arah)**
18. Tabel `bukti_tindak_lanjut`, kolom tambahan pada `users` (tipe_akun, objek_penugasan_id,
    status_undangan), perluasan enum status `tindak_lanjut` — lihat §2.20–2.21 di dokumen 03.
19. Guard & middleware area `/opd/*`, terpisah dari sesi internal (`web`).
20. Alur undangan akun OPD (token set-password) oleh Admin/Sekretariat.
21. Dashboard & form pengajuan bukti tindak lanjut sisi OPD; alur verifikasi (terima/tolak) sisi
    internal, dengan notifikasi dua arah dan reminder otomatis jika belum direspons.

**Fase 3 — Penyempurnaan**
22. Import data historis dari Google Spreadsheet lama (Laravel Excel).
23. Audit log & laporan ekspor (PDF/Excel).
24. Hardening: backup DB terjadwal, HTTPS internal, rate limiting login (khususnya `/opd/login`
    yang paling terekspos ke pihak eksternal).

---

## 7. NON-FUNCTIONAL REQUIREMENTS

- **Ketersediaan:** aplikasi berjalan di jaringan internal Pemda; target uptime jam kerja.
- **Keamanan:** password hashing (bcrypt/argon2), CSRF protection (bawaan Laravel), role
  middleware di setiap route, log aktivitas perubahan data.
- **Backup:** dump MySQL otomatis harian (cron + `mysqldump`), retensi minimal 30 hari.
- **Skalabilitas data:** dirancang untuk multi-tahun PKPPT (partisi logis per `tahun`), bukan
  hanya tahun berjalan.
- **Aksesibilitas:** responsif minimal untuk tablet/desktop (pengguna utama adalah pejabat &
  staf kantor, bukan mobile-first).
- **Bahasa:** antarmuka Bahasa Indonesia, konsisten dengan istilah di dokumen sumber (PKPPT,
  SPT, Irban, dsb.).

---

## 8. DOKUMEN TERKAIT

- `02-PRD-SIPANDA-WEB.md` — kebutuhan produk & lingkup fitur detail (termasuk Perencanaan PKPT & Portal OPD)
- `03-ERD-DATABASE-SCHEMA.md` — skema database & relasi tabel, seluruh modul dalam satu skema
- `04-FUNCTIONAL-SPEC-USER-STORIES.md` — spesifikasi fungsional & user story per modul
- `05-API-SPEC.md` — daftar route/endpoint untuk implementasi Laravel, area internal & OPD
- `08-Usulan-Fitur-Lanjutan-SIPANDA-Bahan-Pimpinan.docx` — materi presentasi 11 fitur usulan
  lanjutan (masih level konsep, belum masuk paket teknis 01–05)

> Dokumen `06-MODUL-PERENCANAAN-EVALUASI-PKPT.md` dan `07-MODUL-PORTAL-OPD.md` sudah digabung
> ke dalam paket 01–05 ini dan tidak lagi digunakan sebagai rujukan terpisah.
