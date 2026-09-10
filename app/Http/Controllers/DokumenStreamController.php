<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DokumenStreamController extends Controller
{
    /**
     * Stream / Tampilkan file dokumen secara inline (PDF / Gambar) tanpa terkena Apache 403 Forbidden.
     */
    public function stream(Request $request): Response
    {
        $path = $request->query('path');
        $download = $request->boolean('download', false);

        return $this->resolveAndServe($path, $download);
    }

    /**
     * Stream berdasarkan path parameter URL: /dokumen/berkas/{path}
     */
    public function streamPath(Request $request, string $path): Response
    {
        $download = $request->boolean('download', false);

        return $this->resolveAndServe($path, $download);
    }

    /**
     * Cari file fisik di seluruh storage disk yang diizinkan lalu sajikan dengan header keamanan inline.
     */
    protected function resolveAndServe(?string $rawPath, bool $forceDownload): Response
    {
        if (! $rawPath) {
            abort(404, 'Path dokumen tidak diberikan.');
        }

        // Bersihkan prefix /storage/, storage/, url host jika ada
        $cleanPath = ltrim($rawPath, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        // 1. Mencegah directory traversal attack & null bytes
        if (str_contains($cleanPath, '..') || str_contains($cleanPath, "\0") || str_contains($cleanPath, '\\')) {
            abort(403, 'Akses path tidak valid.');
        }

        // 2. Blokir ekstensi file berbahaya yang tidak boleh di-stream ke publik
        $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
        $blockedExtensions = ['php', 'phtml', 'phar', 'exe', 'sh', 'bat', 'env', 'sql', 'key', 'pem', 'log', 'sqlite', 'sqlite3', 'htaccess'];
        if (in_array($ext, $blockedExtensions)) {
            abort(403, 'Format berkas tidak diizinkan untuk diakses langsung.');
        }

        // 3. Pengecekan Otorisasi: Direktori sensitif internal & bukti audit wajib login
        $sensitivePrefixes = ['arsip', 'arsip_digital', 'bukti_tl', 'surat_pengantar', 'berkas_konsultasi', 'private'];
        $isSensitive = false;
        foreach ($sensitivePrefixes as $prefix) {
            if (str_starts_with($cleanPath, $prefix . '/') || $cleanPath === $prefix) {
                $isSensitive = true;
                break;
            }
        }

        if ($isSensitive) {
            $isLoggedIn = auth('web')->check() || auth('opd')->check();
            if (! $isLoggedIn) {
                abort(401, 'Silakan login terlebih dahulu untuk mengakses dokumen ini.');
            }
        }

        // 4. Batasi direktori pencarian yang aman (hanya di disk public dan storage app yang sah)
        $candidates = [
            Storage::disk('public')->path($cleanPath),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/private/' . $cleanPath),
            public_path('storage/' . $cleanPath),
        ];

        $targetFile = null;
        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && is_file($real)) {
                $targetFile = $real;
                break;
            }
        }

        if (! $targetFile) {
            abort(404, "Berkas dokumen fisik tidak ditemukan di server.");
        }

        $mimeType = mime_content_type($targetFile) ?: 'application/octet-stream';
        $filename = basename($targetFile);

        if ($ext === 'pdf') {
            $mimeType = 'application/pdf';
        }

        $headers = [
            'Content-Type'           => $mimeType,
            'Content-Disposition'    => ($forceDownload ? 'attachment' : 'inline') . '; filename="' . addslashes($filename) . '"',
            'Cache-Control'          => 'private, no-transform, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'SAMEORIGIN',
        ];

        if ($forceDownload) {
            return response()->download($targetFile, $filename, $headers);
        }

        return response()->file($targetFile, $headers);
    }
}
