<x-app-layout>
    <x-slot name="header">
        Tindak Lanjut Result & Verifikasi OPD
    </x-slot>

    <!-- Header Actions & Search -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Matriks Tindak Lanjut Hasil Pengawasan (LHP)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan jumlah rekomendasi per LHP pada 4 status penyelesaian tindak lanjut Inspektorat.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('tindak-lanjut.export_all', ['tahun' => $tahun, 'status' => $status]) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-md transition-all" title="Ekspor rekapitulasi matriks LHP ke format Excel/CSV">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor Rekap (Excel)</span>
                </a>

                <a href="{{ route('tindak-lanjut.export_kompilasi_daerah', ['tahun' => $tahun]) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-md transition-all" title="Unduh Laporan Kompilasi Pemantauan TL seluruh OPD se-Kabupaten Trenggalek (Standar BPKP / Kemendagri / Laporan Bupati)">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Kompilasi BPKP (Excel)</span>
                </a>

                <a href="{{ route('tindak-lanjut.verifikasi-bukti') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Verifikasi Bukti OPD</span>
                </a>

                <button onclick="document.getElementById('modalTambahTemuanMulti').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Temuan & Rekomendasi</span>
                </button>
            </div>
        </div>

        <!-- 📊 4 Banner Metric Status Standar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 🟦 1. Sesuai -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'selesai']) }}" class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-blue-500 transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sesuai</span>
                    <span class="w-3 h-3 rounded-full bg-blue-500 group-hover:scale-125 transition-transform"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $countSesuai }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $totalRekomendasi > 0 ? round(($countSesuai / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countSesuai / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>

            <!-- 🟩 2. Belum Sesuai -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'proses']) }}" class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-emerald-500 transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Sesuai</span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 group-hover:scale-125 transition-transform"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $countBelumSesuai }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $totalRekomendasi > 0 ? round(($countBelumSesuai / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countBelumSesuai / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>

            <!-- 🟨 3. Belum Ditindaklanjuti -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'belum']) }}" class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-amber-500 transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Ditindaklanjuti</span>
                    <span class="w-3 h-3 rounded-full bg-amber-500 group-hover:scale-125 transition-transform"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $countBelum }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $totalRekomendasi > 0 ? round(($countBelum / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countBelum / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>

            <!-- 🔴 4. Tidak Dapat Ditindaklanjuti (TDT) -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'tdt']) }}" class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-rose-500 transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tidak Dapat Ditindaklanjuti</span>
                    <span class="w-3 h-3 rounded-full bg-rose-500 group-hover:scale-125 transition-transform"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $countTdt }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $totalRekomendasi > 0 ? round(($countTdt / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countTdt / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>
        </div>

        <!-- 💰 Ringkasan Pengembalian Finansial Kas Daerah -->
        <div class="p-4 bg-emerald-950/80 text-white rounded-2xl border border-emerald-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-800/80 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-emerald-200">Realisasi Pengembalian Finansial Kas Daerah</h4>
                    <p class="text-slate-300 text-[11px]">Total pengembalian uang daerah yang berhasil disetorkan ke Kas Daerah.</p>
                </div>
            </div>

            <div class="flex items-center gap-6 font-mono">
                <div>
                    <span class="block text-[10px] text-emerald-400 font-sans">Target Rekomendasi Rp</span>
                    <span class="text-base font-extrabold text-white">Rp {{ number_format($totalNilaiRekomendasi, 0, ',', '.') }}</span>
                </div>
                <div class="h-8 w-px bg-emerald-800"></div>
                <div>
                    <span class="block text-[10px] text-emerald-400 font-sans">Telah Disetor (NTPN)</span>
                    <span class="text-base font-extrabold text-emerald-300">Rp {{ number_format($totalRealisasiSetor, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Box -->
        <form method="GET" action="{{ route('tindak-lanjut.index') }}" class="p-4 sm:p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-wrap items-center justify-between gap-3 text-sm">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. LHP / Judul LHP / Uraian..." class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-200 px-3.5 py-2.5 w-72 focus:ring-2 focus:ring-emerald-500">

                <select name="tahun" onchange="this.form.submit()" class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ (string)$tahun === (string)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status TL --</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>SESUAI</option>
                    <option value="proses" {{ $status === 'proses' ? 'selected' : '' }}>BELUM SESUAI</option>
                    <option value="belum" {{ $status === 'belum' ? 'selected' : '' }}>BELUM DITINDAKLANJUTI</option>
                    <option value="tdt" {{ $status === 'tdt' ? 'selected' : '' }}>TIDAK DAPAT DITINDAKLANJUTI</option>
                </select>

                <button type="submit" class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all cursor-pointer">Filter</button>
                <a href="{{ route('tindak-lanjut.index') }}" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 text-sm font-bold rounded-xl transition-all">Reset</a>
            </div>
        </form>
    </div>

    <!-- Data Table (Setiap Baris LHP langsung membuka Halaman Detail LHP) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 whitespace-nowrap">No. LHP & Tanggal</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Judul LHP</th>
                        <th class="py-3.5 px-4 whitespace-nowrap bg-blue-50/50 dark:bg-blue-950/30 text-blue-900 dark:text-blue-300 border-x border-slate-200 dark:border-slate-800">Nilai Yang Dilakukan Pengawasan</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Nilai Rp & Setor</th>
                        <th class="py-3.5 px-3 text-center bg-blue-50/70 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border-x border-slate-200 dark:border-slate-800 w-28">SESUAI</th>
                        <th class="py-3.5 px-3 text-center bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-r border-slate-200 dark:border-slate-800 w-28">BELUM SESUAI</th>
                        <th class="py-3.5 px-3 text-center bg-amber-50/70 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-r border-slate-200 dark:border-slate-800 w-32">BELUM DITINDAKLANJUTI</th>
                        <th class="py-3.5 px-3 text-center bg-rose-50/70 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border-r border-slate-200 dark:border-slate-800 w-36">TIDAK DAPAT DITINDAKLANJUTI</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($groupedLhp as $group)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Column 1: No. LHP & Tgl LHP -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($group->no_lhp)
                                    <span class="font-mono font-bold text-blue-600 dark:text-blue-400 text-xs block">
                                        📄 {{ $group->no_lhp }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] font-mono italic">SPT: {{ $group->penugasan?->no_spt }}</span>
                                @endif

                                @if($group->tgl_lhp)
                                    <span class="block text-[10px] text-slate-500 font-medium mt-0.5">Tgl: {{ $group->tgl_lhp->format('d/m/Y') }}</span>
                                @endif
                                <span class="inline-block px-1.5 py-0.5 text-[9px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded mt-1">
                                    {{ $group->total_rekomendasi }} Rekomendasi
                                </span>
                            </td>

                            <!-- Column 2: Judul LHP -->
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-900 dark:text-white leading-snug">
                                    {{ $group->judul_lhp ?? 'Laporan Hasil Pengawasan Terkait SPT ' . $group->penugasan?->no_spt }}
                                </p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    @php
                                        $uniqueObjek = $group->items->map(function($i) {
                                            return $i->objekPenugasan ? $i->objekPenugasan->nama : null;
                                        })->filter()->unique();
                                    @endphp
                                    @if($uniqueObjek->isNotEmpty())
                                        @foreach($uniqueObjek as $oname)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                🏛️ {{ $oname }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-[10px] text-slate-500 font-semibold block truncate max-w-xs">
                                            Objek: {{ $group->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? '-' }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Column 3: Nilai Yang Dilakukan Pengawasan (Sebelah Kiri NILAI RP & SETOR) -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] bg-blue-50/20 dark:bg-blue-950/10 border-x border-slate-200 dark:border-slate-800">
                                <span class="block font-extrabold text-blue-700 dark:text-blue-300">
                                    {{ $group->formatted_nilai_diawasi }}
                                </span>
                            </td>

                            <!-- Column 4: Nilai Rekomendasi & Realisasi Setor -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px]">
                                <span class="block font-bold text-slate-900 dark:text-white">Target: {{ $group->formatted_nilai_target }}</span>
                                <span class="block font-bold text-emerald-600 dark:text-emerald-400">Setor: {{ $group->formatted_total_setor }}</span>
                            </td>

                            <!-- Column 5: Jumlah SESUAI -->
                            <td class="py-3.5 px-3 text-center border-x border-slate-200 dark:border-slate-800 bg-blue-50/30 dark:bg-blue-950/20">
                                @if($group->count_sesuai > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-blue-600 text-white font-black rounded-xl text-xs shadow-xs">
                                        {{ $group->count_sesuai }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700 font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 6: Jumlah BELUM SESUAI -->
                            <td class="py-3.5 px-3 text-center border-r border-slate-200 dark:border-slate-800 bg-emerald-50/30 dark:bg-emerald-950/20">
                                @if($group->count_belum_sesuai > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-emerald-600 text-white font-black rounded-xl text-xs shadow-xs">
                                        {{ $group->count_belum_sesuai }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700 font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 7: Jumlah BELUM DITINDAKLANJUTI -->
                            <td class="py-3.5 px-3 text-center border-r border-slate-200 dark:border-slate-800 bg-amber-50/30 dark:bg-amber-950/20">
                                @if($group->count_belum > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-amber-500 text-white font-black rounded-xl text-xs shadow-xs">
                                        {{ $group->count_belum }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700 font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 8: Jumlah TIDAK DAPAT DITINDAKLANJUTI -->
                            <td class="py-3.5 px-3 text-center border-r border-slate-200 dark:border-slate-800 bg-rose-50/30 dark:bg-rose-950/20">
                                @if($group->count_tdt > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-rose-600 text-white font-black rounded-xl text-xs shadow-xs">
                                        {{ $group->count_tdt }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-700 font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 9: Action Button -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('tindak-lanjut.show', $group->first_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all" title="Buka Detail LHP Lengkap di Halaman Baru">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Buka LHP ({{ $group->total_rekomendasi }})</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                Belum ada data LHP & catatan temuan rekomendasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Catatan Temuan & Rekomendasi (Multi Temuan & Multi Rekomendasi) -->
    <div id="modalTambahTemuanMulti" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-4xl w-full p-6 border border-slate-200 dark:border-slate-800 text-xs space-y-4"
            x-data="{
                selectedObjekList: [],
                items: [
                    {
                        objek_penugasan_id: '',
                        temuan: '',
                        rekomendasi: [
                            { uraian: '', nilai_diawasi_rp: '', nilai_rekomendasi_rp: '', tanggal_target: '' }
                        ]
                    }
                ],
                addTemuan() {
                    let defaultObj = (this.selectedObjekList && this.selectedObjekList.length === 1) ? this.selectedObjekList[0].id : '';
                    this.items.push({
                        objek_penugasan_id: defaultObj,
                        temuan: '',
                        rekomendasi: [{ uraian: '', nilai_diawasi_rp: '', nilai_rekomendasi_rp: '', tanggal_target: '' }]
                    });
                },
                removeTemuan(tIndex) {
                    if (this.items.length > 1) {
                        this.items.splice(tIndex, 1);
                    }
                },
                addRekomendasi(tIndex) {
                    this.items[tIndex].rekomendasi.push({ uraian: '', nilai_diawasi_rp: '', nilai_rekomendasi_rp: '', tanggal_target: '' });
                },
                removeRekomendasi(tIndex, rIndex) {
                    if (this.items[tIndex].rekomendasi.length > 1) {
                        this.items[tIndex].rekomendasi.splice(rIndex, 1);
                    }
                }
            }">

            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Catatan Temuan & Rekomendasi (LHP)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Input LHP, Judul LHP, Temuan 1, Temuan 2, dst. serta pemetaan Objek/OPD sasaran.</p>
                </div>
                <button onclick="document.getElementById('modalTambahTemuanMulti').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.store') }}" enctype="multipart/form-data" class="space-y-5 mt-4">
                @csrf

                <!-- Section 1: Header Penugasan & LHP Metadata -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Searchable Combobox No. SPT -->
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('penugasan_id') }}',
                            selectedLabel: '-- Pilih Nomor SPT --',
                            options: [
                                @foreach($penugasanList as $p)
                                    { 
                                        id: '{{ $p->id }}', 
                                        label: 'No. SPT: {{ addslashes($p->no_spt) }} — {{ addslashes($p->irban?->nama_irban ?? 'Semua Irban') }} ({{ addslashes(Str::limit($p->uraian_penugasan, 50)) }})',
                                        objek: [
                                            @foreach($p->objekPenugasan as $obj)
                                                { id: {{ $obj->id }}, nama: '{{ addslashes($obj->nama) }}' },
                                            @endforeach
                                        ]
                                    },
                                @endforeach
                            ],
                            get filteredOptions() {
                                if (!this.search.trim()) return this.options;
                                return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase().trim()));
                            },
                            select(opt) {
                                this.selectedId = opt.id;
                                this.selectedLabel = opt.label;
                                this.open = false;
                                this.search = '';
                                selectedObjekList = opt.objek || [];
                                
                                // Auto set jika hanya ada 1 objek
                                if (selectedObjekList.length === 1) {
                                    items.forEach(it => { it.objek_penugasan_id = selectedObjekList[0].id; });
                                }
                            },
                            init() {
                                if (this.selectedId) {
                                    let found = this.options.find(o => o.id == this.selectedId);
                                    if (found) {
                                        this.selectedLabel = found.label;
                                        selectedObjekList = found.objek || [];
                                        if (selectedObjekList.length === 1) {
                                            items.forEach(it => { it.objek_penugasan_id = selectedObjekList[0].id; });
                                        }
                                    }
                                }
                            }
                        }" class="relative">
                            <label class="block font-bold mb-1 text-emerald-800 dark:text-emerald-400">Pilih Surat Tugas (No. SPT) <span class="text-rose-500">*</span></label>

                            <input type="hidden" name="penugasan_id" :value="selectedId" required>

                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-left text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-xs">
                                <span class="truncate font-semibold text-slate-800 dark:text-slate-200" x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition.origin.top class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl p-2 text-xs">
                                <input type="text" x-model="search" placeholder="🔍 Cari Nomor SPT / Uraian..." class="w-full px-2.5 py-1.5 mb-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs focus:ring-emerald-500">

                                <div class="max-h-48 overflow-y-auto space-y-1">
                                    <template x-for="opt in filteredOptions" :key="opt.id">
                                        <div @click="select(opt)" class="p-2 hover:bg-emerald-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer font-medium text-slate-700 dark:text-slate-200 transition-colors" :class="{'bg-emerald-100 dark:bg-slate-700 font-bold': selectedId == opt.id}">
                                            <span x-text="opt.label"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Lampiran Dokumen Dasar (File PDF LHP/NHP)</label>
                            <input type="file" name="berkas_dasar_lhp" accept=".pdf" class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <div class="sm:col-span-3">
                            <label class="block font-bold mb-1 text-blue-700 dark:text-blue-400">Nomor LHP</label>
                            <input type="text" name="no_lhp" placeholder="mis. 700/85/LHP/406.008/2026" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Judul LHP</label>
                            <input type="text" name="judul_lhp" placeholder="mis. LHP atas Pengelolaan Keuangan & Aset OPD..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block font-bold mb-1 text-blue-700 dark:text-blue-400">Nilai Yang Diawasi (Rp)</label>
                            <input type="text" oninput="formatRupiahInput(this)" name="nilai_diawasi_rp" placeholder="0" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-blue-600">
                            <p class="text-[9px] text-slate-400 mt-0.5">(Jika Tidak Ada Berikan Input 0)</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Tanggal LHP</label>
                            <input type="date" name="tgl_lhp" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                    <template x-for="(tItem, tIndex) in items" :key="tIndex">
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3 relative">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                                <span class="font-black text-emerald-700 dark:text-emerald-400 text-xs flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span x-text="'TEMUAN ' + (tIndex + 1) + ' / CATATAN ' + (tIndex + 1)"></span>
                                </span>
                                <button type="button" @click="removeTemuan(tIndex)" x-show="items.length > 1" class="text-rose-600 hover:text-rose-800 text-xs font-semibold flex items-center gap-1">
                                    &times; Hapus Temuan / Catatan ini
                                </button>
                            </div>

                            <!-- Pilihan Objek Sasaran Jika SPT Memiliki Banyak Objek -->
                            <div x-show="selectedObjekList.length > 1" class="p-3 bg-amber-50/80 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-800/80 space-y-1">
                                <div class="flex items-center justify-between">
                                    <label class="block font-bold text-amber-950 dark:text-amber-200 text-[11px] flex items-center gap-1.5">
                                        <span>🏛️</span>
                                        <span>Objek / Instansi Sasaran untuk Temuan Ini:</span>
                                    </label>
                                    <span class="text-[10px] text-amber-700 dark:text-amber-400 font-semibold">SPT Multi-Objek (<span x-text="selectedObjekList.length"></span> instansi)</span>
                                </div>
                                <select :name="'items[' + tIndex + '][objek_penugasan_id]'" x-model="tItem.objek_penugasan_id" class="w-full rounded-xl border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-amber-500">
                                    <option value="">-- [Umum] Ditujukan untuk Seluruh Objek dalam SPT Ini --</option>
                                    <template x-for="obj in selectedObjekList" :key="obj.id">
                                        <option :value="obj.id" x-text="obj.nama"></option>
                                    </template>
                                </select>
                                <p class="text-[10px] text-amber-800 dark:text-amber-300">Pilih instansi spesifik agar temuan ini hanya tampil dan dapat ditindaklanjuti oleh OPD bersangkutan.</p>
                            </div>

                            <div x-show="selectedObjekList.length === 1" class="flex items-center gap-2 text-slate-600 dark:text-slate-400 text-[11px] font-medium bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                                <span class="text-blue-600 font-bold">🏛️ Objek Sasaran:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="selectedObjekList[0] ? selectedObjekList[0].nama : ''"></span>
                                <span class="text-[10px] text-emerald-600 font-semibold">(Otomatis Terkunci)</span>
                                <input type="hidden" :name="'items[' + tIndex + '][objek_penugasan_id]'" :value="selectedObjekList[0] ? selectedObjekList[0].id : ''">
                            </div>

                            <div>
                                <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300" x-text="'Uraian Temuan ' + (tIndex + 1) + ' / Catatan ' + (tIndex + 1) + ' *'"></label>
                                <textarea :name="'items[' + tIndex + '][temuan]'" x-model="tItem.temuan" required rows="2" placeholder="Tuliskan uraian temuan / catatan hasil pemeriksaan di sini..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500"></textarea>
                            </div>

                            <div class="pl-3 border-l-2 border-emerald-500/40 space-y-3 mt-3">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 text-[11px]" x-text="'Rekomendasi / Saran untuk Temuan ' + (tIndex + 1) + ' / Catatan ' + (tIndex + 1) + ':'"></label>

                                <template x-for="(rItem, rIndex) in tItem.rekomendasi" :key="rIndex">
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-600 dark:text-slate-300 text-[10px]" x-text="'Rekomendasi / Saran ' + (rIndex + 1)"></span>
                                            <button type="button" @click="removeRekomendasi(tIndex, rIndex)" x-show="tItem.rekomendasi.length > 1" class="text-rose-500 hover:text-rose-700 text-[10px] font-bold">
                                                &times; Hapus Rekomendasi / Saran
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                                            <div class="sm:col-span-6">
                                                <label class="block text-[10px] text-slate-500 mb-0.5">Uraian Rekomendasi / Saran <span class="text-rose-500">*</span></label>
                                                <input type="text" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][uraian]'" x-model="rItem.uraian" required placeholder="Uraian rekomendasi / saran wajib..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label class="block text-[10px] font-bold text-emerald-700 dark:text-emerald-400 mb-0.5">Nilai Rekomendasi / Saran (Rp)</label>
                                                <input type="text" oninput="formatRupiahInput(this)" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][nilai_rekomendasi_rp]'" x-model="rItem.nilai_rekomendasi_rp" placeholder="0" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-emerald-600">
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label class="block text-[10px] text-slate-500 mb-0.5">Target Waktu</label>
                                                <input type="date" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][tanggal_target]'" x-model="rItem.tanggal_target" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addRekomendasi(tIndex)" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 hover:bg-emerald-100 font-semibold rounded-xl text-[10px] flex items-center gap-1 border border-emerald-200 dark:border-emerald-800">
                                    + Tambah Rekomendasi / Saran (Rekomendasi / Saran <span x-text="tItem.rekomendasi.length + 1"></span>)
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="addTemuan()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs flex items-center gap-1.5 shadow-sm">
                        <span>+ Tambah Temuan / Catatan Lain (Temuan / Catatan <span x-text="items.length + 1"></span>)</span>
                    </button>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="document.getElementById('modalTambahTemuanMulti').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md shadow-emerald-600/20">Simpan Semua Temuan / Catatan & Rekomendasi / Saran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 💰 Dynamic Live Currency Format Input (Ribuan Titik Koma)
        function formatRupiahInput(el) {
            if (!el) return;
            let cursorPos = el.selectionStart || 0;
            let originalLen = el.value.length;
            let rawVal = el.value.replace(/\D/g, '');
            if (!rawVal) {
                el.value = '';
                return;
            }
            let formatted = new Intl.NumberFormat('id-ID').format(rawVal);
            el.value = formatted;
            let newLen = formatted.length;
            cursorPos = cursorPos + (newLen - originalLen);
            if (cursorPos > 0 && el.setSelectionRange) {
                el.setSelectionRange(cursorPos, cursorPos);
            }
        }
    </script>
</x-app-layout>
