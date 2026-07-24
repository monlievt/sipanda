<?php

use App\Http\Controllers\ArsipDigitalController;
use App\Http\Controllers\BebanKerjaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiTahunanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KegiatanPengawasanController;
use App\Http\Controllers\MasterDataController;
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
    Route::delete('/pkppt/{pkppt}', [PkpptController::class, 'destroy'])->middleware('can:pkppt.delete')->name('pkppt.destroy');

    // Penugasan (SPT) & Export
    Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan.index');
    Route::get('/penugasan/export', [ExportController::class, 'exportPenugasan'])->name('penugasan.export');
    Route::get('/penugasan/create', [PenugasanController::class, 'create'])->middleware('can:penugasan.create')->name('penugasan.create');
    Route::post('/penugasan', [PenugasanController::class, 'store'])->middleware('can:penugasan.create')->name('penugasan.store');
    Route::get('/penugasan/{penugasan}', [PenugasanController::class, 'show'])->name('penugasan.show');
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
    Route::get('/tindak-lanjut/export/lhp/{tindakLanjut}', [ExportController::class, 'exportLhpMatrix'])->name('tindak-lanjut.export_lhp');
    Route::post('/tindak-lanjut', [TindakLanjutController::class, 'store'])->name('tindak-lanjut.store');
    Route::get('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'show'])->name('tindak-lanjut.show');
    Route::put('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])->name('tindak-lanjut.update');
    Route::delete('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'destroy'])->name('tindak-lanjut.destroy');
    Route::post('/tindak-lanjut/{tindakLanjut}/rincian-setor', [TindakLanjutController::class, 'storeRincianSetor'])->name('tindak-lanjut.store_rincian_setor');
    Route::post('/tindak-lanjut/{tindakLanjut}/respon', [TindakLanjutController::class, 'storeRespon'])->name('tindak-lanjut.store_respon');
    Route::patch('/tindak-lanjut/{tindakLanjut}/status', [TindakLanjutController::class, 'updateStatus'])->name('tindak-lanjut.update_status');
    Route::post('/bukti-tindak-lanjut/{bukti}/verifikasi', [VerifikasiBuktiController::class, 'verifikasi'])->name('tindak-lanjut.bukti.verifikasi');

    // Arsip Digital
    Route::get('/arsip', [ArsipDigitalController::class, 'index'])->name('arsip.index');
    Route::post('/arsip', [ArsipDigitalController::class, 'store'])->name('arsip.store');
    Route::get('/arsip/{arsip}/download', [ArsipDigitalController::class, 'download'])->name('arsip.download');
    Route::delete('/arsip/{arsip}', [ArsipDigitalController::class, 'destroy'])->name('arsip.destroy');

    // Beban Kerja Personil
    Route::get('/beban-kerja', [BebanKerjaController::class, 'index'])->name('beban-kerja.index');

    // Perencanaan PKPT (Siklus N-1)
    Route::get('/perencanaan', [PerencanaanPkptController::class, 'index'])->name('perencanaan.index');
    Route::post('/perencanaan/hitung-risiko', [PerencanaanPkptController::class, 'hitungRisiko'])->name('perencanaan.hitung_risiko');
    Route::post('/perencanaan/generate-draft', [PerencanaanPkptController::class, 'generateDraft'])->name('perencanaan.generate_draft');
    Route::post('/perencanaan/kapasitas-sdm', [PerencanaanPkptController::class, 'storeKapasitasSdm'])->name('perencanaan.kapasitas_sdm');
    Route::post('/pkppt/{pkppt}/usulkan', [PerencanaanPkptController::class, 'usulkan'])->name('pkppt.usulkan');
    Route::post('/pkppt/{pkppt}/tetapkan', [PerencanaanPkptController::class, 'tetapkan'])->name('pkppt.tetapkan');

    // Evaluasi Tahunan (Siklus N+1)
    Route::get('/evaluasi', [EvaluasiTahunanController::class, 'index'])->name('evaluasi.index');
    Route::post('/evaluasi/generate', [EvaluasiTahunanController::class, 'generate'])->name('evaluasi.generate');

    // Master Data
    Route::get('/master/users', [MasterDataController::class, 'users'])->name('master.users.index');
    Route::patch('/master/users/{user}', [MasterDataController::class, 'updateUserRole'])->name('master.users.update');
    Route::get('/master/opd-users', [OpdUserManagementController::class, 'index'])->name('master.opd-users.index');
    Route::post('/master/opd-users', [OpdUserManagementController::class, 'store'])->name('master.opd-users.store');
    Route::get('/master/objek-penugasan', [MasterDataController::class, 'objekPenugasan'])->name('master.objek-penugasan.index');
    Route::post('/master/objek-penugasan', [MasterDataController::class, 'storeObjekPenugasan'])->name('master.objek-penugasan.store');
    Route::get('/master/jenis-penugasan', [MasterDataController::class, 'jenisPenugasan'])->name('master.jenis-penugasan.index');
    Route::post('/master/jenis-penugasan', [MasterDataController::class, 'storeJenisPenugasan'])->name('master.jenis-penugasan.store');
    Route::get('/audit-log', [MasterDataController::class, 'auditLog'])->name('audit-log.index');

    // E-Consulting & QnA APIP (Internal)
    Route::get('/konsultasi', [\App\Http\Controllers\KonsultasiController::class, 'index'])->name('konsultasi.index');
    Route::get('/konsultasi/{konsultasi}', [\App\Http\Controllers\KonsultasiController::class, 'show'])->name('konsultasi.show');
    Route::post('/konsultasi/{konsultasi}/disposisi', [\App\Http\Controllers\KonsultasiController::class, 'disposisi'])->name('konsultasi.disposisi');
    Route::post('/konsultasi/{konsultasi}/chat', [\App\Http\Controllers\KonsultasiController::class, 'sendChat'])->name('konsultasi.chat');
    Route::post('/konsultasi/{konsultasi}/terbitkan-ba', [\App\Http\Controllers\KonsultasiController::class, 'terbitkanBa'])->name('konsultasi.terbitkan_ba');
    Route::patch('/konsultasi/{konsultasi}/toggle-faq', [\App\Http\Controllers\KonsultasiController::class, 'toggleFaq'])->name('konsultasi.toggle_faq');
    Route::get('/konsultasi/{konsultasi}/cetak-ba', [\App\Http\Controllers\KonsultasiController::class, 'cetakBa'])->name('konsultasi.cetak_ba');
});

// ─── PORTAL OPD (Guard: opd, Area: /opd/*) ─────────────────────
Route::prefix('opd')->name('opd.')->group(function () {
    // Guest OPD
    Route::middleware('guest:opd')->group(function () {
        Route::get('/login', [OpdAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [OpdAuthController::class, 'login'])->name('login.store');
        Route::get('/undangan/{token}', [OpdAuthController::class, 'showSetPassword'])->name('undangan');
        Route::post('/undangan/{token}', [OpdAuthController::class, 'storePassword'])->name('undangan.store');
    });

    // Authenticated OPD
    Route::middleware('auth:opd')->group(function () {
        Route::post('/logout', [OpdAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [OpdDashboardController::class, 'index'])->name('dashboard');
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
