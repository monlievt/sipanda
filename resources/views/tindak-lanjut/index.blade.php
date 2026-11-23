<x-app-layout>
    <x-slot name="header">
        Tindak Lanjut Result & Verifikasi OPD
    </x-slot>

    <!-- Header Actions & Search -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 class="text-xl font-bold text-ink tracking-tight">Matriks Tindak Lanjut Hasil Pengawasan (LHP)</h2>
                    @if(!empty($scopeBadge))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] text-[11px] font-semibold bg-surface-subtle text-ink-secondary border border-hairline">
                            {{ $scopeBadge }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-ink-secondary mt-0.5">Ringkasan jumlah rekomendasi per LHP pada 4 status penyelesaian tindak lanjut Inspektorat.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tindak-lanjut.export_all', ['tahun' => $tahun, 'status' => $status]) }}" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0" title="Ekspor rekapitulasi matriks LHP ke format Excel/CSV">
                    <svg class="w-4 h-4 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor Rekap (Excel)</span>
                </a>

                <a href="{{ route('tindak-lanjut.export_kompilasi_daerah', ['tahun' => $tahun]) }}" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0" title="Unduh Rekap Tindak Lanjut Pemantauan per Penugasan / OPD (Standar Evaluasi dan Rekonsiliasi)">
                    <svg class="w-4 h-4 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Rekap per Penugasan (Excel)</span>
                </a>

                <a href="{{ route('tindak-lanjut.verifikasi-bukti') }}" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0">
                    <svg class="w-4 h-4 text-blue-800 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Verifikasi Bukti OPD</span>
                </a>

                @if(auth()->user()->hasRole(['admin_irban', 'admin', 'administrator']))
                <button type="button" onclick="document.getElementById('modalTambahTemuanMulti').classList.remove('hidden')" class="ledger-btn-primary !text-xs !py-2 !px-4 !min-h-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Temuan & Rekomendasi</span>
                </button>
                @endif
            </div>
        </div>

        <!-- 4 Banner Metric Status Standar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Sesuai -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'selesai']) }}" class="p-4 bg-surface rounded-[10px] border border-hairline hover:border-blue-600 dark:hover:border-blue-400 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Sesuai</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-700 dark:bg-blue-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-blue-800 dark:text-blue-300 tabular-nums">{{ $countSesuai }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $totalRekomendasi > 0 ? round(($countSesuai / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-700 dark:bg-blue-400 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countSesuai / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>

            <!-- 2. Belum Sesuai -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'proses']) }}" class="p-4 bg-surface rounded-[10px] border border-hairline hover:border-emerald-700 dark:hover:border-emerald-400 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Belum Sesuai</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-700 dark:bg-emerald-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-emerald-800 dark:text-emerald-300 tabular-nums">{{ $countBelumSesuai }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $totalRekomendasi > 0 ? round(($countBelumSesuai / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-700 dark:bg-emerald-400 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countBelumSesuai / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>

            <!-- 3. Belum Ditindaklanjuti -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'belum']) }}" class="p-4 bg-surface rounded-[10px] border border-hairline hover:border-amber-700 dark:hover:border-amber-400 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Belum Ditindaklanjuti</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-600 dark:bg-amber-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-amber-900 dark:text-amber-300 tabular-nums">{{ $countBelum }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $totalRekomendasi > 0 ? round(($countBelum / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-amber-600 dark:bg-amber-400 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countBelum / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>

            <!-- 4. Tidak Dapat Ditindaklanjuti (TDT) -->
            <a href="{{ route('tindak-lanjut.index', ['status' => 'tdt']) }}" class="p-4 bg-surface rounded-[10px] border border-hairline hover:border-red-700 dark:hover:border-red-400 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">Tidak Dapat Ditindaklanjuti</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-red-700 dark:bg-red-400"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-red-800 dark:text-red-300 tabular-nums">{{ $countTdt }}</span>
                    <span class="text-[11px] font-semibold text-ink-muted tabular-nums">
                        {{ $totalRekomendasi > 0 ? round(($countTdt / $totalRekomendasi) * 100, 1) : 0 }}% Total
                    </span>
                </div>
                <div class="w-full bg-surface-subtle rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-red-700 dark:bg-red-400 h-1.5 rounded-full" style="width: {{ $totalRekomendasi > 0 ? ($countTdt / $totalRekomendasi) * 100 : 0 }}%"></div>
                </div>
            </a>
        </div>

        <!-- Ringkasan Pengembalian Finansial Kas Daerah -->
        <div class="p-4 bg-emerald-950 text-white rounded-[10px] border border-emerald-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-900 rounded-[6px]">
                    <svg class="w-6 h-6 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-sm text-emerald-100">Realisasi Pengembalian Finansial Kas Daerah</h4>
                    <p class="text-emerald-200 text-xs">Total pengembalian uang daerah yang berhasil disetorkan ke Kas Daerah.</p>
                </div>
            </div>

            <div class="flex items-center gap-6 font-mono tabular-nums">
                <div>
                    <span class="block text-[11px] text-emerald-200 font-sans">Target Rekomendasi Rp</span>
                    <span class="text-base font-bold text-white">Rp {{ number_format($totalNilaiRekomendasi, 0, ',', '.') }}</span>
                </div>
                <div class="h-8 w-px bg-emerald-800"></div>
                <div>
                    <span class="block text-[11px] text-emerald-200 font-sans">Telah Disetor (NTPN)</span>
                    <span class="text-base font-bold text-emerald-200">Rp {{ number_format($totalRealisasiSetor, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Box -->
        <form method="GET" action="{{ route('tindak-lanjut.index') }}" class="p-4 sm:p-5 bg-surface rounded-[10px] border border-hairline flex flex-wrap items-center justify-between gap-3 text-sm">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. LHP / Judul LHP / Uraian..." class="rounded-[6px] border-hairline-strong bg-surface text-sm text-ink px-3.5 py-2.5 w-72">

                <select name="tahun" onchange="this.form.submit()" class="rounded-[6px] border-hairline-strong bg-surface text-sm font-semibold text-ink px-3.5 py-2.5">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ (string)$tahun === (string)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="rounded-[6px] border-hairline-strong bg-surface text-sm font-semibold text-ink px-3.5 py-2.5">
                    <option value="">-- Semua Status TL --</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>SESUAI</option>
                    <option value="proses" {{ $status === 'proses' ? 'selected' : '' }}>BELUM SESUAI</option>
                    <option value="belum" {{ $status === 'belum' ? 'selected' : '' }}>BELUM DITINDAKLANJUTI</option>
                    <option value="tdt" {{ $status === 'tdt' ? 'selected' : '' }}>TIDAK DAPAT DITINDAKLANJUTI</option>
                </select>

                <button type="submit" class="ledger-btn-primary">Filter</button>
                <a href="{{ route('tindak-lanjut.index') }}" class="ledger-btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Data Table (Setiap Baris LHP langsung membuka Halaman Detail LHP) -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 whitespace-nowrap">No. LHP & Tanggal</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Judul LHP</th>
                        <th class="py-3.5 px-4 whitespace-nowrap bg-blue-50/50 dark:bg-blue-950/30 text-blue-800 dark:text-blue-300 border-x border-hairline">Nilai Yang Dilakukan Pengawasan</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Nilai Rp & Setor</th>
                        <th class="py-3.5 px-3 text-center bg-blue-50/60 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border-x border-hairline w-28">SESUAI</th>
                        <th class="py-3.5 px-3 text-center bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-r border-hairline w-28">BELUM SESUAI</th>
                        <th class="py-3.5 px-3 text-center bg-amber-50/60 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 border-r border-hairline w-32">BELUM DITINDAKLANJUTI</th>
                        <th class="py-3.5 px-3 text-center bg-red-50/60 dark:bg-red-950/40 text-red-800 dark:text-red-300 border-r border-hairline w-36">TIDAK DAPAT DITINDAKLANJUTI</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($groupedLhp as $group)
                        <tr class="hover:bg-surface-subtle/60 transition-colors">
                            <!-- Column 1: No. LHP & Tgl LHP -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($group->no_lhp)
                                    <span class="font-mono font-semibold text-blue-800 dark:text-blue-300 text-xs block">
                                        {{ $group->no_lhp }}
                                    </span>
                                @else
                                    <span class="text-ink-muted text-[11px] font-mono italic">SPT: {{ $group->penugasan?->no_spt }}</span>
                                @endif

                                @if($group->tgl_lhp)
                                    <span class="block text-[11px] text-ink-muted font-medium mt-0.5 tabular-nums">Tgl: {{ $group->tgl_lhp->format('d/m/Y') }}</span>
                                @endif
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-surface-subtle text-ink-secondary font-semibold rounded-[4px] border border-hairline mt-1 tabular-nums">
                                    {{ $group->total_rekomendasi }} Rekomendasi
                                </span>
                            </td>

                            <!-- Column 2: Judul LHP -->
                            <td class="py-3.5 px-4">
                                <p class="font-semibold text-ink leading-snug">
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
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-[4px] text-[10px] font-semibold bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ $oname }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-[11px] text-ink-muted font-medium block truncate max-w-xs">
                                            Objek: {{ $group->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? '-' }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Column 3: Nilai Yang Dilakukan Pengawasan (Sebelah Kiri NILAI RP & SETOR) -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] bg-blue-50/20 dark:bg-blue-950/10 border-x border-hairline tabular-nums">
                                <span class="block font-bold text-blue-800 dark:text-blue-300">
                                    {{ $group->formatted_nilai_diawasi }}
                                </span>
                            </td>

                            <!-- Column 4: Nilai Rekomendasi & Realisasi Setor -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] tabular-nums">
                                <span class="block font-semibold text-ink">Target: {{ $group->formatted_nilai_target }}</span>
                                <span class="block font-semibold text-emerald-800 dark:text-emerald-300">Setor: {{ $group->formatted_total_setor }}</span>
                            </td>

                            <!-- Column 5: Jumlah SESUAI -->
                            <td class="py-3.5 px-3 text-center border-x border-hairline bg-blue-50/30 dark:bg-blue-950/20 tabular-nums">
                                @if($group->count_sesuai > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-blue-700 text-white font-bold rounded-[4px] text-xs">
                                        {{ $group->count_sesuai }}
                                    </span>
                                @else
                                    <span class="text-ink-muted font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 6: Jumlah BELUM SESUAI -->
                            <td class="py-3.5 px-3 text-center border-r border-hairline bg-emerald-50/30 dark:bg-emerald-950/20 tabular-nums">
                                @if($group->count_belum_sesuai > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-brand text-white font-bold rounded-[4px] text-xs">
                                        {{ $group->count_belum_sesuai }}
                                    </span>
                                @else
                                    <span class="text-ink-muted font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 7: Jumlah BELUM DITINDAKLANJUTI -->
                            <td class="py-3.5 px-3 text-center border-r border-hairline bg-amber-50/30 dark:bg-amber-950/20 tabular-nums">
                                @if($group->count_belum > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-amber-700 text-white font-bold rounded-[4px] text-xs">
                                        {{ $group->count_belum }}
                                    </span>
                                @else
                                    <span class="text-ink-muted font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 8: Jumlah TIDAK DAPAT DITINDAKLANJUTI -->
                            <td class="py-3.5 px-3 text-center border-r border-hairline bg-red-50/30 dark:bg-red-950/20 tabular-nums">
                                @if($group->count_tdt > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-red-700 text-white font-bold rounded-[4px] text-xs">
                                        {{ $group->count_tdt }}
                                    </span>
                                @else
                                    <span class="text-ink-muted font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Column 9: Action Button -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('tindak-lanjut.show', $group->first_id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-brand hover:bg-brand-hover text-white font-semibold rounded-[6px] text-xs transition-colors" title="Buka Detail LHP Lengkap di Halaman Baru">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Buka LHP ({{ $group->total_rekomendasi }})</span>
                                    </a>
                                    <a href="{{ route('tindak-lanjut.export_lhp', $group->first_id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-hairline-strong font-semibold rounded-[6px] text-xs transition-colors" title="Ekspor Matriks Tindak Lanjut LHP ini ke Excel (.xlsx)">
                                        <svg class="w-3.5 h-3.5 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Excel</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-ink-muted">
                                Belum ada data LHP & catatan temuan rekomendasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah Catatan Temuan & Rekomendasi (Multi Temuan & Multi Rekomendasi) -->
    <div id="modalTambahTemuanMulti" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-4xl w-full p-6 border border-hairline text-xs space-y-4"
            x-data="{
                selectedObjekList: [],
                selectedIsAudit: false,
                kamusTemuan: {{ Js::from($kodeAtributTemuanList) }},
                kamusRekomendasi: {{ Js::from($kodeAtributRekomendasiList) }},
                items: [
                    {
                        objek_penugasan_id: '',
                        kode_atribut_temuan_id: '',
                        kode_temuan_lengkap: '',
                        saran_rekomendasi: [],
                        temuan: '',
                        rekomendasi: [
                            { 
                                kode_atribut_rekomendasi_id: '',
                                kode_rekomendasi: '',
                                uraian: '', 
                                nilai_diawasi_rp: '', 
                                nilai_rekomendasi_rp: '', 
                                tanggal_target: '' 
                            }
                        ]
                    }
                ],
                addTemuan() {
                    let defaultObj = (this.selectedObjekList && this.selectedObjekList.length === 1) ? this.selectedObjekList[0].id : '';
                    this.items.push({
                        objek_penugasan_id: defaultObj,
                        kode_atribut_temuan_id: '',
                        kode_temuan_lengkap: '',
                        saran_rekomendasi: [],
                        temuan: '',
                        rekomendasi: [{ 
                            kode_atribut_rekomendasi_id: '',
                            kode_rekomendasi: '',
                            uraian: '', 
                            nilai_diawasi_rp: '', 
                            nilai_rekomendasi_rp: '', 
                            tanggal_target: '' 
                        }]
                    });
                },
                removeTemuan(tIndex) {
                    if (this.items.length > 1) {
                        this.items.splice(tIndex, 1);
                    }
                },
                addRekomendasi(tIndex) {
                    this.items[tIndex].rekomendasi.push({ 
                        kode_atribut_rekomendasi_id: '',
                        kode_rekomendasi: '',
                        uraian: '', 
                        nilai_diawasi_rp: '', 
                        nilai_rekomendasi_rp: '', 
                        tanggal_target: '' 
                    });
                },
                removeRekomendasi(tIndex, rIndex) {
                    if (this.items[tIndex].rekomendasi.length > 1) {
                        this.items[tIndex].rekomendasi.splice(rIndex, 1);
                    }
                },
                onPilihTemuanKamus(tIndex, temuanId) {
                    if (!temuanId) {
                        this.items[tIndex].kode_atribut_temuan_id = '';
                        this.items[tIndex].kode_temuan_lengkap = '';
                        this.items[tIndex].saran_rekomendasi = [];
                        return;
                    }
                    let found = this.kamusTemuan.find(k => k.id == temuanId);
                    if (found) {
                        this.items[tIndex].kode_atribut_temuan_id = found.id;
                        this.items[tIndex].kode_temuan_lengkap = found.kode_lengkap;
                        if (!this.items[tIndex].temuan || this.items[tIndex].temuan.trim() === '') {
                            this.items[tIndex].temuan = found.deskripsi;
                        }
                        if (found.alternatif_rekomendasi) {
                            this.items[tIndex].saran_rekomendasi = found.alternatif_rekomendasi.split(',').map(s => s.trim().padStart(2, '0'));
                        } else {
                            this.items[tIndex].saran_rekomendasi = [];
                        }
                    }
                },
                terapkanSaranRekomendasi(tIndex, rIndex, rekKode) {
                    let found = this.kamusRekomendasi.find(r => r.kode == rekKode);
                    if (found) {
                        this.items[tIndex].rekomendasi[rIndex].kode_atribut_rekomendasi_id = found.id;
                        this.items[tIndex].rekomendasi[rIndex].kode_rekomendasi = found.kode;
                        if (!this.items[tIndex].rekomendasi[rIndex].uraian || this.items[tIndex].rekomendasi[rIndex].uraian.trim() === '') {
                            this.items[tIndex].rekomendasi[rIndex].uraian = found.deskripsi;
                        }
                    }
                },
                onPilihRekomendasiKamus(tIndex, rIndex, rekId) {
                    let found = this.kamusRekomendasi.find(r => r.id == rekId);
                    if (found) {
                        this.items[tIndex].rekomendasi[rIndex].kode_atribut_rekomendasi_id = found.id;
                        this.items[tIndex].rekomendasi[rIndex].kode_rekomendasi = found.kode;
                        if (!this.items[tIndex].rekomendasi[rIndex].uraian || this.items[tIndex].rekomendasi[rIndex].uraian.trim() === '') {
                            this.items[tIndex].rekomendasi[rIndex].uraian = found.deskripsi;
                        }
                    } else {
                        this.items[tIndex].rekomendasi[rIndex].kode_atribut_rekomendasi_id = '';
                        this.items[tIndex].rekomendasi[rIndex].kode_rekomendasi = '';
                    }
                }
            }">

            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-semibold text-ink text-base">Tambah Catatan Temuan & Rekomendasi (LHP)</h3>
                    <p class="text-xs text-ink-secondary mt-0.5">Input LHP, Judul LHP, Temuan & Rekomendasi Baku (PermenPAN-RB No. 42/2011) serta pemetaan Objek/OPD sasaran.</p>
                </div>
                <button type="button" onclick="document.getElementById('modalTambahTemuanMulti').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" action="{{ route('tindak-lanjut.store') }}" enctype="multipart/form-data" class="space-y-5 mt-4">
                @csrf

                <!-- Section 1: Header Penugasan & LHP Metadata -->
                <div class="p-4 bg-surface-subtle rounded-[10px] border border-hairline space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Searchable Combobox No. SPT -->
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('penugasan_id') }}',
                            selectedLabel: '-- Pilih Nomor SPT --',
                            options: [
                                @foreach($penugasanList as $p)
                                    @php
                                        $isAudit = ($p->jenisPenugasan && (stripos($p->jenisPenugasan->kategori ?? '', 'audit') !== false || stripos($p->jenisPenugasan->nama ?? '', 'audit') !== false));
                                    @endphp
                                    { 
                                        id: '{{ $p->id }}', 
                                        label: 'No. SPT: {{ addslashes($p->no_spt) }} | {{ addslashes($p->irban?->nama_irban ?? 'Semua Irban') }} ({{ addslashes(Str::limit($p->uraian_penugasan, 50)) }})',
                                        isAudit: {{ $isAudit ? 'true' : 'false' }},
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
                                selectedIsAudit = opt.isAudit || false;
                                
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
                                        selectedIsAudit = found.isAudit || false;
                                        if (selectedObjekList.length === 1) {
                                            items.forEach(it => { it.objek_penugasan_id = selectedObjekList[0].id; });
                                        }
                                    }
                                }
                            }
                        }" class="relative">
                            <label class="block font-semibold mb-1 text-brand-ink">Pilih Surat Tugas (No. SPT) <span class="text-red-700 dark:text-red-400">*</span></label>

                            <input type="hidden" name="penugasan_id" :value="selectedId" required>

                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 bg-surface border border-hairline-strong rounded-[6px] text-left text-xs focus:outline-none focus:ring-2 focus:ring-brand">
                                <span class="truncate font-semibold text-ink" x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-ink-muted transition-transform duration-150 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition.origin.top class="absolute z-50 left-0 right-0 mt-1 bg-surface border border-hairline rounded-[10px] shadow-lg p-2 text-xs">
                                <input type="text" x-model="search" placeholder="Cari Nomor SPT / Uraian..." class="w-full px-2.5 py-1.5 mb-2 rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">

                                <div class="max-h-48 overflow-y-auto space-y-1">
                                    <template x-for="opt in filteredOptions" :key="opt.id">
                                        <button type="button" @click="select(opt)" class="w-full text-left p-2 hover:bg-brand-soft rounded-[6px] font-medium text-ink transition-colors" :class="{'bg-brand-soft text-brand-ink font-semibold': selectedId == opt.id}">
                                            <div class="flex items-center justify-between gap-2">
                                                <span x-text="opt.label"></span>
                                                <span x-show="opt.isAudit" class="text-[10px] font-semibold px-1.5 py-0.5 rounded-[4px] bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 shrink-0">AUDIT</span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold mb-1 text-ink">Lampiran Dokumen Dasar (File PDF LHP/NHP)</label>
                            <input type="file" name="berkas_dasar_lhp" accept=".pdf" class="w-full text-xs text-ink-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-[6px] file:border file:border-red-200 dark:file:border-red-800 file:text-xs file:font-semibold file:bg-red-50 dark:file:bg-red-950/50 file:text-red-800 dark:file:text-red-300 hover:file:bg-red-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-3 border-t border-hairline">
                        <div class="sm:col-span-3">
                            <label class="block font-semibold mb-1 text-blue-800 dark:text-blue-300">Nomor LHP</label>
                            <input type="text" name="no_lhp" placeholder="mis. 700/85/LHP/406.008/2026" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block font-semibold mb-1 text-ink">Judul LHP</label>
                            <input type="text" name="judul_lhp" placeholder="mis. LHP atas Pengelolaan Keuangan & Aset OPD..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block font-semibold mb-1 text-blue-800 dark:text-blue-300">Nilai Yang Diawasi (Rp)</label>
                            <input type="text" oninput="formatRupiahInput(this)" name="nilai_diawasi_rp" placeholder="0" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-blue-800 dark:text-blue-300 tabular-nums">
                            <p class="text-[10px] text-ink-muted mt-0.5">(Jika Tidak Ada Berikan Input 0)</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-semibold mb-1 text-ink">Tanggal LHP</label>
                            <input type="date" name="tgl_lhp" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                    <template x-for="(tItem, tIndex) in items" :key="tIndex">
                        <div class="p-4 bg-surface rounded-[10px] border border-hairline space-y-3 relative">
                            <div class="flex items-center justify-between border-b border-hairline pb-2">
                                <span class="font-bold text-brand-ink text-xs flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand"></span>
                                    <span x-text="'TEMUAN ' + (tIndex + 1) + ' / CATATAN ' + (tIndex + 1)"></span>
                                    <span x-show="tItem.kode_temuan_lengkap" class="font-mono text-[10px] font-semibold px-2 py-0.5 rounded-[4px] bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800" x-text="'Kode: ' + tItem.kode_temuan_lengkap"></span>
                                </span>
                                <button type="button" @click="removeTemuan(tIndex)" x-show="items.length > 1" class="text-red-800 dark:text-red-300 hover:underline text-xs font-semibold flex items-center gap-1">
                                    &times; Hapus Temuan / Catatan ini
                                </button>
                            </div>

                            <!-- Pilihan Objek Sasaran Jika SPT Memiliki Banyak Objek -->
                            <div x-show="selectedObjekList.length > 1" class="p-3 bg-amber-50/80 dark:bg-amber-950/40 rounded-[6px] border border-amber-200 dark:border-amber-800 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="block font-semibold text-amber-900 dark:text-amber-300 text-[11px]">
                                        Objek / Instansi Sasaran untuk Temuan Ini:
                                    </label>
                                    <span class="text-[11px] text-amber-900 dark:text-amber-300 font-semibold">SPT Multi-Objek (<span x-text="selectedObjekList.length"></span> instansi)</span>
                                </div>
                                <select :name="'items[' + tIndex + '][objek_penugasan_id]'" x-model="tItem.objek_penugasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-ink">
                                    <option value="">-- [Umum] Ditujukan untuk Seluruh Objek dalam SPT Ini --</option>
                                    <template x-for="obj in selectedObjekList" :key="obj.id">
                                        <option :value="obj.id" x-text="obj.nama"></option>
                                    </template>
                                </select>
                                <p class="text-[11px] text-amber-900 dark:text-amber-300">Pilih instansi spesifik agar temuan ini hanya tampil dan dapat ditindaklanjuti oleh OPD bersangkutan.</p>
                            </div>

                            <div x-show="selectedObjekList.length === 1" class="flex items-center gap-2 text-ink-secondary text-[11px] font-medium bg-surface-subtle p-2.5 rounded-[6px] border border-hairline">
                                <span class="text-blue-800 dark:text-blue-300 font-semibold">Objek Sasaran:</span>
                                <span class="font-semibold text-ink" x-text="selectedObjekList[0] ? selectedObjekList[0].nama : ''"></span>
                                <span class="text-[10px] text-emerald-800 dark:text-emerald-300 font-semibold">(Otomatis Terkunci)</span>
                                <input type="hidden" :name="'items[' + tIndex + '][objek_penugasan_id]'" :value="selectedObjekList[0] ? selectedObjekList[0].id : ''">
                            </div>

                            <!-- Fitur Kamus Temuan PermenPAN-RB (Dropdown & Quick Select) -->
                            <div class="p-3 bg-emerald-50/70 dark:bg-emerald-950/30 rounded-[6px] border border-emerald-200 dark:border-emerald-800 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="block font-semibold text-emerald-900 dark:text-emerald-200 text-[11px]">
                                        Klasifikasi Kode Atribut Temuan (PermenPAN-RB No. 42/2011):
                                    </label>
                                    <span class="text-[10px] text-emerald-800 dark:text-emerald-300 font-semibold">Kamus Baku 89 Jenis Temuan</span>
                                </div>
                                <select @change="onPilihTemuanKamus(tIndex, $event.target.value)" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs text-ink">
                                    <option value="">-- Pilih Jenis Temuan Baku PermenPAN-RB (Opsional) --</option>
                                    <optgroup label="KELOMPOK 1: TEMUAN KETIDAKPATUHAN TERHADAP PERATURAN">
                                        @foreach($kodeAtributTemuanList->where('kode_kelompok', '1') as $kt)
                                            <option value="{{ $kt->id }}">{{ $kt->kode_lengkap }} - {{ $kt->deskripsi }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="KELOMPOK 2: KELEMAHAN SISTEM PENGENDALIAN INTERN (SPI)">
                                        @foreach($kodeAtributTemuanList->where('kode_kelompok', '2') as $kt)
                                            <option value="{{ $kt->id }}">{{ $kt->kode_lengkap }} - {{ $kt->deskripsi }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="KELOMPOK 3: 3E (KETIDAKEFEKTIFAN, KETIDAKEFISIENAN, KETIDAKHEMATAN)">
                                        @foreach($kodeAtributTemuanList->where('kode_kelompok', '3') as $kt)
                                            <option value="{{ $kt->id }}">{{ $kt->kode_lengkap }} - {{ $kt->deskripsi }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <input type="hidden" :name="'items[' + tIndex + '][kode_atribut_temuan_id]'" :value="tItem.kode_atribut_temuan_id">
                                <input type="hidden" :name="'items[' + tIndex + '][kode_temuan_lengkap]'" :value="tItem.kode_temuan_lengkap">

                                <!-- Quick Badge Saran Rekomendasi PermenPAN-RB -->
                                <div x-show="tItem.saran_rekomendasi && tItem.saran_rekomendasi.length > 0" class="pt-1.5 flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[11px] font-semibold text-ink-secondary">Alternatif Rekomendasi Disarankan:</span>
                                    <template x-for="rekKode in tItem.saran_rekomendasi" :key="rekKode">
                                        <button type="button" @click="terapkanSaranRekomendasi(tIndex, 0, rekKode)" class="px-2 py-0.5 rounded-[4px] text-[10px] font-semibold bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-700 transition-colors" :title="'Klik untuk terapkan ke Rekomendasi 1'">
                                            + Kode <span x-text="rekKode"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold mb-1 text-ink" x-text="'Uraian Temuan ' + (tIndex + 1) + ' / Catatan ' + (tIndex + 1) + ' *'"></label>
                                <textarea :name="'items[' + tIndex + '][temuan]'" x-model="tItem.temuan" required rows="2" placeholder="Tuliskan uraian temuan / catatan hasil pemeriksaan di sini..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                            </div>

                            <div class="pl-3 border-l-2 border-brand/40 space-y-3 mt-3">
                                <label class="block font-semibold text-ink text-[11px]" x-text="'Rekomendasi / Saran untuk Temuan ' + (tIndex + 1) + ' / Catatan ' + (tIndex + 1) + ':'"></label>

                                <template x-for="(rItem, rIndex) in tItem.rekomendasi" :key="rIndex">
                                    <div class="p-3.5 bg-surface-subtle rounded-[6px] border border-hairline space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-ink-secondary text-[11px]" x-text="'Rekomendasi / Saran ' + (rIndex + 1)"></span>
                                                <span x-show="rItem.kode_rekomendasi" class="font-mono text-[10px] font-semibold px-1.5 py-0.5 rounded-[4px] bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800" x-text="'Kode: ' + rItem.kode_rekomendasi"></span>
                                            </div>
                                            <button type="button" @click="removeRekomendasi(tIndex, rIndex)" x-show="tItem.rekomendasi.length > 1" class="text-red-800 dark:text-red-300 hover:underline text-[11px] font-semibold">
                                                &times; Hapus Rekomendasi / Saran
                                            </button>
                                        </div>

                                        <!-- Pilihan Kode Rekomendasi PermenPAN-RB -->
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                                            <div class="sm:col-span-12">
                                                <label class="block text-[11px] text-ink-secondary mb-0.5">Klasifikasi Kode Rekomendasi Baku PermenPAN-RB (Opsional)</label>
                                                <select :value="rItem.kode_atribut_rekomendasi_id" @change="onPilihRekomendasiKamus(tIndex, rIndex, $event.target.value)" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                                    <option value="">-- Pilih Kode Rekomendasi PermenPAN-RB (01 s/d 14) --</option>
                                                    @foreach($kodeAtributRekomendasiList as $kr)
                                                        <option value="{{ $kr->id }}">{{ $kr->kode }} - {{ $kr->deskripsi }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][kode_atribut_rekomendasi_id]'" :value="rItem.kode_atribut_rekomendasi_id">
                                                <input type="hidden" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][kode_rekomendasi]'" :value="rItem.kode_rekomendasi">
                                            </div>

                                            <div class="sm:col-span-6">
                                                <label class="block text-[11px] text-ink-secondary mb-0.5">Uraian Rekomendasi / Saran <span class="text-red-700 dark:text-red-400">*</span></label>
                                                <input type="text" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][uraian]'" x-model="rItem.uraian" required placeholder="Uraian rekomendasi / saran wajib..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label class="block text-[11px] font-semibold text-brand-ink mb-0.5">Nilai Rekomendasi / Saran (Rp)</label>
                                                <input type="text" oninput="formatRupiahInput(this)" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][nilai_rekomendasi_rp]'" x-model="rItem.nilai_rekomendasi_rp" placeholder="0" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-semibold text-brand-ink tabular-nums">
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label class="block text-[11px] text-ink-secondary mb-0.5">Target Waktu</label>
                                                <input type="date" :name="'items[' + tIndex + '][rekomendasi][' + rIndex + '][tanggal_target]'" x-model="rItem.tanggal_target" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addRekomendasi(tIndex)" class="px-3 py-1.5 bg-brand-soft text-brand-ink hover:bg-emerald-100 dark:hover:bg-emerald-950 font-semibold rounded-[6px] text-[11px] inline-flex items-center gap-1 border border-brand-border transition-colors">
                                    + Tambah Rekomendasi / Saran (Rekomendasi / Saran <span x-text="tItem.rekomendasi.length + 1"></span>)
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-3 flex flex-wrap items-center justify-between gap-3 border-t border-hairline">
                    <button type="button" @click="addTemuan()" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0">
                        <span>+ Tambah Temuan / Catatan Lain (Temuan / Catatan <span x-text="items.length + 1"></span>)</span>
                    </button>

                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="document.getElementById('modalTambahTemuanMulti').classList.add('hidden')" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">Batal</button>
                        <button type="submit" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">Simpan Semua Temuan / Catatan & Rekomendasi / Saran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dynamic Live Currency Format Input (Ribuan Titik Koma)
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
