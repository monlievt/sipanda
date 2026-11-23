# SINGAWASI Design System (Hybrid Institutional Ledger)

> Presisi ledger ala Stripe di atas kanvas kertas hangat ala Notion, dengan aksen Emerald Inspektorat Kabupaten Trenggalek dan palet semantik pengawasan.

**Dial:** `ENERGY 1 / RHYTHM 2 / MOTION 1`
**Theme:** Light-first (`#f6f5f4` canvas) dengan dukungan penuh Dark Mode (`.dark`, `#0e1111` canvas).

## Design Read & Prinsip Inti

1. **Kanvas Hangat, Kartu Tajam**: Latar utama menggunakan kertas hangat (`#f6f5f4`) agar nyaman dibaca auditor dan operator OPD selama sesi kerja panjang, sementara kartu dan tabel menggunakan putih bersih (`#ffffff`) dengan garis pemisah tipis `1px` (`#e4e2dd`) tanpa bayangan berat.
2. **Presisi Angka Ledger**: Semua tabel, metrik realisasi, persentase tindak lanjut, dan nominal rupiah APBD menggunakan `font-variant-numeric: tabular-nums` (`tnum`) agar sejajar vertikal.
3. **Satu Aksen Institusional**: Hijau Emerald Deep (`#047857`) menjadi satu-satunya warna aksi utama (*primary CTA*, fokus input, dan indikator aktif). Warna semantik lain hanya dipakai untuk status audit nyata (Sesuai/Selesai, Belum Sesuai/Verifikasi, Belum Tindak Lanjut/Peringatan, Ditolak/Terlambat).
4. **Hirarki Radius Disengaja**:
   - `4px`: tag status mikro dan badge tabel.
   - `6px`: tombol aksi dan input formulir.
   - `10px`: kartu data, panel, dan modal.
   - `9999px`: hanya indikator hitungan notifikasi dan pill status kompak.
5. **Elevasi Minimal & Gerak Tenang**: Tidak ada glow dekoratif, tidak ada gradasi latar belakang generik, tidak ada emoji di tombol/heading, dan tidak ada animasi *pulse* pada elemen statis. Transisi hover/fokus dibatasi 150ms dan menghormati `prefers-reduced-motion`.

## Tokens Warna (Terverifikasi WCAG AA >= 4.5:1)

| Peran | Light Mode | Dark Mode | Rasio Kontras |
|---|---|---|---|
| Page Canvas | `#f6f5f4` | `#0e1111` | Dasar kanvas |
| Sidebar Surface | `#efede9` | `#121617` | Pemisah navigasi |
| Card Surface | `#ffffff` | `#161a1b` | Permukaan konten |
| Subtle Surface / Table Head | `#f3f1ed` | `#1c2123` | Header tabel & baris hover |
| Hairline Border | `#e4e2dd` | `#272d2f` | Pembatas `1px` |
| Input Border | `#cdc9c2` | `#363e41` | Batas kontrol >= 3:1 |
| Primary Ink | `#11181c` | `#f2f4f3` | 16.47:1 (light) / 17.17:1 (dark) |
| Secondary Ink | `#4b5563` | `#b8c0be` | 7.56:1 (light) / 9.45:1 (dark) |
| Muted Ink | `#5c6066` | `#8f9996` | 5.81:1 (light) / 5.98:1 (dark) |
| Primary Accent (CTA) | `#047857` (hover `#065f46`) | `#047857` (teks link `#34d399`) | 5.48:1 putih di atas `#047857` |
| Semantic Positive (Selesai / SS) | `#065f46` on `#ecfdf5` | `#6ee7b7` on `#064e3b/40` | 7.29:1 (light) |
| Semantic Info (Verifikasi / BS) | `#1d4ed8` on `#eff6ff` | `#93c5fd` on `#1e3a8a/40` | 6.16:1 (light) |
| Semantic Warning (Berjalan / BTL) | `#92400e` on `#fffbeb` | `#fcd34d` on `#78350f/40` | 6.84:1 (light) |
| Semantic Negative (Error / Terlambat) | `#b91c1c` on `#fef2f2` | `#fca5a5` on `#7f1d1d/40` | 5.91:1 (light) |

## Tipografi

- **Font Utama**: `Plus Jakarta Sans` (weights `400`, `500`, `600`, `700`). Dipilih karena karakter geometris-humanisnya dirancang dengan proporsi aksara Latin modern yang sangat jelas untuk istilah panjang Bahasa Indonesia di tabel dan formulir pemerintahan.
- **Angka**: `tabular-nums` aktif pada seluruh tabel, angka metrik, tanggal, dan nominal anggaran.
- **Skala**:
  - Hero publik: `clamp(1.875rem, 3.6vw, 3rem)`, weight `700`, tracking `-0.025em`, line-height `1.12`.
  - Judul halaman internal: `1.25rem` (20px), weight `700`, tracking `-0.015em`.
  - Judul kartu / seksi: `0.9375rem` (15px), weight `600`.
  - Body / formulir: `0.875rem` (14px), line-height `1.5`.
  - Tabel / metadata: `0.75rem` sampai `0.8125rem` (12px-13px).

## Aksesibilitas & Interaksi

- Seluruh elemen interaktif aktif otomatis menggunakan `cursor: pointer`, kontrol nonaktif menggunakan `cursor: not-allowed`.
- Fokus keyboard (`:focus-visible`) menggunakan ring kontras tinggi `2px` di mode terang maupun gelap.
- Target sentuh mobile minimal `44px` untuk kontrol navigasi utama dan tombol aksi.
- Tidak ada penggunaan tanda hubung panjang (em dash) pada teks UI.
