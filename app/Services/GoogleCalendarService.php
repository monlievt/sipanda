<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CalendarEvent;
use App\Models\Penugasan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID', ''));
        $this->clientSecret = config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET', ''));
        $this->redirectUri = config('services.google.redirect', env('GOOGLE_REDIRECT_URI', url('/google-calendar/callback')));
    }

    /**
     * URL Oauth Consent Google Calendar.
     */
    public function getAuthUrl(): string
    {
        $params = [
            'client_id'       => $this->clientId,
            'redirect_uri'    => $this->redirectUri,
            'response_type'   => 'code',
            'scope'           => 'https://www.googleapis.com/auth/calendar.events',
            'access_type'     => 'offline',
            'prompt'          => 'consent select_account',
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Handle OAuth Callback & Simpan Token ke User.
     */
    public function handleCallback(string $code, User $user): bool
    {
        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code'          => $code,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri'  => $this->redirectUri,
                'grant_type'    => 'authorization_code',
            ]);

            if ($response->failed()) {
                Log::error('[SIPANDA Google OAuth] Gagal tukar token:', $response->json() ?? []);
                return false;
            }

            $data = $response->json();
            $expiresAt = now()->addSeconds($data['expires_in'] ?? 3600);

            $user->update([
                'google_access_token'     => $data['access_token'] ?? null,
                'google_refresh_token'    => $data['refresh_token'] ?? $user->google_refresh_token,
                'google_token_expires_at' => $expiresAt,
            ]);

            ActivityLog::catat('users', $user->id, 'update', null, ['action' => 'connect_google_calendar']);
            return true;

        } catch (\Throwable $e) {
            Log::error('[SIPANDA Google OAuth] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh access token jika sudah kadaluarsa.
     */
    protected function ensureFreshToken(User $user): ?string
    {
        if (! $user->google_refresh_token) {
            return $user->google_access_token;
        }

        if ($user->google_token_expires_at && $user->google_token_expires_at->gt(now()->addMinutes(5))) {
            return $user->google_access_token;
        }

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $user->google_refresh_token,
                'grant_type'    => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user->update([
                    'google_access_token'     => $data['access_token'],
                    'google_token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
                ]);
                return $data['access_token'];
            }
        } catch (\Throwable $e) {
            Log::error('[SIPANDA Google Token Refresh] Gagal: ' . $e->getMessage());
        }

        return $user->google_access_token;
    }

    /**
     * Sinkronisasi Event Penugasan ke Google Calendar.
     */
    public function syncPenugasanEvent(Penugasan $penugasan, User $user): bool
    {
        $token = $this->ensureFreshToken($user);
        if (! $token) {
            return false;
        }

        $calEvent = CalendarEvent::firstOrNew([
            'penugasan_id' => $penugasan->id,
            'user_id'      => $user->id,
        ]);

        $eventData = [
            'summary'     => "[SIPANDA] {$penugasan->no_spt} - {$penugasan->uraian_penugasan}",
            'description' => "Penugasan Inspektorat Trenggalek\nSPT: {$penugasan->no_spt}\nIrban: " . ($penugasan->irban?->nama_irban ?? '-') . "\nObjek: " . $penugasan->objekPenugasan->pluck('nama')->implode(', '),
            'start'       => [
                'date' => $penugasan->tanggal_mulai->format('Y-m-d'),
            ],
            'end'         => [
                'date' => $penugasan->tanggal_selesai->addDay()->format('Y-m-d'),
            ],
        ];

        try {
            if ($calEvent->google_event_id) {
                // Update existing event
                $res = Http::withToken($token)->put(
                    "https://www.googleapis.com/calendar/v3/calendars/primary/events/{$calEvent->google_event_id}",
                    $eventData
                );
            } else {
                // Create new event
                $res = Http::withToken($token)->post(
                    'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                    $eventData
                );
            }

            if ($res->successful()) {
                $respData = $res->json();
                $calEvent->google_event_id = $respData['id'] ?? $calEvent->google_event_id;
                $calEvent->tanggal_mulai   = $penugasan->tanggal_mulai;
                $calEvent->tanggal_selesai = $penugasan->tanggal_selesai;
                $calEvent->status_sinkron  = 'tersinkron';
                $calEvent->save();
                return true;
            }

            $calEvent->status_sinkron = 'gagal';
            $calEvent->save();
            return false;

        } catch (\Throwable $e) {
            Log::error('[SIPANDA Google Calendar Sync] Gagal: ' . $e->getMessage());
            return false;
        }
    }
}
