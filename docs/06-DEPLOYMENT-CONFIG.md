# DEPLOYMENT & ENVIRONMENT CONFIGURATION
# SIPANDA Web — Inspektorat Kabupaten Trenggalek

Versi: 1.0 | Status: **PERLU DILENGKAPI oleh Diskominfo/IT Inspektorat sebelum implementasi dimulai**

---

## 1. INFORMASI SERVER (ISI OLEH DISKOMINFO)

> ⚠️ Bagian ini harus diisi sebelum memulai setup environment. Tanpa informasi ini,
> developer tidak bisa memastikan kompatibilitas konfigurasi aplikasi.

| Parameter | Nilai | Status |
|---|---|---|
| Sistem Operasi Server | ___ (mis. Ubuntu 22.04 / CentOS 8) | ❌ Belum diisi |
| Versi PHP tersedia | ___ (target: 8.3) | ❌ Belum diisi |
| Versi MySQL tersedia | ___ (target: 8.0) | ❌ Belum diisi |
| Web server | ___ (Nginx / Apache) | ❌ Belum diisi |
| RAM server | ___ GB | ❌ Belum diisi |
| Storage tersedia untuk arsip | ___ GB | ❌ Belum diisi |
| Redis tersedia? | Ya / Tidak | ❌ Belum diisi |
| Supervisor tersedia? | Ya / Tidak | ❌ Belum diisi |
| Docker tersedia? | Ya / Tidak | ❌ Belum diisi |

---

## 2. DOMAIN & JARINGAN

| Parameter | Nilai | Status |
|---|---|---|
| Domain/subdomain internal | ___ (mis. `sipanda.trenggalek.go.id`) | ❌ Belum diisi |
| Protokol | HTTP saja / HTTPS (sertifikat internal) | ❌ Belum diisi |
| Jaringan | Intranet Pemda saja / bisa diakses internet? | ❌ Belum diisi |
| IP Server internal | ___ | ❌ Belum diisi |

> **Catatan Portal OPD:** Jika Portal OPD perlu diakses oleh pihak luar Pemda (OPD di luar kantor),
> server harus bisa diakses dari internet dengan HTTPS. Jika hanya intranet, OPD harus VPN dulu.

---

## 3. EMAIL / SMTP

| Parameter | Nilai | Status |
|---|---|---|
| SMTP host | ___ | ❌ Belum diisi |
| SMTP port | ___ (25 / 465 / 587) | ❌ Belum diisi |
| SMTP username | ___ | ❌ Belum diisi |
| SMTP password | ___ | ❌ Belum diisi |
| Pengirim default (`FROM`) | ___ (mis. `sipanda@trenggalek.go.id`) | ❌ Belum diisi |
| Nama pengirim | ___ (mis. `SIPANDA - Inspektorat Trenggalek`) | ❌ Belum diisi |

> Jika SMTP internal tidak tersedia, alternatif: Gmail Relay via akun Google Workspace Pemda,
> atau layanan transaksional seperti Mailgun/Brevo (perlu akses internet dari server).

---

## 4. GOOGLE OAUTH (untuk SSO & Calendar)

> **Tugas Diskominfo / IT Inspektorat — bukan tugas developer aplikasi.**

