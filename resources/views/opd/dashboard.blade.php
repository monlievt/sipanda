<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Tindak Lanjut LHP — Portal OPD SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased font-sans min-h-screen flex flex-col">
    <!-- Navbar OPD -->
    <nav class="bg-teal-700 text-white shadow-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('opd.dashboard') }}" class="font-extrabold text-lg tracking-tight hover:text-teal-200">
                        SIPANDA <span class="text-xs font-normal opacity-80">| Portal OPD</span>
                    </a>
                    <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-teal-800/80 hover:bg-teal-600 text-white flex items-center gap-1.5 transition-all shadow-xs">
                        💬 E-Consulting (QnA APIP)
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-teal-100 hidden sm:inline-block">PIC: <strong>{{ $user->nama }}</strong></span>
                    <form method="POST" action="{{ route('opd.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs bg-teal-900 hover:bg-teal-950 px-3.5 py-1.5 rounded-xl text-white font-semibold transition-colors cursor-pointer">
                            Keluar &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Body OPD -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-semibold flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stat Cards OPD (Matriks Standar PTL Pengawasan) -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <p class="text-[11px] font-bold text-slate-400 uppercase">Total LHP Diawasi</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $rekap['total_lhp'] }} <span class="text-xs font-semibold text-slate-400">Dokumen</span></p>
                <p class="text-[10px] text-slate-500 mt-0.5">Total {{ $rekap['total_rekomendasi'] }} Rekomendasi</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 shadow-xs bg-emerald-50/20">
                <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">🟢 Sesuai (SS)</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $rekap['sesuai'] }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></p>
                <p class="text-[10px] text-emerald-600/80 mt-0.5">Tuntas & Selesai Diverifikasi</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-blue-200 dark:border-blue-800/60 shadow-xs bg-blue-50/20">
                <p class="text-[11px] font-bold text-blue-700 dark:text-blue-400 uppercase">🔵 Belum Sesuai (BS)</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $rekap['belum_sesuai'] }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></p>
                <p class="text-[10px] text-blue-600/80 mt-0.5">Dalam Proses / Verifikasi</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-200 dark:border-amber-800/60 shadow-xs bg-amber-50/20">
                <p class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase">🟡 Belum di-TL (BTL)</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ $rekap['belum'] }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></p>
                <p class="text-[10px] text-amber-600/80 mt-0.5">Perlu Tindak Lanjut Cepat</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <p class="text-[11px] font-bold text-slate-500 uppercase">⚪ Tidak Dapat di-TL</p>
                <p class="text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">{{ $rekap['tdt'] }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></p>
                <p class="text-[10px] text-slate-400 mt-0.5">Kategori TDT / TDL BPKP</p>
            </div>
        </div>

        @if($rekap['total_target_rp'] > 0 || $rekap['total_setor_rp'] > 0)
        <!-- Financial Summary Banner -->
        <div class="bg-gradient-to-r from-teal-900 to-slate-900 text-white rounded-3xl p-5 border border-teal-800/60 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-[10px] font-bold tracking-widest text-teal-300 uppercase">Ringkasan Kewajiban Finansial / Pengembalian Kas Daerah</span>
                <div class="flex items-center gap-6 flex-wrap text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Total Kewajiban Setor:</span>
                        <span class="text-lg font-black text-white">Rp {{ number_format($rekap['total_target_rp'], 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-teal-300 block text-[10px]">Telah Disetor:</span>
                        <span class="text-lg font-black text-teal-300">Rp {{ number_format($rekap['total_setor_rp'], 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-rose-300 block text-[10px]">Sisa Kurang Setor:</span>
                        <span class="text-lg font-black text-rose-300">Rp {{ number_format($rekap['sisa_setor_rp'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-slate-400 block uppercase font-bold">Progres Penyelesaian LHP</span>
                <span class="text-2xl font-black text-teal-400">{{ $rekap['persen_selesai'] }}%</span>
            </div>
        </div>
        @endif

        <!-- Filter Box -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs">
            <form method="GET" action="{{ route('opd.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-xs">
                <div>
                    <label class="block font-semibold text-slate-500 uppercase mb-1">Tahun Pengawasan</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2.5 focus:ring-teal-500">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-500 uppercase mb-1">Cari No. LHP / Judul</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik No. LHP atau Judul..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3 py-2.5 focus:ring-teal-500">
                </div>
                <div>
                    <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                        🔍 Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Rekapitulasi LHP OPD -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Daftar Laporan Hasil Pengawasan (LHP) & Status Tindak Lanjut</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Menampilkan seluruh LHP yang ditujukan untuk instansi ini</p>
                </div>
                <span class="text-xs font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-3 py-1 rounded-xl border border-teal-200 dark:border-teal-800">
                    Total: {{ $groupedLhp->count() }} LHP
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4 w-10 text-center">No</th>
                            <th class="py-3.5 px-4">Nomor & Tanggal LHP</th>
                            <th class="py-3.5 px-4">Judul / Uraian LHP</th>
                            <th class="py-3.5 px-4 text-center">Matriks Rekomendasi</th>
                            <th class="py-3.5 px-4 text-center">Kewajiban Setor (Rp)</th>
                            <th class="py-3.5 px-4 text-center">Progres</th>
                            <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($groupedLhp as $index => $lhp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-500">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-teal-700 dark:text-teal-300 block text-xs">{{ $lhp->no_lhp }}</span>
                                    <span class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($lhp->tgl_lhp)->format('d/m/Y') }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-sm">
                                    <span class="font-bold text-slate-900 dark:text-white block line-clamp-2 leading-relaxed">{{ $lhp->judul_lhp }}</span>
                                    @if($lhp->penugasan?->irban)
                                        <span class="text-[10px] text-slate-400">Pengawas: {{ $lhp->penugasan->irban->nama_irban }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-bold rounded-lg text-[10px]" title="Sesuai">
                                            🟢 {{ $lhp->count_sesuai }} SS
                                        </span>
                                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 font-bold rounded-lg text-[10px]" title="Belum Sesuai">
                                            🔵 {{ $lhp->count_belum_sesuai }} BS
                                        </span>
                                        <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-bold rounded-lg text-[10px]" title="Belum di-TL">
                                            🟡 {{ $lhp->count_belum }} BTL
                                        </span>
                                        @if($lhp->count_tdt > 0)
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-[10px]" title="Tidak Dapat di-TL">
                                            ⚪ {{ $lhp->count_tdt }} TDT
                                        </span>
                                        @endif
                                    </div>
                                    <span class="block text-[10px] text-slate-400 mt-1">Total {{ $lhp->total_rekomendasi }} Rekomendasi</span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($lhp->total_nilai_target > 0)
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs">{{ $lhp->formatted_nilai_target }}</span>
                                        <span class="text-[10px] text-emerald-600 block">Disetor: {{ $lhp->formatted_total_setor }}</span>
                                        @if($lhp->sisa_setor_rp > 0)
                                            <span class="text-[10px] text-rose-600 font-semibold block">Sisa: {{ $lhp->formatted_sisa_setor }}</span>
                                        @else
                                            <span class="text-[9px] px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded font-bold">LUNAS</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-xs">- Non Finansial -</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="w-16 mx-auto">
                                        <div class="text-[10px] font-bold text-slate-700 dark:text-slate-300 mb-0.5">{{ $lhp->persen_selesai }}%</div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-teal-600 h-1.5 rounded-full" style="width: {{ $lhp->persen_selesai }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('opd.lhp.show', $lhp->first_id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold rounded-xl text-xs shadow-sm transition-all cursor-pointer">
                                        <span>🔍 Rincian LHP</span>
                                        <span>&rarr;</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold">Belum ada dokumen LHP / rekomendasi pengawasan yang ditujukan untuk instansi ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />
</body>
</html>
