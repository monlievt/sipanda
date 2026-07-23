<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OpdAuthController extends Controller
{
    /**
     * Tampilkan halaman login khusus OPD.
     */
    public function showLogin(): View
    {
        return view('opd.auth.login');
    }

    /**
     * Proses login OPD (guard 'opd').
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email    = trim($request->email);
        $password = trim($request->password);

        $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->where('tipe_akun', 'opd')
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Akun OPD dengan email (' . $email . ') tidak ditemukan.'])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Akun OPD ini sedang dinonaktifkan.'])->withInput();
        }

        if ($user->status_undangan === 'pending') {
            return back()->withErrors(['email' => 'Akun Anda belum diaktifkan. Silakan periksa link undangan dari email Anda.'])->withInput();
        }

        if (Hash::check($password, $user->password)) {
            Auth::guard('opd')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('opd.dashboard'));
        }

        return back()->withErrors(['email' => 'Kata sandi yang Anda masukkan salah.'])->withInput();
    }

    /**
     * Halaman set password pertama kali lewat token undangan.
     */
    public function showSetPassword(string $token): View|RedirectResponse
    {
        $user = User::where('token_undangan', $token)
            ->where('tipe_akun', 'opd')
            ->first();

        if (! $user || ! $user->token_masih_berlaku) {
            return redirect()->route('opd.login')->withErrors(['email' => 'Link undangan tidak valid atau sudah kedaluwarsa. Silakan hubungi Inspektorat.']);
        }

        return view('opd.auth.set-password', compact('user', 'token'));
    }

    /**
     * Simpan password baru dan aktifkan akun OPD.
     */
    public function storePassword(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('token_undangan', $token)
            ->where('tipe_akun', 'opd')
            ->first();

        if (! $user || ! $user->token_masih_berlaku) {
            return redirect()->route('opd.login')->withErrors(['email' => 'Link undangan tidak valid atau sudah kedaluwarsa.']);
        }

        $user->update([
            'password'           => Hash::make($request->password),
            'status_undangan'    => 'aktif',
            'token_undangan'     => null,
            'token_kedaluwarsa'  => null,
            'is_active'          => true,
            'email_verified_at'  => now(),
        ]);

        Auth::guard('opd')->login($user);

        return redirect()->route('opd.dashboard')->with('status', 'Akun OPD berhasil diaktifkan! Selamat datang di Portal OPD.');
    }

    /**
     * Logout OPD.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('opd')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('opd.login');
    }
}
