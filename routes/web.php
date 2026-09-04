<?php

use App\Http\Controllers\ArsipDigitalController;
use App\Http\Controllers\BebanKerjaController;
use App\Http\Controllers\BeritaAcaraTlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiTahunanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\KegiatanPengawasanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Opd\OpdAuthController;
use App\Http\Controllers\Opd\OpdDashboardController;
use App\Http\Controllers\OpdUserManagementController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\PerencanaanPkptController;
use App\Http\Controllers\PkpptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TindakLanjutController;
use App\Http\Controllers\VerifikasiBuktiController;
use Illuminate\Support\Facades\Route;

// Landing Page & Public Dashboard Transparansi (Tanpa Login)
Route::get('/', [\App\Http\Controllers\PublicDashboardController::class, 'index'])->name('welcome');
Route::get('/faq', [\App\Http\Controllers\KonsultasiController::class, 'faqIndex'])->name('faq.index');
Route::get('/regulasi', [\App\Http\Controllers\RegulasiHukumController::class, 'publicIndex'])->name('regulasi.public.index');
Route::get('/regulasi/{regulasi}/preview', [\App\Http\Controllers\RegulasiHukumController::class, 'publicPreview'])->name('regulasi.public.preview');
Route::get('/regulasi/{regulasi}/download', [\App\Http\Controllers\RegulasiHukumController::class, 'publicDownload'])->name('regulasi.public.download');

// Universal Document Streaming & Preview (Bypass Apache 403 Forbidden pada Storage)
Route::get('/dokumen/berkas', [\App\Http\Controllers\DokumenStreamController::class, 'stream'])->name('dokumen.stream');
Route::get('/dokumen/berkas/{path}', [\App\Http\Controllers\DokumenStreamController::class, 'streamPath'])->where('path', '.*')->name('dokumen.stream.path');

