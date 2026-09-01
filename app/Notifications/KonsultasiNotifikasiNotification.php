<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\Konsultasi;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class KonsultasiNotifikasiNotification extends Notification
{
    /**
     * @param Konsultasi $konsultasi
     * @param string     $tipeEvent 'disposisi' | 'chat_baru' | 'ba_terbit'
     * @param string     $pengirimNama
     * @param string     $cuplikanPesan
     */
    public function __construct(
        public readonly Konsultasi $konsultasi,
        public readonly string $tipeEvent,
        public readonly string $pengirimNama = '',
        public readonly string $cuplikanPesan = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $topik      = Str::limit($this->konsultasi->topik, 100);

        [$subjek, $headline, $url] = match($this->tipeEvent) {
            'disposisi' => [
                "[SIPANDA e-Consulting] Disposisi Konsultasi Baru: {$topik}",
                "Terdapat permohonan konsultasi baru yang didisposisikan kepada Anda:",
                route('konsultasi.show', $this->konsultasi->id),
            ],
            'ba_terbit' => [
                "[SIPANDA e-Consulting] Berita Acara Konsultasi Telah Terbit: {$topik}",
                "Berita Acara (BA) hasil konsultasi APIP telah resmi diterbitkan oleh Inspektorat:",
                route('opd.konsultasi.show', $this->konsultasi->id),
            ],
            default => [ // 'chat_baru'
                "[SIPANDA e-Consulting] Pesan Baru dari {$this->pengirimNama}: {$topik}",
                "Ada tanggapan/pesan konsultasi baru dalam thread:",
                $notifiable->isOpd() ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id),
            ]
        };

        $mail = (new MailMessage)
            ->subject($subjek)
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line($headline)
            ->line('')
            ->line("**Topik Konsultasi :** {$this->konsultasi->topik}")
            ->line("**Kategori         :** " . ucfirst($this->konsultasi->kategori));

        if ($this->cuplikanPesan) {
            $mail->line("**Cuplikan Pesan   :** " . Str::limit($this->cuplikanPesan, 150));
        }

        return $mail
            ->line('')
            ->action('Buka Ruang Konsultasi', $url)
            ->salutation('Hormat kami, Layanan e-Consulting APIP — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $topik      = Str::limit($this->konsultasi->topik, 100);

        if ($this->tipeEvent === 'disposisi') {
            $url = route('konsultasi.show', $this->konsultasi->id);
            return "💬 *DISPOSISI KONSULTASI APIP BARU — SIPANDA*\n\n" .
                   "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
                   "Terdapat permohonan konsultasi baru yang didisposisikan kepada Anda:\n\n" .
                   "📌 *Topik:* {$topik}\n" .
                   "🏛️ *Pemohon:* " . ($this->konsultasi->user?->objekPenugasan?->nama ?? $this->konsultasi->user?->nama_display ?? 'OPD') . "\n\n" .
                   "Buka ruang konsultasi untuk menanggapi:\n" .
                   "🔗 {$url}\n\n" .
                   "_Layanan Advisory APIP Inspektorat Trenggalek_";
        }

        if ($this->tipeEvent === 'ba_terbit') {
            $url = route('opd.konsultasi.show', $this->konsultasi->id);
            return "📑 *BERITA ACARA KONSULTASI DITERBITKAN — SIPANDA*\n\n" .
                   "Yth. Bpk/Ibu *{$namaSapaan}*,\n" .
                   "Berita Acara (BA) resmi layanan konsultasi telah diterbitkan oleh Tim Auditor Inspektorat:\n\n" .
                   "📌 *Topik:* {$topik}\n\n" .
                   "Silakan unduh dokumen Berita Acara pada tautan:\n" .
                   "🔗 {$url}\n\n" .
                   "_Inspektorat Kabupaten Trenggalek_";
        }

        // Chat baru
        $url = $notifiable->isOpd() ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id);
        $pesanSingkat = Str::limit($this->cuplikanPesan, 140);

        return "💬 *PESAN BARU E-CONSULTING — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Terdapat tanggapan baru dari *{$this->pengirimNama}*:\n\n" .
               "📌 *Topik:* {$topik}\n" .
               "💬 *Pesan:* _{$pesanSingkat}_\n\n" .
               "Buka percakapan konsultasi:\n" .
               "🔗 {$url}\n\n" .
               "_Layanan e-Consulting APIP Inspektorat Trenggalek_";
    }
}
