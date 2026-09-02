<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class SipandaBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipanda:backup 
                            {--email : Kirimkan salinan backup ke email administrator}
                            {--keep=7 : Batas hari penyimpanan file backup di server sebelum dihapus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cadangkan database SIPANDA secara otomatis dan kirim salinan ke email / cloud';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Memulai pencadangan database SIPANDA...');

        $backup = $backupService->createBackup();

        if (! $backup['success']) {
            $this->error('Gagal mencadangkan database: ' . ($backup['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->info("✓ Database berhasil dicadangkan: {$backup['filename']} ({$backup['size']})");

        // Kirim email jika dipanggil dengan opsi --email ATAU setting auto-email aktif
        $autoEmailEnabled = AppSetting::get('backup_auto_email_enabled') === '1';
        $shouldSendEmail = $this->option('email') || $autoEmailEnabled;

        if ($shouldSendEmail) {
            $targetEmail = AppSetting::get('backup_email_destination') ?: 'nugrahenisetya72@gmail.com';
            $this->info("Mengirimkan berkas cadangan ke email {$targetEmail}...");

            $mailResult = $backupService->sendBackupEmail($targetEmail);

            if ($mailResult['success']) {
                $this->info("✓ Berkas cadangan berhasil dikirim ke {$targetEmail}.");
            } else {
                $this->warn("⚠ Gagal mengirimkan email: " . ($mailResult['error'] ?? 'Unknown error'));
            }
        }

        // Pembersihan file backup lama
        $keepDays = (int) $this->option('keep');
        $deleted = $backupService->cleanOldBackups($keepDays);
        if ($deleted > 0) {
            $this->info("✓ {$deleted} berkas cadangan lama (> {$keepDays} hari) telah dibersihkan dari server.");
        }

        $this->info('Proses pencadangan selesai.');
        return Command::SUCCESS;
    }
}