// API Chatbot Asisten Penasihat Virtual APIP (Tanpa Login & OPD)
Route::post('/api/ai/ask', [\App\Http\Controllers\Api\ApipAiChatController::class, 'ask'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('api.ai.ask');

// Webhook Inbound WAHA WhatsApp Gateway (Tanpa CSRF)
Route::post('/api/webhook/whatsapp', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Cookie\Middleware\EncryptCookies::class])
    ->name('webhook.whatsapp');

// Submit Feedback & Bug Report UAT (Dapat dikirim oleh Internal, OPD, maupun Tester)
Route::post('/feedback/submit', [\App\Http\Controllers\UatFeedbackController::class, 'submit'])->name('feedback.submit');

// ─── INTERNAL AREA (Guard: web) ──────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard Realtime
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // PKPPT Tahunan & Export
    Route::get('/pkppt', [PkpptController::class, 'index'])->name('pkppt.index');
    Route::get('/pkppt/export', [ExportController::class, 'exportPkppt'])->name('pkppt.export');
    Route::post('/pkppt', [PkpptController::class, 'store'])->middleware('can:pkppt.create')->name('pkppt.store');
    Route::put('/pkppt/{pkppt}', [PkpptController::class, 'update'])->middleware('can:pkppt.edit')->name('pkppt.update');
    Route::post('/pkppt/{pkppt}/revisi', [PkpptController::class, 'revisi'])->middleware('can:pkppt.edit')->name('pkppt.revisi');
    Route::delete('/pkppt/{pkppt}', [PkpptController::class, 'destroy'])->middleware('can:pkppt.delete')->name('pkppt.destroy');

    // Penugasan (SPT) & Export
    Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan.index');
    Route::get('/penugasan/export', [ExportController::class, 'exportPenugasan'])->name('penugasan.export');
    Route::get('/penugasan/create', [PenugasanController::class, 'create'])->middleware('can:penugasan.create')->name('penugasan.create');
    Route::post('/penugasan', [PenugasanController::class, 'store'])->middleware('can:penugasan.create')->name('penugasan.store');
    Route::get('/penugasan/{penugasan}', [PenugasanController::class, 'show'])->name('penugasan.show');
    Route::get('/penugasan/{penugasan}/cetak', [PenugasanController::class, 'cetak'])->name('penugasan.cetak');
    Route::get('/penugasan/{penugasan}/edit', [PenugasanController::class, 'edit'])->middleware('can:penugasan.edit')->name('penugasan.edit');
    Route::put('/penugasan/{penugasan}', [PenugasanController::class, 'update'])->middleware('can:penugasan.edit')->name('penugasan.update');
    Route::delete('/penugasan/{penugasan}', [PenugasanController::class, 'destroy'])->middleware('can:penugasan.delete')->name('penugasan.destroy');
    Route::patch('/penugasan/{penugasan}/status', [PenugasanController::class, 'updateStatus'])->middleware('can:penugasan.update_status')->name('penugasan.update_status');

    // Kegiatan Pengawasan (Monitoring Realisasi)
    Route::get('/kegiatan-pengawasan', [KegiatanPengawasanController::class, 'index'])->name('kegiatan-pengawasan.index');

    // Tindak Lanjut Result & Verifikasi Bukti OPD
    Route::get('/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
    Route::get('/tindak-lanjut/verifikasi-bukti', [VerifikasiBuktiController::class, 'index'])->name('tindak-lanjut.verifikasi-bukti');
    Route::get('/tindak-lanjut/export/all', [ExportController::class, 'exportAllLhpMatrix'])->name('tindak-lanjut.export_all');
    Route::get('/tindak-lanjut/export/kompilasi-daerah', [ExportController::class, 'exportKompilasiDaerahExcel'])->name('tindak-lanjut.export_kompilasi_daerah');
    Route::get('/tindak-lanjut/export/lhp/{tindakLanjut}', [ExportController::class, 'exportLhpMatrix'])->whereNumber('tindakLanjut')->name('tindak-lanjut.export_lhp');
    Route::get('/tindak-lanjut/{tindakLanjut}/berita-acara', [BeritaAcaraTlController::class, 'cetakLhp'])->whereNumber('tindakLanjut')->name('tindak-lanjut.berita_acara');
    Route::get('/tindak-lanjut/opd/{objek}/berita-acara', [BeritaAcaraTlController::class, 'cetakOpd'])->whereNumber('objek')->name('tindak-lanjut.berita_acara_opd');
    Route::post('/tindak-lanjut', [TindakLanjutController::class, 'store'])->name('tindak-lanjut.store');
    Route::get('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'show'])->whereNumber('tindakLanjut')->name('tindak-lanjut.show');
    Route::put('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])->whereNumber('tindakLanjut')->name('tindak-lanjut.update');
    Route::delete('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'destroy'])->whereNumber('tindakLanjut')->name('tindak-lanjut.destroy');
    Route::post('/tindak-lanjut/{tindakLanjut}/rincian-setor', [TindakLanjutController::class, 'storeRincianSetor'])->whereNumber('tindakLanjut')->name('tindak-lanjut.store_rincian_setor');
    Route::post('/tindak-lanjut/{tindakLanjut}/respon', [TindakLanjutController::class, 'storeRespon'])->whereNumber('tindakLanjut')->name('tindak-lanjut.store_respon');
    Route::patch('/tindak-lanjut/{tindakLanjut}/status', [TindakLanjutController::class, 'updateStatus'])->whereNumber('tindakLanjut')->name('tindak-lanjut.update_status');
    Route::post('/bukti-tindak-lanjut/{bukti}/verifikasi', [VerifikasiBuktiController::class, 'verifikasi'])->whereNumber('bukti')->name('tindak-lanjut.bukti.verifikasi');

    // Arsip Digital
    Route::get('/arsip', [ArsipDigitalController::class, 'index'])->name('arsip.index');
    Route::post('/arsip', [ArsipDigitalController::class, 'store'])->name('arsip.store');
    Route::get('/arsip/{arsip}/preview', [ArsipDigitalController::class, 'preview'])->name('arsip.preview');
    Route::get('/arsip/{arsip}/download', [ArsipDigitalController::class, 'download'])->name('arsip.download');
    Route::delete('/arsip/{arsip}', [ArsipDigitalController::class, 'destroy'])->name('arsip.destroy');

    // Beban Kerja Personil
    Route::get('/beban-kerja', [BebanKerjaController::class, 'index'])->name('beban-kerja.index');

    // Perencanaan PKPT (Siklus N-1)
    Route::get('/perencanaan', [PerencanaanPkptController::class, 'index'])->middleware('can:perencanaan.view')->name('perencanaan.index');
    Route::post('/perencanaan/hitung-risiko', [PerencanaanPkptController::class, 'hitungRisiko'])->name('perencanaan.hitung_risiko');
    Route::post('/perencanaan/generate-draft', [PerencanaanPkptController::class, 'generateDraft'])->name('perencanaan.generate_draft');
    Route::post('/perencanaan/kapasitas-sdm', [PerencanaanPkptController::class, 'storeKapasitasSdm'])->name('perencanaan.kapasitas_sdm');
    Route::post('/pkppt/{pkppt}/usulkan', [PerencanaanPkptController::class, 'usulkan'])->name('pkppt.usulkan');
    Route::post('/pkppt/{pkppt}/reviu', [PerencanaanPkptController::class, 'reviu'])->name('pkppt.reviu');
    Route::post('/pkppt/{pkppt}/tetapkan', [PerencanaanPkptController::class, 'tetapkan'])->name('pkppt.tetapkan');

    // Evaluasi Tahunan (Siklus N+1)
    Route::get('/evaluasi', [EvaluasiTahunanController::class, 'index'])->middleware('can:evaluasi.view')->name('evaluasi.index');
    Route::post('/evaluasi/generate', [EvaluasiTahunanController::class, 'generate'])->middleware('can:evaluasi.generate')->name('evaluasi.generate');

    // Master Data
    Route::get('/master/users', [MasterDataController::class, 'users'])->middleware('can:users.view')->name('master.users.index');
    Route::post('/master/users', [MasterDataController::class, 'storeUser'])->middleware('can:users.create')->name('master.users.store');
    Route::patch('/master/users/{user}', [MasterDataController::class, 'updateUserRole'])->middleware('can:users.edit')->name('master.users.update');
    Route::patch('/master/users/{user}/toggle-status', [MasterDataController::class, 'toggleUserStatus'])->middleware('can:users.edit')->name('master.users.toggle_status');
    Route::get('/master/opd-users', [OpdUserManagementController::class, 'index'])->middleware('can:opd_users.manage')->name('master.opd-users.index');
    Route::post('/master/opd-users', [OpdUserManagementController::class, 'store'])->middleware('can:opd_users.manage')->name('master.opd-users.store');
    Route::put('/master/opd-users/{user}', [OpdUserManagementController::class, 'update'])->middleware('can:opd_users.manage')->name('master.opd-users.update');
    Route::patch('/master/opd-users/{user}/toggle-status', [OpdUserManagementController::class, 'toggleStatus'])->middleware('can:opd_users.manage')->name('master.opd-users.toggle_status');
    Route::post('/master/opd-users/{user}/regenerate-token', [OpdUserManagementController::class, 'regenerateToken'])->middleware('can:opd_users.manage')->name('master.opd-users.regenerate_token');
    Route::delete('/master/opd-users/{user}', [OpdUserManagementController::class, 'destroy'])->middleware('can:opd_users.manage')->name('master.opd-users.destroy');
    Route::get('/master/objek-penugasan', [MasterDataController::class, 'objekPenugasan'])->middleware('can:master.view')->name('master.objek-penugasan.index');
    Route::post('/master/objek-penugasan', [MasterDataController::class, 'storeObjekPenugasan'])->middleware('can:master.create')->name('master.objek-penugasan.store');
    Route::put('/master/objek-penugasan/{objek}', [MasterDataController::class, 'updateObjekPenugasan'])->middleware('can:master.edit')->name('master.objek-penugasan.update');
    Route::delete('/master/objek-penugasan/{objek}', [MasterDataController::class, 'destroyObjekPenugasan'])->middleware('can:master.delete')->name('master.objek-penugasan.destroy');
    Route::get('/master/jenis-penugasan', [MasterDataController::class, 'jenisPenugasan'])->middleware('can:master.view')->name('master.jenis-penugasan.index');
    Route::post('/master/jenis-penugasan', [MasterDataController::class, 'storeJenisPenugasan'])->middleware('can:master.create')->name('master.jenis-penugasan.store');
    Route::put('/master/jenis-penugasan/{jenis}', [MasterDataController::class, 'updateJenisPenugasan'])->middleware('can:master.edit')->name('master.jenis-penugasan.update');
    Route::delete('/master/jenis-penugasan/{jenis}', [MasterDataController::class, 'destroyJenisPenugasan'])->middleware('can:master.delete')->name('master.jenis-penugasan.destroy');
    Route::get('/audit-log', [MasterDataController::class, 'auditLog'])->middleware('can:audit_log.view')->name('audit-log.index');

    // Master Bank Regulasi & Dasar Hukum APIP
    Route::get('/master/regulasi', [\App\Http\Controllers\RegulasiHukumController::class, 'index'])->name('master.regulasi.index');
    Route::post('/master/regulasi', [\App\Http\Controllers\RegulasiHukumController::class, 'store'])->name('master.regulasi.store');
    Route::put('/master/regulasi/{regulasi}', [\App\Http\Controllers\RegulasiHukumController::class, 'update'])->name('master.regulasi.update');
    Route::delete('/master/regulasi/{regulasi}', [\App\Http\Controllers\RegulasiHukumController::class, 'destroy'])->name('master.regulasi.destroy');
    Route::get('/master/regulasi/{regulasi}/preview', [\App\Http\Controllers\RegulasiHukumController::class, 'preview'])->name('master.regulasi.preview');
    Route::get('/master/regulasi/{regulasi}/download', [\App\Http\Controllers\RegulasiHukumController::class, 'download'])->name('master.regulasi.download');

    // Master Bank Artikel FAQ APIP
    Route::get('/master/faq', [\App\Http\Controllers\FaqArtikelController::class, 'index'])->name('master.faq.index');
    Route::post('/master/faq', [\App\Http\Controllers\FaqArtikelController::class, 'store'])->name('master.faq.store');
    Route::put('/master/faq/{faq}', [\App\Http\Controllers\FaqArtikelController::class, 'update'])->name('master.faq.update');
    Route::delete('/master/faq/{faq}', [\App\Http\Controllers\FaqArtikelController::class, 'destroy'])->name('master.faq.destroy');

    // Kotak Masukan, Saran & Bug Report UAT
    Route::get('/master/feedback', [\App\Http\Controllers\UatFeedbackController::class, 'index'])->name('master.feedback.index');
    Route::patch('/master/feedback/{feedback}/status', [\App\Http\Controllers\UatFeedbackController::class, 'updateStatus'])->name('master.feedback.update_status');
    Route::delete('/master/feedback/{feedback}', [\App\Http\Controllers\UatFeedbackController::class, 'destroy'])->name('master.feedback.destroy');

    // Import Data Historis dari Spreadsheet / CSV
    Route::get('/import', [ImportController::class, 'index'])->middleware('can:master.create')->name('import.index');
    Route::get('/import/template/{type}', [ImportController::class, 'template'])->name('import.template');
    Route::post('/import/preview', [ImportController::class, 'preview'])->middleware('can:master.create')->name('import.preview');
    Route::post('/import/execute', [ImportController::class, 'store'])->middleware('can:master.create')->name('import.store');

    // Pusat Backup Database & Pembersihan Data Percobaan (Khusus Admin & Sekretariat)
    Route::middleware(['role:admin|sekretariat'])->group(function () {
        Route::get('/master/backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
        Route::get('/master/backup/download-current', [\App\Http\Controllers\BackupController::class, 'downloadCurrent'])->middleware('throttle:10,1')->name('backup.download_current');
        Route::get('/master/backup/download/{filename}', [\App\Http\Controllers\BackupController::class, 'downloadFile'])->middleware('throttle:10,1')->name('backup.download_file');
        Route::delete('/master/backup/delete/{filename}', [\App\Http\Controllers\BackupController::class, 'deleteFile'])->name('backup.delete_file');
        Route::post('/master/backup/send-email', [\App\Http\Controllers\BackupController::class, 'sendEmailTest'])->middleware('throttle:3,1')->name('backup.send_email');
        Route::post('/master/backup/settings', [\App\Http\Controllers\BackupController::class, 'updateSettings'])->name('backup.update_settings');
        Route::post('/master/backup/purge-dummy', [\App\Http\Controllers\BackupController::class, 'purgeDummy'])->middleware(['role:admin', 'throttle:5,1'])->name('backup.purge_dummy');
    });

    // E-Consulting & QnA APIP (Internal)
    Route::get('/konsultasi', [\App\Http\Controllers\KonsultasiController::class, 'index'])->name('konsultasi.index');
    Route::get('/konsultasi/{konsultasi}', [\App\Http\Controllers\KonsultasiController::class, 'show'])->name('konsultasi.show');
    Route::post('/konsultasi/{konsultasi}/disposisi-inspektur', [\App\Http\Controllers\KonsultasiController::class, 'disposisiInspektur'])->name('konsultasi.disposisi_inspektur');
    Route::post('/konsultasi/{konsultasi}/disposisi', [\App\Http\Controllers\KonsultasiController::class, 'disposisi'])->name('konsultasi.disposisi');
    Route::post('/konsultasi/{konsultasi}/chat', [\App\Http\Controllers\KonsultasiController::class, 'sendChat'])->name('konsultasi.chat');
    Route::post('/konsultasi/{konsultasi}/terbitkan-ba', [\App\Http\Controllers\KonsultasiController::class, 'terbitkanBa'])->name('konsultasi.terbitkan_ba');
    Route::patch('/konsultasi/{konsultasi}/toggle-faq', [\App\Http\Controllers\KonsultasiController::class, 'toggleFaq'])->name('konsultasi.toggle_faq');
    Route::get('/konsultasi/{konsultasi}/cetak-ba', [\App\Http\Controllers\KonsultasiController::class, 'cetakBa'])->name('konsultasi.cetak_ba');

    // Notifikasi In-App
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/unread-json', [NotifikasiController::class, 'getUnreadList'])->name('notifikasi.unread_json');
    Route::get('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/tandai-semua-dibaca', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.mark_all_read');
    Route::delete('/notifikasi/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');

    // Google Calendar Integration
    Route::get('/google-calendar/connect', [GoogleCalendarController::class, 'connect'])->name('google.connect');
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
    Route::post('/google-calendar/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google.disconnect');
    Route::post('/penugasan/{penugasan}/sync-calendar', [GoogleCalendarController::class, 'syncPenugasan'])->name('penugasan.sync_calendar');
});

// ─── PORTAL OPD (Guard: opd, Area: /opd/*) ─────────────────────
Route::prefix('opd')->name('opd.')->group(function () {
    // Guest OPD
    Route::middleware('guest:opd')->group(function () {
        Route::get('/login', [OpdAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [OpdAuthController::class, 'login'])->name('login.store')->middleware('throttle:5,1');
        Route::get('/undangan/{token}', [OpdAuthController::class, 'showSetPassword'])->name('undangan');
        Route::post('/undangan/{token}', [OpdAuthController::class, 'storePassword'])->name('undangan.store');

        // Lupa Password OPD
        Route::get('/lupa-password', [OpdAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/lupa-password', [OpdAuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
        Route::get('/reset-password/{token}', [OpdAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [OpdAuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
    });

    // Authenticated OPD
    Route::middleware('auth:opd')->group(function () {
        Route::post('/logout', [OpdAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [OpdDashboardController::class, 'index'])->name('dashboard');
        Route::get('/lhp/{tindakLanjut}', [OpdDashboardController::class, 'showLhp'])->name('lhp.show');
        Route::get('/lhp/{tindakLanjut}/berita-acara', [BeritaAcaraTlController::class, 'cetakLhp'])->name('lhp.berita_acara');
        Route::get('/tindak-lanjut/{tindakLanjut}', [OpdDashboardController::class, 'show'])->name('tindak-lanjut.show');
        Route::post('/tindak-lanjut/{tindakLanjut}/bukti', [OpdDashboardController::class, 'storeBukti'])->name('tindak-lanjut.bukti.store');

        // E-Consulting & QnA APIP Sisi OPD
        Route::get('/konsultasi', [\App\Http\Controllers\Opd\OpdKonsultasiController::class, 'index'])->name('konsultasi.index');
        Route::get('/konsultasi/create', [\App\Http\Controllers\Opd\OpdKonsultasiController::class, 'create'])->name('konsultasi.create');
        Route::post('/konsultasi', [\App\Http\Controllers\Opd\OpdKonsultasiController::class, 'store'])->name('konsultasi.store');
        Route::get('/konsultasi/{konsultasi}', [\App\Http\Controllers\Opd\OpdKonsultasiController::class, 'show'])->name('konsultasi.show');
        Route::post('/konsultasi/{konsultasi}/chat', [\App\Http\Controllers\Opd\OpdKonsultasiController::class, 'sendChat'])->name('konsultasi.chat');
    });
});

require __DIR__.'/auth.php';
