<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\TindakLanjut;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TindakLanjutMandekNotification extends Notification
{
    /**
     * Notifikasi ke Irban jika OPD belum merespons rekomendasi lebih dari 14 hari.
     *
     * @param TindakLanjut $tindakLanjut Rekomendasi yang mandek
     * @param int          $hariMandek   Jumlah hari sejak rekomendasi dibuat / diperbarui
     */
    public function __construct(
        public readonly TindakLanjut $tindakLanjut,
        public readonly int $hariMandek
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $noSpt       = $this->tindakLanjut->penugasan?->no_spt ?? '-';
        $rekomendasi = Str::limit($this->tindakLanjut->rekomendasi, 120);
        $namaSapaan  = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $statusLabel = match($this->tindakLanjut->status_tindak_lanjut) {
            'belum'       => 'Belum Ditindaklanjuti',
            'proses'      => 'Dalam Proses',
            'dikembalikan'=> 'Dikembalikan (perlu revisi)',
            default       => $this->tindakLanjut->status_tindak_lanjut,
        };

        return (new MailMessage)
            ->subject("[SIPANDA] Peringatan: Rekomendasi Mandek {$this->hariMandek} Hari — SPT {$noSpt}")
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line("Rekomendasi tindak lanjut berikut **belum mendapat respons OPD selama lebih dari {$this->hariMandek} hari:**")
            ->line('')
            ->line("**No. SPT    :** {$noSpt}")
            ->line("**Status     :** {$statusLabel}")
            ->line("**Rekomendasi:** {$rekomendasi}")
            ->line('')
            ->action('Lihat Detail Tindak Lanjut', route('tindak-lanjut.show', $this->tindakLanjut->id))
            ->line('Mohon ditindaklanjuti agar tidak menghambat proses pengawasan.')
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan  = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noSpt       = $this->tindakLanjut->penugasan?->no_spt ?? '-';
        $noLhp       = $this->tindakLanjut->no_lhp ?: $noSpt;
        $rekomendasi = Str::limit($this->tindakLanjut->rekomendasi, 140);
        $opdNames    = $this->tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?: '-';
        $url         = route('tindak-lanjut.show', $this->tindakLanjut->id);

        return "⚠️ *PERINGATAN TLHP MANDEK (> {$this->hariMandek} HARI) — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Terdapat rekomendasi tindak lanjut LHP yang belum ada progres penyelesaian dari OPD:\n\n" .
               "📄 *No. Dokumen:* {$noLhp}\n" .
               "🏛️ *OPD Target:* {$opdNames}\n" .
               "🎯 *Rekomendasi:* {$rekomendasi}\n\n" .
               "Buka rincian matriks tindak lanjut:\n" .
               "🔗 {$url}\n\n" .
               "_Pesan otomatis dari SIPANDA Inspektorat Kab. Trenggalek._";
    }
}
