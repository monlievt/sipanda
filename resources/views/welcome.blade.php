<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SINGAWASI | Sistem Informasi Pengawasan Terintegrasi Inspektorat Kabupaten Trenggalek</title>

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
<body class="min-h-screen font-sans antialiased bg-canvas text-ink flex flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand focus:text-white focus:rounded-[6px] focus:text-sm focus:font-semibold">
        Lewati ke konten utama
    </a>

    <!-- Top Navigation Bar -->
    <header x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false" class="border-b border-hairline bg-surface sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Logo Brand -->
                <a href="{{ route('welcome') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-[6px] bg-brand flex items-center justify-center text-white shrink-0" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="font-bold text-base text-ink tracking-tight block leading-tight">SINGAWASI</span>
                        <span class="text-[11px] text-ink-muted font-medium block truncate">Inspektorat Daerah Kabupaten Trenggalek</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav aria-label="Navigasi utama" class="hidden md:flex items-center gap-2.5">
                    <a href="{{ route('faq.index') }}" class="inline-flex items-center min-h-[40px] px-3 text-sm font-medium text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle transition-colors">
                        Bank FAQ Publik
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

                    @auth('web')
                        <a href="{{ route('dashboard') }}" class="ledger-btn-primary">
                            Buka Dashboard Internal
                        </a>
                    @elseauth('opd')
                        <a href="{{ route('opd.dashboard') }}" class="ledger-btn-primary">
                            Buka Portal OPD
                        </a>
                    @else
                        <a href="{{ route('opd.login') }}" class="ledger-btn-secondary">
                            Login Portal OPD
                        </a>
                        <a href="{{ route('login') }}" class="ledger-btn-primary">
                            Login Internal APIP
                        </a>
                    @endauth
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
                            aria-controls="public-mobile-nav"
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
            <nav id="public-mobile-nav" x-show="mobileOpen" style="display: none;" aria-label="Navigasi seluler" class="md:hidden py-4 border-t border-hairline space-y-2">
                <a href="{{ route('faq.index') }}" class="flex items-center min-h-[44px] px-3 rounded-[6px] text-sm font-medium text-ink hover:bg-surface-subtle">
                    Bank FAQ Publik
                </a>
                @auth('web')
                    <a href="{{ route('dashboard') }}" class="ledger-btn-primary w-full min-h-[44px]">
                        Buka Dashboard Internal
                    </a>
                @elseauth('opd')
                    <a href="{{ route('opd.dashboard') }}" class="ledger-btn-primary w-full min-h-[44px]">
                        Buka Portal OPD
                    </a>
                @else
                    <a href="{{ route('opd.login') }}" class="ledger-btn-secondary w-full min-h-[44px]">
                        Login Portal OPD
                    </a>
                    <a href="{{ route('login') }}" class="ledger-btn-primary w-full min-h-[44px]">
                        Login Internal APIP
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="main-content" class="flex-1">
        <!-- SECTION 1: HERO + REAL-TIME LEDGER SUMMARY (2-Column Institutional Split) -->
        <section class="border-b border-hairline py-12 sm:py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                    <!-- Left Narrative & Portal Access -->
                    <div class="lg:col-span-7 space-y-6">
                        <p class="text-xs font-semibold text-brand-ink tracking-wide uppercase">
                            Inspektorat Daerah Kabupaten Trenggalek
                        </p>

                        <h1 class="font-bold text-ink tracking-[-0.025em] leading-[1.12]" style="font-size: clamp(1.875rem, 3.4vw, 2.875rem);">
                            Sistem Informasi Pengawasan Terintegrasi (SINGAWASI)
                        </h1>

                        <p class="text-sm sm:text-base text-ink-secondary max-w-2xl leading-relaxed">
                            Portal resmi pengelolaan perencanaan PKPT, pelaksanaan penugasan pengawasan (Assurance dan Consulting), pemantauan matriks tindak lanjut rekomendasi LHP, serta layanan referensi advis APIP bagi Perangkat Daerah.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('login') }}" class="ledger-btn-primary min-h-[44px] px-5">
                                Akses Portal Internal APIP
                            </a>
                            <a href="{{ route('opd.login') }}" class="ledger-btn-secondary min-h-[44px] px-5">
                                Akses Portal OPD Eksternal
                            </a>
                            <a href="{{ route('faq.index') }}" class="inline-flex items-center min-h-[44px] px-2 text-sm font-semibold text-brand-ink hover:underline">
                                Buka Bank FAQ & QnA
                            </a>
                        </div>
                    </div>

                    <!-- Right Real-Time Supervisory Ledger Card -->
                    <div class="lg:col-span-5">
                        <div class="bg-surface border border-hairline rounded-[10px] overflow-hidden">
                            <div class="px-5 py-4 border-b border-hairline bg-surface-subtle flex items-center justify-between gap-2">
                                <div>
                                    <h2 class="text-sm font-bold text-ink">Ikhtisar Pengawasan Tahun {{ $tahun }}</h2>
                                    <p class="text-xs text-ink-muted mt-0.5">Rekapitulasi langsung dari basis data penugasan</p>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-[4px] bg-brand-soft text-brand-ink border border-brand-border tabular-nums shrink-0">
                                    TA {{ $tahun }}
                                </span>
                            </div>

                            <dl class="divide-y divide-hairline">
                                <div class="px-5 py-4 flex items-baseline justify-between gap-4">
                                    <div>
                                        <dt class="text-xs font-semibold text-ink-muted">Realisasi Pengawasan</dt>
                                        <dd class="text-xs text-ink-secondary mt-0.5 tabular-nums">Target PKPPT: {{ $totalPkppt }} laporan</dd>
                                    </div>
                                    <dd class="text-2xl font-bold text-ink tabular-nums">
                                        {{ $totalPenugasan }} <span class="text-xs font-medium text-ink-muted">SPT</span>
                                    </dd>
                                </div>

                                <div class="px-5 py-4 flex items-baseline justify-between gap-4">
                                    <div>
                                        <dt class="text-xs font-semibold text-ink-muted">Capaian Tindak Lanjut LHP</dt>
                                        <dd class="text-xs text-ink-secondary mt-0.5 tabular-nums">{{ $countSelesai }} dari {{ $totalRekomendasi }} rekomendasi selesai</dd>
                                    </div>
                                    <dd class="text-2xl font-bold text-brand-ink tabular-nums">
                                        {{ $persenSelesai }}%
                                    </dd>
                                </div>

                                <div class="px-5 py-4 flex items-baseline justify-between gap-4">
                                    <div>
                                        <dt class="text-xs font-semibold text-ink-muted">Nilai Anggaran Diawasi</dt>
                                        <dd class="text-xs text-ink-secondary mt-0.5">Cakupan APBD dan Dana Desa</dd>
                                    </div>
                                    <dd class="text-lg sm:text-xl font-bold text-ink tabular-nums text-right">
                                        Rp {{ number_format($totalNilaiDiawasi, 0, ',', '.') }}
                                    </dd>
                                </div>

                                <div class="px-5 py-4 flex items-baseline justify-between gap-4">
                                    <div>
                                        <dt class="text-xs font-semibold text-ink-muted">Status Pelaksanaan SPT</dt>
                                        <dd class="text-xs text-ink-secondary mt-0.5 tabular-nums">{{ $penugasanSelesai }} SPT selesai diperiksa</dd>
                                    </div>
                                    <dd class="text-lg font-bold text-amber-800 dark:text-amber-300 tabular-nums">
                                        {{ $penugasanBerjalan }} <span class="text-xs font-medium text-ink-muted">berjalan</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2: RUANG LINGKUP LAYANAN (Editorial Split + Hairline Rows) -->
        <section class="py-14 sm:py-16 border-b border-hairline bg-surface">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                    <div class="lg:col-span-4 space-y-2">
                        <h2 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">
                            Cakupan Layanan Pengawasan dan Consulting APIP
                        </h2>
                        <p class="text-sm text-ink-secondary leading-relaxed">
                            Seluruh tahapan pengawasan internal, verifikasi bukti perbaikan Perangkat Daerah, dan referensi konsultasi tercatat dalam satu alur kerja yang dapat diaudit.
                        </p>
                    </div>

                    <div class="lg:col-span-8 divide-y divide-hairline border-t border-b border-hairline">
                        <div class="py-5 sm:py-6 grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-6 items-start">
                            <div class="sm:col-span-4 flex items-center gap-2.5">
                                <span class="text-xs font-semibold text-ink-muted tabular-nums">01</span>
                                <h3 class="font-bold text-ink text-sm sm:text-base">Assurance (Penjaminan Mutu)</h3>
                            </div>
                            <p class="sm:col-span-8 text-sm text-ink-secondary leading-relaxed">
                                Pengawalan audit kinerja, audit keuangan, reviu LPPD dan LAKIP, evaluasi SPIP, serta pemantauan realisasi pengawasan tahunan berdasarkan PKPPT dan Surat Perintah Tugas (SPT).
                            </p>
                        </div>

                        <div class="py-5 sm:py-6 grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-6 items-start">
                            <div class="sm:col-span-4 flex items-center gap-2.5">
                                <span class="text-xs font-semibold text-ink-muted tabular-nums">02</span>
                                <h3 class="font-bold text-ink text-sm sm:text-base">Matriks Tindak Lanjut & Setoran</h3>
                            </div>
                            <p class="sm:col-span-8 text-sm text-ink-secondary leading-relaxed">
                                Pemantauan status rekomendasi LHP (Sesuai, Belum Sesuai, Belum Ditindaklanjuti, dan TDT), unggah serta verifikasi bukti perbaikan OPD secara dua arah, dan rekapitulasi penyetoran ke Kas Daerah.
                            </p>
                        </div>

                        <div class="py-5 sm:py-6 grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-6 items-start">
                            <div class="sm:col-span-4 space-y-1.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs font-semibold text-ink-muted tabular-nums">03</span>
                                    <h3 class="font-bold text-ink text-sm sm:text-base">Consulting & E-Consulting QnA</h3>
                                </div>
                                <span class="inline-block ml-6 text-[11px] font-semibold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 px-2 py-0.5 rounded-[4px]">
                                    Tahap Pengembangan
                                </span>
                            </div>
                            <p class="sm:col-span-8 text-sm text-ink-secondary leading-relaxed">
                                Layanan permohonan konsultasi daring maupun tatap muka, penunjukan tim advis APIP per permohonan OPD, ruang diskusi terstruktur, dan penerbitan Berita Acara resmi dalam format PDF.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 3: ARTIKEL QNA / FAQ PUBLIK -->
        @if(!empty($publicFaqs) && count($publicFaqs) > 0)
            <section class="py-14 sm:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-hairline pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-ink tracking-tight">Artikel QnA dan Advis Pengawasan Terbaru</h2>
                            <p class="text-sm text-ink-secondary mt-1">Topik konsultasi dan advis resmi Inspektorat yang dipublikasikan sebagai rujukan Perangkat Daerah.</p>
                        </div>

                        <a href="{{ route('faq.index') }}" class="text-sm font-semibold text-brand-ink hover:underline shrink-0">
                            Lihat seluruh Bank FAQ
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($publicFaqs as $faq)
                            <article class="p-5 bg-surface rounded-[10px] border border-hairline space-y-3 flex flex-col justify-between">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 font-semibold text-[11px] rounded-[4px]">
                                            {{ $faq->area_konsultasi }}
                                        </span>
                                        <time class="text-xs text-ink-muted font-medium tabular-nums">
                                            {{ !empty($faq->updated_at) ? $faq->updated_at->format('d/m/Y') : date('d/m/Y') }}
                                        </time>
                                    </div>

                                    <h3 class="font-bold text-ink text-sm sm:text-base leading-snug line-clamp-2">{{ $faq->judul_permasalahan }}</h3>
                                </div>

                                <div class="p-3.5 bg-surface-subtle rounded-[6px] border border-hairline text-xs space-y-1">
                                    <span class="font-semibold text-brand-ink block">Advis Resmi APIP</span>
                                    <p class="text-ink-secondary line-clamp-3 leading-relaxed whitespace-pre-line">{{ $faq->kesimpulan_advis }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

    <!-- FOOTER -->
    <footer class="mt-auto bg-surface border-t border-hairline py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs text-ink-muted">
            <div class="space-y-0.5">
                <span class="font-semibold text-ink block">Inspektorat Daerah Kabupaten Trenggalek</span>
                <span>Jl. Gajah Mada No. 1 Trenggalek, Jawa Timur | Telp. (0355) 791407</span>
            </div>

            <div class="sm:text-right">
                <span>&copy; {{ date('Y') }} SINGAWASI Web. Inspektorat Daerah Kabupaten Trenggalek.</span>
            </div>
        </div>
    </footer>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />
</body>
</html>
