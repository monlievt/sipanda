<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        PermenPAN-RB No. 42 Tahun 2011
                    </span>
                    <span class="text-xs text-slate-400">• Standar Baku Pengawasan APIP</span>
                </div>
                <h2 class="font-bold text-xl sm:text-2xl text-slate-800 dark:text-slate-100 mt-1">
                    Kamus Kode Atribut Temuan & Rekomendasi Audit
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Klasifikasi baku temuan dan rekomendasi pengawasan internal untuk menjaga konsistensi LHP dan Laporan Ikhtisar Hasil Pengawasan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ asset('docs/template/kode_atribut_audit.xlsx') }}" download class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Excel Kamus Baku</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 pb-12">
        <!-- Navigation Tabs & Search Filter -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs p-4 sm:p-5">
            <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
                <!-- Tabs -->
                <div class="flex items-center gap-2 border-b lg:border-b-0 border-slate-200 dark:border-slate-800 pb-3 lg:pb-0">
                    <a href="{{ route('kamus-atribut.index', ['tab' => 'temuan', 'kelompok' => $kelompok, 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'temuan' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Kamus Temuan Audit ({{ $listTemuan->count() }})</span>
                    </a>
                    <a href="{{ route('kamus-atribut.index', ['tab' => 'rekomendasi', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'rekomendasi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>Kamus Kode Rekomendasi ({{ $listRekomendasi->count() }})</span>
                    </a>
                </div>

                <!-- Search & Filters -->
                <form method="GET" action="{{ route('kamus-atribut.index') }}" class="flex flex-col sm:flex-row items-center gap-2.5">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    @if($tab === 'temuan')
                    <select name="kelompok" onchange="this.form.submit()" class="w-full sm:w-auto text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2">
                        <option value="">-- Semua Kelompok Temuan --</option>
                        <option value="1" {{ $kelompok === '1' ? 'selected' : '' }}>1. Ketidakpatuhan Terhadap Peraturan</option>
                        <option value="2" {{ $kelompok === '2' ? 'selected' : '' }}>2. Kelemahan Sistem Pengendalian Intern (SPI)</option>
                        <option value="3" {{ $kelompok === '3' ? 'selected' : '' }}>3. 3E (Ketidakefektifan, Ketidakefisienan, Ketidakhematan)</option>
                    </select>
                    @endif

                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode atau uraian..." 
                               class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 pl-8 pr-3 py-2 focus:ring-2 focus:ring-emerald-500">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-semibold transition">
                        Filter
                    </button>
                    @if($search || $kelompok)
                    <a href="{{ route('kamus-atribut.index', ['tab' => $tab]) }}" class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 px-2">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        @if($tab === 'temuan')
            <!-- TAB: KAMUS TEMUAN AUDIT -->
            @if($groupedTemuan->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Tidak ada data temuan yang sesuai dengan kriteria pencarian.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($groupedTemuan as $namaKelompok => $subKelompokGroup)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                        <!-- Kelompok Header -->
                        <div class="bg-slate-100 dark:bg-slate-800/80 px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">
                                    {{ $subKelompokGroup->first()->first()->kode_kelompok }}
                                </span>
                                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">
                                    {{ $namaKelompok }}
                                </h3>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                {{ $subKelompokGroup->flatten()->count() }} Jenis Temuan
                            </span>
                        </div>

                        <!-- Sub Kelompok & Jenis Temuan List -->
                        <div class="p-5 space-y-6">
                            @foreach($subKelompokGroup as $namaSubKelompok => $jenisItems)
                            <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-slate-50/50 dark:bg-slate-800/30">
                                <div class="bg-slate-100/70 dark:bg-slate-800 px-4 py-2.5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                        Sub {{ $jenisItems->first()->kode_sub_kelompok }}
                                    </span>
                                    <h4 class="font-semibold text-xs text-slate-700 dark:text-slate-200">
                                        {{ $namaSubKelompok }}
                                    </h4>
                                </div>

                                <div class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                                    @foreach($jenisItems as $item)
                                    <div class="p-3.5 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition flex flex-col md:flex-row md:items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 flex-1">
                                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50 shrink-0">
                                                {{ $item->kode_lengkap }}
                                            </span>
                                            <div>
                                                <p class="text-xs font-medium text-slate-800 dark:text-slate-200 leading-relaxed">
                                                    {{ $item->deskripsi }}
                                                </p>
                                            </div>
                                        </div>

                                        @if(!empty($item->alternatif_rekomendasi))
                                        <div class="shrink-0 flex items-center gap-1.5 flex-wrap pl-11 md:pl-0">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saran Rek:</span>
                                            @foreach($item->alternatif_rekomendasi_array as $rekKode)
                                                @php $rekInfo = $mapRekomendasi->get($rekKode); @endphp
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 cursor-help" 
                                                      title="{{ $rekInfo ? $rekInfo->deskripsi : 'Kode ' . $rekKode }}">
                                                    {{ $rekKode }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

        @else
            <!-- TAB: KAMUS REKOMENDASI AUDIT -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="bg-slate-100 dark:bg-slate-800/80 px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        14 Kode Baku Rekomendasi Pengawasan APIP
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Lampiran 2 PermenPAN-RB 42/2011</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($listRekomendasi as $rek)
                    <div class="p-4 sm:p-5 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 font-mono font-bold text-sm flex items-center justify-center shrink-0">
                            {{ $rek->kode }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 leading-snug">
                                {{ $rek->deskripsi }}
                            </h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">
                                Kode Atribut Baku: <strong class="font-mono text-slate-600 dark:text-slate-300">{{ $rek->kode }}</strong>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
