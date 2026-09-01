<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\Penugasan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PenugasanReminderNotification extends Notification
{
    /**
     * @param Penugasan $penugasan Data penugasan yang akan dimulai
     * @param string    $jenis     'h3' untuk H-3 atau 'h1' untuk H-1 (besok)
     */
    public function __construct(
        public readonly Penugasan $penugasan,
        public readonly string $jenis
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label      = $this->jenis === 'h3' ? 'H-3 (3 hari lagi)' : 'H-1 (BESOK)';
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $tglMulai   = $this->penugasan->tanggal_mulai->translatedFormat('d F Y');
        $tglSelesai = $this->penugasan->tanggal_selesai?->translatedFormat('d F Y') ?? '-';

        return (new MailMessage)
            ->subject("[SIPANDA] Reminder {$label}: {$this->penugasan->no_spt}")
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line("Ini adalah pengingat otomatis **{$label}** untuk kegiatan penugasan berikut:")
            ->line('')
            ->line("**No. SPT :** {$this->penugasan->no_spt}")
            ->line("**Uraian  :** {$this->penugasan->uraian_penugasan}")
            ->line("**Mulai   :** {$tglMulai}")
            ->line("**Selesai :** {$tglSelesai}")
            ->line('')
            ->action('Lihat Detail Penugasan', route('penugasan.show', $this->penugasan->id))
            ->line('Harap mempersiapkan diri dan berkoordinasi dengan anggota tim.')
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $label      = $this->jenis === 'h3' ? 'H-3 (3 Hari Lagi)' : '⏰ H-1 (BESOK)';
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $tglMulai   = $this->penugasan->tanggal_mulai->translatedFormat('d F Y');
        $tglSelesai = $this->penugasan->tanggal_selesai?->translatedFormat('d F Y') ?? '-';
        $url        = route('penugasan.show', $this->penugasan->id);
        $objekNames = $this->penugasan->objekPenugasan?->pluck('nama')->implode(', ') ?: '-';

        return "🔔 *PENGINGAT PENUGASAN (SPT) — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Ini adalah pengingat otomatis *{$label}* untuk jadwal penugasan berikut:\n\n" .
               "📄 *No. SPT:* {$this->penugasan->no_spt}\n" .
               "📋 *Kegiatan:* {$this->penugasan->uraian_penugasan}\n" .
               "🏛️ *Objek Penugasan:* {$objekNames}\n" .
               "📅 *Jadwal:* {$tglMulai} s.d. {$tglSelesai}\n\n" .
               "Silakan buka tautan berikut untuk detail dan lembar kerja:\n" .
               "🔗 {$url}\n\n" .
               "_Pesan otomatis dari SIPANDA Inspektorat Kab. Trenggalek._";
    }
}
