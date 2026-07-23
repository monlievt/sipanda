# SPESIFIKASI FUNGSIONAL & USER STORIES
# SIPANDA Web

Format tiap item: **User Story** + **Kriteria Penerimaan (Acceptance Criteria)**.
Disusun per modul, urut sesuai roadmap di Blueprint.

---

## MODUL 1 — Autentikasi & Manajemen Pengguna

**US-1.1** Sebagai pengguna, saya ingin login dengan email & password ATAU tombol "Login
dengan Google", agar saya bisa masuk sesuai preferensi tanpa harus selalu ingat password.
- AC: Halaman login menampilkan form manual + tombol Google SSO.
- AC: Jika email Google belum terdaftar sebagai user, tampilkan pesan "akun belum terdaftar,
  hubungi Sekretariat" (bukan auto-register, demi kontrol akses).
- AC: Setelah login, pengguna diarahkan ke Home sesuai role.

**US-1.2** Sebagai Admin Sistem atau Sekretariat, saya ingin menambah/mengubah/menonaktifkan
pengguna dan menetapkan role serta Irban-nya, agar akses selalu sesuai struktur organisasi terkini.
- AC: Form user mencantumkan nama, NIP, email, jabatan, pangkat, golongan, role, Irban (wajib diisi untuk role `irban` dan `admin_irban`; kosong untuk Admin/Sekretariat/Inspektur).
- AC: Untuk role `admin_irban`, field Irban wajib dipilih — sistem menolak jika kosong.
- AC: User nonaktif tidak bisa login meski kredensial benar.
- AC: Data awal pengguna dapat di-import dari file `docs/data-pegawai.csv` via seeder (NIP, nama, email, jabatan, pangkat, golongan sudah tersedia).

---

## MODUL 2 — Master Data

**US-2.1** Sebagai Sekretariat, saya ingin mengelola daftar Objek Penugasan (OPD/Kecamatan/
Desa), agar pilihan di form Input Penugasan selalu up to date tanpa mengetik manual.
- AC: CRUD objek penugasan dengan kategori.
- AC: Objek yang sudah dipakai di penugasan tidak bisa dihapus, hanya dinonaktifkan.

**US-2.2** Sebagai Sekretariat, saya ingin mengelola master Jenis Penugasan dan Sumber
Penugasan, agar konsisten dengan kategori Assurance/Consulting yang berlaku.

---

## MODUL 3 — PKPPT (Rencana Pengawasan Tahunan)

**US-3.1** Sebagai Sekretariat, saya ingin menginput rencana PKPPT tahunan (area pengawasan,
jenis, sasaran, jadwal rencana, jumlah laporan, Irban pelaksana), agar ada baseline resmi
untuk dibandingkan dengan realisasi.
- AC: Satu baris PKPPT = satu area pengawasan per tahun.
- AC: Tahun PKPPT wajib diisi dan tidak bisa diubah setelah ada penugasan terkait.

---

## MODUL 4 — Input Penugasan (padanan "Input Data" di SIPANDA v1)

**US-4.1** Sebagai Auditor/P2UPD, Admin Irban, atau Irban, saya ingin menginput data penugasan
baru (No. SPT, uraian, objek, sumber, tanggal, susunan tim, jenis penugasan), agar tercatat
resmi di sistem sejak awal, bukan belakangan.
- AC: No. SPT wajib unik — sistem menolak & menampilkan pesan jika sudah dipakai.
- AC: Field Objek Penugasan, Wakil Penanggung Jawab, Pengendali Teknis, Ketua Tim, Anggota Tim
  mendukung multi-select dari master data (bukan free text).
- AC: Pengguna memilih apakah penugasan ini "Sesuai PKPPT" (lalu pilih baris PKPPT terkait)
  atau "Di luar PKPPT" (pkppt_id kosong).
- AC: Status awal default "Belum Berjalan", progres 0%.
- AC: Jika pengguna adalah `admin_irban` atau `irban`, field Irban otomatis terisi sesuai Irban
  miliknya dan tidak bisa diubah.