### Langkah yang harus diselesaikan:
- [ ] Buat project di [Google Cloud Console](https://console.cloud.google.com)
- [ ] Aktifkan API: **Google OAuth 2.0**, **Google Calendar API**
- [ ] Buat OAuth Client ID (tipe: Web application)
- [ ] Daftarkan Authorized Redirect URIs:
  - `https://[domain]/auth/google/callback` (login internal)
  - `https://[domain]/opd/auth/google/callback` (login OPD jika dipakai)
  - `https://[domain]/profil/google-calendar/callback` (koneksi kalender)
- [ ] Salin **Client ID** dan **Client Secret** ke file `.env` production

### Nilai yang dibutuhkan untuk `.env`:
```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://[domain]/auth/google/callback
```

> ⚠️ Jika Google OAuth belum siap saat deploy, fitur SSO di-disable dulu.
> Login manual (email + password) tetap berfungsi sebagai fallback.

---

## 5. KONFIGURASI QUEUE & SCHEDULER

### Opsi A: Tanpa Redis (server sederhana)
```env
QUEUE_CONNECTION=database
```
- Queue disimpan di tabel `jobs` di MySQL.
- Jalankan worker dengan: `php artisan queue:work --daemon`
- Butuh **Supervisor** untuk memastikan worker tetap berjalan.

### Opsi B: Dengan Redis (lebih andal)
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Laravel Scheduler (wajib untuk reminder & backup):
Tambahkan satu baris cron di server:
```bash
* * * * * cd /path/to/sipanda && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. KEBIJAKAN FILE ARSIP DIGITAL

| Parameter | Nilai yang Disepakati |
|---|---|
| Tipe file diizinkan | `pdf`, `docx`, `xlsx`, `jpg`, `png` |
| Ukuran maksimum per file | **10 MB** *(konfirmasi dengan Inspektorat)* |
| Total storage quota | ❌ **Perlu ditentukan** |
| Direktori penyimpanan | `storage/app/arsip/` (symlink ke NAS jika ada) |
| Backup file | ❌ **Perlu ditentukan** — ikut mysqldump atau terpisah? |
| Retensi file | ❌ **Perlu ditentukan** — berapa tahun? |
| Siapa yang bisa hapus file | Pengunggah (jika belum diverifikasi) + Admin Sistem |

---

## 7. KONFIGURASI DOCKER (OPSIONAL)

Jika Docker Compose digunakan, struktur layanan:

```yaml
# docker-compose.yml (template)
services:
  app:
    build: .
    volumes:
      - ./storage:/var/www/html/storage
    depends_on:
      - db
    environment:
      - APP_ENV=production

  db:
    image: mysql:8.0
    volumes:
      - db_data:/var/lib/mysql
    environment:
      MYSQL_DATABASE: sipanda
      MYSQL_USER: sipanda_user
      MYSQL_PASSWORD: [ISI_PASSWORD]
      MYSQL_ROOT_PASSWORD: [ISI_ROOT_PASSWORD]

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf

volumes:
  db_data:
```

---

## 8. CHECKLIST SEBELUM GO-LIVE

### Keamanan
- [ ] `.env` tidak ter-commit ke Git (ada di `.gitignore`)
- [ ] `APP_DEBUG=false` di production
- [ ] `APP_KEY` di-generate fresh (`php artisan key:generate`)
- [ ] HTTPS aktif (sertifikat SSL dari CA internal atau Let's Encrypt)
- [ ] Rate limiting aktif di route `/login` dan `/opd/login`
- [ ] File `storage/` tidak bisa diakses langsung via URL (hanya via controller)

### Fungsional
- [ ] Queue worker berjalan via Supervisor
- [ ] Laravel Scheduler aktif (cron terdaftar)
- [ ] SMTP terverifikasi: kirim email test berhasil
- [ ] Google OAuth terverifikasi: SSO login berhasil (jika aktif)
- [ ] Backup DB harian berjalan (`BackupDatabaseJob`)

### Data
- [ ] Seeder pegawai dari `data-pegawai.csv` sudah dijalankan
- [ ] Master data Irban, OPD/Kecamatan/Desa sudah diinput
- [ ] Akun Admin Sistem sudah dibuat (Pranata Komputer)
- [ ] Password awal semua pengguna sudah dikomunikasikan

---

## 9. KONTAK PENANGGUNG JAWAB

| Peran | Nama | Kontak |
|---|---|---|
| Admin Sistem / Developer | Nandito Monliev Passa, S.Kom | nanditomonlievpassa@gmail.com |
| Pranata Komputer (Admin Sistem prod.) | Fryza Rachmania M, A.Md.Kom | fryza.rachmania@gmail.com |
| Koordinator Diskominfo | ___ | ❌ Belum diisi |
