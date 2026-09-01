<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\TindakLanjut;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class JatuhTempoTlhpNotification extends Notification
{
    /**
     * @param TindakLanjut $tindakLanjut
     * @param int          $sisaHari 7 untuk H-7, 0 untuk Hari-H
     */
    public function __construct(
        public readonly TindakLanjut $tindakLanjut,
        public readonly int $sisaHari
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noLhp      = $this->tindakLanjut->no_lhp ?: ($this->tindakLanjut->penugasan?->no_spt ?? '-');
        $label      = $this->sisaHari === 0 ? 'HARI INI JATUH TEMPO' : "TERSISA {$this->sisaHari} HARI LAGI";
        $tglTarget  = $this->tindakLanjut->tanggal_target ? $this->tindakLanjut->tanggal_target->translatedFormat('d F Y') : '-';
        $rekomendasi = Str::limit($this->tindakLanjut->rekomendasi, 120);

        return (new MailMessage)
            ->subject("[SIPANDA] Peringatan Batas Waktu Tindak Lanjut: {$label} — {$noLhp}")
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line("Mengingatkan batas waktu penyelesaian tindak lanjut hasil pengawasan (**{$label}**):")
            ->line('')
            ->line("**No. LHP/SPT   :** {$noLhp}")
            ->line("**Batas Waktu   :** {$tglTarget}")
            ->line("**Rekomendasi   :** {$rekomendasi}")
            ->line('')
            ->action('Unggah / Cek Bukti Tindak Lanjut', route('opd.tindak-lanjut.show', $this->tindakLanjut->id))
            ->line('Mohon segera menyelesaikan dan mengunggah dokumen pertanggungjawaban sebelum batas waktu berakhir.')
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noLhp      = $this->tindakLanjut->no_lhp ?: ($this->tindakLanjut->penugasan?->no_spt ?? '-');
        $label      = $this->sisaHari === 0 ? '🚨 HARI INI JATUH TEMPO' : "⏳ TERSISA {$this->sisaHari} HARI";
        $tglTarget  = $this->tindakLanjut->tanggal_target ? $this->tindakLanjut->tanggal_target->translatedFormat('d F Y') : '-';
        $rekomendasi = Str::limit($this->tindakLanjut->rekomendasi, 140);
        $url        = route('opd.tindak-lanjut.show', $this->tindakLanjut->id);

        return "⚠️ *PERINGATAN BATAS WAKTU TINDAK LANJUT — SIPANDA*\n\n" .
               "Yth. Bpk/Ibu *{$namaSapaan}*,\n" .
               "Batas waktu penyelesaian rekomendasi tindak lanjut LHP (*{$label}*):\n\n" .
               "📄 *No. Dokumen:* {$noLhp}\n" .
               "📅 *Batas Akhir:* {$tglTarget}\n" .
               "🎯 *Rekomendasi:* {$rekomendasi}\n\n" .
               "Mohon segera unggah dokumen bukti perbaikan:\n" .
               "🔗 {$url}\n\n" .
               "_SIPANDA — Inspektorat Kabupaten Trenggalek_";
    }
}
