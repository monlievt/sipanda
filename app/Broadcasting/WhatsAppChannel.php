<?php

namespace App\Broadcasting;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function __construct(
        protected WhatsAppService $whatsappService
    ) {}

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // Cari nomor WhatsApp dari notifiable
        $to = $notifiable->routeNotificationFor('whatsapp', $notification)
            ?? $notifiable->no_hp
            ?? $notifiable->telepon
            ?? $notifiable->nomor_wa
            ?? null;

        if (! $to) {
            Log::info("[SIPANDA WhatsAppChannel] Pengguna ID {$notifiable->id} tidak memiliki nomor telepon/WA.");
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        if (! empty($message)) {
            $this->whatsappService->sendText($to, $message);
        }
    }
}
