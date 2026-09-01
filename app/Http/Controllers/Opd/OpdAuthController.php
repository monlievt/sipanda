<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\OpdResetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
     * Tampilkan form permohonan reset kata sandi OPD.
     */
    public function showForgotPassword(): View
    {
        return view('opd.auth.forgot-password');
    }

    /**
     * Kirim email tautan reset kata sandi ke akun OPD.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::whereRaw('LOWER(email) = ?', [$email])
            ->where('tipe_akun', 'opd')
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tersebut tidak terdaftar sebagai akun PIC OPD.'])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Akun OPD ini sedang dinonaktifkan. Silakan hubungi Inspektorat.'])->withInput();
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]
        );

        try {
            $user->notify(new OpdResetPasswordNotification($token));
        } catch (\Throwable $e) {
            Log::warning("[SIPANDA OPD Reset Password] Gagal kirim email reset password ke {$email}: " . $e->getMessage());
        }

        return back()->with('status', 'Tautan untuk mengatur ulang kata sandi telah dikirimkan ke email Anda.');
    }

    /**
     * Tampilkan form ubah kata sandi dengan token reset.
     */
    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->input('email');
        return view('opd.auth.reset-password', compact('token', 'email'));
    }

    /**
     * Simpan kata sandi baru hasil reset.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower(trim($request->email));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return back()->withErrors(['email' => 'Tautan reset kata sandi tidak valid atau telah kedaluwarsa.'])->withInput();
        }

        // Kedaluwarsa dalam 60 menit
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'Tautan reset kata sandi sudah kedaluwarsa (> 60 menit). Silakan ajukan ulang.'])->withInput();
        }

        if (! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset kata sandi tidak cocok.'])->withInput();
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])
            ->where('tipe_akun', 'opd')
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Pengguna tidak ditemukan.'])->withInput();
        }

        $sebelum = $user->toArray();
        $user->update([
            'password'        => Hash::make($request->password),
            'status_undangan' => 'aktif',
            'is_active'       => true,
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        ActivityLog::catat('users', $user->id, 'update', $sebelum, $user->toArray());

        return redirect()->route('opd.login')->with('status', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
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

