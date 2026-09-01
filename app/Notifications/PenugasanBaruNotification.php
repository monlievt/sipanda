<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\Penugasan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PenugasanBaruNotification extends Notification
{
    /**
     * @param Penugasan $penugasan
     * @param string    $peranTim misal: 'Ketua Tim', 'Pengendali Teknis', 'Anggota Tim'
     */
    public function __construct(
        public readonly Penugasan $penugasan,
        public readonly string $peranTim = 'Anggota Tim'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $tglMulai   = $this->penugasan->tanggal_mulai->translatedFormat('d F Y');
        $tglSelesai = $this->penugasan->tanggal_selesai?->translatedFormat('d F Y') ?? '-';
        $objekNames = $this->penugasan->objekPenugasan->pluck('nama')->implode(', ') ?: '-';

        return (new MailMessage)
            ->subject("[SIPANDA] Penerbitan SPT Baru: {$this->penugasan->no_spt}")
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line("Anda telah ditugaskan sebagai **{$this->peranTim}** dalam Surat Perintah Tugas (SPT) berikut:")
            ->line('')
            ->line("**No. SPT   :** {$this->penugasan->no_spt}")
            ->line("**Kegiatan :** {$this->penugasan->uraian_penugasan}")
            ->line("**Objek    :** {$objekNames}")
            ->line("**Jadwal   :** {$tglMulai} s.d. {$tglSelesai}")
            ->line('')
            ->action('Buka SPT & Lembar Kerja', route('penugasan.show', $this->penugasan->id))
            ->line('Harap mempelajari fokus pengawasan dan berkoordinasi dengan tim.')
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $tglMulai   = $this->penugasan->tanggal_mulai->translatedFormat('d F Y');
        $tglSelesai = $this->penugasan->tanggal_selesai?->translatedFormat('d F Y') ?? '-';
        $objekNames = $this->penugasan->objekPenugasan->pluck('nama')->implode(', ') ?: '-';
        $url        = route('penugasan.show', $this->penugasan->id);

        return "📋 *PENERBITAN PENUGASAN (SPT) BARU — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Anda telah ditugaskan sebagai *{$this->peranTim}* pada penugasan berikut:\n\n" .
               "📄 *No. SPT:* {$this->penugasan->no_spt}\n" .
               "📋 *Kegiatan:* {$this->penugasan->uraian_penugasan}\n" .
               "🏛️ *Objek Penugasan:* {$objekNames}\n" .
               "📅 *Jadwal:* {$tglMulai} s.d. {$tglSelesai}\n\n" .
               "Silakan buka tautan berikut untuk melihat lembar kerja dan detail tim:\n" .
               "🔗 {$url}\n\n" .
               "_SIPANDA — Inspektorat Kabupaten Trenggalek_";
    }
}