- AC: Setelah simpan, sistem otomatis: (a) mencatat activity_log, (b) menjadwalkan reminder
  H-3 dan H-1, (c) jika pengguna mengaktifkan sinkron kalender, membuat event Google Calendar
  untuk tiap anggota tim.

**US-4.2** Sebagai Auditor/P2UPD atau Admin Irban, saya ingin mengubah status pelaksanaan
(belum berjalan/berjalan/selesai), progres %, dan keterangan hasil, agar dashboard selalu
mencerminkan kondisi terkini.
- AC: Hanya anggota tim penugasan tsb, Admin Irban terkait, Irban terkait, atau Admin Sistem
  yang bisa mengubah.
- AC: Perubahan status ke "selesai" mewajibkan pengisian keterangan hasil.

---

## MODUL 5 — Data Penugasan (padanan "Hasil Input Non PKPPT")

**US-5.1** Sebagai pengguna (sesuai hak akses), saya ingin melihat tabel seluruh penugasan
(PKPPT & non-PKPPT) dengan filter (tahun, Irban, status, jenis, sesuai/tidak PKPPT) dan
pencarian No. SPT/uraian, agar mudah menelusuri data tanpa scroll spreadsheet panjang.
- AC: Tabel bisa diekspor ke Excel/PDF.
- AC: Irban hanya melihat data wilayahnya; Inspektur & Admin melihat semua.

---

## MODUL 6 — Kegiatan Pengawasan (Monitoring PKPPT vs Realisasi)

**US-6.1** Sebagai Irban/Inspektur, saya ingin melihat perbandingan otomatis antara rencana
PKPPT (jadwal, jumlah laporan rencana) dengan realisasi (jumlah & status penugasan terkait),
agar tidak perlu menghitung manual seperti di spreadsheet lama.
- AC: Untuk tiap baris PKPPT, tampilkan jumlah realisasi, jumlah selesai, dan status keseluruhan
  (sesuai jadwal / terlambat / belum mulai) dihitung otomatis dari tanggal & status penugasan.
- AC: Warna indikator (hijau/kuning/merah) mengikuti logika: selesai tepat waktu = hijau,
  berjalan tapi lewat rencana selesai = kuning, belum berjalan & lewat rencana mulai = merah.

---

## MODUL 7 — Dashboard Realtime

**US-7.1** Sebagai semua role, saya ingin melihat ringkasan status pelaksanaan PKPPT (persentase
selesai/berjalan/belum berjalan) dalam bentuk pie chart, per tanggal terkini, agar mendapat
gambaran cepat seperti "Dashboard Realtime" versi lama.
- AC: Data dihitung live dari tabel `penugasan`, bukan input manual berkala.
- AC: Filter berdasarkan tahun dan Irban.

**US-7.2** Sebagai pengguna, saya ingin melihat rekap jumlah realisasi penugasan per kategori
(Consulting: Advisory/Facilitative Role/Training Role; Assurance: Monitoring/Evaluasi/
Monitoring dan Evaluasi/Reviu) dengan breakdown Selesai/Dalam Proses/Total, sama seperti tabel
di Dashboard Realtime lama.

---

## MODUL 8 — Tindak Lanjut & Arsip Digital *(fitur baru)*

**US-8.1** Sebagai Irban/Auditor, saya ingin mencatat tindak lanjut atas suatu penugasan
(temuan, rekomendasi, status, target waktu), agar hasil pengawasan tidak berhenti di laporan
tapi termonitor sampai selesai — menjawab kelemahan "belum terintegrasi dengan data pemantauan
tindak lanjut" di SIPANDA v1.
- AC: Satu penugasan bisa punya banyak catatan tindak lanjut.
- AC: Dashboard menampilkan jumlah tindak lanjut per status (belum/proses/selesai).

