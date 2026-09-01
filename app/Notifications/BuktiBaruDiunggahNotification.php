<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\BuktiTindakLanjut;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BuktiBaruDiunggahNotification extends Notification
{
    /**
     * @param BuktiTindakLanjut $bukti
     * @param string            $namaOpd
     */
    public function __construct(
        public readonly BuktiTindakLanjut $bukti,
        public readonly string $namaOpd
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $namaSapaan  = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noLhp       = $this->bukti->tindakLanjut?->no_lhp ?: ($this->bukti->tindakLanjut?->penugasan?->no_spt ?? '-');
        $rekomendasi = Str::limit($this->bukti->tindakLanjut?->rekomendasi, 120);

        return (new MailMessage)
            ->subject("[SIPANDA] Bukti Tindak Lanjut Baru dari {$this->namaOpd}")
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line("PIC dari **{$this->namaOpd}** baru saja mengunggah bukti tindak lanjut baru untuk diverifikasi:")
            ->line('')
            ->line("**No. LHP/SPT :** {$noLhp}")
            ->line("**Rekomendasi :** {$rekomendasi}")
            ->line("**Keterangan  :** " . ($this->bukti->catatan_opd ?: 'Tidak ada catatan khusus'))
            ->line('')
            ->action('Verifikasi Bukti Sekarang', route('tindak-lanjut.show', $this->bukti->tindak_lanjut_id))
            ->line('Silakan periksa dokumen lampiran dan berikan status verifikasi (Diterima/Ditolak/TDT).')
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan  = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noLhp       = $this->bukti->tindakLanjut?->no_lhp ?: ($this->bukti->tindakLanjut?->penugasan?->no_spt ?? '-');
        $rekomendasi = Str::limit($this->bukti->tindakLanjut?->rekomendasi, 120);
        $url         = route('tindak-lanjut.show', $this->bukti->tindak_lanjut_id);

        return "📥 *BUKTI TINDAK LANJUT BARU MASUK — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "PIC dari *{$this->namaOpd}* baru saja mengunggah bukti perbaikan tindak lanjut:\n\n" .
               "📄 *No. Dokumen:* {$noLhp}\n" .
               "🎯 *Rekomendasi:* {$rekomendasi}\n\n" .
               "Silakan buka SIPANDA untuk meneliti berkas dan memverifikasi:\n" .
               "🔗 {$url}\n\n" .
               "_SIPANDA — Inspektorat Kabupaten Trenggalek_";
    }
}
