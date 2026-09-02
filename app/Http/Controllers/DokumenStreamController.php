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
     * Cari file fisik di seluruh storage disk (public, local/private, root app) lalu sajikan dengan header inline.
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

        // Mencegah directory traversal attack
        if (str_contains($cleanPath, '..')) {
            abort(403, 'Akses path tidak valid.');
        }

        $candidates = [
            Storage::disk('public')->path($cleanPath),
            Storage::disk('local')->path($cleanPath),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/private/' . $cleanPath),
            storage_path('app/' . $cleanPath),
            public_path('storage/' . $cleanPath),
        ];

        $targetFile = null;
        foreach ($candidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                $targetFile = $candidate;
                break;
            }
        }

        if (! $targetFile) {
            abort(404, "Berkas dokumen fisik tidak ditemukan di server ({$cleanPath}).");
        }

        $mimeType = mime_content_type($targetFile) ?: 'application/pdf';
        $filename = basename($targetFile);

        // Jika ekstensi adalah PDF tapi mime gagal dideteksi
        if (str_ends_with(strtolower($targetFile), '.pdf')) {
            $mimeType = 'application/pdf';
        }

        if ($forceDownload) {
            return response()->download($targetFile, $filename, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->file($targetFile, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control'       => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
