<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Regulasi & Pedoman Pengawasan — SIPANDA Inspektorat Kab. Trenggalek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex flex-col selection:bg-emerald-500 selection:text-white">

    <!-- Header Navbar -->
    <header class="border-b border-slate-800/80 bg-slate-900/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white font-extrabold text-lg">
                        SP
                    </div>
                    <div>
                        <span class="font-black text-lg tracking-tight text-white block leading-none">SIPANDA</span>
                        <span class="text-[10px] text-emerald-400 font-medium tracking-wide uppercase">Inspektorat Trenggalek</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="/" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Dashboard Publik</a>
                <a href="{{ route('faq.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Bank FAQ & QnA</a>
                <a href="{{ route('regulasi.public.index') }}" class="text-xs font-bold text-emerald-400 transition-colors">Pusat Regulasi</a>
                <a href="/login" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all">
                    Login Portal
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-b border-slate-800 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-800/60 mb-4">
                📚 Jaringan Dokumentasi & Informasi Regulasi Pengawasan
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">
                Pusat Regulasi, Juknis & Standar Biaya APIP
            </h1>
            <p class="text-sm sm:text-base text-slate-400 max-w-2xl mx-auto">
                Kumpulan peraturan perundang-undangan, Perbup Trenggalek, pedoman teknis pengadaan, dan petunjuk operasional pengawasan keuangan daerah yang dapat diunduh bebas.
            </p>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('regulasi.public.index') }}" class="mt-8 max-w-2xl mx-auto flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <svg class="w-5 h-5 absolute left-3.5 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor perbup, kata kunci, judul regulasi..." class="w-full pl-11 pr-4 py-3 bg-slate-800/90 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                </div>
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-emerald-500/20 cursor-pointer">
                    Cari Dokumen
                </button>
            </form>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
        <!-- Kategori Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 text-xs font-semibold scrollbar-none">
            <a href="{{ route('regulasi.public.index') }}" class="px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ !$kategori ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                Semua Kategori
            </a>
            <a href="{{ route('regulasi.public.index', ['kategori' => 'keuangan']) }}" class="px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $kategori === 'keuangan' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                💰 Keuangan & Anggaran
            </a>
            <a href="{{ route('regulasi.public.index', ['kategori' => 'pbj']) }}" class="px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $kategori === 'pbj' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                🏗️ Pengadaan Barang & Jasa (PBJ)
            </a>
            <a href="{{ route('regulasi.public.index', ['kategori' => 'desa']) }}" class="px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $kategori === 'desa' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                🌾 Dana & Keuangan Desa
            </a>
            <a href="{{ route('regulasi.public.index', ['kategori' => 'aset']) }}" class="px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $kategori === 'aset' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                🏛️ Aset & BMD
            </a>
            <a href="{{ route('regulasi.public.index', ['kategori' => 'kepegawaian']) }}" class="px-4 py-2 rounded-xl transition-all whitespace-nowrap {{ $kategori === 'kepegawaian' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                👔 Disiplin ASN & Etika
            </a>
        </div>

        <!-- Grid Dokumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($regulasiList as $reg)
                <div class="bg-slate-800/60 border border-slate-700/80 rounded-2xl p-5 hover:border-emerald-500/50 hover:bg-slate-800/90 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide bg-emerald-950/80 text-emerald-300 border border-emerald-800/60">
                                {{ strtoupper($reg->jenis_regulasi) }} &bull; {{ $reg->tahun }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">
                                {{ ucfirst($reg->kategori) }}
                            </span>
                        </div>

                        <h3 class="font-bold text-white text-sm group-hover:text-emerald-400 transition-colors leading-snug mb-1">
                            {{ $reg->nomor_regulasi }}
                        </h3>
                        <p class="text-xs font-semibold text-slate-300 mb-2.5">
                            {{ $reg->judul }}
                        </p>

                        @if($reg->ringkasan_eksekutif)
                            <p class="text-[11px] text-slate-400 line-clamp-3 leading-relaxed mb-4">
                                {{ $reg->ringkasan_eksekutif }}
                            </p>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-700/60 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">
                            {{ $reg->diunduh_count }}x diunduh &bull; {{ $reg->ukuran_kb ?: 'PDF' }}
                        </span>
                        @if($reg->file_path)
                            <a href="{{ route('regulasi.public.download', $reg->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-[11px] shadow-sm transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh PDF
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Dokumen Teks</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <p class="text-base font-semibold">Belum ada dokumen regulasi yang ditemukan.</p>
                    <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $regulasiList->links() }}
        </div>
    </main>

    <footer class="border-t border-slate-800 bg-slate-950 py-8 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} Inspektorat Kabupaten Trenggalek &bull; Sistem Informasi Pengawasan Terintegrasi (SIPANDA)</p>
    </footer>

</body>
</html>
