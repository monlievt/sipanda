<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use App\Models\Pkppt;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PkpptStatusNotification extends Notification
{
    /**
     * @param Pkppt  $pkppt
     * @param string $aksi 'diusulkan' | 'direviu' | 'ditetapkan'
     * @param string $pelakuNama
     */
    public function __construct(
        public readonly Pkppt $pkppt,
        public readonly string $aksi,
        public readonly string $pelakuNama = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $statusText = strtoupper($this->aksi);

        return (new MailMessage)
            ->subject("[SIPANDA] Pembaruan Status PKPT {$this->pkppt->tahun}: {$statusText}")
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line("Draf Perencanaan PKPT Tahun **{$this->pkppt->tahun}** (Versi {$this->pkppt->versi_revisi}) telah diperbarui statusnya menjadi **{$statusText}** oleh {$this->pelakuNama}.")
            ->line('')
            ->line("**Area Pengawasan  :** {$this->pkppt->area_pengawasan}")
            ->line("**Jenis Pengawasan :** {$this->pkppt->jenis_pengawasan}")
            ->line('')
            ->action('Lihat Draf PKPT', route('pkppt.index', ['tahun' => $this->pkppt->tahun]))
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $statusText = match($this->aksi) {
            'diusulkan'  => '📥 DIUSULKAN KE SEKRETARIAT',
            'direviu'    => '🔍 TELAH DIREVIU (MENUNGGU PENETAPAN)',
            'ditetapkan' => '✅ TELAH DITETAPKAN OLEH INSPEKTUR',
            default      => strtoupper($this->aksi),
        };
        $url = route('pkppt.index', ['tahun' => $this->pkppt->tahun]);

        return "🏛️ *STATUS PERENCANAAN PKPT — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Draf PKPT Tahun *{$this->pkppt->tahun}* (v{$this->pkppt->versi_revisi}):\n\n" .
               "📌 *Area:* {$this->pkppt->area_pengawasan}\n" .
               "📊 *Status:* *{$statusText}*\n" .
               "👤 *Oleh:* {$this->pelakuNama}\n\n" .
               "Tinjau detail PKPT pada tautan:\n" .
               "🔗 {$url}\n\n" .
               "_Inspektorat Kabupaten Trenggalek_";
    }
}
