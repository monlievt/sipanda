<?php

namespace App\Services;

use App\Mail\DatabaseBackupMail;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DatabaseBackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (! File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Buat berkas cadangan database (.sql).
     */
    public function createBackup(): array
    {
        $timestamp = date('Y-m-d_His');
        $filename = "sipanda_backup_{$timestamp}.sql";
        $filepath = "{$this->backupDir}/{$filename}";

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        try {
            if ($driver === 'mysql') {
                $sqlContent = $this->dumpMysql();
            } else {
                $sqlContent = $this->dumpSqlite();
            }

            File::put($filepath, $sqlContent);

            $bytes = filesize($filepath);
            $sizeFormatted = $this->formatBytes($bytes);

            AppSetting::set('backup_last_run', now()->toDateTimeString());

            return [
                'success'   => true,
                'filename'  => $filename,
                'filepath'  => $filepath,
                'size'      => $sizeFormatted,
                'bytes'     => $bytes,
                'timestamp' => now()->format('d/m/Y H:i:s'),
            ];
        } catch (\Throwable $e) {
            Log::error('[SIPANDA Backup Error] ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim berkas cadangan ke email admin.
     */
    public function sendBackupEmail(?string $targetEmail = null): array
    {
        $backup = $this->createBackup();
        if (! $backup['success']) {
            return $backup;
        }

        $email = $targetEmail
            ?: AppSetting::get('backup_email_destination')
            ?: (auth()->user()?->email ?? 'nugrahenisetya72@gmail.com');

        try {
            Mail::to($email)->send(new DatabaseBackupMail(
                $backup['filepath'],
                $backup['filename'],
                $backup['size'],
                now()->format('d/m/Y H:i')
            ));

            return [
                'success'  => true,
                'message'  => "Salinan cadangan database ({$backup['filename']} - {$backup['size']}) berhasil dikirimkan ke {$email}.",
                'backup'   => $backup,
                'email'    => $email,
            ];
        } catch (\Throwable $e) {
            Log::error('[SIPANDA Backup Mail Error] ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => "Gagal mengirimkan email ke {$email}: " . $e->getMessage(),
                'backup'  => $backup,
            ];
        }
    }

    /**
     * Dapatkan daftar seluruh file backup di server.
     */
    public function listBackups(): array
    {
        if (! File::isDirectory($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql' || $file->getExtension() === 'gz' || $file->getExtension() === 'zip') {
                $backups[] = [
                    'filename'   => $file->getFilename(),
                    'size'       => $this->formatBytes($file->getSize()),
                    'bytes'      => $file->getSize(),
                    'created_at' => Carbon::createFromTimestamp($file->getMTime())->format('d/m/Y H:i:s'),
                    'age'        => Carbon::createFromTimestamp($file->getMTime())->diffForHumans(),
                    'timestamp'  => $file->getMTime(),
                ];
            }
        }

        // Urutkan dari yang paling baru
        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Hapus berkas backup tertentu.
     */
    public function deleteBackup(string $filename): bool
    {
        // Sanitasi nama file untuk mencegah path traversal
        $clean = basename($filename);
        $path = "{$this->backupDir}/{$clean}";

        if (File::exists($path)) {
            return File::delete($path);
        }

        return false;
    }

    /**
     * Hapus berkas backup lama yang melebihi jumlah hari tertentu.
     */
    public function cleanOldBackups(int $keepDays = 7): int
    {
        $files = File::files($this->backupDir);
        $deleted = 0;
        $threshold = now()->subDays($keepDays)->timestamp;

        foreach ($files as $file) {
            if ($file->getMTime() < $threshold) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Hitung ringkasan statistik data transaksional saat ini untuk info pembersihan dummy.
     */
    public function getPurgeableStats(): array
    {
        return [
            'penugasan'     => DB::table('penugasan')->count(),
            'tindak_lanjut' => DB::table('tindak_lanjut')->count(),
            'bukti_tl'      => DB::table('bukti_tindak_lanjut')->count(),
            'konsultasi'    => DB::table('konsultasi')->count(),
            'notifikasi'    => DB::table('notifikasi')->count(),
            'activity_logs' => DB::table('activity_log')->count(),
        ];
    }

    /**
     * Eksekusi pembersihan data percobaan (Purge Dummy Data).
     */
    public function purgeDummyData(array $modules, int $userId): array
    {
        $deletedInfo = [];

        DB::beginTransaction();
        try {
            // 1. Modul Tindak Lanjut & LHP
            if (in_array('tindak_lanjut', $modules)) {
                $countBukti = DB::table('bukti_tindak_lanjut')->count();
                DB::table('bukti_tindak_lanjut')->delete();

                if (Schema::hasTable('rincian_penyetoran_tl')) {
                    DB::table('rincian_penyetoran_tl')->delete();
                }

                $countTl = DB::table('tindak_lanjut')->count();
                DB::table('tindak_lanjut')->delete();

                if (Schema::hasTable('laporan_hasil')) {
                    DB::table('laporan_hasil')->delete();
                }

                $deletedInfo[] = "Tindak Lanjut & LHP ({$countTl} rekomendasi, {$countBukti} berkas bukti)";
            }

            // 2. Modul Penugasan (SPT)
            if (in_array('penugasan', $modules)) {
                DB::table('penugasan_objek')->delete();
                DB::table('penugasan_tim')->delete();
                if (Schema::hasTable('penugasan_irban')) {
                    DB::table('penugasan_irban')->delete();
                }
                $countSpt = DB::table('penugasan')->count();
                DB::table('penugasan')->delete();

                $deletedInfo[] = "Penugasan & SPT ({$countSpt} data SPT)";
            }

            // 3. Modul E-Consulting (QnA)
            if (in_array('konsultasi', $modules)) {
                DB::table('konsultasi_chat')->delete();
                DB::table('konsultasi_tim')->delete();
                $countKonsul = DB::table('konsultasi')->count();
                DB::table('konsultasi')->delete();

                $deletedInfo[] = "E-Consulting ({$countKonsul} tiket konsultasi & chat)";
            }

            // 4. Modul Log & Notifikasi Percobaan
            if (in_array('logs', $modules)) {
                $countNotif = DB::table('notifikasi')->count();
                DB::table('notifikasi')->delete();

                $countLogs = DB::table('activity_log')->count();
                DB::table('activity_log')->delete();

                $deletedInfo[] = "Log Aktivitas ({$countLogs} log) & Notifikasi ({$countNotif} notif)";
            }

            DB::commit();

            // Catat log pembersihan resmi
            ActivityLog::catat('system', 0, 'delete', null, [
                'action'        => 'purge_dummy_data',
                'user_id'       => $userId,
                'cleaned'       => $deletedInfo,
                'executed_at'   => now()->toDateTimeString(),
            ]);

            return [
                'success' => true,
                'message' => 'Data percobaan berhasil dibersihkan: ' . implode(', ', $deletedInfo) . '.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[SIPANDA Purge Error] ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Gagal membersihkan data percobaan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Dumper MySQL Murni PHP (Kompatibel dengan segala shared hosting/VPS).
     */
    protected function dumpMysql(): string
    {
        $tables = DB::select('SHOW TABLES');
        $output = "-- ========================================================\n";
        $output .= "-- SIPANDA KABUPATEN TRENGGALEK DATABASE BACKUP (MYSQL)\n";
        $output .= "-- Tanggal: " . now()->format('Y-m-d H:i:s') . "\n";
        $output .= "-- ========================================================\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n";

        foreach ($tables as $tableObj) {
            $tableArr = (array) $tableObj;
            $tableName = reset($tableArr);

            // Structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createArr = (array) $createTable[0];
            $createSql = $createArr['Create Table'];

            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Struktur Tabel `{$tableName}`\n";
            $output .= "-- --------------------------------------------------------\n";
            $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $output .= $createSql . ";\n\n";

            // Data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $output .= "-- Data Tabel `{$tableName}`\n";
                foreach ($rows as $row) {
                    $rowArr = (array) $row;
                    $cols = array_map(fn($c) => "`{$c}`", array_keys($rowArr));
                    $vals = array_map(function ($val) {
                        if (is_null($val)) return "NULL";
                        return "'" . addslashes((string)$val) . "'";
                    }, array_values($rowArr));

                    $output .= "INSERT INTO `{$tableName}` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ");\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $output;
    }

    /**
     * Dumper SQLite.
     */
    protected function dumpSqlite(): string
    {
        $tables = DB::select('SELECT name, sql FROM sqlite_master WHERE type="table" AND name NOT LIKE "sqlite_%"');
        $output = "-- ========================================================\n";
        $output .= "-- SIPANDA KABUPATEN TRENGGALEK DATABASE BACKUP (SQLITE)\n";
        $output .= "-- Tanggal: " . now()->format('Y-m-d H:i:s') . "\n";
        $output .= "-- ========================================================\n\n";

        foreach ($tables as $t) {
            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Struktur Tabel `{$t->name}`\n";
            $output .= "-- --------------------------------------------------------\n";
            $output .= "DROP TABLE IF EXISTS \"{$t->name}\";\n";
            $output .= $t->sql . ";\n\n";

            $rows = DB::table($t->name)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $rowArr = (array) $row;
                    $cols = array_map(fn($c) => "\"{$c}\"", array_keys($rowArr));
                    $vals = array_map(function ($val) {
                        if (is_null($val)) return "NULL";
                        return "'" . addslashes((string)$val) . "'";
                    }, array_values($rowArr));

                    $output .= "INSERT INTO \"{$t->name}\" (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ");\n";
                }
                $output .= "\n";
            }
        }

        return $output;
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