**US-8.2** Sebagai pengguna, saya ingin mengunggah dan mengarsipkan file (surat tugas, laporan
hasil, bukti tindak lanjut) yang terhubung ke penugasan, agar dokumen tidak lagi tersebar di
folder pribadi masing-masing personil.
- AC: Tipe file dibatasi (pdf, docx, xlsx, jpg, png), ukuran maksimum ditentukan (mis. 10MB).
- AC: File bisa dikategorikan dan dicari berdasarkan No. SPT terkait.

---

## MODUL 9 — Beban Kerja Personil *(fitur baru)*

**US-9.1** Sebagai Irban/Inspektur, saya ingin melihat daftar & jumlah penugasan (surat tugas
pengawasan maupun non-pengawasan) yang ditangani seorang personil dalam rentang tanggal
tertentu, agar bisa menghindari penumpukan beban pada satu orang — menjawab kelemahan eksplisit
di dokumen sumber: "belum dapat menampilkan penugasan yang dilakukan atas satu personil dalam
jangka waktu tertentu".
- AC: Pilih personil + rentang tanggal → tampil tabel penugasan dengan peran (ketua tim/anggota/
  dst.) di masing-masing.
- AC: Ada ringkasan agregat: total penugasan aktif, total selesai, dalam rentang dipilih.
- AC: Bisa diakses per-Irban (lihat semua personil di wilayahnya) atau lintas-Irban (Inspektur).

---

## MODUL 10 — Notifikasi & Kalender

**US-10.1** Sebagai anggota tim penugasan, saya ingin menerima email pengingat H-3 dan H-1
sebelum tanggal mulai kegiatan, agar tidak lupa seperti fungsi reminder di Google Calendar versi
lama.
- AC: Scheduler harian mengecek penugasan dengan tanggal_mulai = today+3 atau today+1, kirim
  email ke seluruh anggota tim terkait.
- AC: Status pengiriman tercatat di tabel `notifikasi`.

**US-10.2** Sebagai pengguna yang menghubungkan akun Google-nya, saya ingin penugasan saya
otomatis muncul di Google Calendar pribadi, agar konsisten dengan kebiasaan lama memakai
kalender digital.
- AC: Opsional (toggle di profil pengguna), tidak wajib bagi yang hanya login manual.

---

## MODUL 11 — Audit Log

**US-11.1** Sebagai Admin, saya ingin melihat riwayat siapa mengubah data apa dan kapan, agar
ada akuntabilitas yang sebelumnya tidak mungkin dilakukan di Google Spreadsheet bersama.
- AC: Log mencatat minimal: user, tabel, aksi (create/update/delete), waktu, ringkasan
  perubahan (data sebelum/sesudah dalam JSON).
- AC: Log hanya bisa dibaca oleh Admin/Inspektur, tidak bisa dihapus dari UI.

---

## MODUL 12 — Perencanaan PKPT Berbasis Risiko Sederhana *(fitur baru, siklus N-1)*

**US-12.1** Sebagai Sekretariat, saya ingin sistem menghitung skor risiko tiap objek pengawasan
secara otomatis (aging, anggaran, temuan, tindak lanjut mandek, pengaduan khusus), agar
penyusunan PKPT tidak dimulai dari kertas kosong tiap tahun.
- AC: Skor dihitung dari data yang sudah ada di sistem (`penugasan`, `tindak_lanjut`) ditambah
  input manual anggaran per objek.
- AC: Skor bisa disesuaikan manual oleh Irban dengan catatan justifikasi wajib diisi.

**US-12.2** Sebagai Sekretariat, saya ingin menetapkan kapasitas SDM (hari kerja tersedia) per
Irban untuk tahun rencana, agar draf PKPT yang dihasilkan realistis terhadap sumber daya yang
benar-benar ada.

**US-12.3** Sebagai Sekretariat, saya ingin sistem menghasilkan draf PKPT otomatis (objek
skor tertinggi dialokasikan sampai kapasitas SDM Irban habis), agar tinggal direviu, bukan
disusun manual dari awal.
- AC: Objek yang tidak tertampung masuk daftar cadangan untuk tahun berikutnya.

