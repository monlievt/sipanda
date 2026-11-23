<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aktivasi Akun Portal OPD | SINGAWASI</title>

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
        <a href="{{ route('welcome') }}" class="text-xs font-semibold text-ink-secondary hover:text-ink transition-colors">
            Kembali ke Beranda
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-brand-ink">Portal Perangkat Daerah (OPD)</p>
                        <h1 class="text-xl font-bold text-ink tracking-tight leading-tight">Aktivasi Akun Portal OPD</h1>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-ink-secondary leading-relaxed">
                    Halo <strong class="text-ink">{{ $user->nama }}</strong> ({{ $user->email }}), buat kata sandi untuk mengaktifkan akun OPD Anda.
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="mb-4 p-3.5 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-[6px] text-red-800 dark:text-red-200 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('opd.undangan.store', $token) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="block text-xs sm:text-sm font-semibold text-ink mb-1">Kata Sandi Baru</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                        class="w-full"
                        placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-semibold text-ink mb-1">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                        class="w-full"
                        placeholder="Ketik ulang kata sandi">
                </div>

                <div class="pt-1">
                    <button type="submit" class="ledger-btn-primary w-full min-h-[44px]">
                        Aktifkan & Masuk
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
