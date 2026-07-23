# SPESIFIKASI ROUTE / API
# SIPANDA Web (Laravel — web routes berbasis Blade/Livewire, bukan REST publik)

Versi: 2.0 (Konsolidasi) — mencakup route modul inti, Perencanaan & Evaluasi PKPT, dan Portal OPD.

Karena aplikasi ini internal (server on-premise, satu frontend Blade/Livewire), route di bawah
ditulis sebagai **web routes** Laravel. Middleware `auth` + `role:xxx` (spatie/laravel-permission)
diterapkan di seluruh grup kecuali auth.

---

## 1. AUTENTIKASI

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/login` | Halaman login (manual + tombol Google) | guest |
| POST | `/login` | Proses login manual | guest |
| GET | `/auth/google/redirect` | Redirect ke Google OAuth | guest |
| GET | `/auth/google/callback` | Callback Google, cari user by email/google_id | guest |
| POST | `/logout` | Logout | auth |

## 2. HOME / DASHBOARD

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/` | Landing, menu sesuai role | auth |
| GET | `/dashboard` | Dashboard Realtime (filter `?tahun=&irban_id=`) | auth |
| GET | `/dashboard/data` | (Livewire/AJAX) data agregat untuk chart | auth |

## 3. MASTER DATA (Admin/Sekretariat)

| Method | Route | Deskripsi |
|---|---|---|
| GET/POST | `/master/irban` | List & create Irban |
| PUT/DELETE | `/master/irban/{id}` | Update/nonaktifkan |
| GET/POST | `/master/objek-penugasan` | List & create objek penugasan |
| PUT/DELETE | `/master/objek-penugasan/{id}` | Update/nonaktifkan |
| GET/POST | `/master/jenis-penugasan` | List & create jenis penugasan |
| GET/POST | `/master/sumber-penugasan` | List & create sumber penugasan |
| GET/POST | `/master/users` | List & create pengguna |
| PUT | `/master/users/{id}` | Update role/irban/status aktif |

## 4. PKPPT

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/pkppt` | List rencana PKPPT (filter tahun) | auth |
| GET | `/pkppt/create` | Form tambah | admin, sekretariat |
| POST | `/pkppt` | Simpan | admin, sekretariat |
| GET | `/pkppt/{id}/edit` | Form ubah | admin, sekretariat |
| PUT | `/pkppt/{id}` | Update | admin, sekretariat |
| DELETE | `/pkppt/{id}` | Hapus (hanya jika belum ada penugasan terkait) | admin |

## 5. PENUGASAN (Input Data & Data Penugasan)

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/penugasan` | Tabel semua penugasan + filter/search | auth (scoped by irban) |
| GET | `/penugasan/create` | Form input baru | irban, auditor |
| POST | `/penugasan` | Simpan penugasan baru (+ tim + objek) | irban, auditor |
| GET | `/penugasan/{id}` | Detail penugasan | auth (scoped) |
| GET | `/penugasan/{id}/edit` | Form ubah | pemilik/irban/admin |
| PUT | `/penugasan/{id}` | Update data | pemilik/irban/admin |
| PATCH | `/penugasan/{id}/status` | Update cepat status/progres | anggota tim/irban/admin |
| DELETE | `/penugasan/{id}` | Hapus (soft delete) | admin |
| GET | `/penugasan/export` | Ekspor Excel/PDF sesuai filter aktif | auth |

## 6. KEGIATAN PENGAWASAN (Monitoring PKPPT vs Realisasi)

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/kegiatan-pengawasan` | Tabel perbandingan rencana vs realisasi (filter tahun/irban) | auth |
| GET | `/kegiatan-pengawasan/{pkppt_id}` | Detail realisasi per baris PKPPT | auth |

## 6B. PERENCANAAN PKPT (siklus N-1)

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/perencanaan/{tahun}/universe` | Lihat universe pengawasan untuk tahun rencana | sekretariat, admin |
| GET/POST | `/perencanaan/{tahun}/kapasitas-sdm` | Input kapasitas SDM per Irban | sekretariat, admin |
| POST | `/perencanaan/{tahun}/hitung-risiko` | Jalankan penghitungan skor risiko seluruh objek | sekretariat, admin |
| GET | `/perencanaan/{tahun}/penilaian-risiko` | Lihat/ubah skor per objek (dengan catatan manual) | irban, sekretariat |
| POST | `/perencanaan/{tahun}/generate-draft-pkppt` | Buat draf PKPT otomatis dari skor + kapasitas | sekretariat, admin |
| POST | `/pkppt/{id}/usulkan` | Irban mengusulkan penyesuaian draf | irban |
| POST | `/pkppt/{id}/tetapkan` | Inspektur menetapkan PKPT final | inspektur |

