<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank FAQ & Smart AI Advisory APIP — Inspektorat Trenggalek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 flex flex-col min-h-screen selection:bg-emerald-500 selection:text-white" x-data="faqApp()">

    <!-- Top Navigation Bar -->
    <nav class="border-b border-slate-800/80 bg-slate-900/90 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo Brand -->
                <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center font-black text-white text-xl shadow-md shadow-emerald-500/20">
                        SP
                    </div>
                    <div>
                        <span class="font-black text-lg text-white tracking-tight block leading-tight hover:text-emerald-400 transition-colors">
                            SIPANDA <span class="text-emerald-400 font-bold">WEB</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wide">Inspektorat Trenggalek</span>
                    </div>
                </a>

                <!-- Action Nav Options -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('welcome') }}" class="text-xs font-semibold px-3 py-2 rounded-xl text-slate-400 hover:text-white transition-colors hidden md:block">
                        Dashboard Publik
                    </a>
                    <a href="{{ route('regulasi.public.index') }}" class="text-xs font-semibold px-3 py-2 rounded-xl text-slate-400 hover:text-white transition-colors hidden sm:block">
                        📚 Pusat Regulasi
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
                        <a href="{{ route('opd.login') }}" class="text-xs font-bold px-3.5 py-2 rounded-xl bg-teal-900/80 hover:bg-teal-800 text-teal-200 border border-teal-700/60 transition-all">
                            🏛️ Login OPD
                        </a>
                        <a href="{{ route('login') }}" class="text-xs font-bold px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all">
                            🔐 Login Pegawai
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container FAQ -->
    <main class="max-w-7xl mx-auto py-8 sm:py-10 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-8">
        
        <!-- Banner Header & AI Assistant Hook -->
        <div class="p-6 sm:p-8 bg-gradient-to-br from-slate-900 via-indigo-950/70 to-slate-900 rounded-3xl border border-indigo-500/30 text-white shadow-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative overflow-hidden">
            <div class="space-y-3 relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 rounded-full text-[10px] font-extrabold uppercase tracking-wider">
                    🤖 AI Advisory & Basis Regulasi Pengawasan
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Bank FAQ & Konsultasi Regulasi APIP
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Pusat penelaahan regulasi perundang-undangan, standar biaya masukan (SBM), tata kelola APBD/APBDes, pengadaan barang jasa, dan asisten virtual cerdas Inspektorat Kabupaten Trenggalek.
                </p>
            </div>

            <div class="relative z-10 flex flex-wrap gap-3 shrink-0">
                <button @click="openAiChat()" class="px-5 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-emerald-500/20 inline-flex items-center gap-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Tanya Asisten AI APIP (24/7)
                </button>
                <a href="{{ route('regulasi.public.index') }}" class="px-4 py-3 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-bold rounded-2xl transition-all inline-flex items-center gap-2">
                    📂 Unduh PDF Perbup
                </a>
            </div>
        </div>

        <!-- Filter & Search FAQ -->
        <div class="bg-slate-900/90 p-4 sm:p-5 rounded-3xl border border-slate-800 shadow-md">
            <form method="GET" action="{{ route('faq.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-6">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kasus, kata kunci regulasi (mis. Honor, Sewa, PBJ, DD)..." class="w-full text-xs pl-10 pr-4 py-2.5 rounded-xl border-slate-700 bg-slate-950 text-slate-200 focus:ring-2 focus:ring-emerald-500 placeholder-slate-500">
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <select name="area" onchange="this.form.submit()" class="w-full text-xs py-2.5 rounded-xl border-slate-700 bg-slate-950 text-slate-200 font-semibold focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Semua Kategori Pengawasan --</option>
                        <option value="keuangan" {{ $area === 'keuangan' ? 'selected' : '' }}>Keuangan APBD / SBM</option>
                        <option value="pbj" {{ $area === 'pbj' ? 'selected' : '' }}>Pengadaan Barang & Jasa (PBJ)</option>
                        <option value="desa" {{ $area === 'desa' ? 'selected' : '' }}>Pemerintahan & Dana Desa</option>
                        <option value="aset" {{ $area === 'aset' ? 'selected' : '' }}>Aset & Barang Milik Daerah (BMD)</option>
                        <option value="kepegawaian" {{ $area === 'kepegawaian' ? 'selected' : '' }}>Disiplin ASN & Etika</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                        Filter
                    </button>
                    @if($search || $area)
                        <a href="{{ route('faq.index') }}" class="py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl text-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Section 1: Bank FAQ Tematik Resmi APIP -->
        @if($faqArtikels->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        Basis Tanya-Jawab Regulasi Resmi APIP
                    </h2>
                    <p class="text-xs text-slate-400">Penelaahan yuridis dan pedoman operasional yang disusun langsung oleh Tim Inspektorat.</p>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-800 px-3 py-1 rounded-full">
                    {{ $faqArtikels->count() }} Topik
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($faqArtikels as $faq)
                    <div class="p-5 bg-slate-900 rounded-2xl border border-slate-800/80 hover:border-emerald-500/40 transition-all flex flex-col justify-between" x-data="{ expanded: false }">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 bg-emerald-950 border border-emerald-800/80 text-emerald-300 font-bold text-[10px] rounded-lg uppercase">
                                    {{ ucfirst($faq->kategori) }}
                                </span>
                                @if($faq->regulasi)
                                    <a href="{{ route('regulasi.public.download', $faq->regulasi->id) }}" class="text-[10px] text-blue-400 hover:underline inline-flex items-center gap-1 font-semibold">
                                        📄 {{ $faq->regulasi->nomor_regulasi }}
                                    </a>
                                @endif
                            </div>

                            <h3 class="font-bold text-white text-sm leading-snug">
                                {{ $faq->pertanyaan }}
                            </h3>

                            @if($faq->dasar_hukum_rujukan)
                                <p class="text-[11px] text-emerald-400 font-mono font-medium bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-900/50">
                                    ⚖️ {{ $faq->dasar_hukum_rujukan }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-3 mt-3 border-t border-slate-800/80">
                            <button type="button" @click="expanded = !expanded" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center justify-between w-full cursor-pointer">
                                <span x-text="expanded ? 'Tutup Uraian Advis ▲' : 'Buka Advis & Penjelasan Lengkap ▼'"></span>
                            </button>

                            <div x-show="expanded" x-cloak class="mt-3 p-4 bg-slate-950 rounded-xl border border-slate-800 text-xs space-y-1">
                                <span class="font-bold text-emerald-300 block text-[11px]">💡 Pertimbangan & Advis APIP:</span>
                                <p class="text-slate-200 leading-relaxed whitespace-pre-line">{{ $faq->jawaban }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Section 2: Hasil Konsultasi yang Dipublikasikan (e-Consulting) -->
        <div class="space-y-4 pt-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                        Studi Kasus & Hasil Konsultasi Perangkat Daerah
                    </h2>
                    <p class="text-xs text-slate-400">Kasus nyata yang telah selesai ditelaah dan disetujui untuk dipublikasikan secara anonim.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($faqs as $item)
                    <div class="p-5 bg-slate-900 rounded-2xl border border-slate-800/80 hover:border-blue-500/40 transition-all flex flex-col justify-between" x-data="{ open: false }">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 bg-blue-950 border border-blue-800/80 text-blue-300 font-bold text-[10px] rounded-lg uppercase">
                                    {{ $item->area_konsultasi }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-semibold">{{ $item->updated_at->format('d/m/Y') }}</span>
                            </div>

                            <h3 class="font-bold text-white text-sm leading-snug">{{ $item->judul_permasalahan }}</h3>

                            <div class="p-3 bg-slate-950 rounded-xl text-xs space-y-1 border border-slate-800/80">
                                <span class="font-bold text-slate-400 text-[10px] block uppercase tracking-wider">Kasus / Permasalahan:</span>
                                <p class="text-slate-300 text-xs leading-relaxed line-clamp-3">{{ $item->uraian_permasalahan }}</p>
                            </div>
                        </div>

                        <div class="pt-3 mt-3 border-t border-slate-800/80">
                            <button type="button" @click="open = !open" class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center justify-between w-full cursor-pointer">
                                <span x-text="open ? 'Tutup Advis APIP ▲' : 'Lihat Berita Acara & Advis Resmi ▼'"></span>
                            </button>

                            <div x-show="open" x-cloak class="mt-3 p-4 bg-blue-950/30 rounded-xl border border-blue-900/60 text-xs space-y-1">
                                <span class="font-bold text-blue-300 block text-[11px]">💡 Advis Resmi APIP Inspektorat:</span>
                                <p class="text-slate-200 leading-relaxed whitespace-pre-line">{{ $item->kesimpulan_advis }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    @if($faqArtikels->isEmpty())
                        <div class="col-span-full py-12 bg-slate-900 rounded-3xl border border-slate-800 text-center space-y-2">
                            <p class="text-slate-400 text-sm font-semibold">Belum ada artikel tanya-jawab yang cocok dengan filter pencarian.</p>
                            <p class="text-xs text-slate-500">Anda dapat mencoba mencari dengan kata kunci lain atau bertanya langsung ke Asisten AI.</p>
                        </div>
                    @endif
                @endforelse
            </div>

            @if($faqs->hasPages())
                <div class="pt-4">
                    {{ $faqs->links() }}
                </div>
            @endif
        </div>

        <!-- Section 3: Regulasi & Juknis Paling Sering Dicari -->
        @if($regulasiPopuler->isNotEmpty())
        <div class="pt-6 border-t border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    📂 Dokumen Regulasi Pengawasan Terpopuler
                </h2>
                <a href="{{ route('regulasi.public.index') }}" class="text-xs font-semibold text-emerald-400 hover:underline">
                    Lihat Semua Regulasi &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($regulasiPopuler as $rp)
                    <div class="p-4 bg-slate-900/80 border border-slate-800 rounded-xl hover:border-emerald-500/30 transition-all flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-wider block">{{ $rp->nomor_regulasi }}</span>
                            <p class="font-semibold text-xs text-white truncate">{{ $rp->judul }}</p>
                            <span class="text-[10px] text-slate-400">Tahun {{ $rp->tahun }} &bull; {{ $rp->diunduh_count }}x diunduh</span>
                        </div>
                        <a href="{{ route('regulasi.public.download', $rp->id) }}" class="p-2 bg-emerald-950 text-emerald-300 hover:bg-emerald-900 rounded-lg text-xs font-bold shrink-0 transition-colors" title="Unduh PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </main>

    <!-- Floating AI Assistant Floating Trigger Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button @click="openAiChat()" class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs rounded-full shadow-2xl shadow-emerald-500/40 flex items-center gap-2.5 transition-all hover:scale-105 cursor-pointer">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
            </span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Tanya AI APIP</span>
        </button>
    </div>

    <!-- Modal AI Smart Advisory Chatbot -->
    <div x-show="showAiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
        <div @click.away="showAiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl max-w-2xl w-full flex flex-col h-[620px] max-h-[90vh] overflow-hidden">
            
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 bg-gradient-to-r from-emerald-950/80 via-slate-900 to-teal-950/80 border-b border-slate-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-extrabold text-sm shadow-md">
                        🤖
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm leading-tight flex items-center gap-2">
                            Asisten Virtual Penasihat APIP
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 text-[9px] rounded-full font-extrabold border border-emerald-500/30">AI Grounded</span>
                        </h3>
                        <p class="text-[10px] text-slate-400">Menjawab strictly berbasis dokumen regulasi & FAQ resmi Inspektorat Trenggalek.</p>
                    </div>
                </div>
                <button @click="showAiModal = false" class="p-2 text-slate-400 hover:text-white text-xl font-bold">&times;</button>
            </div>

            <!-- Chat Message Thread Area -->
            <div id="aiChatThread" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 text-xs">
                <!-- Welcome Message from Bot -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-xs shrink-0 mt-0.5">
                        SP
                    </div>
                    <div class="bg-slate-800/90 border border-slate-700/80 p-3.5 rounded-2xl rounded-tl-xs max-w-[85%] text-slate-200 space-y-2">
                        <p class="leading-relaxed">
                            Halo! Saya <strong>Asisten Virtual APIP Inspektorat Trenggalek</strong>. 
                        </p>
                        <p class="leading-relaxed text-slate-300">
                            Silakan tanyakan permasalahan regulasi pengawasan, pertanggungjawaban keuangan, batas nilai PBJ, atau ketentuan dana desa. Saya akan memberikan jawaban yang merujuk pada regulasi resmi.
                        </p>
                        <!-- Quick Prompts -->
                        <div class="pt-2 border-t border-slate-700/60 space-y-1.5">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Contoh Pertanyaan Cepat:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="askQuick('Berapa batas nilai pengadaan langsung barang dan jasa pemerintah?')" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-950 text-emerald-400 rounded-lg text-[10px] font-medium border border-slate-700 cursor-pointer">
                                    🏗️ Batas Nilai Pengadaan Langsung PBJ
                                </button>
                                <button type="button" @click="askQuick('Bagaimana ketentuan honorarium narasumber PNS dan non-PNS?')" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-950 text-emerald-400 rounded-lg text-[10px] font-medium border border-slate-700 cursor-pointer">
                                    💰 Honor Narasumber (SBM)
                                </button>
                                <button type="button" @click="askQuick('Apa syarat penyaluran Dana Desa tahap 2 dan pelaporannya?')" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-950 text-emerald-400 rounded-lg text-[10px] font-medium border border-slate-700 cursor-pointer">
                                    🌾 Penyaluran Dana Desa
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
                                <div class="bg-emerald-600 text-white p-3.5 rounded-2xl rounded-tr-xs max-w-[85%] leading-relaxed shadow-sm font-medium">
                                    <p x-text="msg.text"></p>
                                </div>
                            </div>
                        </template>

                        <!-- Assistant Message -->
                        <template x-if="msg.role === 'assistant'">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-xs shrink-0 mt-0.5">
                                    SP
                                </div>
                                <div class="bg-slate-800/90 border border-slate-700/80 p-3.5 rounded-2xl rounded-tl-xs max-w-[88%] text-slate-200 space-y-3">
                                    <div class="leading-relaxed whitespace-pre-line text-slate-200" x-html="formatMarkdown(msg.text)"></div>

                                    <!-- Sources Link Badges -->
                                    <template x-if="msg.sources && msg.sources.length > 0">
                                        <div class="pt-2.5 border-t border-slate-700/60 space-y-1.5">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">📄 Dokumen Rujukan Terkait:</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="src in msg.sources" :key="src.nomor">
                                                    <a :href="src.url" target="_blank" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-950 text-blue-400 hover:text-blue-300 rounded-lg text-[10px] font-semibold border border-slate-700 flex items-center gap-1">
                                                        <span>📥</span>
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
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-xs shrink-0">
                        SP
                    </div>
                    <div class="bg-slate-800 border border-slate-700/80 p-3 rounded-2xl rounded-tl-xs text-slate-400 text-xs flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memindai dokumen regulasi & menyusun pertimbangan advis...</span>
                    </div>
                </div>
            </div>

            <!-- Input Chat Box Form -->
            <div class="p-3 sm:p-4 bg-slate-950 border-t border-slate-800 shrink-0">
                <form @submit.prevent="submitQuestion()" class="flex items-center gap-2">
                    <input type="text" x-model="userInput" :disabled="isLoading" placeholder="Ketik pertanyaan regulasi Anda di sini..." class="flex-1 text-xs px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 disabled:opacity-50">
                    <button type="submit" :disabled="isLoading || !userInput.trim()" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md disabled:opacity-40 cursor-pointer">
                        Kirim
                    </button>
                </form>
                <div class="mt-1.5 flex items-center justify-between text-[10px] text-slate-500">
                    <span>*Advis bersifat telaah normatif & tidak menggantikan audit resmi.</span>
                    @auth('opd')
                        <a href="{{ route('opd.konsultasi.create') }}" class="text-teal-400 hover:underline">Ajukan Konsultasi Resmi &rarr;</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Landing Page -->
    <footer class="mt-auto bg-slate-950 border-t border-slate-800/80 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                <span class="font-bold text-slate-300 block">Inspektorat Daerah Kabupaten Trenggalek</span>
                <span>Jl. Brigjen Soetran No. 9, Sumbergedong, Trenggalek, Jawa Timur</span>
            </div>

            <div class="text-center sm:text-right">
                <span>&copy; {{ date('Y') }} SIPANDA Web. Hak Cipta Dilindungi.</span>
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
                        .replace(/`(.*?)`/g, '<code class="bg-slate-900 text-emerald-300 px-1 py-0.5 rounded font-mono text-[11px]">$1</code>')
                        .replace(/^### (.*$)/gim, '<h4 class="font-bold text-emerald-400 mt-2 mb-1 text-xs">$1</h4>')
                        .replace(/^## (.*$)/gim, '<h3 class="font-bold text-white mt-2.5 mb-1 text-sm">$1</h3>');
                    return formatted;
                }
            }
        }
    </script>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />
</body>
</html>
