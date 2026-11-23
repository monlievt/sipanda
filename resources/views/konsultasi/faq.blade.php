<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank FAQ & Smart AI Advisory APIP | Inspektorat Trenggalek</title>

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

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-canvas text-ink flex flex-col min-h-screen" x-data="faqApp()">
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
                <nav aria-label="Navigasi utama" class="hidden md:flex items-center gap-2">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center min-h-[40px] px-3 text-sm font-medium text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle transition-colors">
                        Dashboard Publik
                    </a>
                    <a href="{{ route('faq.index') }}" aria-current="page" class="inline-flex items-center min-h-[40px] px-3 text-sm font-semibold text-brand-ink bg-brand-soft border border-brand-border rounded-[6px] transition-colors">
                        Bank FAQ Publik
                    </a>
                    <a href="{{ route('regulasi.public.index') }}" class="inline-flex items-center min-h-[40px] px-3 text-sm font-medium text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle transition-colors">
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

                    @auth('web')
                        <a href="{{ route('dashboard') }}" class="ledger-btn-primary">
                            Dashboard Internal
                        </a>
                    @elseauth('opd')
                        <a href="{{ route('opd.dashboard') }}" class="ledger-btn-primary">
                            Portal OPD
                        </a>
                    @else
                        <a href="{{ route('opd.login') }}" class="ledger-btn-secondary">
                            Login OPD
                        </a>
                        <a href="{{ route('login') }}" class="ledger-btn-primary">
                            Login Pegawai
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
                            aria-controls="faq-mobile-nav"
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
            <nav id="faq-mobile-nav" x-show="mobileOpen" x-cloak aria-label="Navigasi seluler" class="md:hidden py-4 border-t border-hairline space-y-2">
                <a href="{{ route('welcome') }}" class="flex items-center min-h-[44px] px-3 rounded-[6px] text-sm font-medium text-ink hover:bg-surface-subtle">
                    Dashboard Publik
                </a>
                <a href="{{ route('regulasi.public.index') }}" class="flex items-center min-h-[44px] px-3 rounded-[6px] text-sm font-medium text-ink hover:bg-surface-subtle">
                    Pusat Regulasi
                </a>
                @auth('web')
                    <a href="{{ route('dashboard') }}" class="ledger-btn-primary w-full min-h-[44px]">
                        Dashboard Internal
                    </a>
                @elseauth('opd')
                    <a href="{{ route('opd.dashboard') }}" class="ledger-btn-primary w-full min-h-[44px]">
                        Portal OPD
                    </a>
                @else
                    <a href="{{ route('opd.login') }}" class="ledger-btn-secondary w-full min-h-[44px]">
                        Login OPD
                    </a>
                    <a href="{{ route('login') }}" class="ledger-btn-primary w-full min-h-[44px]">
                        Login Pegawai
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Container FAQ -->
    <main id="main-content" class="max-w-7xl mx-auto py-8 sm:py-10 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-8">

        <!-- Banner Header & AI Assistant Hook -->
        <section class="p-6 sm:p-8 bg-surface rounded-[10px] border border-hairline flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2.5 max-w-2xl">
                <span class="inline-flex items-center px-2.5 py-0.5 bg-brand-soft border border-brand-border text-brand-ink rounded-[4px] text-[11px] font-semibold uppercase tracking-wide">
                    AI Advisory & Basis Regulasi Pengawasan
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-[-0.02em] text-ink">
                    Bank FAQ & Konsultasi Regulasi APIP
                </h1>
                <p class="text-sm text-ink-secondary leading-relaxed">
                    Pusat penelaahan regulasi perundang-undangan, standar biaya masukan (SBM), tata kelola APBD/APBDes, pengadaan barang jasa, dan asisten virtual Inspektorat Kabupaten Trenggalek.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 shrink-0">
                <button type="button" @click="openAiChat()" class="ledger-btn-primary min-h-[44px] px-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Tanya Asisten AI APIP (24/7)</span>
                </button>
                <a href="{{ route('regulasi.public.index') }}" class="ledger-btn-secondary min-h-[44px] px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh PDF Perbup</span>
                </a>
            </div>
        </section>

        <!-- Filter & Search FAQ -->
        <section class="bg-surface p-4 sm:p-5 rounded-[10px] border border-hairline">
            <form method="GET" action="{{ route('faq.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label for="faq-search" class="block text-xs font-semibold text-ink mb-1">Kata Kunci Regulasi / Kasus</label>
                    <input id="faq-search" type="text" name="search" value="{{ $search }}" placeholder="Cari kasus, kata kunci regulasi (mis. Honor, Sewa, PBJ, DD)..." class="w-full">
                </div>

                <div class="sm:col-span-4">
                    <label for="faq-area" class="block text-xs font-semibold text-ink mb-1">Kategori Pengawasan</label>
                    <select id="faq-area" name="area" onchange="this.form.submit()" class="w-full font-medium">
                        <option value="">Semua Kategori Pengawasan</option>
                        <option value="keuangan" {{ $area === 'keuangan' ? 'selected' : '' }}>Keuangan APBD / SBM</option>
                        <option value="pbj" {{ $area === 'pbj' ? 'selected' : '' }}>Pengadaan Barang & Jasa (PBJ)</option>
                        <option value="desa" {{ $area === 'desa' ? 'selected' : '' }}>Pemerintahan & Dana Desa</option>
                        <option value="aset" {{ $area === 'aset' ? 'selected' : '' }}>Aset & Barang Milik Daerah (BMD)</option>
                        <option value="kepegawaian" {{ $area === 'kepegawaian' ? 'selected' : '' }}>Disiplin ASN & Etika</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="ledger-btn-primary w-full min-h-[42px]">
                        Filter
                    </button>
                    @if($search || $area)
                        <a href="{{ route('faq.index') }}" class="ledger-btn-secondary min-h-[42px] px-3">Reset</a>
                    @endif
                </div>
            </form>
        </section>

        <!-- Section 1: Bank FAQ Tematik Resmi APIP -->
        @if($faqArtikels->isNotEmpty())
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 border-b border-hairline pb-3">
                <div>
                    <h2 class="text-lg font-bold text-ink tracking-tight">
                        Basis Tanya-Jawab Regulasi Resmi APIP
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-secondary mt-0.5">Penelaahan yuridis dan pedoman operasional yang disusun langsung oleh Tim Inspektorat.</p>
                </div>
                <span class="text-xs font-semibold text-ink-secondary bg-surface-subtle border border-hairline px-2.5 py-1 rounded-[4px] tabular-nums w-max">
                    {{ $faqArtikels->count() }} Topik
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($faqArtikels as $faq)
                    <article class="p-5 bg-surface rounded-[10px] border border-hairline flex flex-col justify-between" x-data="{ expanded: false }">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="px-2 py-0.5 bg-brand-soft border border-brand-border text-brand-ink font-semibold text-[11px] rounded-[4px] uppercase">
                                    {{ ucfirst($faq->kategori) }}
                                </span>
                                @if($faq->regulasi)
                                    <a href="{{ route('regulasi.public.download', $faq->regulasi->id) }}" class="text-xs text-brand-ink hover:underline inline-flex items-center gap-1 font-semibold">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>{{ $faq->regulasi->nomor_regulasi }}</span>
                                    </a>
                                @endif
                            </div>

                            <h3 class="font-bold text-ink text-sm sm:text-base leading-snug">
                                {{ $faq->pertanyaan }}
                            </h3>

                            @if($faq->dasar_hukum_rujukan)
                                <p class="text-xs text-brand-ink font-mono font-medium bg-brand-soft px-2.5 py-1.5 rounded-[6px] border border-brand-border">
                                    Dasar Hukum: {{ $faq->dasar_hukum_rujukan }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-3 mt-4 border-t border-hairline">
                            <button type="button" @click="expanded = !expanded" :aria-expanded="expanded.toString()" class="text-xs font-semibold text-brand-ink hover:underline flex items-center justify-between w-full min-h-[36px]">
                                <span x-text="expanded ? 'Tutup Uraian Advis' : 'Buka Advis & Penjelasan Lengkap'"></span>
                                <svg class="w-4 h-4 transition-transform duration-150 shrink-0" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="expanded" x-cloak class="mt-3 p-4 bg-surface-subtle rounded-[6px] border border-hairline text-xs space-y-1.5">
                                <span class="font-bold text-brand-ink block">Pertimbangan & Advis APIP</span>
                                <p class="text-ink-secondary leading-relaxed whitespace-pre-line">{{ $faq->jawaban }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Section 2: Hasil Konsultasi yang Dipublikasikan (e-Consulting) -->
        <section class="space-y-4 pt-2">
            <div class="border-b border-hairline pb-3">
                <h2 class="text-lg font-bold text-ink tracking-tight">
                    Studi Kasus & Hasil Konsultasi Perangkat Daerah
                </h2>
                <p class="text-xs sm:text-sm text-ink-secondary mt-0.5">Kasus nyata yang telah selesai ditelaah dan disetujui untuk dipublikasikan secara anonim.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($faqs as $item)
                    <article class="p-5 bg-surface rounded-[10px] border border-hairline flex flex-col justify-between" x-data="{ open: false }">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800/60 text-blue-800 dark:text-blue-300 font-semibold text-[11px] rounded-[4px] uppercase">
                                    {{ $item->area_konsultasi }}
                                </span>
                                <time class="text-xs text-ink-muted font-medium tabular-nums">{{ $item->updated_at->format('d/m/Y') }}</time>
                            </div>

                            <h3 class="font-bold text-ink text-sm sm:text-base leading-snug">{{ $item->judul_permasalahan }}</h3>

                            <div class="p-3.5 bg-surface-subtle rounded-[6px] text-xs space-y-1 border border-hairline">
                                <span class="font-semibold text-ink-muted text-[11px] block uppercase tracking-wide">Kasus / Permasalahan</span>
                                <p class="text-ink-secondary leading-relaxed line-clamp-3">{{ $item->uraian_permasalahan }}</p>
                            </div>
                        </div>

                        <div class="pt-3 mt-4 border-t border-hairline">
                            <button type="button" @click="open = !open" :aria-expanded="open.toString()" class="text-xs font-semibold text-brand-ink hover:underline flex items-center justify-between w-full min-h-[36px]">
                                <span x-text="open ? 'Tutup Advis APIP' : 'Lihat Berita Acara & Advis Resmi'"></span>
                                <svg class="w-4 h-4 transition-transform duration-150 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak class="mt-3 p-4 bg-blue-50/70 dark:bg-blue-950/30 rounded-[6px] border border-blue-200 dark:border-blue-800/60 text-xs space-y-1.5">
                                <span class="font-bold text-blue-800 dark:text-blue-300 block">Advis Resmi APIP Inspektorat</span>
                                <p class="text-ink-secondary leading-relaxed whitespace-pre-line">{{ $item->kesimpulan_advis }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    @if($faqArtikels->isEmpty())
                        <div class="col-span-full py-12 px-4 bg-surface rounded-[10px] border border-hairline text-center space-y-1.5">
                            <p class="text-ink text-sm font-semibold">Belum ada artikel tanya-jawab yang cocok dengan filter pencarian.</p>
                            <p class="text-xs text-ink-muted">Anda dapat mencoba mencari dengan kata kunci lain atau bertanya langsung ke Asisten AI.</p>
                        </div>
                    @endif
                @endforelse
            </div>

            @if($faqs->hasPages())
                <div class="pt-4">
                    {{ $faqs->links() }}
                </div>
            @endif
        </section>

        <!-- Section 3: Regulasi & Juknis Paling Sering Dicari -->
        @if($regulasiPopuler->isNotEmpty())
        <section class="pt-6 border-t border-hairline space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h2 class="text-base sm:text-lg font-bold text-ink tracking-tight">
                    Dokumen Regulasi Pengawasan Terpopuler
                </h2>
                <a href="{{ route('regulasi.public.index') }}" class="text-xs sm:text-sm font-semibold text-brand-ink hover:underline">
                    Lihat Semua Regulasi
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($regulasiPopuler as $rp)
                    <div class="p-4 bg-surface border border-hairline rounded-[10px] flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-brand-ink uppercase tracking-wide block">{{ $rp->nomor_regulasi }}</span>
                            <p class="font-semibold text-xs sm:text-sm text-ink truncate mt-0.5">{{ $rp->judul }}</p>
                            <span class="text-xs text-ink-muted tabular-nums">Tahun {{ $rp->tahun }} | {{ $rp->diunduh_count }}x diunduh</span>
                        </div>
                        <a href="{{ route('regulasi.public.download', $rp->id) }}" aria-label="Unduh PDF {{ $rp->nomor_regulasi }}" class="inline-flex items-center justify-center min-h-[40px] min-w-[40px] bg-brand-soft text-brand-ink hover:bg-brand hover:text-white border border-brand-border rounded-[6px] shrink-0 transition-colors" title="Unduh PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

    </main>

    <!-- Floating AI Assistant Trigger Button -->
    <div class="fixed bottom-6 right-6 z-40">
        <button type="button" @click="openAiChat()" class="ledger-btn-primary min-h-[44px] px-4 rounded-[6px] border border-brand-hover">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Tanya AI APIP</span>
        </button>
    </div>

    <!-- Modal AI Smart Advisory Chatbot -->
    <div x-show="showAiModal" x-cloak role="dialog" aria-modal="true" aria-labelledby="ai-modal-title" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-3 sm:p-4">
        <div @click.away="showAiModal = false" class="bg-surface border border-hairline rounded-[10px] max-w-2xl w-full flex flex-col h-[620px] max-h-[90vh] overflow-hidden">

            <!-- Modal Header -->
            <div class="p-4 sm:p-5 bg-surface-subtle border-b border-hairline flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-[6px] bg-brand flex items-center justify-center text-white font-bold text-xs shrink-0" aria-hidden="true">
                        AI
                    </div>
                    <div class="min-w-0">
                        <h2 id="ai-modal-title" class="font-bold text-ink text-sm leading-tight flex items-center gap-2 flex-wrap">
                            <span>Asisten Virtual Penasihat APIP</span>
                            <span class="px-2 py-0.5 bg-brand-soft text-brand-ink text-[10px] rounded-[4px] font-semibold border border-brand-border">AI Grounded</span>
                        </h2>
                        <p class="text-xs text-ink-muted mt-0.5">Menjawab berbasis dokumen regulasi & FAQ resmi Inspektorat Trenggalek.</p>
                    </div>
                </div>
                <button type="button" @click="showAiModal = false" aria-label="Tutup jendela asisten AI" class="inline-flex items-center justify-center min-h-[36px] min-w-[36px] text-ink-secondary hover:text-ink hover:bg-surface rounded-[6px] border border-transparent hover:border-hairline transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Chat Message Thread Area -->
            <div id="aiChatThread" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 text-xs bg-canvas">
                <!-- Welcome Message from Bot -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-[6px] bg-brand flex items-center justify-center text-white font-bold text-[11px] shrink-0 mt-0.5" aria-hidden="true">
                        SP
                    </div>
                    <div class="bg-surface border border-hairline p-3.5 rounded-[10px] max-w-[88%] text-ink space-y-2">
                        <p class="leading-relaxed">
                            Halo. Saya <strong>Asisten Virtual APIP Inspektorat Trenggalek</strong>.
                        </p>
                        <p class="leading-relaxed text-ink-secondary">
                            Silakan tanyakan permasalahan regulasi pengawasan, pertanggungjawaban keuangan, batas nilai PBJ, atau ketentuan dana desa. Jawaban akan merujuk pada regulasi resmi.
                        </p>
                        <!-- Quick Prompts -->
                        <div class="pt-2.5 border-t border-hairline space-y-1.5">
                            <span class="text-[11px] font-semibold text-ink-muted block uppercase">Contoh Pertanyaan Cepat</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="askQuick('Berapa batas nilai pengadaan langsung barang dan jasa pemerintah?')" class="px-2.5 py-1.5 bg-surface-subtle hover:bg-brand-soft text-brand-ink rounded-[6px] text-xs font-medium border border-hairline transition-colors">
                                    Batas Nilai Pengadaan Langsung PBJ
                                </button>
                                <button type="button" @click="askQuick('Bagaimana ketentuan honorarium narasumber PNS dan non-PNS?')" class="px-2.5 py-1.5 bg-surface-subtle hover:bg-brand-soft text-brand-ink rounded-[6px] text-xs font-medium border border-hairline transition-colors">
                                    Honor Narasumber (SBM)
                                </button>
                                <button type="button" @click="askQuick('Apa syarat penyaluran Dana Desa tahap 2 dan pelaporannya?')" class="px-2.5 py-1.5 bg-surface-subtle hover:bg-brand-soft text-brand-ink rounded-[6px] text-xs font-medium border border-hairline transition-colors">
                                    Penyaluran Dana Desa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Chat Bubbles -->
                <template x-for="(msg, idx) in chatMessages" :key="idx">
                    <div>
                        <!-- User Message -->
                        <template x-if="msg.role === 'user'">
                            <div class="flex items-start justify-end gap-2">
                                <div class="bg-brand text-white p-3.5 rounded-[10px] max-w-[85%] leading-relaxed font-medium">
                                    <p x-text="msg.text"></p>
                                </div>
                            </div>
                        </template>

                        <!-- Assistant Message -->
                        <template x-if="msg.role === 'assistant'">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-[6px] bg-brand flex items-center justify-center text-white font-bold text-[11px] shrink-0 mt-0.5" aria-hidden="true">
                                    SP
                                </div>
                                <div class="bg-surface border border-hairline p-3.5 rounded-[10px] max-w-[88%] text-ink space-y-3">
                                    <div class="leading-relaxed whitespace-pre-line text-ink" x-html="formatMarkdown(msg.text)"></div>

                                    <!-- Sources Link Badges -->
                                    <template x-if="msg.sources && msg.sources.length > 0">
                                        <div class="pt-2.5 border-t border-hairline space-y-1.5">
                                            <span class="text-[11px] font-semibold text-ink-muted uppercase tracking-wide block">Dokumen Rujukan Terkait</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="src in msg.sources" :key="src.nomor">
                                                    <a :href="src.url" target="_blank" class="px-2.5 py-1 bg-surface-subtle hover:bg-brand-soft text-brand-ink rounded-[4px] text-xs font-semibold border border-hairline inline-flex items-center gap-1.5">
                                                        <span x-text="src.nomor"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Loading Indicator -->
                <div x-show="isLoading" class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-[6px] bg-brand flex items-center justify-center text-white font-bold text-[11px] shrink-0" aria-hidden="true">
                        SP
                    </div>
                    <div class="bg-surface border border-hairline p-3 rounded-[10px] text-ink-secondary text-xs flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-brand-ink" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memindai dokumen regulasi & menyusun pertimbangan advis...</span>
                    </div>
                </div>
            </div>

            <!-- Input Chat Box Form -->
            <div class="p-3.5 sm:p-4 bg-surface border-t border-hairline shrink-0">
                <form @submit.prevent="submitQuestion()" class="space-y-1.5">
                    <label for="ai-chat-input" class="block text-xs font-semibold text-ink">Pertanyaan Regulasi</label>
                    <div class="flex items-center gap-2">
                        <input id="ai-chat-input" type="text" x-model="userInput" :disabled="isLoading" placeholder="Ketik pertanyaan regulasi Anda di sini..." class="flex-1 disabled:opacity-50">
                        <button type="submit" :disabled="isLoading || !userInput.trim()" class="ledger-btn-primary min-h-[42px] px-5 disabled:opacity-40">
                            Kirim
                        </button>
                    </div>
                </form>
                <div class="mt-2 flex items-center justify-between gap-2 text-[11px] text-ink-muted">
                    <span>*Advis bersifat telaah normatif & tidak menggantikan audit resmi.</span>
                    @auth('opd')
                        <a href="{{ route('opd.konsultasi.create') }}" class="font-semibold text-brand-ink hover:underline shrink-0">Ajukan Konsultasi Resmi</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-auto bg-surface border-t border-hairline py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs text-ink-muted">
            <div class="space-y-0.5">
                <span class="font-semibold text-ink block">Inspektorat Daerah Kabupaten Trenggalek</span>
                <span>Jl. Brigjen Soetran No. 9, Sumbergedong, Trenggalek, Jawa Timur</span>
            </div>

            <div class="sm:text-right">
                <span>&copy; {{ date('Y') }} SINGAWASI Web. Hak Cipta Dilindungi.</span>
            </div>
        </div>
    </footer>

    <!-- Alpine.js FAQ & AI Chat App -->
    <script>
        function faqApp() {
            return {
                showAiModal: false,
                userInput: '',
                isLoading: false,
                chatMessages: [],

                openAiChat() {
                    this.showAiModal = true;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                },

                askQuick(text) {
                    this.userInput = text;
                    this.submitQuestion();
                },

                async submitQuestion() {
                    const q = this.userInput.trim();
                    if (!q || this.isLoading) return;

                    // Add user message
                    this.chatMessages.push({ role: 'user', text: q });
                    this.userInput = '';
                    this.isLoading = true;
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch('/api/ai/ask', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ question: q })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.chatMessages.push({
                                role: 'assistant',
                                text: data.answer,
                                sources: data.sources || []
                            });
                        } else {
                            this.chatMessages.push({
                                role: 'assistant',
                                text: data.answer || 'Mohon maaf, terjadi kendala saat memproses jawaban. Silakan coba kembali.',
                                sources: []
                            });
                        }
                    } catch (err) {
                        this.chatMessages.push({
                            role: 'assistant',
                            text: 'Gagal terhubung ke layanan cerdas APIP. Pastikan koneksi internet stabil.',
                            sources: []
                        });
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                scrollToBottom() {
                    const el = document.getElementById('aiChatThread');
                    if (el) el.scrollTop = el.scrollHeight;
                },

                formatMarkdown(text) {
                    if (!text) return '';
                    // Simple Markdown replacement for bold, code, bullets
                    let formatted = text
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/`(.*?)`/g, '<code class="bg-surface-subtle text-brand-ink px-1 py-0.5 rounded-[4px] font-mono text-[11px]">$1</code>')
                        .replace(/^### (.*$)/gim, '<h4 class="font-bold text-brand-ink mt-2 mb-1 text-xs">$1</h4>')
                        .replace(/^## (.*$)/gim, '<h3 class="font-bold text-ink mt-2.5 mb-1 text-sm">$1</h3>');
                    return formatted;
                }
            }
        }
    </script>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />
</body>
</html>
