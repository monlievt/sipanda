<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Kata Sandi | Portal OPD SINGAWASI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleThemeMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex flex-col">
    <header class="w-full max-w-md mx-auto px-4 pt-6 flex items-center justify-between">
        <a href="{{ route('opd.login') }}" class="text-xs font-semibold text-ink-secondary hover:text-ink transition-colors">
            Kembali ke Halaman Masuk
        </a>
        <button type="button"
                onclick="toggleThemeMode()"
                aria-label="Ganti mode terang atau gelap"
                title="Ganti Mode Terang / Gelap"
                class="inline-flex items-center justify-center min-h-[40px] min-w-[40px] text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface border border-transparent hover:border-hairline transition-colors">
            <svg class="w-[18px] h-[18px] block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg class="w-[18px] h-[18px] hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md bg-surface rounded-[10px] p-6 sm:p-8 border border-hairline">
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-brand text-white rounded-[6px] flex items-center justify-center shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <span class="inline-block px-2 py-0.5 bg-brand-soft text-brand-ink border border-brand-border text-[11px] font-semibold rounded-[4px] uppercase tracking-wide">Kata Sandi Baru</span>
                        <h1 class="text-xl font-bold text-ink tracking-tight leading-tight mt-1">Atur Ulang Kata Sandi</h1>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-ink-secondary leading-relaxed">
                    Silakan masukkan kata sandi baru untuk akun Portal OPD Anda.
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="mb-4 p-3.5 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-[6px] text-red-800 dark:text-red-200 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('opd.password.update') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-xs sm:text-sm font-semibold text-ink mb-1">Email Resmi OPD</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"
                        class="w-full"
                        placeholder="dinas@trenggalek.go.id">
                </div>

                <div>
                    <label for="password" class="block text-xs sm:text-sm font-semibold text-ink mb-1">Kata Sandi Baru</label>
                    <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                        class="w-full"
                        placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-semibold text-ink mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full"
                        placeholder="Ulangi kata sandi baru">
                </div>

                <div class="pt-1">
                    <button type="submit" class="ledger-btn-primary w-full min-h-[44px]">
                        Simpan Kata Sandi Baru
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-hairline text-center">
                <a href="{{ route('opd.login') }}" class="inline-flex items-center justify-center min-h-[36px] text-xs sm:text-sm font-semibold text-brand-ink hover:underline">
                    Kembali ke Halaman Masuk
                </a>
            </div>
        </div>
    </main>
</body>
</html>
