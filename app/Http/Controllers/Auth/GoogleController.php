<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Redirect pengguna ke halaman Google OAuth.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google OAuth.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan otentikasi dengan Google: ' . $e->getMessage(),
            ]);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Email (' . $googleUser->getEmail() . ') belum terdaftar di SIPANDA. Silakan hubungi Sekretariat.',
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Sekretariat.',
            ]);
        }

        // Update google_id jika belum ada
        if (empty($user->google_id)) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        // Login user
        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
