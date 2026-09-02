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
     * @param string     $tipeEvent 'permohonan_baru' | 'disposisi_inspektur' | 'disposisi_irban' | 'chat_baru' | 'ba_terbit'
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
        $opdNama    = $this->konsultasi->objekPenugasan?->nama ?? $this->konsultasi->pemohon?->nama_display ?? 'Perangkat Daerah';

        [$subjek, $headline, $url] = match($this->tipeEvent) {
            'permohonan_baru' => [
                "[SIPANDA e-Consulting] Permohonan Konsultasi Baru dari {$opdNama}: {$topik}",
                "Terdapat permohonan konsultasi baru yang diajukan oleh {$opdNama}. Mohon berkenan memberikan arahan dan mendisposisikan kepada Irban terkait:",
                route('konsultasi.show', $this->konsultasi->id),
            ],
            'disposisi_inspektur' => [
                "[SIPANDA e-Consulting] Disposisi Inspektur: {$topik}",
                "Inspektur telah mendisposisikan permohonan konsultasi dari {$opdNama} kepada Irban Anda. Mohon menugaskan Tim Auditor/PPUPD:",
                route('konsultasi.show', $this->konsultasi->id),
            ],
            'disposisi_irban' => [
                "[SIPANDA e-Consulting] Penugasan Tim Konsultasi APIP: {$topik}",
                "Anda telah ditugaskan oleh Irban dalam Tim Konsultasi APIP untuk menanggapi permohonan dari {$opdNama}:",
                route('konsultasi.show', $this->konsultasi->id),
            ],
            'ba_terbit' => [
                "[SIPANDA e-Consulting] Berita Acara Konsultasi Telah Terbit: {$topik}",
                "Berita Acara (BA) hasil konsultasi APIP telah resmi diterbitkan oleh Inspektorat:",
                (method_exists($notifiable, 'isOpd') && $notifiable->isOpd()) ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id),
            ],
            default => [ // 'chat_baru'
                "[SIPANDA e-Consulting] Pesan Baru dari {$this->pengirimNama}: {$topik}",
                "Ada tanggapan/pesan konsultasi baru dalam thread konsultasi:",
                (method_exists($notifiable, 'isOpd') && $notifiable->isOpd()) ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id),
            ]
        };

        $mail = (new MailMessage)
            ->subject($subjek)
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line($headline)
            ->line('')
            ->line("**Nomor Tiket      :** {$this->konsultasi->nomor_tiket}")
            ->line("**Topik Konsultasi :** {$this->konsultasi->topik}")
            ->line("**Instansi Pemohon :** {$opdNama}")
            ->line("**Area / Kategori  :** " . ucfirst($this->konsultasi->kategori));

        if ($this->konsultasi->catatan_disposisi_inspektur && in_array($this->tipeEvent, ['disposisi_inspektur', 'disposisi_irban'])) {
            $mail->line("**Arahan Inspektur :** {$this->konsultasi->catatan_disposisi_inspektur}");
        }

        if ($this->cuplikanPesan) {
            $mail->line("**Catatan / Pesan  :** " . Str::limit($this->cuplikanPesan, 150));
        }

        return $mail
            ->line('')
            ->action('Buka Ruang Konsultasi', $url)
            ->salutation('Hormat kami, Layanan e-Consulting APIP — Inspektorat Daerah Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $topik      = Str::limit($this->konsultasi->topik, 100);
        $opdNama    = $this->konsultasi->objekPenugasan?->nama ?? $this->konsultasi->pemohon?->nama_display ?? 'Perangkat Daerah';

        if ($this->tipeEvent === 'permohonan_baru') {
            $url = route('konsultasi.show', $this->konsultasi->id);
            return "📩 *PERMOHONAN KONSULTASI BARU — SIPANDA*\n\n" .
                   "Yth. Bapak/Ibu *{$namaSapaan}*,\n" .
                   "Terdapat permohonan konsultasi masuk dari *{$opdNama}*:\n\n" .
                   "🎫 *No. Tiket:* {$this->konsultasi->nomor_tiket}\n" .
                   "📌 *Topik:* {$topik}\n" .
                   "🏛️ *Area:* " . ucfirst($this->konsultasi->kategori) . "\n\n" .
                   "Mohon berkenan memberikan arahan dan disposisi ke Irban terkait:\n" .
                   "🔗 {$url}\n\n" .
                   "_Inspektorat Daerah Kabupaten Trenggalek_";
        }

        if ($this->tipeEvent === 'disposisi_inspektur') {
            $url = route('konsultasi.show', $this->konsultasi->id);
            $arahan = $this->konsultasi->catatan_disposisi_inspektur ?: '-';
            return "📋 *DISPOSISI KONSULTASI DARI INSPEKTUR — SIPANDA*\n\n" .
                   "Yth. Irban / Tim *{$namaSapaan}*,\n" .
                   "Inspektur telah mendisposisikan konsultasi dari *{$opdNama}* kepada Irban Anda:\n\n" .
                   "🎫 *No. Tiket:* {$this->konsultasi->nomor_tiket}\n" .
                   "📌 *Topik:* {$topik}\n" .
                   "✍️ *Arahan Inspektur:* _{$arahan}_\n\n" .
                   "Silakan tunjuk Tim Auditor/PPUPD dan tentukan metode konsultasi:\n" .
                   "🔗 {$url}\n\n" .
                   "_Inspektorat Daerah Kabupaten Trenggalek_";
        }

        if ($this->tipeEvent === 'disposisi_irban') {
            $url = (method_exists($notifiable, 'isOpd') && $notifiable->isOpd()) ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id);
            return "👥 *PENUGASAN TIM KONSULTASI APIP — SIPANDA*\n\n" .
                   "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
                   "Tim Konsultasi APIP telah ditetapkan untuk melayani topik:\n\n" .
                   "📌 *Topik:* {$topik}\n" .
                   "🏛️ *Pemohon:* {$opdNama}\n" .
                   "💬 *Metode:* " . ($this->konsultasi->metode_disetujui === 'offline' ? 'Tatap Muka di Inspektorat' : 'Online Chat Interaktif') . "\n\n" .
                   "Buka ruang konsultasi:\n" .
                   "🔗 {$url}\n\n" .
                   "_Layanan Advisory APIP Trenggalek_";
        }

        if ($this->tipeEvent === 'ba_terbit') {
            $url = (method_exists($notifiable, 'isOpd') && $notifiable->isOpd()) ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id);
            return "📑 *BERITA ACARA KONSULTASI DITERBITKAN — SIPANDA*\n\n" .
                   "Yth. Bpk/Ibu *{$namaSapaan}*,\n" .
                   "Berita Acara (BA) resmi layanan konsultasi telah diterbitkan:\n\n" .
                   "📌 *Topik:* {$topik}\n\n" .
                   "Unduh dokumen Berita Acara pada tautan:\n" .
                   "🔗 {$url}\n\n" .
                   "_Inspektorat Kabupaten Trenggalek_";
        }

        // Chat baru
        $url = (method_exists($notifiable, 'isOpd') && $notifiable->isOpd()) ? route('opd.konsultasi.show', $this->konsultasi->id) : route('konsultasi.show', $this->konsultasi->id);
        $pesanSingkat = Str::limit($this->cuplikanPesan, 140);

        return "💬 *PESAN BARU E-CONSULTING — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Terdapat tanggapan baru dari *{$this->pengirimNama}*:\n\n" .
               "📌 *Topik:* {$topik}\n" .
               "💬 *Pesan:* _{$pesanSingkat}_\n\n" .
               "Buka percakapan konsultasi:\n" .
               "🔗 {$url}\n\n" .
               "_Layanan e-Consulting APIP Trenggalek_";
    }
}
