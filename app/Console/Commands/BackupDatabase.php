<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipanda:backup-db {--keep=30 : Jumlah hari retensi file backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatisasi Backup Database SIPANDA Web (MySQL & SQLite) dengan Pembersihan Retensi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai proses backup database SIPANDA...');

        $backupDir = storage_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');
        $connection = config('database.default');

        try {
            if ($connection === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (! File::exists($dbPath)) {
                    $this->error("File database SQLite tidak ditemukan pada {$dbPath}");
                    return self::FAILURE;
                }

                $targetFile = "{$backupDir}/sipanda_sqlite_backup_{$timestamp}.sqlite";
                File::copy($dbPath, $targetFile);

                $this->info("✓ Backup SQLite berhasil: {$targetFile}");
            } elseif ($connection === 'mysql') {
                $host = config('database.connections.mysql.host');
                $port = config('database.connections.mysql.port', 3306);
                $database = config('database.connections.mysql.database');
                $username = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');

                $targetSql = "{$backupDir}/sipanda_mysql_{$timestamp}.sql";
                $targetGz  = "{$targetSql}.gz";

                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s %s | gzip > %s',
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    escapeshellarg($targetGz)
                );

                exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception("mysqldump gagal dieksekusi (code: {$returnVar}).");
                }

                $this->info("✓ Backup MySQL terkompresi berhasil: {$targetGz}");
            }

            // Pembersihan File Backup Lama (Retensi)
            $keepDays = (int) $this->option('keep');
            $files = File::files($backupDir);
            $deletedCount = 0;

            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp($file->getMTime());
                if ($lastModified->lt(now()->subDays($keepDays))) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                $this->info("✓ Membersihkan {$deletedCount} file backup lama (> {$keepDays} hari).");
            }

            ActivityLog::catat('system', 0, 'backup_database', null, [
                'connection' => $connection,
                'status'     => 'success',
                'timestamp'  => $timestamp,
            ]);

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("Gagal melakukan backup: " . $e->getMessage());
            Log::error("[SIPANDA Backup] Gagal: " . $e->getMessage());

            ActivityLog::catat('system', 0, 'backup_database', null, [
                'connection' => $connection,
                'status'     => 'failed',
                'error'      => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
