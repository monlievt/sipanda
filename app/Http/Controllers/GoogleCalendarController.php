<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function __construct(
        protected GoogleCalendarService $calendarService
    ) {}

    /**
     * Alihkan ke Google OAuth Consent Screen.
     */
    public function connect(): RedirectResponse
    {
        return redirect()->away($this->calendarService->getAuthUrl());
    }

    /**
     * Terima callback OAuth dari Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $code = $request->input('code');

        if (! $code) {
            return redirect()->route('profile.edit')->with('error', 'Otorisasi Google Calendar dibatalkan.');
        }

        $success = $this->calendarService->handleCallback($code, auth()->user());

        if ($success) {
            return redirect()->route('profile.edit')->with('status', '✓ Akun Google Calendar berhasil ditautkan ke SIPANDA.');
        }

        return redirect()->route('profile.edit')->with('error', 'Gagal menghubungkan Google Calendar. Periksa kredensial OAuth.');
    }

    /**
     * Putuskan tautan Google Calendar.
     */
    public function disconnect(): RedirectResponse
    {
        $user = auth()->user();
        $user->update([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
        ]);

        return redirect()->route('profile.edit')->with('status', 'Tautan Google Calendar telah diputus.');
    }

    /**
     * Sinkronkan satu penugasan ke Google Calendar pengguna.
     */
    public function syncPenugasan(Penugasan $penugasan): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->google_access_token) {
            return back()->with('error', 'Silakan tautkan akun Google Calendar terlebih dahulu pada menu Profil Pengguna.');
        }

        $success = $this->calendarService->syncPenugasanEvent($penugasan, $user);

        if ($success) {
            return back()->with('status', '✓ Jadwal penugasan berhasil disinkronkan ke Google Calendar Anda.');
        }

        return back()->with('error', 'Gagal menyinkronkan jadwal ke Google Calendar. Coba hubungkan ulang akun.');
    }
}
