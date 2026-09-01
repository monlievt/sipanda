<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\BuktiTindakLanjut;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuktiVerifikasiNotification extends Notification
{
    /**
     * Notifikasi ke pengguna OPD setelah bukti tindak lanjutnya diverifikasi
     * (diterima / ditolak / TDT) oleh Auditor/Irban Inspektorat.
     *
     * @param BuktiTindakLanjut $bukti            Bukti yang baru diverifikasi
     * @param string            $statusVerifikasi  'diterima' | 'ditolak' | 'tdt'
     */
    public function __construct(
        public readonly BuktiTindakLanjut $bukti,
        public readonly string $statusVerifikasi
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noSpt      = $this->bukti->tindakLanjut?->penugasan?->no_spt ?? '-';

        [$subjek, $headline, $keterangan, $warna] = match($this->statusVerifikasi) {
            'diterima' => [
                "[SIPANDA] ✅ Bukti Tindak Lanjut DITERIMA — SPT {$noSpt}",
                '✅ Bukti Tindak Lanjut Anda DITERIMA',
                'Selamat! Bukti yang Anda unggah telah diverifikasi dan diterima oleh Inspektorat. Rekomendasi ini kini berstatus **SESUAI**.',
                'success',
            ],
            'tdt' => [
                "[SIPANDA] ℹ️ Rekomendasi Ditetapkan TDT — SPT {$noSpt}",
                'ℹ️ Rekomendasi Ditetapkan Tidak Dapat Ditindaklanjuti (TDT)',
                'Rekomendasi ini telah ditetapkan dengan status **Tidak Dapat Ditindaklanjuti (TDT)** oleh Inspektorat.',
                'info',
            ],
            default => [  // 'ditolak'
                "[SIPANDA] ⚠️ Bukti Tindak Lanjut Memerlukan Revisi — SPT {$noSpt}",
                '⚠️ Bukti Tindak Lanjut Memerlukan Revisi',
                'Bukti yang Anda unggah belum memenuhi persyaratan. Silakan perbaiki dan unggah ulang sesuai catatan dari Inspektorat.',
                'warning',
            ],
        };

        $mail = (new MailMessage)
            ->subject($subjek)
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line($headline)
            ->line('')
            ->line("**No. SPT:** {$noSpt}");

        if ($this->bukti->catatan_verifikasi) {
            $mail->line("**Catatan Inspektorat:** {$this->bukti->catatan_verifikasi}");
        }

        return $mail
            ->line('')
            ->action('Lihat Detail Rekomendasi', route('opd.tindak-lanjut.show', $this->bukti->tindak_lanjut_id))
            ->line($keterangan)
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $noSpt      = $this->bukti->tindakLanjut?->penugasan?->no_spt ?? '-';
        $noLhp      = $this->bukti->tindakLanjut?->no_lhp ?: $noSpt;
        $url        = route('opd.tindak-lanjut.show', $this->bukti->tindak_lanjut_id);
        $statusText = match($this->statusVerifikasi) {
            'diterima' => '✅ DITERIMA (SESUAI)',
            'tdt'      => 'ℹ️ DITETAPKAN TDT',
            default    => '⚠️ PERLU REVISI / PERBAIKAN',
        };

        $msg = "📑 *HASIL VERIFIKASI BUKTI TLHP — SIPANDA*\n\n" .
               "Yth. Bpk/Ibu *{$namaSapaan}*,\n" .
               "Bukti tindak lanjut yang Anda unggah telah diverifikasi oleh Tim Auditor Inspektorat:\n\n" .
               "📄 *No. Dokumen:* {$noLhp}\n" .
               "📊 *Hasil Verifikasi:* *{$statusText}*\n";

        if ($this->bukti->catatan_verifikasi) {
            $msg .= "💬 *Catatan Auditor:* {$this->bukti->catatan_verifikasi}\n";
        }

        $msg .= "\nSilakan cek detail pada portal OPD:\n" .
                "🔗 {$url}\n\n" .
                "_Inspektorat Kabupaten Trenggalek_";

        return $msg;
    }
}
