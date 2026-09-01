<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Konsultasi;
use App\Models\KonsultasiChat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from WAHA WhatsApp Gateway.
     */
    public function handle(Request $request): JsonResponse
    {
        $event = $request->input('event');
        $payload = $request->input('payload', []);

        // Hanya proses event pesan masuk (message / message.upsert)
        if (! in_array($event, ['message', 'message.upsert', 'message.any'])) {
            return response()->json(['status' => 'ignored', 'event' => $event]);
        }

        // Abaikan pesan yang dikirim oleh bot sendiri
        $fromMe = $payload['fromMe'] ?? false;
        if ($fromMe) {
            return response()->json(['status' => 'ignored_self_message']);
        }

        $from = $payload['from'] ?? null; // format: 6281234567890@c.us
        $body = trim($payload['body'] ?? '');

        if (! $from || empty($body)) {
            return response()->json(['status' => 'empty_payload']);
        }

        // Ekstrak angka murni dari nomor pengirim
        $cleanPhone = preg_replace('/[^\d]/', '', explode('@', $from)[0]);
        $localPhone = str_starts_with($cleanPhone, '62') ? ('0' . substr($cleanPhone, 2)) : $cleanPhone;

        Log::info("[SIPANDA WA Webhook] Pesan masuk dari {$cleanPhone} ({$localPhone}): {$body}");

        // Cari pengguna berdasarkan nomor telepon / no_hp
        $user = User::where('no_hp', 'like', "%{$cleanPhone}%")
            ->orWhere('no_hp', 'like', "%{$localPhone}%")
            ->orWhere('telepon', 'like', "%{$cleanPhone}%")
            ->orWhere('telepon', 'like', "%{$localPhone}%")
            ->first();

        if ($user) {
            // Cek apakah pengguna memiliki thread konsultasi aktif yang sedang berjalan
            $konsultasiAktif = Konsultasi::where('user_id', $user->id)
                ->whereIn('status', ['proses', 'menunggu_disposisi'])
                ->latest()
                ->first();

            if ($konsultasiAktif) {
                KonsultasiChat::create([
                    'konsultasi_id' => $konsultasiAktif->id,
                    'user_id'       => $user->id,
                    'pesan'         => "[WhatsApp] " . $body,
                ]);

                $konsultasiAktif->touch();

                Log::info("[SIPANDA WA Webhook] Pesan WhatsApp diteruskan ke Thread Konsultasi #{$konsultasiAktif->id}");
            }

            ActivityLog::catat('whatsapp_inbound', $user->id, 'receive', null, [
                'from'    => $cleanPhone,
                'message' => $body,
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
