<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected DatabaseBackupService $backupService
    ) {}

    /**
     * Tampilkan Halaman Pusat Backup Database & Pembersihan Data Percobaan.
     */
    public function index(): View
    {
        $settings = [
            'auto_email_enabled' => AppSetting::get('backup_auto_email_enabled', '0') === '1',
            'email_destination'  => AppSetting::get('backup_email_destination', auth()->user()?->email ?? 'nugrahenisetya72@gmail.com'),
            'email_frequency'    => AppSetting::get('backup_email_frequency', 'daily'),
            'gdrive_enabled'     => AppSetting::get('backup_gdrive_enabled', '0') === '1',
            'gdrive_folder'      => AppSetting::get('backup_gdrive_folder', 'SIPANDA_TRENGGALEK_BACKUP'),
            'last_run'           => AppSetting::get('backup_last_run'),
        ];

        $backupsList = $this->backupService->listBackups();
        $purgeStats  = $this->backupService->getPurgeableStats();

        return view('master.backup.index', compact('settings', 'backupsList', 'purgeStats'));
    }

    /**
     * Mode 1: Unduh Cadangan Database Instan Saat Ini.
     */
    public function downloadCurrent(): BinaryFileResponse|RedirectResponse
    {
        $backup = $this->backupService->createBackup();

        if (! $backup['success']) {
            return back()->with('error', 'Gagal membuat file cadangan: ' . ($backup['error'] ?? 'Unknown error'));
        }

        return response()->download($backup['filepath'], $backup['filename'], [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Unduh berkas backup yang tersimpan di server.
     */
    public function downloadFile(string $filename): BinaryFileResponse|RedirectResponse
    {
        $clean = basename($filename);
        $path = storage_path("app/backups/{$clean}");

        if (! file_exists($path)) {
            return back()->with('error', 'Berkas cadangan tidak ditemukan di server.');
        }

        return response()->download($path, $clean);
    }

    /**
     * Hapus berkas backup tertentu dari server.
     */
    public function deleteFile(string $filename): RedirectResponse
    {
        $clean = basename($filename);
        $deleted = $this->backupService->deleteBackup($clean);

        if ($deleted) {
            return back()->with('status', "Berkas cadangan {$clean} berhasil dihapus.");
        }

        return back()->with('error', 'Berkas tidak ditemukan atau gagal dihapus.');
    }

    /**
     * Mode 2 (Aksi Cepat): Tes Pengiriman Backup ke Email Admin Sekarang.
     */
    public function sendEmailTest(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email tujuan wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
        ]);

        $result = $this->backupService->sendBackupEmail($request->email);

        if ($result['success']) {
            return back()->with('status', $result['message']);
        }

        return back()->with('error', $result['error'] ?? 'Gagal mengirimkan email.');
    }

    /**
     * Simpan Pengaturan Checklist / Toggle Backup Otomatis.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'email_destination' => ['nullable', 'email'],
            'email_frequency'   => ['nullable', 'in:daily,weekly,monthly'],
            'gdrive_folder'     => ['nullable', 'string', 'max:100'],
        ]);

        AppSetting::set('backup_auto_email_enabled', $request->boolean('auto_email_enabled') ? '1' : '0');
        if ($request->filled('email_destination')) {
            AppSetting::set('backup_email_destination', trim($request->email_destination));
        }
        AppSetting::set('backup_email_frequency', $request->input('email_frequency', 'daily'));

        AppSetting::set('backup_gdrive_enabled', $request->boolean('gdrive_enabled') ? '1' : '0');
        if ($request->filled('gdrive_folder')) {
            AppSetting::set('backup_gdrive_folder', trim($request->gdrive_folder));
        }

        return back()->with('status', 'Pengaturan checklist backup otomatis berhasil diperbarui.');
    }

    /**
     * Fitur Khusus Super Admin: Pembersihan Data Percobaan (Purge Dummy Data).
     */
    public function purgeDummy(Request $request): RedirectResponse
    {
        // 1. Otorisasi Ketat: Hanya Super Admin / Admin
        if (! auth()->user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Akses ditolak. Fitur pembersihan data percobaan hanya untuk Super Admin.');
        }

        // 2. Validasi Kata Kunci Konfirmasi
        $request->validate([
            'modules'             => ['required', 'array', 'min:1'],
            'konfirmasi_kata'     => ['required', 'string'],
            'password_konfirmasi' => ['required', 'string'],
        ], [
            'modules.required'             => 'Pilih minimal satu modul yang ingin dibersihkan datanya.',
            'konfirmasi_kata.required'     => 'Ketik frasa persetujuan sesuai petunjuk.',
            'password_konfirmasi.required' => 'Password akun Anda wajib dimasukkan.',
        ]);

        if (trim(strtoupper($request->konfirmasi_kata)) !== 'BERSIHKAN DATA PERCOBAAN') {
            return back()->with('error', 'Frasa konfirmasi tidak cocok. Anda harus mengetik persis: BERSIHKAN DATA PERCOBAAN');
        }

        // 3. Verifikasi Password Akun Super Admin
        if (! Hash::check($request->password_konfirmasi, auth()->user()->password)) {
            return back()->with('error', 'Password akun Anda salah. Tindakan pembersihan dibatalkan demi keamanan.');
        }

        // 4. Eksekusi Pembersihan
        $result = $this->backupService->purgeDummyData($request->modules, auth()->id());

        if ($result['success']) {
            return back()->with('status', '✓ ' . $result['message']);
        }

        return back()->with('error', $result['error'] ?? 'Gagal membersihkan data.');
    }
}
