<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SINGAWASI') }} | Inspektorat Kabupaten Trenggalek</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
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
                {{ $slot }}
            </div>
        </main>

        <!-- Global UAT Feedback & Bug Report Widget -->
        <x-uat-feedback-widget />
    </body>
</html>
