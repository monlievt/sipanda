<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected bool $enabled;
    protected string $apiUrl;
    protected string $apiKey;
    protected string $session;

    public function __construct()
    {
        $this->enabled = (bool) config('services.waha.enabled', env('WAHA_ENABLED', true));
        $this->apiUrl  = rtrim(config('services.waha.url', env('WAHA_API_URL', 'http://127.0.0.1:3000')), '/');
        $this->apiKey  = config('services.waha.api_key', env('WAHA_API_KEY', ''));
        $this->session = config('services.waha.session', env('WAHA_SESSION', 'default'));
    }

    /**
     * Cek apakah service WhatsApp aktif.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Format nomor ponsel Indonesia ke format WhatsApp Chat ID (misal: 6281234567890@c.us).
     */
    public function formatChatId(string $phone): string
    {
        // Bersihkan karakter selain angka
        $clean = preg_replace('/[^\d]/', '', $phone);

        // Ubah 08xxx menjadi 628xxx
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        // Jika sudah 62xxx langsung gunakan
        if (! str_contains($clean, '@c.us')) {
            $clean .= '@c.us';
        }

        return $clean;
    }

    /**
     * Kirim pesan teks WhatsApp melalui WAHA.
     */
    public function sendText(string $to, string $message): array
    {
        if (! $this->enabled) {
            Log::info("[SIPANDA WhatsApp] Pengiriman dinonaktifkan (WAHA_ENABLED=false). Ke: {$to}");
            return ['success' => false, 'message' => 'WhatsApp gateway dinonaktifkan.'];
        }

        $chatId = $this->formatChatId($to);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Api-Key'    => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/api/sendText", [
                    'session' => $this->session,
                    'chatId'  => $chatId,
                    'text'    => $message,
                ]);

            if ($response->successful()) {
                Log::info("[SIPANDA WhatsApp] Berhasil kirim pesan ke {$chatId}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning("[SIPANDA WhatsApp] Gagal kirim pesan ke {$chatId}. Status: {$response->status()}", [
                'body' => $response->body()
            ]);

            return ['success' => false, 'error' => $response->body()];

        } catch (\Throwable $e) {
            Log::error("[SIPANDA WhatsApp] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Kirim berkas / dokumen / media melalui WAHA.
     */
    public function sendFile(string $to, string $fileUrl, string $caption = '', string $filename = 'dokumen.pdf'): array
    {
        if (! $this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp gateway dinonaktifkan.'];
        }

        $chatId = $this->formatChatId($to);

        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'X-Api-Key'    => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/api/sendFile", [
                    'session' => $this->session,
                    'chatId'  => $chatId,
                    'file'    => [
                        'url'      => $fileUrl,
                        'filename' => $filename,
                    ],
                    'caption' => $caption,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->body()];

        } catch (\Throwable $e) {
            Log::error("[SIPANDA WhatsApp sendFile] Exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ambil status sesi WAHA (WORKING, SCAN_QR_CODE, STOPPED).
     */
    public function getSessionStatus(): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['X-Api-Key' => $this->apiKey])
                ->get("{$this->apiUrl}/api/sessions/{$this->session}");

            if ($response->successful()) {
                return ['success' => true, 'status' => $response->json('status', 'UNKNOWN')];
            }

            return ['success' => false, 'status' => 'DISCONNECTED'];

        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'OFFLINE', 'error' => $e->getMessage()];
        }
    }
}