**US-12.4** Sebagai Irban, saya ingin mengusulkan penyesuaian atas draf PKPT wilayah saya, dan
sebagai Inspektur saya ingin menetapkan PKPT final, agar ada alur persetujuan resmi tercatat
di sistem.
- AC: Status PKPT mengikuti alur draft → diusulkan → direviu → ditetapkan.
- AC: PKPT yang sudah "ditetapkan" terkunci, revisi berikutnya menambah `versi_revisi`.

---

## MODUL 13 — Evaluasi Tahunan *(fitur baru, siklus N+1)*

**US-13.1** Sebagai Inspektur/Irban, saya ingin melihat rekap capaian PKPT tahun berjalan
(% objek terealisasi, % laporan tepat waktu) dan capaian tindak lanjut (% selesai), agar
evaluasi tahunan tidak lagi dihitung manual dari banyak sumber.

**US-13.2** Sebagai sistem, skor risiko pada siklus Perencanaan PKPT tahun berikutnya harus
otomatis memperhitungkan hasil evaluasi tahun ini — objek yang lama tidak diperiksa atau
tindak lanjutnya mandek naik prioritasnya, agar "lingkaran" perencanaan-pelaksanaan-evaluasi
benar-benar tertutup, bukan tiga proses terpisah.

---

## MODUL 14 — Portal OPD *(fitur baru, interaksi tindak lanjut dua arah)*

**US-14.1** Sebagai Admin/Sekretariat, saya ingin membuat akun PIC untuk tiap objek pemeriksaan
dan mengirim undangan set-password, agar OPD punya akses resmi tanpa password dikirim terbuka.
- AC: Akun baru berstatus "pending" sampai PIC mengatur password lewat token undangan
  (berlaku 3x24 jam).

**US-14.2** Sebagai PIC OPD, saya ingin login di area terpisah dan hanya melihat rekomendasi
tindak lanjut yang ditujukan ke instansi saya, agar tidak ada kebocoran data ke OPD lain.
- AC: Setiap query di area `/opd/*` difilter oleh `objek_penugasan_id` milik akun yang login.

**US-14.3** Sebagai PIC OPD, saya ingin mengunggah bukti tindak lanjut beserta catatan
penjelasan untuk suatu rekomendasi, agar prosesnya tercatat di sistem, bukan lagi lewat
surat/email manual.
- AC: Status `tindak_lanjut` berubah menjadi "menunggu_verifikasi" setelah bukti diunggah.
- AC: Auditor/Irban penanggung jawab menerima notifikasi email.

**US-14.4** Sebagai Auditor/Irban, saya ingin memverifikasi bukti yang diajukan OPD — menerima
(status jadi "selesai") atau menolak dengan catatan (status kembali "proses", OPD diberi tahu
untuk melengkapi ulang) — agar validitas tindak lanjut tetap terjaga meski prosesnya sudah
melibatkan pihak eksternal.
- AC: Seluruh riwayat pengajuan & verifikasi tersimpan di `bukti_tindak_lanjut`, digunakan
  sebagai dasar perhitungan % tindak lanjut selesai di Modul 13.

---

## RINGKASAN PRIORITAS (MoSCoW)

| Prioritas | Modul |
|---|---|
| Must have | Autentikasi, Master Data, PKPPT, Input Penugasan, Data Penugasan, Kegiatan Pengawasan, Dashboard Realtime |
| Should have | Tindak Lanjut & Arsip Digital, Beban Kerja Personil, Notifikasi Email, Perencanaan PKPT Berbasis Risiko, Portal OPD |
| Could have | Integrasi Google Calendar, Import data historis, Ekspor PDF terformat, Evaluasi Tahunan otomatis |
| Won't have (fase ini) | Approval berjenjang digital untuk SPT, integrasi SIMPEG, analitik prediktif |
