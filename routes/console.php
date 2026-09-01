<?php

use App\Models\Penugasan;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ─── Contoh bawaan Laravel (tidak dihapus) ──────────────────────────
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── SIPANDA: Jadwal Otomatis ────────────────────────────────────────

/**
 * Reminder H-3 & H-1 penugasan + peringatan TL mandek >14 hari.
 * Dijalankan setiap hari pukul 07.00 WIB.
 * Pastikan cron server aktif:
 *   * * * * * cd /path/to/sipanda && php artisan schedule:run >> /dev/null 2>&1
 */
Schedule::command('sipanda:send-reminders')
    ->dailyAt('07:00')
    ->name('sipanda-send-reminders')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('[SIPANDA Scheduler] Command sipanda:send-reminders GAGAL dijalankan.');
    });

/**
 * Auto-sync status penugasan: 'belum_berjalan' → 'berjalan'
 * saat tanggal mulai sudah tiba. Dijalankan setiap hari pukul 00.05 WIB.
 * (Dipindah dari PenugasanController::index() agar tidak dijalankan
 *  setiap kali halaman daftar penugasan dibuka.)
 */
Schedule::call(function () {
    $jumlah = Penugasan::where('status', 'belum_berjalan')
        ->whereDate('tanggal_mulai', '<=', now()->toDateString())
        ->update(['status' => 'berjalan']);

    if ($jumlah > 0) {
        \Illuminate\Support\Facades\Log::info("[SIPANDA Scheduler] Auto-sync status: {$jumlah} penugasan diubah menjadi 'berjalan'.");
    }
})
->dailyAt('00:05')
->name('sipanda-autosync-status-penugasan')
->withoutOverlapping();

/**
 * Backup database harian otomatis setiap pukul 01:00 WIB dengan retensi 30 hari.
 */
Schedule::command('sipanda:backup-db')
    ->dailyAt('01:00')
    ->name('sipanda-backup-db')
    ->withoutOverlapping();
