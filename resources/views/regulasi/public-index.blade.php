<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Regulasi & Pedoman Pengawasan | SINGAWASI Inspektorat Kab. Trenggalek</title>

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
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand focus:text-white focus:rounded-[6px] focus:text-sm focus:font-semibold">
        Lewati ke konten utama
    </a>

    <!-- Header Navbar -->
    <header x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false" class="border-b border-hairline bg-surface sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <a href="/" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-[6px] bg-brand flex items-center justify-center text-white shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="font-bold text-base tracking-tight text-ink block leading-tight">SINGAWASI</span>
                        <span class="text-[11px] text-ink-muted font-medium block truncate">Inspektorat Daerah Kabupaten Trenggalek</span>
                    </div>
                </a>

                <nav aria-label="Navigasi utama" class="hidden md:flex items-center gap-2">
                    <a href="/" class="inline-flex items-center min-h-[40px] px-3 text-sm font-medium text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle transition-colors">
                        Dashboard Publik
                    </a>
                    <a href="{{ route('faq.index') }}" class="inline-flex items-center min-h-[40px] px-3 text-sm font-medium text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle transition-colors">
                        Bank FAQ & QnA
                    </a>
                    <a href="{{ route('regulasi.public.index') }}" aria-current="page" class="inline-flex items-center min-h-[40px] px-3 text-sm font-semibold text-brand-ink bg-brand-soft border border-brand-border rounded-[6px] transition-colors">
                        Pusat Regulasi
                    </a>

                    <button type="button"
                            onclick="toggleThemeMode()"
                            aria-label="Ganti mode terang atau gelap"
                            title="Ganti Mode Terang / Gelap"
                            class="inline-flex items-center justify-center min-h-[40px] min-w-[40px] text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle border border-transparent hover:border-hairline transition-colors">
                        <svg class="w-[18px] h-[18px] block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg class="w-[18px] h-[18px] hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <a href="/login" class="ledger-btn-primary">
                        Login Portal
                    </a>
                </nav>

                <!-- Mobile Controls -->
                <div class="flex md:hidden items-center gap-2 shrink-0">
                    <button type="button"
                            onclick="toggleThemeMode()"
                            aria-label="Ganti mode terang atau gelap"
                            class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] text-ink-secondary hover:text-ink rounded-[6px] border border-hairline">
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg class="w-5 h-5 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            :aria-expanded="mobileOpen.toString()"
                            aria-controls="regulasi-mobile-nav"
                            class="inline-flex items-center gap-1.5 min-h-[44px] px-3 text-xs font-semibold text-ink rounded-[6px] border border-hairline bg-surface hover:bg-surface-subtle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Menu</span>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Menu -->
            <nav id="regulasi-mobile-nav" x-show="mobileOpen" style="display: none;" aria-label="Navigasi seluler" class="md:hidden py-4 border-t border-hairline space-y-2">
                <a href="/" class="flex items-center min-h-[44px] px-3 rounded-[6px] text-sm font-medium text-ink hover:bg-surface-subtle">
                    Dashboard Publik
                </a>
                <a href="{{ route('faq.index') }}" class="flex items-center min-h-[44px] px-3 rounded-[6px] text-sm font-medium text-ink hover:bg-surface-subtle">
                    Bank FAQ & QnA
                </a>
                <a href="/login" class="ledger-btn-primary w-full min-h-[44px]">
                    Login Portal
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="flex-1 w-full">
        <!-- Hero Section -->
        <section class="py-10 sm:py-12 bg-surface border-b border-hairline">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center space-y-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] text-xs font-semibold bg-brand-soft text-brand-ink border border-brand-border">
                    Jaringan Dokumentasi & Informasi Regulasi Pengawasan
                </span>
                <h1 class="text-2xl sm:text-4xl font-bold text-ink tracking-[-0.025em]">
                    Pusat Regulasi, Juknis & Standar Biaya APIP
                </h1>
                <p class="text-sm sm:text-base text-ink-secondary max-w-2xl mx-auto leading-relaxed">
                    Kumpulan peraturan perundang-undangan, Perbup Trenggalek, pedoman teknis pengadaan, dan petunjuk operasional pengawasan keuangan daerah yang dapat diunduh bebas.
                </p>

                <!-- Search Bar -->
                <form method="GET" action="{{ route('regulasi.public.index') }}" class="pt-3 max-w-2xl mx-auto text-left">
                    <label for="regulasi-search" class="block text-xs font-semibold text-ink mb-1.5">Cari Dokumen Regulasi</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input id="regulasi-search" type="text" name="search" value="{{ $search }}" placeholder="Cari nomor perbup, kata kunci, judul regulasi..." class="flex-1 w-full">
                        <button type="submit" class="ledger-btn-primary min-h-[42px] px-6 shrink-0">
                            Cari Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <!-- Kategori Tabs -->
            <nav aria-label="Kategori regulasi" class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 text-xs font-semibold">
                <a href="{{ route('regulasi.public.index') }}" class="inline-flex items-center min-h-[40px] px-4 rounded-[6px] border transition-colors whitespace-nowrap {{ !$kategori ? 'bg-brand text-white border-brand' : 'bg-surface text-ink-secondary border-hairline hover:bg-surface-subtle hover:text-ink' }}">
                    Semua Kategori
                </a>
                <a href="{{ route('regulasi.public.index', ['kategori' => 'keuangan']) }}" class="inline-flex items-center min-h-[40px] px-4 rounded-[6px] border transition-colors whitespace-nowrap {{ $kategori === 'keuangan' ? 'bg-brand text-white border-brand' : 'bg-surface text-ink-secondary border-hairline hover:bg-surface-subtle hover:text-ink' }}">
                    Keuangan & Anggaran
                </a>
                <a href="{{ route('regulasi.public.index', ['kategori' => 'pbj']) }}" class="inline-flex items-center min-h-[40px] px-4 rounded-[6px] border transition-colors whitespace-nowrap {{ $kategori === 'pbj' ? 'bg-brand text-white border-brand' : 'bg-surface text-ink-secondary border-hairline hover:bg-surface-subtle hover:text-ink' }}">
                    Pengadaan Barang & Jasa (PBJ)
                </a>
                <a href="{{ route('regulasi.public.index', ['kategori' => 'desa']) }}" class="inline-flex items-center min-h-[40px] px-4 rounded-[6px] border transition-colors whitespace-nowrap {{ $kategori === 'desa' ? 'bg-brand text-white border-brand' : 'bg-surface text-ink-secondary border-hairline hover:bg-surface-subtle hover:text-ink' }}">
                    Dana & Keuangan Desa
                </a>
                <a href="{{ route('regulasi.public.index', ['kategori' => 'aset']) }}" class="inline-flex items-center min-h-[40px] px-4 rounded-[6px] border transition-colors whitespace-nowrap {{ $kategori === 'aset' ? 'bg-brand text-white border-brand' : 'bg-surface text-ink-secondary border-hairline hover:bg-surface-subtle hover:text-ink' }}">
                    Aset & BMD
                </a>
                <a href="{{ route('regulasi.public.index', ['kategori' => 'kepegawaian']) }}" class="inline-flex items-center min-h-[40px] px-4 rounded-[6px] border transition-colors whitespace-nowrap {{ $kategori === 'kepegawaian' ? 'bg-brand text-white border-brand' : 'bg-surface text-ink-secondary border-hairline hover:bg-surface-subtle hover:text-ink' }}">
                    Disiplin ASN & Etika
                </a>
            </nav>

            <!-- Grid Dokumen -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($regulasiList as $reg)
                    <article class="bg-surface border border-hairline rounded-[10px] p-5 flex flex-col justify-between transition-colors hover:border-hairline-strong">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase tracking-wide bg-brand-soft text-brand-ink border border-brand-border tabular-nums">
                                    {{ $reg->jenis_regulasi_label }} | {{ $reg->tahun }}
                                </span>
                                <span class="text-xs text-ink-muted font-medium">
                                    {{ ucfirst($reg->kategori) }}
                                </span>
                            </div>

                            <h2 class="font-bold text-ink text-sm sm:text-base leading-snug mb-1">
                                {{ $reg->nomor_regulasi }}
                            </h2>
                            <p class="text-xs sm:text-sm font-medium text-ink-secondary mb-2.5">
                                {{ $reg->judul }}
                            </p>

                            @if($reg->ringkasan_eksekutif)
                                <p class="text-xs text-ink-muted line-clamp-3 leading-relaxed mb-4">
                                    {{ $reg->ringkasan_eksekutif }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-hairline flex items-center justify-between gap-2">
                            <span class="text-xs text-ink-muted tabular-nums">
                                {{ $reg->diunduh_count }}x diunduh | {{ $reg->ukuran_kb ?: 'PDF' }}
                            </span>
                            @if($reg->file_path)
                                <a href="{{ route('regulasi.public.download', $reg->id) }}" class="ledger-btn-primary text-xs min-h-[36px] px-3 py-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh PDF</span>
                                </a>
                            @else
                                <span class="text-xs text-ink-muted italic">Dokumen Teks</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-12 px-4 bg-surface border border-hairline rounded-[10px] text-center">
                        <p class="text-base font-semibold text-ink">Belum ada dokumen regulasi yang ditemukan.</p>
                        <p class="text-xs text-ink-muted mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $regulasiList->links() }}
            </div>
        </div>
    </main>

    <footer class="mt-auto border-t border-hairline bg-surface py-8 text-center text-xs text-ink-muted">
        <p>&copy; {{ date('Y') }} Inspektorat Kabupaten Trenggalek | Sistem Informasi PeNGAWAsan TerintegraSI (SINGAWASI)</p>
    </footer>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />

    <!-- Global PDF & Document Preview Modal -->
    <x-pdf-preview-modal />
</body>
</html>
