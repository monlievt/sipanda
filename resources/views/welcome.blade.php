<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPANDA - Sistem Informasi Pengawasan Terintegrasi | Inspektorat Kabupaten Trenggalek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 flex flex-col min-h-screen">

    <!-- Top Navigation Bar -->
    <nav class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
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
                    <a href="{{ route('faq.index') }}" class="text-xs font-bold px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 flex items-center gap-1.5 transition-all border border-slate-700">
                        📚 Bank FAQ Publik
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

    <!-- Main Hero & Visual Banner -->
    <div class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950 pt-12 pb-20 border-b border-slate-800/60">
        <!-- Glow accents -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 right-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Portal Transparansi Pengawasan & Consulting APIP
            </div>

            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight max-w-4xl mx-auto leading-tight">
                Sistem Informasi Pengawasan Terintegrasi (<span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-blue-400">SIPANDA</span>)
            </h1>

            <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Platform digital resmi Inspektorat Daerah Kabupaten Trenggalek untuk pengawalan Perencanaan PKPT, Pelaksanaan Penugasan Pengawasan (Assurance & Consulting), Matriks Tindak Lanjut Rekomendasi, dan Layanan E-Consulting QnA APIP.
            </p>

            <!-- Quick Action Buttons -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-600/30 transition-all transform hover:-translate-y-0.5">
                    🔑 Akses Portal Internal APIP
                </a>
                <a href="{{ route('opd.login') }}" class="px-6 py-3 bg-teal-800/80 hover:bg-teal-700 text-teal-100 font-black text-xs uppercase tracking-wider rounded-2xl border border-teal-500/40 shadow-lg transition-all transform hover:-translate-y-0.5">
                    🏛️ Akses Portal OPD Eksternal
                </a>
                <a href="{{ route('faq.index') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-2xl border border-slate-700 transition-all">
                    📚 Pusat Informasi FAQ & QnA
                </a>
            </div>
        </div>
    </div>

    <!-- Public Transparency Metrics Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Stat 1 -->
            <div class="p-6 bg-slate-900/90 backdrop-blur-md rounded-3xl border border-slate-800 shadow-xl space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Realisasi Pengawasan</span>
                <p class="text-3xl font-black text-emerald-400">{{ $totalPenugasan }} <span class="text-xs font-semibold text-slate-400">SPT ({{ $tahun }})</span></p>
                <span class="text-[11px] text-slate-400 block pt-1">Target PKPPT: {{ $totalPkppt }} Laporan</span>
            </div>

            <!-- Stat 2 -->
            <div class="p-6 bg-slate-900/90 backdrop-blur-md rounded-3xl border border-slate-800 shadow-xl space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Capaian Tindak Lanjut</span>
                <p class="text-3xl font-black text-blue-400">{{ $persenSelesai }}%</p>
                <span class="text-[11px] text-slate-400 block pt-1">{{ $countSelesai }} dari {{ $totalRekomendasi }} Rekomendasi Selesai</span>
            </div>

            <!-- Stat 3 -->
            <div class="p-6 bg-slate-900/90 backdrop-blur-md rounded-3xl border border-slate-800 shadow-xl space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nilai Diawasi (APBD/Des)</span>
                <p class="text-2xl font-black text-amber-400 font-mono">Rp {{ number_format($totalNilaiDiawasi, 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-400 block pt-1">Total Nilai Anggaran Pengawasan</span>
            </div>

            <!-- Stat 4 -->
            <div class="p-6 bg-slate-900/90 backdrop-blur-md rounded-3xl border border-slate-800 shadow-xl space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Pelaksanaan</span>
                <p class="text-3xl font-black text-purple-400">{{ $penugasanBerjalan }} <span class="text-xs font-semibold text-slate-400">Berjalan</span></p>
                <span class="text-[11px] text-slate-400 block pt-1">{{ $penugasanSelesai }} SPT Selesai Diperiksa</span>
            </div>
        </div>
    </div>

    <!-- Modul & Fitur Utama SIPANDA -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-black text-white tracking-tight">Cakupan Layanan Pengawasan & Consulting APIP</h2>
            <p class="text-xs text-slate-400 max-w-xl mx-auto">Pengawasan internal terpadu berbasis risiko dan layanan konsultasi dua arah untuk Perangkat Daerah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="p-6 bg-slate-900/60 rounded-3xl border border-slate-800 space-y-4 hover:border-emerald-500/50 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-2xl font-black">
                    🛡️
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-white text-base">Assurance (Penjaminan Mutu)</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Pengawalan Audit Kinerja, Audit Keuangan, Reviu LPPD/LAKIP, Evaluasi SPIP, dan Monitoring Pengawasan Tahunan (PKPPT & SPT).</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 bg-slate-900/60 rounded-3xl border border-slate-800 space-y-4 hover:border-blue-500/50 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl font-black">
                    💬
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-white text-base">Consulting & E-Consulting QnA</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Layanan konsultasi online/tatap muka, penunjukan Tim APIP per permohonan OPD, chatroom 2 arah, dan penerbitan Berita Acara PDF resmi.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 bg-slate-900/60 rounded-3xl border border-slate-800 space-y-4 hover:border-purple-500/50 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-2xl font-black">
                    📊
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-white text-base">Matriks Tindak Lanjut & Setoran</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Pemantauan progres rekomendasi LHP real-time, verifikasi bukti perbaikan OPD, serta rekapitulasi nilai pengawasan & penyetoran Kasda.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Artikel QnA / FAQ Publik Terbaru -->
    @if($publicFaqs->isNotEmpty())
        <div class="bg-slate-900/40 border-y border-slate-800 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight">Artikel QnA & Advis Pengawasan Publik Terbaru</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Topik konsultasi dan advis resmi Inspektorat yang telah dipublikasikan untuk menjadi referensi OPD.</p>
                    </div>

                    <a href="{{ route('faq.index') }}" class="text-xs font-bold text-emerald-400 hover:text-emerald-300">
                        Lihat Seluruh Bank FAQ &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($publicFaqs as $faq)
                        <div class="p-5 bg-slate-900 rounded-3xl border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 bg-blue-950 text-blue-300 font-extrabold text-[10px] rounded-lg uppercase">
                                    {{ $faq->area_konsultasi }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-semibold">{{ $faq->updated_at->format('d/m/Y') }}</span>
                            </div>

                            <h3 class="font-bold text-white text-sm line-clamp-1">{{ $faq->judul_permasalahan }}</h3>

                            <div class="p-3 bg-slate-950 rounded-2xl text-xs space-y-1">
                                <span class="font-bold text-emerald-400 text-[11px] block">💡 Advis Resmi APIP:</span>
                                <p class="text-slate-300 line-clamp-2 leading-relaxed whitespace-pre-line">{{ $faq->kesimpulan_advis }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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
