# ROADMAP PENGEMBANGAN LANJUTAN SIPANDA WEB
## Pedoman AI Agent — Inspektorat Kabupaten Trenggalek

**Versi Dokumen:** 1.0
**Terakhir diperbarui:** 1 September 2026
**Status Proyek:** 100% Selesai — Seluruh Sprint 1 s.d. Sprint 8 Selesai Diimplementasikan & Diverifikasi
**Dibuat oleh:** Analisis Antigravity AI atas seluruh codebase

> ⚠️ **INSTRUKSI UNTUK AI AGENT**
> Baca dokumen ini **seluruhnya** sebelum mengerjakan task apapun di project SIPANDA.
> Setiap perubahan wajib mengacu pada konvensi, prioritas, dan checklist yang tertulis di sini.
> Setelah menyelesaikan setiap sprint, update bagian `## STATUS SPRINT` di bawah.

---

## DAFTAR ISI

1. [Konteks Proyek](#1-konteks-proyek)
2. [Tech Stack & Arsitektur](#2-tech-stack--arsitektur)
3. [State Codebase Saat Ini](#3-state-codebase-saat-ini)
4. [Konvensi & Aturan Kode](#4-konvensi--aturan-kode)
5. [SPRINT 1 — Security Hardening (WAJIB DIKERJAKAN PERTAMA)](#5-sprint-1--security-hardening)
6. [SPRINT 2 — Email Notification & Scheduler](#6-sprint-2--email-notification--scheduler)
7. [SPRINT 3 — Perbaikan UI & Fitur Master Data](#7-sprint-3--perbaikan-ui--fitur-master-data)
8. [SPRINT 4 — Perencanaan PKPT: Histori Versi & Alur Reviu](#8-sprint-4--perencanaan-pkpt-histori-versi--alur-reviu)
9. [SPRINT 5 — Import Data Historis dari Spreadsheet](#9-sprint-5--import-data-historis-dari-spreadsheet)
10. [SPRINT 6 — Notifikasi In-App UI & Reset Password OPD](#10-sprint-6--notifikasi-in-app-ui--reset-password-opd)
11. [SPRINT 7 — Performance & Query Optimization](#11-sprint-7--performance--query-optimization)
12. [SPRINT 8 — Production Readiness & Google Calendar](#12-sprint-8--production-readiness--google-calendar)
13. [Fitur Masa Depan (Backlog)](#13-fitur-masa-depan-backlog)
14. [STATUS SPRINT (Update Setiap Selesai)](#14-status-sprint)

---

## 1. KONTEKS PROYEK

**Nama Sistem:** SIPANDA Web — Sistem Informasi Pengawasan Terintegrasi
**Instansi:** Inspektorat Kabupaten Trenggalek
**Dasar Hukum:** Perbup Trenggalek No. 1 Tahun 2024

**Tujuan Utama:**
Menggantikan SIPANDA v1 (Google Spreadsheet) dengan aplikasi web terintegrasi yang mencakup:
pemantauan penugasan (SPT), tindak lanjut hasil pengawasan, arsip digital, perencanaan PKPT
berbasis risiko, portal khusus OPD, dan e-consulting (layanan advisory APIP).

**Dokumen Referensi** (semua ada di `/docs/`):
- `01-BLUEPRINT-SIPANDA-WEB.md` — panduan teknis & roadmap fase
- `02-PRD-SIPANDA-WEB.md` — kebutuhan produk & scope
- `03-ERD-DATABASE-SCHEMA.md` — skema database lengkap
- `04-FUNCTIONAL-SPEC-USER-STORIES.md` — user stories per modul
- `05-API-SPEC.md` — daftar route & endpoint
- `06-DEPLOYMENT-CONFIG.md` — konfigurasi deployment production
- `07-PANDUAN-PENGUJIAN-TIM-IRBAN.md` — panduan testing untuk pengguna

---

## 2. TECH STACK & ARSITEKTUR

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Backend | Laravel | 11 (framework ^13.8) |
| PHP | PHP | ^8.3 |
| Database DEV | SQLite | (file: `database/database.sqlite`) |
| Database PROD | **MySQL 8** | Wajib MySQL saat production |
| Auth | Laravel Breeze + Spatie Permission + Socialite | Breeze ^2.4, Spatie ^8.3 |
| Frontend | Blade + Tailwind CSS | Livewire ^4.3 (terpasang, belum digunakan) |
| Export | CSV via `StreamedResponse` | (maatwebsite/excel BELUM dipasang) |
| File Storage | Local disk (`public` & `private`) | |
| Queue | Database driver | |
| Notification | In-app (tabel `notifikasi`) | Email belum aktif |

**Guard Auth:**
- `web` — pengguna internal (admin, sekretariat, inspektur, admin_irban, irban, auditor)
- `opd` — pengguna eksternal OPD (area `/opd/*`)

**RBAC (Spatie Permission):**
6 role internal: `admin`, `sekretariat`, `inspektur`, `irban`, `admin_irban`, `auditor`
1 role eksternal: `opd` (guard `opd`)

---

## 3. STATE CODEBASE SAAT INI

### Sudah Selesai & Berfungsi
- Autentikasi: login manual + Google SSO (`GoogleController`)
- RBAC: 6 role internal + 1 role OPD (Spatie Permission)
- Modul Penugasan (SPT): CRUD + multi-irban + ST Perpanjangan + auto-sync status
- Modul PKPPT: CRUD + alur `draft -> diusulkan -> ditetapkan`
- Modul Tindak Lanjut: CRUD + rincian penyetoran NTPN + LHP per dokumen
- Modul Arsip Digital: upload/download/delete
- Modul Beban Kerja Personil
- Modul Kegiatan Pengawasan (monitoring PKPPT vs realisasi)
- Dashboard Realtime (rekap status + breakdown jenis)
- Modul Perencanaan PKPT: skor risiko 5 variabel + generate draf otomatis
- Modul Evaluasi Tahunan
- Portal OPD: guard terpisah, undangan token, dashboard OPD, bukti tindak lanjut
- Verifikasi Bukti TL: terima/tolak dengan catatan
- E-Consulting: konsultasi OPD + chat + disposisi + berita acara + FAQ publik
- Export CSV: Penugasan, PKPPT, Matriks LHP
- Audit Log: sebagian controller (belum konsisten semua)
- Reminder command: `sipanda:send-reminders` (hanya in-app, belum email)

### Belum Ada / Belum Berfungsi
- Email notification via SMTP (command sudah ada tapi tidak kirim email)
- Scheduler cron terdaftar (`routes/console.php` masih kosong)
- UI notifikasi in-app (bell icon di navbar)
- Rate limiting login (internal & OPD)
- Edit/Delete Objek Penugasan & Jenis Penugasan di Master Data
- Forgot password untuk OPD
- Import dari Google Spreadsheet (`maatwebsite/excel` belum di-install)
- Histori versi PKPT & alur `direviu`
- Google Calendar integration
- Docker Compose untuk deployment
- Backup DB terjadwal

### Perlu Diperbaiki (Bug / Security)
- `APP_DEBUG=true` di `.env.example`
- Tidak ada `throttle` middleware di login routes
- Nama file upload menggunakan `getClientOriginalName()` — rentan path traversal
- Route `/audit-log` tidak ada middleware permission
- File arsip tersimpan di disk `public` — seharusnya `private` untuk arsip sensitif
- `SESSION_ENCRYPT=false`
- `ActivityLog::catat()` bisa `null` user_id saat dipanggil dari console
- N+1 query di `TindakLanjutController::index()` (query ganda)
- `APP_LOCALE=en` seharusnya `id`
- Duplikasi file migration (dua file untuk `add_nilai_diawasi_rp`)
- `laravel/pao` di composer.json dev-dependencies — kemungkinan typo

---

## 4. KONVENSI & ATURAN KODE

> AI Agent WAJIB mengikuti konvensi ini saat menulis atau memodifikasi kode.

### 4.1 Bahasa & Penamaan
- **Antarmuka pengguna:** seluruhnya Bahasa Indonesia
- **Kode (variabel, method, kolom DB):** snake_case Bahasa Indonesia/Inggris campuran (ikuti yang sudah ada)
- **Class PHP:** PascalCase
- **Route name:** `modul.aksi` (contoh: `penugasan.create`, `tindak-lanjut.store`)
- **View folder:** kebab-case (contoh: `tindak-lanjut/`, `kegiatan-pengawasan/`)
- **Tabel DB:** snake_case (contoh: `penugasan`, `tindak_lanjut`, `objek_penugasan`)

### 4.2 Controller
- Gunakan type-hinting return (`View`, `RedirectResponse`)
- Validasi selalu memakai `$request->validate([...])` dengan pesan error Bahasa Indonesia
- Setelah create/update/delete yang penting: panggil `ActivityLog::catat()`
- Soft delete menggunakan `SoftDeletes` trait — jangan hard delete kecuali ada alasan kuat

### 4.3 Model
- Relasi selalu didefinisikan sebagai method dengan nama yang deskriptif
- Gunakan `$casts` untuk kolom boolean, date, float, json
- Buat `scope` untuk filter yang sering dipakai
- Accessor & mutator diizinkan untuk display/format data

### 4.4 Database & Migration
- **Setiap kolom baru** harus dalam migration terpisah, bukan mengedit migration lama
- Nama migration: `YYYY_MM_DD_HHMMSS_deskripsi_singkat.php`
- Selalu sertakan `->nullable()` pada kolom opsional
- Foreign key wajib punya index

### 4.5 Security — Aturan Wajib
- **File upload:** Selalu gunakan `Str::uuid()` sebagai nama file, bukan nama asli dari client
- **File sensitif** (arsip, bukti TL, LHP): simpan di disk `local` (private), bukan `public`
  - Untuk akses download, gunakan controller, bukan URL langsung ke /storage/
- **Rate limiting:** Semua route login wajib punya `throttle` middleware
- **Permission:** Setiap route yang butuh role tertentu wajib punya `->middleware('can:permission.name')`
- **Audit log:** Setiap perubahan data penting wajib dicatat

### 4.6 ActivityLog — Konvensi Pemanggilan
```php
// Saat create:
ActivityLog::catat('nama_tabel', $model->id, 'create', null, $model->toArray());

// Saat update:
$sebelum = $model->toArray();
$model->update($validated);
ActivityLog::catat('nama_tabel', $model->id, 'update', $sebelum, $model->fresh()->toArray());

// Saat delete:
$sebelum = $model->toArray();
$model->delete();
ActivityLog::catat('nama_tabel', $model->id, 'delete', $sebelum, null);
```

### 4.7 View & Blade
- Layout utama: `layouts/app.blade.php` (authenticated) dan `layouts/guest.blade.php`
- Komponen reusable: simpan di `resources/views/components/`
- Flash message: gunakan `session('status')` dan `session('error')`
- Konfirmasi delete: selalu tampilkan modal konfirmasi

---

## 5. SPRINT 1 — SECURITY HARDENING

> **Prioritas: KRITIS — Kerjakan ini SEBELUM fitur apapun lainnya**
> Estimasi: 1-2 hari kerja

### [S1-01] Perbaiki `.env.example` & konfigurasi dasar
- Ubah `APP_DEBUG=true` menjadi `APP_DEBUG=false`
- Ubah `APP_LOCALE=en` menjadi `APP_LOCALE=id`
- Ubah `SESSION_ENCRYPT=false` menjadi `SESSION_ENCRYPT=true`
- Tambahkan variabel yang hilang: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`

### [S1-02] Rate Limiting pada Login Routes
Edit `routes/web.php`:
```php
// Login OPD - tambahkan throttle
Route::post('/login', [OpdAuthController::class, 'login'])
    ->name('login.store')
    ->middleware('throttle:5,1');

// Pastikan routes/auth.php juga punya throttle:10,1 di POST /login internal
```

### [S1-03] Perbaiki Upload File — Nama File Aman + Disk Private
Ganti pattern di SEMUA controller yang upload file:
```php
// SEBELUM (tidak aman):
$fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
$filePath = $file->storeAs('folder', $fileName, 'public');

// SESUDAH (aman):
use Illuminate\Support\Str;
$fileName = Str::uuid() . '.' . $file->extension();
$filePath = $file->storeAs('arsip/' . date('Y/m'), $fileName); // disk 'local' (default)
```
File yang perlu diubah:
- `TindakLanjutController.php` (method store, storeRespon, update)
- `ArsipDigitalController.php`
- `KonsultasiController.php`
- `Opd/OpdKonsultasiController.php`
- `Opd/OpdDashboardController.php`

Buat route download yang memeriksa auth:
```php
Route::get('/arsip/{arsip}/download', [ArsipDigitalController::class, 'download'])->name('arsip.download');
```
Method download:
```php
public function download(ArsipDigital $arsip): StreamedResponse
{
    abort_unless(Storage::exists($arsip->path_file), 404);
    return Storage::download($arsip->path_file, $arsip->nama_file);
}
```

### [S1-04] Tambahkan Middleware Permission ke Route yang Kurang
```php
// Di routes/web.php:
Route::get('/audit-log', ...)->middleware('can:audit_log.view')->name('audit-log.index');
Route::get('/tindak-lanjut/verifikasi-bukti', ...)->middleware('can:bukti.verifikasi');
Route::get('/perencanaan', ...)->middleware('can:perencanaan.view');
Route::get('/evaluasi', ...)->middleware('can:evaluasi.view');
Route::get('/master/users', ...)->middleware('can:users.view');
Route::get('/master/opd-users', ...)->middleware('can:opd_users.manage');
```

### [S1-05] Perbaiki ActivityLog — Handle null user_id
Di `app/Models/ActivityLog.php`:
```php
public static function catat(string $tabel, int $recordId, string $aksi, ?array $sebelum = null, ?array $sesudah = null): void
{
    $userId = auth()->id() ?? auth()->guard('opd')->id() ?? null;
    
    static::create([
        'user_id'      => $userId,
        'tabel'        => $tabel,
        'record_id'    => $recordId,
        'aksi'         => $aksi,
        'data_sebelum' => $sebelum,
        'data_sesudah' => $sesudah,
        'ip_address'   => request()->ip(),
        'created_at'   => now(),
    ]);
}
```
Pastikan kolom `user_id` di migration `activity_log` adalah `->nullable()`.

### [S1-06] Tambahkan Audit Log ke Controller yang Belum Punya
- `ArsipDigitalController` (store, destroy)
- `EvaluasiTahunanController` (generate)
- `OpdUserManagementController` (store)

---

## 6. SPRINT 2 — EMAIL NOTIFICATION & SCHEDULER

> **Prioritas: TINGGI — Fitur inti yang disebutkan di PRD tapi belum berfungsi**
> Estimasi: 2-3 hari kerja

### [S2-01] Buat Laravel Notification Classes

Buat file: `app/Notifications/PenugasanReminderNotification.php`
```php
<?php
namespace App\Notifications;

use App\Models\Penugasan;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PenugasanReminderNotification extends Notification
{
    public function __construct(
        public Penugasan $penugasan,
        public string $jenis // 'h3' atau 'h1'
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $label = $this->jenis === 'h3' ? 'H-3' : 'H-1 (Besok)';
        return (new MailMessage)
            ->subject("[SIPANDA] Reminder {$label}: {$this->penugasan->no_spt}")
            ->greeting("Yth. Bapak/Ibu {$notifiable->nama_tanpa_gelar}")
            ->line("Ini adalah pengingat {$label} untuk kegiatan penugasan:")
            ->line("**No. SPT:** {$this->penugasan->no_spt}")
            ->line("**Uraian:** {$this->penugasan->uraian_penugasan}")
            ->line("**Tanggal Mulai:** {$this->penugasan->tanggal_mulai->format('d F Y')}")
            ->action('Lihat Detail', route('penugasan.show', $this->penugasan->id))
            ->salutation("Hormat kami, SIPANDA — Inspektorat Kab. Trenggalek");
    }
}
```

Buat juga:
- `app/Notifications/TindakLanjutMandekNotification.php` — notifikasi ke Irban jika TL > 14 hari
- `app/Notifications/BuktiVerifikasiNotification.php` — notifikasi ke OPD setelah verifikasi

### [S2-02] Update Command `SendPenugasanReminders` untuk Kirim Email
Update `app/Console/Commands/SendPenugasanReminders.php`:
- Tambahkan `$user->notify(new PenugasanReminderNotification($penugasan, 'h3'))` setelah `Notifikasi::firstOrCreate()`
- Tetap simpan ke tabel `notifikasi` untuk in-app notification

### [S2-03] Daftarkan Scheduler di `routes/console.php`
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('sipanda:send-reminders')->dailyAt('07:00');
```

### [S2-04] Update `.env.example` dengan Variabel Mail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.trenggalek.go.id
MAIL_PORT=587
MAIL_USERNAME=sipanda@trenggalek.go.id
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=sipanda@trenggalek.go.id
MAIL_FROM_NAME="SIPANDA - Inspektorat Trenggalek"
```

### [S2-05] Notifikasi Verifikasi Bukti ke OPD
Di `VerifikasiBuktiController::verifikasi()`, setelah update status:
```php
$pengunggah = $bukti->pengunggah;
if ($pengunggah && $pengunggah->email) {
    $pengunggah->notify(new BuktiVerifikasiNotification($bukti, $validated['status_verifikasi']));
}
```

---

## 7. SPRINT 3 — PERBAIKAN UI & FITUR MASTER DATA

> **Prioritas: SEDANG**
> Estimasi: 2-3 hari kerja

### [S3-01] Edit & Delete Objek Penugasan
Tambahkan method di `MasterDataController`:
- `updateObjekPenugasan(Request $request, ObjekPenugasan $objek): RedirectResponse`
- `destroyObjekPenugasan(ObjekPenugasan $objek): RedirectResponse`

Tambahkan route:
```php
Route::put('/master/objek-penugasan/{objek}', [MasterDataController::class, 'updateObjekPenugasan'])->name('master.objek-penugasan.update');
Route::delete('/master/objek-penugasan/{objek}', [MasterDataController::class, 'destroyObjekPenugasan'])->middleware('can:master.delete')->name('master.objek-penugasan.destroy');
```

### [S3-02] Edit & Delete Jenis Penugasan
Sama seperti [S3-01] untuk `JenisPenugasan`.

### [S3-03] Filter di Audit Log
Update `MasterDataController::auditLog()` dengan filter: tabel, aksi, user, rentang tanggal.
Tambahkan pagination yang sudah ada dengan `->withQueryString()`.

### [S3-04] Forgot Password untuk OPD
Buat flow reset password di area `/opd/*`:
- Route: `GET /opd/lupa-password`, `POST /opd/lupa-password`, `GET /opd/reset/{token}`, `POST /opd/reset/{token}`
- Tambahkan method di `OpdAuthController`: `showForgotPassword`, `sendResetLink`, `showReset`, `storeNewPassword`
- Kirim email berisi link reset (token baru, bukan token undangan)
- View: `resources/views/opd/auth/forgot-password.blade.php`, `reset-password.blade.php`

### [S3-05] Toggle Aktif/Nonaktif Pengguna di Master Users
Di view `master/users.blade.php`, tambahkan tombol toggle langsung di tabel tanpa form edit penuh.

---

## 8. SPRINT 4 — PERENCANAAN PKPT: HISTORI VERSI & ALUR REVIU

> **Prioritas: SEDANG**
> Estimasi: 2-3 hari kerja

### [S4-01] Migration Baru: Kolom Versi & pkppt_induk_id
```php
Schema::table('pkppt', function (Blueprint $table) {
    $table->unsignedInteger('versi')->default(1)->after('status');
    $table->unsignedBigInteger('pkppt_induk_id')->nullable()->after('versi');
    $table->foreign('pkppt_induk_id')->references('id')->on('pkppt')->nullOnDelete();
    $table->text('catatan_revisi')->nullable()->after('pkppt_induk_id');
    $table->timestamp('direviu_pada')->nullable();
    $table->unsignedBigInteger('direviu_oleh')->nullable();
    $table->foreign('direviu_oleh')->references('id')->on('users')->nullOnDelete();
});
```

### [S4-02] Tambah Status `direviu` pada Alur PKPT
Alur lengkap: `draft -> diusulkan -> direviu -> ditetapkan`

Tambahkan method `reviu()` di `PerencanaanPkptController`:
```php
public function reviu(Pkppt $pkppt): RedirectResponse
{
    $pkppt->update([
        'status'       => 'direviu',
        'direviu_pada' => now(),
        'direviu_oleh' => auth()->id(),
    ]);
    ActivityLog::catat('pkppt', $pkppt->id, 'update', null, ['status' => 'direviu']);
    return back()->with('status', "PKPPT dalam proses reviu.");
}
```

### [S4-03] Relasi Versi di Model `Pkppt`
```php
public function pkpptInduk() { return $this->belongsTo(Pkppt::class, 'pkppt_induk_id'); }
public function revisi() { return $this->hasMany(Pkppt::class, 'pkppt_induk_id'); }
```

### [S4-04] Fungsi Revisi PKPT
Tambahkan `revisiPkppt()` yang:
1. Membuat record PKPT baru (salinan) dengan `pkppt_induk_id` = id PKPT lama
2. Menaikkan `versi` + 1
3. Mengubah status PKPT lama menjadi `diarsipkan`
4. PKPT baru dimulai dari status `draft`

---

## 9. SPRINT 5 — IMPORT DATA HISTORIS DARI SPREADSHEET

> **Prioritas: SEDANG** (penting untuk migrasi dari SIPANDA v1)
> Estimasi: 3-4 hari kerja

### [S5-01] Install Package
```bash
composer require maatwebsite/excel
```

### [S5-02] Buat Import Classes
- `app/Imports/PenugasanImport.php` — mapping kolom spreadsheet ke model `Penugasan`
- `app/Imports/TindakLanjutImport.php` — mapping ke model `TindakLanjut`

Referensi field: lihat `docs/02-PRD-SIPANDA-WEB.md` bagian 6.

### [S5-03] Buat ImportController & Route
`app/Http/Controllers/ImportController.php` dengan method:
- `showImport(): View`
- `importPenugasan(Request $request): RedirectResponse`
- `importTindakLanjut(Request $request): RedirectResponse`

Route (hanya admin):
```php
Route::get('/import', [ImportController::class, 'showImport'])->middleware('can:master.create')->name('import.index');
Route::post('/import/penugasan', ...)->middleware('can:master.create')->name('import.penugasan');
```

### [S5-04] Template Excel untuk Import
Sediakan file template yang bisa diunduh di `public/templates/`:
- `template-import-penugasan.xlsx`
- `template-import-tindak-lanjut.xlsx`

---

## 10. SPRINT 6 — NOTIFIKASI IN-APP UI & RESET PASSWORD OPD

> **Prioritas: SEDANG**
> Estimasi: 2 hari kerja

### [S6-01] Bell Icon Notifikasi di Navbar
Update `resources/views/layouts/navigation.blade.php`:
- Tambahkan bell icon dengan badge counter (jumlah notifikasi belum dibaca)
- Dropdown berisi 5 notifikasi terbaru
- Link "Lihat Semua" ke `/notifikasi`

Perlu dibuat:
- Route: `GET /notifikasi`, `PATCH /notifikasi/{id}/baca`, `POST /notifikasi/baca-semua`
- Controller: `app/Http/Controllers/NotifikasiController.php`
- View: `resources/views/notifikasi/index.blade.php`

### [S6-02] Mark Notifikasi sebagai Dibaca
```php
public function markAsRead(Notifikasi $notifikasi): RedirectResponse
{
    abort_unless($notifikasi->user_id === auth()->id(), 403);
    $notifikasi->update(['status' => 'dibaca']);
    return back();
}
```

---

## 11. SPRINT 7 — PERFORMANCE & QUERY OPTIMIZATION

> **Prioritas: RENDAH-SEDANG** (penting sebelum data volume besar)
> Estimasi: 1-2 hari kerja

### [S7-01] Perbaiki N+1 Query di `TindakLanjutController::index()`
Hapus query kedua yang redundan:
```php
// HAPUS baris ini (data yang sama diambil dua kali):
$allTl = TindakLanjut::with('rincianPenyetoran')->get();

// Gunakan $allTindakLanjut yang sudah diambil sebelumnya untuk menghitung metrik
$totalRekomendasi = $allTindakLanjut->count();
$countSesuai = $allTindakLanjut->where('status_tindak_lanjut', 'selesai')->count();
// dst...
```

### [S7-02] Pagination TindakLanjut
Saat ini `->get()` tanpa pagination. Ubah ke paginate per LHP/SPT, bukan per rekomendasi individual.

### [S7-03] Index Database yang Hilang
Cek dan tambahkan index di migration untuk kolom yang sering di-query:
- `penugasan`: `irban_id`, `status`, `tanggal_mulai`
- `tindak_lanjut`: `penugasan_id`, `status_tindak_lanjut`
- `notifikasi`: `user_id`, `status`
- `activity_log`: `tabel`, `user_id`, `created_at`

### [S7-04] Pindah Auto-Sync Status ke Scheduler
Saat ini auto-sync dijalankan setiap GET `/penugasan`. Pindahkan ke scheduler harian:
```php
// routes/console.php:
Schedule::call(function () {
    Penugasan::where('status', 'belum_berjalan')
        ->where('tanggal_mulai', '<=', now()->startOfDay())
        ->update(['status' => 'berjalan']);
})->daily();
```
Hapus kode auto-sync dari `PenugasanController::index()`.

---

## 12. SPRINT 8 — PRODUCTION READINESS & GOOGLE CALENDAR

> **Prioritas: RENDAH** (diperlukan saat siap deploy ke server production)
> Estimasi: 3-4 hari kerja

### [S8-01] Migrasi dari SQLite ke MySQL
Update `.env` production:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipanda
DB_USERNAME=sipanda_user
DB_PASSWORD=[password_kuat]
```
Jalankan: `php artisan migrate --force` di server production.

### [S8-02] Buat Docker Compose
File `docker-compose.yml` berdasarkan template di `06-DEPLOYMENT-CONFIG.md` bagian 7.
Services yang dibutuhkan: `app` (PHP-FPM 8.3), `nginx`, `db` (MySQL 8.0), `queue` (worker Laravel Queue).

### [S8-03] Command Backup Database
Buat `app/Console/Commands/BackupDatabase.php`:
- Jalankan `mysqldump` dan simpan ke `storage/backups/`
- Hapus backup yang lebih dari 30 hari
- Daftarkan ke scheduler: `Schedule::command('sipanda:backup-db')->dailyAt('01:00');`

### [S8-04] Google Calendar Integration (Opsional per User)
Integrasi Google Calendar API untuk push event penugasan ke kalender pribadi:
- Simpan OAuth token di kolom `google_calendar_token` (tabel `users`)
- Buat `app/Services/GoogleCalendarService.php`
- Trigger saat penugasan dibuat atau diperbarui

### [S8-05] Lengkapi Checklist Go-Live
Verifikasi semua item di `06-DEPLOYMENT-CONFIG.md` bagian 8 sebelum minta Diskominfo deploy.

---

## 13. FITUR MASA DEPAN (BACKLOG)

> Jangan kerjakan kecuali ada persetujuan eksplisit dari pemilik proyek.

| # | Fitur | Keterangan |
|---|-------|------------|
| B1 | Livewire components | Pertimbangkan untuk form penugasan yang kompleks |
| B2 | Integrasi SIMPEG/e-office Pemda | Out-of-scope Fase 1 (PRD 4.2) |
| B3 | Digital signature workflow | Out-of-scope Fase 1 |
| B4 | Analitik prediktif / AI scoring risiko | Kemungkinan masa depan (PRD 4.2) |
| B5 | Aplikasi mobile native | Out-of-scope; cukup web responsif |
| B6 | Export PDF laporan formal | Pertimbangkan `barryvdh/laravel-dompdf` |
| B7 | Dashboard publik lebih detail | Halaman `/` ada, perlu diperkaya |
| B8 | Multi-tahun PKPT dalam satu tampilan | Untuk analisis tren antar tahun |

---

## 14. STATUS SPRINT

> AI AGENT: Update tabel ini setiap selesai mengerjakan sprint atau task individual.
> Ubah status: belum -> sedang -> selesai

### Sprint 1 — Security Hardening

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S1-01 | Perbaiki `.env.example` & konfigurasi dasar | selesai ✅ | 1 Sep 2026 |
| S1-02 | Rate limiting pada login routes | selesai ✅ | 1 Sep 2026 |
| S1-03 | Perbaiki upload file — nama file aman + disk private | selesai ✅ | 1 Sep 2026 |
| S1-04 | Tambahkan middleware permission ke route yang kurang | selesai ✅ | 1 Sep 2026 |
| S1-05 | Perbaiki ActivityLog — handle null user_id | selesai ✅ | 1 Sep 2026 |
| S1-06 | Tambahkan audit log ke controller yang belum punya | selesai ✅ | 1 Sep 2026 |

### Sprint 2 — Email Notification & Scheduler

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S2-01 | Buat Laravel Notification classes | selesai ✅ | 1 Sep 2026 |
| S2-02 | Update command `SendPenugasanReminders` | selesai ✅ | 1 Sep 2026 |
| S2-03 | Daftarkan scheduler di `routes/console.php` | selesai ✅ | 1 Sep 2026 |
| S2-04 | Update `.env.example` dengan variabel mail | selesai ✅ | 1 Sep 2026 |
| S2-05 | Notifikasi verifikasi bukti ke OPD | selesai ✅ | 1 Sep 2026 |

### Sprint 3 — Perbaikan UI & Fitur Master Data

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S3-01 | Edit & Delete Objek Penugasan | selesai ✅ | 1 Sep 2026 |
| S3-02 | Edit & Delete Jenis Penugasan | selesai ✅ | 1 Sep 2026 |
| S3-03 | Filter di Audit Log | selesai ✅ | 1 Sep 2026 |
| S3-04 | Forgot password untuk OPD | selesai ✅ | 1 Sep 2026 |
| S3-05 | Toggle aktif/nonaktif pengguna di Master Users | selesai ✅ | 1 Sep 2026 |

### Sprint 4 — Perencanaan PKPT: Histori Versi & Alur Reviu

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S4-01 | Tambah Migration: Kolom versi & histori PKPT | selesai ✅ | 1 Sep 2026 |
| S4-02 | Tambah status `direviu` pada alur PKPT | selesai ✅ | 1 Sep 2026 |
| S4-03 | Relasi versi di model `Pkppt` | selesai ✅ | 1 Sep 2026 |
| S4-04 | Fungsi revisi PKPT di controller & UI | selesai ✅ | 1 Sep 2026 |

### Sprint 5 — Import Data Historis

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S5-01 | Buat Service / Helper Importer CSV | selesai ✅ | 1 Sep 2026 |
| S5-02 | Controller & Route Import Data Historis | selesai ✅ | 1 Sep 2026 |
| S5-03 | UI Upload & Preview Import | selesai ✅ | 1 Sep 2026 |
| S5-04 | Logging & Rollback Handling saat Import | selesai ✅ | 1 Sep 2026 |

### Sprint 6 — Notifikasi In-App UI

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S6-01 | Buat `NotifikasiController` | selesai ✅ | 1 Sep 2026 |
| S6-02 | Buat UI Icon Lonceng + Dropdown di Navbar | selesai ✅ | 1 Sep 2026 |
| S6-03 | Buat Halaman Daftar Notifikasi Lengkap | selesai ✅ | 1 Sep 2026 |
| S6-04 | Auto-Refresh Unread Badge (Polling AJAX 30s) | selesai ✅ | 1 Sep 2026 |

### Sprint 7 — Performance & Query Optimization

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S7-01 | Eager loading & hilangkan N+1 di `TindakLanjutController` | selesai ✅ | 1 Sep 2026 |
| S7-02 | Optimasi query & Caching di `DashboardController` & `PublicDashboardController` | selesai ✅ | 1 Sep 2026 |
| S7-03 | Migration Composite Indexes di database | selesai ✅ | 1 Sep 2026 |
| S7-04 | Caching query agregat berat | selesai ✅ | 1 Sep 2026 |

### Sprint 8 — Production Readiness & Google Calendar

| Kode | Task | Status | Tanggal Selesai |
|------|------|--------|----------------|
| S8-01 | Konfigurasi MySQL, Redis, & Environment Production | selesai ✅ | 1 Sep 2026 |
| S8-02 | Docker Compose, Dockerfile, Nginx & MySQL Conf | selesai ✅ | 1 Sep 2026 |
| S8-03 | Command `sipanda:backup-db` & Scheduler Harian | selesai ✅ | 1 Sep 2026 |
| S8-04 | Google Calendar Integration (Service, Controller & UI) | selesai ✅ | 1 Sep 2026 |
| S8-05 | Script Deployment Otomatis (`deploy.sh`) & Checklist Go-Live | selesai ✅ | 1 Sep 2026 |

---

## CATATAN KHUSUS UNTUK AI AGENT

1. **Selalu baca dokumen ini dulu** sebelum memulai task apapun di proyek SIPANDA.
2. **Jangan langsung mengubah logika bisnis** tanpa memahami alur di `04-FUNCTIONAL-SPEC-USER-STORIES.md`.
3. **Jangan melewati Sprint 1** — security hardening wajib dikerjakan sebelum fitur lain.
4. **Sebelum membuat migration baru**, cek seluruh file migration yang ada agar tidak duplikasi.
5. **Jangan mengubah `database.sqlite`** secara langsung — selalu lewat migration dan seeder.
6. **Jika ada keragu-raguan** antara pendekatan teknis A dan B, pilih yang lebih konsisten dengan kode yang sudah ada.
7. **Setiap sprint selesai**, update tabel status di bagian 14 dokumen ini.
8. **Dokumen ini disimpan di `/docs/`** — ini adalah source of truth pengembangan lanjutan SIPANDA.