## 6C. EVALUASI TAHUNAN (siklus N+1)

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/evaluasi/{tahun}` | Dashboard evaluasi tahunan (capaian, tindak lanjut) | auth |
| POST | `/evaluasi/{tahun}/generate` | Hitung & simpan ringkasan evaluasi tahun tsb | admin, inspektur |

## 7. TINDAK LANJUT & ARSIP DIGITAL

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/penugasan/{id}/tindak-lanjut` | List tindak lanjut penugasan tsb | auth (scoped) |
| POST | `/penugasan/{id}/tindak-lanjut` | Tambah catatan tindak lanjut | irban, auditor |
| PUT | `/tindak-lanjut/{id}` | Update status/tanggal selesai | irban, auditor, admin |
| GET | `/penugasan/{id}/arsip` | List file arsip | auth (scoped) |
| POST | `/penugasan/{id}/arsip` | Unggah file | irban, auditor |
| DELETE | `/arsip/{id}` | Hapus file | pengunggah/admin |
| GET | `/tindak-lanjut/{id}/bukti` | Lihat bukti yang diajukan OPD untuk rekomendasi ini | irban, auditor, admin |
| POST | `/bukti-tindak-lanjut/{id}/verifikasi` | Terima/tolak bukti OPD (dengan catatan) | irban, auditor, admin |

## 7B. PORTAL OPD (area eksternal, prefix `/opd`, guard terpisah)

| Method | Route | Deskripsi |
|---|---|---|
| GET | `/opd/login` | Halaman login khusus OPD (manual + opsi Google) |
| GET | `/opd/undangan/{token}` | Halaman set password pertama kali |
| POST | `/opd/undangan/{token}` | Simpan password, aktifkan akun |
| GET | `/opd/dashboard` | Daftar rekomendasi tindak lanjut untuk objeknya + status |
| GET | `/opd/tindak-lanjut/{id}` | Detail rekomendasi + riwayat pengajuan bukti sebelumnya |
| POST | `/opd/tindak-lanjut/{id}/bukti` | Unggah bukti + catatan (bisa multi-file) |
| GET | `/opd/riwayat` | Riwayat seluruh rekomendasi (selesai maupun masih berjalan) |
| GET/POST | `/master/opd-users` | (internal) Kelola akun PIC OPD — buat, kirim undangan, nonaktifkan — role: admin, sekretariat |

## 8. BEBAN KERJA PERSONIL

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/beban-kerja` | Form pilih personil/rentang tanggal + hasil | irban, inspektur, admin |
| GET | `/beban-kerja/{user_id}` | Detail beban kerja satu personil (filter tanggal) | irban, inspektur, admin |

## 9. NOTIFIKASI & KALENDER

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/notifikasi` | Daftar notifikasi pengguna login | auth |
| POST | `/profil/google-calendar/connect` | Hubungkan akun Google untuk sinkron kalender | auth |
| POST | `/profil/google-calendar/disconnect` | Putuskan sinkron | auth |

## 10. AUDIT LOG

| Method | Route | Deskripsi | Role |
|---|---|---|---|
| GET | `/audit-log` | Tabel log aktivitas (filter user/tabel/tanggal) | admin, inspektur |

---

## 11. SCHEDULED JOBS (Laravel Scheduler + Queue)

| Job | Jadwal | Deskripsi |
|---|---|---|
| `KirimReminderH3H1Job` | Harian, 06:00 | Cek penugasan mulai H+3/H+1, kirim email ke tim |
| `SinkronGoogleCalendarJob` | Setiap perubahan tanggal penugasan (event-driven) | Push/update event ke Google Calendar pengguna terkait |
| `BackupDatabaseJob` | Harian, 23:00 | `mysqldump` terjadwal ke storage backup |
| `HitungSkorRisikoJob` | Dipicu manual oleh Sekretariat (musim perencanaan) | Hitung ulang seluruh `penilaian_risiko` untuk tahun rencana tertentu |
| `GenerateEvaluasiTahunanJob` | Dipicu manual (awal tahun N+1) | Hitung & simpan ringkasan `evaluasi_tahunan`, umpan balik ke skor risiko |
| `ReminderTindakLanjutOPDJob` | Harian, 07:00 | Cek rekomendasi tindak lanjut belum direspons OPD > 14 hari, kirim reminder + tembusan Irban |

---

## 12. VALIDASI KUNCI (contoh Form Request Laravel)

```php
// StorePenugasanRequest
[
    'no_spt' => 'required|string|max:50|unique:penugasan,no_spt',
    'uraian_penugasan' => 'required|string',
    'objek_penugasan_ids' => 'required|array|min:1',
    'objek_penugasan_ids.*' => 'exists:objek_penugasan,id',
    'sumber_penugasan_id' => 'required|exists:sumber_penugasan,id',
    'jenis_penugasan_id' => 'required|exists:jenis_penugasan,id',
    'tanggal_mulai' => 'required|date',
    'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
    'pkppt_id' => 'nullable|exists:pkppt,id',
    'tim.wakil_penanggung_jawab' => 'required|array|min:1',
    'tim.pengendali_teknis' => 'required|array|min:1',
    'tim.ketua_tim' => 'required|array|min:1',
    'tim.anggota_tim' => 'required|array|min:1',
    'tim.*.*' => 'exists:users,id',
]
```
