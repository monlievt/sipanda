<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestWhatsAppNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipanda:test-wa 
                            {nomor : Nomor WhatsApp tujuan (contoh: 081234567890)} 
                            {pesan? : Pesan teks yang ingin dikirimkan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji coba pengiriman pesan WhatsApp melalui Gateway WAHA';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $service): int
    {
        $nomor = $this->argument('nomor');
        $pesan = $this->argument('pesan') ?: (
            "🔔 *UJI COBA NOTIFIKASI WHATSAPP SIPANDA*\n\n" .
            "Halo! Pesan ini adalah uji coba koneksi gateway WhatsApp (WAHA) dari Sistem Informasi Pengawasan Terintegrasi (SIPANDA) Inspektorat Kabupaten Trenggalek.\n\n" .
            "⏱️ Waktu Uji: " . now()->translatedFormat('d F Y H:i:s') . " WIB\n" .
            "✓ Status: Terhubung dan Siap Digunakan."
        );

        $this->info("Menghubungi WAHA WhatsApp Gateway...");

        // 1. Cek Status Sesi
        $sessionStatus = $service->getSessionStatus();
        $this->line("• Status Sesi WAHA: <comment>" . ($sessionStatus['status'] ?? 'UNKNOWN') . "</comment>");

        // 2. Format Nomor
        $formattedChatId = $service->formatChatId($nomor);
        $this->line("• Nomor Tujuan: <comment>{$nomor}</comment> → Chat ID: <info>{$formattedChatId}</info>");

        // 3. Kirim Pesan
        $this->info("Mengirim pesan...");
        $result = $service->sendText($nomor, $pesan);

        if ($result['success']) {
            $this->info("✓ Pesan WhatsApp BERHASIL dikirimkan ke {$nomor}!");
            return self::SUCCESS;
        }

        $this->error("✗ Gagal mengirim pesan: " . ($result['error'] ?? $result['message'] ?? 'Unknown error'));
        $this->line("<comment>Tips:</comment> Pastikan container Docker WAHA aktif pada port 3000 dan sesi WhatsApp telah di-scan melalui dashboard WAHA.");

        return self::FAILURE;
    }
}
