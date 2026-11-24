<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal OPD | SINGAWASI Inspektorat Trenggalek</title>

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-brand-ink">Portal Perangkat Daerah (OPD)</p>
                        <h1 class="text-xl font-bold text-ink tracking-tight leading-tight">Tindak Lanjut Pengawasan</h1>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-ink-secondary leading-relaxed">
                    Akses khusus PIC Perangkat Daerah untuk merespons dan mengunggah bukti tindak lanjut rekomendasi LHP.
                </p>
            </div>

            @if ($errors->any())
                <div role="alert" class="mb-4 p-3.5 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-[8px] text-red-800 dark:text-red-200 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div role="status" class="mb-4 p-3.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[8px] text-emerald-900 dark:text-emerald-200 text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('opd.login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs sm:text-sm font-semibold text-ink mb-1">Email Resmi OPD</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full"
                        placeholder="dinas@trenggalekkab.go.id">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs sm:text-sm font-semibold text-ink">Kata Sandi</label>
                        <a href="{{ route('opd.password.request') }}" class="text-xs font-semibold text-brand-ink hover:underline">
                            Lupa kata sandi?
                        </a>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full"
                        placeholder="Masukkan kata sandi">
                </div>

                <div class="pt-1">
                    <label for="remember" class="inline-flex items-center gap-2">
                        <input id="remember" type="checkbox" name="remember" class="rounded-[4px] border-hairline-strong text-brand focus:ring-brand">
                        <span class="text-xs sm:text-sm text-ink-secondary font-medium">Ingat sesi ini</span>
                    </label>
                </div>

                <div class="pt-1">
                    <button type="submit" class="ledger-btn-primary w-full min-h-[44px]">
                        Masuk ke Portal OPD
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-hairline text-center">
                <p class="text-xs text-ink-muted">Staf Inspektorat atau Auditor APIP?</p>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center min-h-[36px] mt-1 text-xs sm:text-sm font-semibold text-brand-ink hover:underline">
                    Masuk ke Aplikasi Internal SINGAWASI
                </a>
            </div>
        </div>
    </main>
</body>
</html>
