<x-app-layout>
    <x-slot name="header">
        Realisasi PKPPT: Perbandingan Rencana (PKPPT) vs Realisasi
    </x-slot>

    <!-- Main Container with Alpine State for Detail Modal -->
    <div x-data="{
        showModalDetail: false,
        activePkppt: null,
        openDetail(item) {
            this.activePkppt = item;
            this.showModalDetail = true;
        }
    }">

        <!-- Header & Filter -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink tracking-tight">Monitoring Realisasi PKPPT</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Perbandingan otomatis rencana kegiatan PKPPT dengan Surat Perintah Tugas (SPT) realisasi.</p>
            </div>

            <form method="GET" action="{{ route('kegiatan-pengawasan.index') }}" class="flex items-center gap-3">
                <div>
                    <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->isPimpinanOrAdmin())
                <div>
                    <select name="irban_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink">
                        <option value="">-- Semua Irban --</option>
                        @foreach($irbans as $irban)
                            <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </form>
        </div>

        <!-- Summary Indicators Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5 mb-6">
            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3">
                <div class="w-10 h-10 rounded-[6px] bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-800 dark:text-emerald-300 tabular-nums">{{ $rekap['indikator_hijau'] }}</p>
                    <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-wider">Selesai Target</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3">
                <div class="w-10 h-10 rounded-[6px] bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-300 tabular-nums">{{ $rekap['indikator_biru'] }}</p>
                    <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-wider">Sedang Berjalan</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3">
                <div class="w-10 h-10 rounded-[6px] bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-900 dark:text-amber-300 tabular-nums">{{ $rekap['indikator_kuning'] }}</p>
                    <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-wider">Terlambat</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3">
                <div class="w-10 h-10 rounded-[6px] bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-800 dark:text-red-300 tabular-nums">{{ $rekap['indikator_merah'] + $rekap['indikator_abu'] }}</p>
                    <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-wider">Belum Dimulai</p>
                </div>
            </div>

            <div class="bg-surface p-4 rounded-[10px] border border-hairline flex items-center gap-3 col-span-2 sm:col-span-1">
                <div class="w-10 h-10 rounded-[6px] bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 text-purple-800 dark:text-purple-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-800 dark:text-purple-300 tabular-nums">{{ $rekap['total_realisasi'] }}</p>
                    <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-wider">Total Realisasi SPT</p>
                </div>
            </div>
        </div>

        <!-- Table Perbandingan PKPPT vs Realisasi -->
        <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                        <tr>
                            <th class="py-3.5 px-4 w-10 text-center">No</th>
                            <th class="py-3.5 px-4">Area Pengawasan</th>
                            <th class="py-3.5 px-4">Pelaksana (Irban)</th>
                            <th class="py-3.5 px-4">Jadwal Rencana</th>
                            <th class="py-3.5 px-4 text-center">Rencana Laporan</th>
                            <th class="py-3.5 px-4 text-center">Realisasi SPT</th>
                            <th class="py-3.5 px-4 text-center">Realisasi Selesai</th>
                            <th class="py-3.5 px-4 text-center">Indikator Monitoring</th>
                            <th class="py-3.5 px-4 text-center w-28">Rincian Tim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline">
                        @forelse($listPkppt as $index => $item)
                            @php
                                $jsonItem = [
                                    'id'                       => $item->id,
                                    'area_pengawasan'          => $item->area_pengawasan,
                                    'jenis_pengawasan'         => $item->jenis_pengawasan,
                                    'sasaran'                  => $item->sasaran ?? '-',
                                    'nama_irban'               => $item->irban?->nama_irban ?? 'Semua Irban',
                                    'jumlah_laporan_rencana'   => $item->jumlah_laporan_rencana,
                                    'rencana_mulai'            => $item->rencana_mulai->format('d/m/Y'),
                                    'rencana_selesai'          => $item->rencana_selesai_laporan->format('d/m/Y'),
                                    'indikator_label'          => $item->indikator_label,
                                    'indikator'                => $item->indikator,
                                    'penugasan'                => $item->penugasan->map(function($p) {
                                        return [
                                            'id'                 => $p->id,
                                            'no_spt'             => $p->no_spt,
                                            'uraian_penugasan'   => $p->uraian_penugasan,
                                            'tanggal_mulai'      => $p->tanggal_mulai ? $p->tanggal_mulai->format('d/m/Y') : '-',
                                            'tanggal_selesai'    => $p->tanggal_selesai ? $p->tanggal_selesai->format('d/m/Y') : '-',
                                            'status_label'       => $p->status_label,
                                            'status'             => $p->status,
                                            'objek_names'        => $p->objekPenugasan->pluck('nama')->implode(', '),
                                            'tim'                => $p->tim->sortBy(function($t) {
                                                return match($t->peran) {
                                                    'penanggung_jawab' => 1,
                                                    'wakil_penanggung_jawab' => 2,
                                                    'pengendali_teknis' => 3,
                                                    'ketua_tim' => 4,
                                                    'anggota_tim' => 5,
                                                    default => 6,
                                                };
                                            })->map(function($t) {
                                                return [
                                                    'nama'        => $t->user?->name ?? 'Personil Tim',
                                                    'nip'         => $t->user?->nip ?? '-',
                                                    'peran_label' => $t->peran_label,
                                                ];
                                            })->values(),
                                            'lhp_items'          => $p->tindakLanjut->map(function($tl) {
                                                return [
                                                    'id'         => $tl->id,
                                                    'no_lhp'     => $tl->no_lhp ?? '-',
                                                    'judul_lhp'  => $tl->judul_lhp ?? '-',
                                                    'tgl_lhp'    => $tl->tgl_lhp ? $tl->tgl_lhp->format('d/m/Y') : '-',
                                                ];
                                            })->values(),
                                        ];
                                    })->values(),
                                ];
                            @endphp
                            <tr class="hover:bg-surface-subtle/70 transition-colors">
                                <td class="py-3 px-4 font-semibold text-center text-ink-muted tabular-nums">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-ink">{{ $item->area_pengawasan }}</p>
                                    <span class="text-[11px] text-ink-muted">Jenis: {{ $item->jenis_pengawasan }} | Sasaran: {{ $item->sasaran ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-ink-secondary">
                                    {{ $item->irban?->nama_irban ?? 'Semua Irban' }}
                                </td>
                                <td class="py-3 px-4 text-ink-secondary font-mono text-[11px] tabular-nums">
                                    {{ $item->rencana_mulai->format('d/m/Y') }} - {{ $item->rencana_selesai_laporan->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-ink tabular-nums">
                                    {{ $item->jumlah_laporan_rencana }} Laporan
                                </td>
                                <td class="py-3 px-4 text-center font-semibold">
                                    <button type="button" @click="openDetail({{ json_encode($jsonItem) }})" class="px-2.5 py-1 bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-[6px] text-xs font-semibold inline-flex items-center gap-1 border border-blue-200 dark:border-blue-800 transition-colors tabular-nums" title="Klik untuk membuka rincian penugasan tim">
                                        <span>{{ $item->penugasan->count() }} SPT</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-brand-ink tabular-nums">
                                    {{ $item->penugasan->where('status', 'selesai')->count() }} Selesai
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-[4px] text-[10px] font-bold uppercase
                                        {{ $item->indikator === 'hijau' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                                        {{ $item->indikator === 'kuning' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : '' }}
                                        {{ $item->indikator === 'merah' ? 'bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800' : '' }}
                                        {{ $item->indikator === 'biru' ? 'bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}
                                        {{ $item->indikator === 'abu' ? 'bg-surface-subtle text-ink-secondary border border-hairline' : '' }}">
                                        {{ $item->indikator_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <button type="button" @click="openDetail({{ json_encode($jsonItem) }})" class="ledger-btn-secondary !min-h-0 !py-1.5 !px-3 !text-xs mx-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>Rincian Tim</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-ink-muted">Belum ada data perencanaan PKPPT pada tahun ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pop-up Modal Rincian Penugasan & Tim Pengawasan (Keterkaitan Database Real-Time) -->
        <div x-show="showModalDetail" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-surface rounded-[10px] shadow-lg max-w-4xl w-full p-6 border border-hairline text-xs space-y-4">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-hairline">
                    <div>
                        <span class="text-xs font-semibold text-blue-800 dark:text-blue-300 uppercase tracking-wider block">Rincian Realisasi Penugasan PKPPT</span>
                        <h3 class="font-bold text-ink text-base leading-tight mt-0.5" x-text="activePkppt?.area_pengawasan"></h3>
                        <p class="text-[11px] text-ink-secondary mt-0.5">
                            Pelaksana: <span class="font-semibold text-ink" x-text="activePkppt?.nama_irban"></span> |
                            Jadwal: <span class="font-mono font-semibold text-ink tabular-nums" x-text="activePkppt?.rencana_mulai + ' - ' + activePkppt?.rencana_selesai"></span> |
                            Target Laporan: <span class="font-semibold text-brand-ink tabular-nums" x-text="activePkppt?.jumlah_laporan_rencana + ' Laporan'"></span>
                        </p>
                    </div>
                    <button type="button" @click="showModalDetail = false" class="text-ink-muted hover:text-ink text-xl font-bold p-1 leading-none" aria-label="Tutup">&times;</button>
                </div>

                <!-- Modal Body: Loop List Penugasan (SPT) -->
                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    <template x-if="activePkppt?.penugasan && activePkppt.penugasan.length > 0">
                        <div class="space-y-4">
                            <template x-for="(st, sIdx) in activePkppt.penugasan" :key="st.id">
                                <div class="p-5 bg-surface-subtle rounded-[10px] border border-hairline space-y-4">
                                    
                                    <!-- Header ST -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-hairline">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-[6px] bg-brand text-white font-bold flex items-center justify-center text-xs tabular-nums" x-text="sIdx + 1"></span>
                                            <div>
                                                <span class="text-[10px] text-ink-muted font-semibold uppercase tracking-wider block">Nomor Surat Tugas (No. SPT):</span>
                                                <span class="font-mono font-bold text-sm text-blue-800 dark:text-blue-300" x-text="st.no_spt"></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 rounded-[4px] text-xs font-bold uppercase tracking-wider"
                                                :class="{
                                                    'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': st.status === 'selesai',
                                                    'bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800': st.status === 'berjalan',
                                                    'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800': st.status === 'belum_berjalan'
                                                }"
                                                x-text="'Status SPT: ' + st.status_label">
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Grid Info Tanggal & Objek OPD -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="p-3 bg-surface rounded-[6px] border border-hairline space-y-1">
                                            <span class="font-semibold text-ink-muted text-[10px] uppercase block">Tanggal Penugasan (SPT):</span>
                                            <p class="font-mono font-semibold text-ink tabular-nums" x-text="st.tanggal_mulai + ' s/d ' + st.tanggal_selesai"></p>
                                        </div>

                                        <div class="p-3 bg-surface rounded-[6px] border border-hairline space-y-1">
                                            <span class="font-semibold text-ink-muted text-[10px] uppercase block">Objek OPD Penugasan Target:</span>
                                            <p class="font-semibold text-ink" x-text="st.objek_names"></p>
                                        </div>
                                    </div>

                                    <!-- Personil Tim yang Berjalan -->
                                    <div class="p-3.5 bg-surface rounded-[6px] border border-hairline space-y-2">
                                        <span class="font-semibold text-ink text-xs block">
                                            Personil Tim Pengawasan yang Berjalan:
                                        </span>

                                        <template x-if="st.tim && st.tim.length > 0">
                                            <div class="space-y-1.5 p-2 bg-surface-subtle rounded-[6px] border border-hairline">
                                                <template x-for="pTim in st.tim" :key="pTim.nama">
                                                    <div class="flex items-center justify-between text-xs py-1 px-2 hover:bg-surface rounded-[4px] transition-colors">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-3.5 h-3.5 text-brand-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            <span class="font-semibold text-ink" x-text="pTim.nama"></span>
                                                            <span class="text-ink-secondary font-medium" x-text="'(' + pTim.peran_label + ')'"></span>
                                                        </div>
                                                        <span class="text-[10px] text-ink-muted font-mono tabular-nums" x-text="pTim.nip !== '-' ? 'NIP: ' + pTim.nip : ''"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!st.tim || st.tim.length === 0">
                                            <p class="text-ink-muted text-[11px] italic p-2">Belum ada rincian susunan tim personil yang didaftarkan pada SPT ini.</p>
                                        </template>
                                    </div>

                                    <!-- Rincian Dokumen LHP Hasil Pengawasan -->
                                    <div class="p-3.5 bg-blue-50/70 dark:bg-blue-950/30 rounded-[6px] border border-blue-200 dark:border-blue-800 space-y-2">
                                        <span class="font-semibold text-blue-800 dark:text-blue-300 text-xs block">
                                            Rincian Laporan Hasil Pengawasan (LHP):
                                        </span>

                                        <template x-if="st.lhp_items && st.lhp_items.length > 0">
                                            <div class="space-y-2">
                                                <template x-for="lhp in st.lhp_items" :key="lhp.id">
                                                    <div class="p-3 bg-surface rounded-[6px] border border-hairline flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                        <div>
                                                            <span class="font-mono font-semibold text-blue-800 dark:text-blue-300 text-xs block" x-text="'Nomor LHP: ' + lhp.no_lhp"></span>
                                                            <p class="font-semibold text-ink mt-0.5" x-text="'Judul: ' + lhp.judul_lhp"></p>
                                                            <span class="text-[10px] text-ink-muted tabular-nums" x-text="'Tanggal Terbit LHP: ' + lhp.tgl_lhp"></span>
                                                        </div>

                                                        <a :href="'/tindak-lanjut/' + lhp.id" target="_blank" class="px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-[6px] text-xs inline-flex items-center gap-1 shrink-0 transition-colors">
                                                            <span>Buka Matriks LHP</span>
                                                        </a>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!st.lhp_items || st.lhp_items.length === 0">
                                            <p class="text-ink-muted text-[11px] italic">Belum ada dokumen LHP yang terbit untuk Surat Tugas (SPT) ini.</p>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!activePkppt?.penugasan || activePkppt.penugasan.length === 0">
                        <div class="p-8 text-center bg-surface-subtle rounded-[10px] border border-hairline text-ink-muted">
                            Belum ada Surat Tugas (SPT) realisasi yang dikaitkan dengan kegiatan PKPPT ini.
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-hairline flex items-center justify-end">
                    <button type="button" @click="showModalDetail = false" class="ledger-btn-secondary !text-xs">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
