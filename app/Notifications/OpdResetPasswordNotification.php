<?php

namespace App\Notifications;

use App\Broadcasting\WhatsAppChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OpdResetPasswordNotification extends Notification
{
    /**
     * @param string $token Token reset password OPD
     */
    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', WhatsAppChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('opd.password.reset', ['token' => $this->token, 'email' => $notifiable->email]);
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu PIC OPD';

        return (new MailMessage)
            ->subject('[SIPANDA] Permohonan Reset Kata Sandi Portal OPD')
            ->greeting("Yth. Bapak/Ibu {$namaSapaan},")
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Portal OPD Anda di SIPANDA.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Tautan reset kata sandi ini berlaku selama 60 menit.')
            ->line('Jika Anda tidak merasa mengajukan permintaan ini, silakan abaikan email ini. Akun Anda tetap aman.')
            ->salutation('Hormat kami, SIPANDA — Inspektorat Kabupaten Trenggalek');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $namaSapaan = $notifiable->nama_tanpa_gelar ?? $notifiable->nama_display ?? $notifiable->name ?? 'Bapak/Ibu';
        $url        = route('opd.password.reset', ['token' => $this->token, 'email' => $notifiable->email]);

        return "🔑 *PERMINTAAN RESET KATA SANDI PORTAL OPD — SIPANDA*\n\n" .
               "Halo Bpk/Ibu *{$namaSapaan}*,\n" .
               "Kami menerima permintaan untuk mengatur ulang kata sandi akun Portal OPD SIPANDA Anda.\n\n" .
               "Klik tautan berikut untuk membuat kata sandi baru (berlaku 60 menit):\n" .
               "🔗 {$url}\n\n" .
               "_Jika Anda tidak meminta reset sandi, abaikan pesan ini. Akun Anda tetap aman._\n" .
               "_SIPANDA — Inspektorat Kabupaten Trenggalek_";
    }
}
