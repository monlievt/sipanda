<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank FAQ & Pusat Informasi QnA Pengawasan | Inspektorat Trenggalek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 flex flex-col min-h-screen">

    <!-- Top Navigation Bar -->
    <nav class="border-b border-slate-800 bg-slate-900/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 flex items-center justify-center font-black text-white text-xl shadow-md shadow-emerald-600/30">
                        S
                    </div>
                    <div>
                        <a href="{{ route('welcome') }}" class="font-black text-lg text-white tracking-tight block leading-tight hover:text-emerald-400 transition-colors">
                            SIPANDA <span class="text-emerald-400 font-bold">WEB</span>
                        </a>
                        <span class="text-[11px] text-slate-400 font-semibold block">Inspektorat Daerah Kabupaten Trenggalek</span>
                    </div>
                </div>

                <!-- Action Nav Options -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('welcome') }}" class="text-xs font-bold px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 flex items-center gap-1.5 transition-all border border-slate-700">
                        &larr; Beranda Utama
                    </a>

                    @auth('web')
                        <a href="{{ route('dashboard') }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-all">
                            Dashboard Internal &rarr;
                        </a>
                    @elseauth('opd')
                        <a href="{{ route('opd.dashboard') }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md transition-all">
                            Portal OPD &rarr;
                        </a>
                    @else
                        <a href="{{ route('opd.login') }}" class="text-xs font-bold px-3.5 py-2 rounded-xl bg-teal-800/80 hover:bg-teal-700 text-teal-100 transition-all border border-teal-600">
                            🏛️ Login Portal OPD
                        </a>
                        <a href="{{ route('login') }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-all">
                            🔐 Login Internal APIP &rarr;
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container FAQ -->
    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-8">
        <!-- Banner Header -->
        <div class="p-8 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-3xl border border-blue-500/30 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative overflow-hidden">
            <div class="space-y-2 relative z-10">
                <span class="px-3 py-1 bg-blue-500/20 border border-blue-400/30 text-blue-300 rounded-full text-[10px] font-extrabold uppercase tracking-wider">Pusat Informasi & Edukasi APIP Publik</span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Bank FAQ & Advis Resmi Pengawasan</h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">Kumpulan artikel tanya jawab, solusi permasalahan keuangan/pengawasan, dan advis resmi Inspektorat yang telah dipublikasikan untuk dipelajari seluruh Perangkat Daerah.</p>
            </div>

            <div class="relative z-10 shrink-0">
                @auth('opd')
                    <a href="{{ route('opd.konsultasi.create') }}" class="px-5 py-3 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-2xl shadow-lg inline-flex items-center gap-2 transition-all">
                        💬 Ajukan Konsultasi OPD &rarr;
                    </a>
                @elseauth('web')
                    <a href="{{ route('konsultasi.index') }}" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-2xl shadow-lg inline-flex items-center gap-2 transition-all">
                        💬 Kelola E-Consulting Internal &rarr;
                    </a>
                @else
                    <a href="{{ route('opd.login') }}" class="px-5 py-3 bg-teal-700 hover:bg-teal-600 text-white text-xs font-bold rounded-2xl shadow-lg inline-flex items-center gap-2 transition-all">
                        🏛️ Login OPD untuk Konsultasi &rarr;
                    </a>
                @endauth
            </div>
        </div>

        <!-- Filter & Search FAQ -->
        <div class="bg-slate-900 p-5 rounded-3xl border border-slate-800 shadow-md">
            <form method="GET" action="{{ route('faq.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari topik kata kunci (mis. Pengadaan, APBD, Dana Desa, Aset)..." class="w-full text-xs rounded-xl border-slate-700 bg-slate-950 text-slate-200 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-4">
                    <select name="area" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-700 bg-slate-950 text-slate-200 focus:ring-blue-500">
                        <option value="">-- Semua Area Konsultasi --</option>
                        <option value="Pengadaan Barang & Jasa" {{ $area === 'Pengadaan Barang & Jasa' ? 'selected' : '' }}>Pengadaan Barang & Jasa</option>
                        <option value="Keuangan APBD/APBDes" {{ $area === 'Keuangan APBD/APBDes' ? 'selected' : '' }}>Keuangan APBD/APBDes</option>
                        <option value="Tata Kelola Desa & Alokasi Dana" {{ $area === 'Tata Kelola Desa & Alokasi Dana' ? 'selected' : '' }}>Tata Kelola Desa</option>
                        <option value="Aset & Barang Milik Daerah (BMD)" {{ $area === 'Aset & Barang Milik Daerah (BMD)' ? 'selected' : '' }}>Aset & BMD</option>
                        <option value="Akuntabilitas Kinerja (SAKIP/LAKIP)" {{ $area === 'Akuntabilitas Kinerja (SAKIP/LAKIP)' ? 'selected' : '' }}>Akuntabilitas Kinerja</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                        Cari Artikel
                    </button>
                </div>
            </form>
        </div>

        <!-- Grid Cards FAQ Artikel -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($faqs as $item)
                <div class="p-6 bg-slate-900 rounded-3xl border border-slate-800 shadow-md space-y-4 flex flex-col justify-between" x-data="{ open: false }">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 bg-blue-950 border border-blue-800 text-blue-300 font-extrabold text-[10px] rounded-lg uppercase">
                                {{ $item->area_konsultasi }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-semibold">Dipublikasikan: {{ $item->updated_at->format('d/m/Y') }}</span>
                        </div>

                        <h3 class="font-bold text-white text-base leading-snug">{{ $item->judul_permasalahan }}</h3>

                        <div class="p-4 bg-slate-950 rounded-2xl text-xs space-y-1 border border-slate-800/80">
                            <span class="font-bold text-slate-400 text-[11px] block">Tanya / Permasalahan:</span>
                            <p class="text-slate-300 leading-relaxed">{{ $item->uraian_permasalahan }}</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800">
                        <button type="button" @click="open = !open" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center justify-between w-full">
                            <span x-text="open ? 'Sembunyikan Advis APIP ▲' : 'Lihat Advis & Solusi APIP Resmi ▼'"></span>
                        </button>

                        <div x-show="open" x-cloak class="mt-3 p-4 bg-emerald-950/40 rounded-2xl border border-emerald-900 text-xs space-y-1">
                            <span class="font-bold text-emerald-300 block">💡 Advis Resmi APIP Inspektorat:</span>
                            <p class="text-slate-200 leading-relaxed whitespace-pre-line">{{ $item->kesimpulan_advis }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 bg-slate-900 rounded-3xl border border-slate-800 text-center space-y-2">
                    <p class="text-slate-400 text-sm font-semibold">Belum ada artikel QnA / FAQ publik yang dipublikasikan.</p>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Konsultasi yang telah selesai dan disetujui Irban untuk dipublikasikan akan muncul di halaman ini.</p>
                </div>
            @endforelse
        </div>

        @if($faqs->hasPages())
            <div class="pt-4">
                {{ $faqs->links() }}
            </div>
        @endif
    </main>

    <!-- Footer Landing Page -->
    <footer class="mt-auto bg-slate-950 border-t border-slate-800/80 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                <span class="font-bold text-slate-300 block">Inspektorat Daerah Kabupaten Trenggalek</span>
                <span>Jl. Gajah Mada No. 1 Trenggalek, Jawa Timur | Telp. (0355) 791407</span>
            </div>

            <div class="text-center sm:text-right">
                <span>&copy; {{ date('Y') }} SIPANDA Web. Hak Cipta Dilindungi Undang-Undang.</span>
            </div>
        </div>
    </footer>
</body>
</html>
