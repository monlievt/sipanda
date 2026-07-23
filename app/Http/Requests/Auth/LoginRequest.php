<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Normalisasi input: trim & lower
        $rawInput   = trim($this->input('email'));
        $password   = trim($this->input('password'));
        $isEmail    = filter_var($rawInput, FILTER_VALIDATE_EMAIL);
        $field      = $isEmail ? 'email' : 'nip';

        // Cari user case-insensitive (LOWER)
        $user = User::where(function ($q) use ($field, $rawInput) {
            $q->whereRaw("LOWER({$field}) = ?", [strtolower($rawInput)])
              ->orWhere($field, $rawInput);
        })->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Email/NIP (' . $rawInput . ') belum terdaftar di sistem. Silakan periksa kembali.',
            ]);
        }

        if (! $user->is_active) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Akun ini sedang dinonaktifkan. Silakan hubungi Sekretariat.',
            ]);
        }

        // Attempt login dengan user ID / instance
        if (! Auth::loginUsingId($user->id, $this->boolean('remember')) || ! \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            // Logout jika hash check gagal
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
