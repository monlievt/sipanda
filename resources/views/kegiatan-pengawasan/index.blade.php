<x-app-layout>
    <x-slot name="header">
        Kegiatan Pengawasan — Perbandingan Rencana (PKPPT) vs Realisasi
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
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Monitoring Realisasi PKPPT</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbandingan otomatis rencana kegiatan PKPPT dengan Surat Perintah Tugas (SPT) realisasi.</p>
            </div>

            <form method="GET" action="{{ route('kegiatan-pengawasan.index') }}" class="flex items-center gap-3">
                <div>
                    <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!auth()->user()->hasRole(['irban', 'admin_irban']))
                <div>
                    <select name="irban_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500">
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
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    🟢
                </div>
                <div>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $rekap['indikator_hijau'] }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Selesai Target</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-lg">
                    🔵
                </div>
                <div>
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $rekap['indikator_biru'] }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Sedang Berjalan</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg">
                    🟡
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $rekap['indikator_kuning'] }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Terlambat</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-lg">
                    🔴
                </div>
                <div>
                    <p class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $rekap['indikator_merah'] + $rekap['indikator_abu'] }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Belum Dimulai</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-3 col-span-2 sm:col-span-1">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold text-lg">
                    📋
                </div>
                <div>
                    <p class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ $rekap['total_realisasi'] }}</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase">Total Realisasi SPT</p>
                </div>
            </div>
        </div>

        <!-- Table Perbandingan PKPPT vs Realisasi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
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
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
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
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $item->area_pengawasan }}</p>
                                    <span class="text-[10px] text-slate-400">Jenis: {{ $item->jenis_pengawasan }} | Sasaran: {{ $item->sasaran ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $item->irban?->nama_irban ?? 'Semua Irban' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-400 font-mono text-[11px]">
                                    {{ $item->rencana_mulai->format('d/m/Y') }} - {{ $item->rencana_selesai_laporan->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-900 dark:text-white">
                                    {{ $item->jumlah_laporan_rencana }} Laporan
                                </td>
                                <td class="py-3 px-4 text-center font-bold">
                                    <button type="button" @click="openDetail({{ json_encode($jsonItem) }})" class="px-2.5 py-1 bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 hover:bg-blue-100 rounded-xl text-xs font-black inline-flex items-center gap-1 border border-blue-200 dark:border-blue-800 shadow-xs" title="Klik untuk membuka rincian penugasan tim">
                                        <span>{{ $item->penugasan->count() }} SPT</span>
                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $item->penugasan->where('status', 'selesai')->count() }} Selesai
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                        {{ $item->indikator === 'hijau' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200' : '' }}
                                        {{ $item->indikator === 'kuning' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200' : '' }}
                                        {{ $item->indikator === 'merah' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200' : '' }}
                                        {{ $item->indikator === 'biru' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-200' : '' }}
                                        {{ $item->indikator === 'abu' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200' : '' }}">
                                        {{ $item->indikator_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <button type="button" @click="openDetail({{ json_encode($jsonItem) }})" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs flex items-center gap-1 shadow-xs mx-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>Rincian Tim</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">Belum ada data perencanaan PKPPT pada tahun ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 🔍 Pop-up Modal Rincian Penugasan & Tim Pengawasan (Keterkaitan Database Real-Time) -->
        <div x-show="showModalDetail" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-4xl w-full p-6 border border-slate-200 dark:border-slate-800 text-xs space-y-4">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Rincian Realisasi Penugasan PKPPT</span>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base leading-tight mt-0.5" x-text="activePkppt?.area_pengawasan"></h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Pelaksana: <span class="font-bold text-slate-700 dark:text-slate-300" x-text="activePkppt?.nama_irban"></span> |
                            Jadwal: <span class="font-mono font-bold text-slate-700 dark:text-slate-300" x-text="activePkppt?.rencana_mulai + ' - ' + activePkppt?.rencana_selesai"></span> |
                            Target Laporan: <span class="font-bold text-emerald-600" x-text="activePkppt?.jumlah_laporan_rencana + ' Laporan'"></span>
                        </p>
                    </div>
                    <button type="button" @click="showModalDetail = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <!-- Modal Body: Loop List Penugasan (SPT) -->
                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    <template x-if="activePkppt?.penugasan && activePkppt.penugasan.length > 0">
                        <div class="space-y-4">
                            <template x-for="(st, sIdx) in activePkppt.penugasan" :key="st.id">
                                <div class="p-5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-4">
                                    
                                    <!-- Header ST -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200 dark:border-slate-700">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-xs shadow-xs" x-text="sIdx + 1"></span>
                                            <div>
                                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Nomor Surat Tugas (No. SPT):</span>
                                                <span class="font-mono font-extrabold text-sm text-blue-600 dark:text-blue-400" x-text="st.no_spt"></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider"
                                                :class="{
                                                    'bg-emerald-100 text-emerald-800 border border-emerald-200': st.status === 'selesai',
                                                    'bg-blue-100 text-blue-800 border border-blue-200': st.status === 'berjalan',
                                                    'bg-amber-100 text-amber-800 border border-amber-200': st.status === 'belum_berjalan'
                                                }"
                                                x-text="'Status SPT: ' + st.status_label">
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Grid Info Tanggal & Objek OPD -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 space-y-1">
                                            <span class="font-bold text-slate-500 text-[10px] block">🗓️ Tanggal Penugasan (SPT):</span>
                                            <p class="font-mono font-bold text-slate-800 dark:text-slate-200" x-text="st.tanggal_mulai + ' s/d ' + st.tanggal_selesai"></p>
                                        </div>

                                        <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 space-y-1">
                                            <span class="font-bold text-slate-500 text-[10px] block">🏢 Objek OPD Penugasan Target:</span>
                                            <p class="font-bold text-slate-800 dark:text-slate-200" x-text="st.objek_names"></p>
                                        </div>
                                    </div>

                                    <!-- 👥 Personil Tim yang Berjalan -->
                                    <div class="p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 text-xs block">
                                            👥 Personil Tim Pengawasan yang Berjalan:
                                        </span>

                                        <template x-if="st.tim && st.tim.length > 0">
                                            <div class="space-y-1.5 p-2 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700">
                                                <template x-for="pTim in st.tim" :key="pTim.nama">
                                                    <div class="flex items-center justify-between text-xs py-1 px-2 hover:bg-white dark:hover:bg-slate-700/50 rounded-lg transition-colors">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-emerald-500 font-bold">👤</span>
                                                            <span class="font-bold text-slate-900 dark:text-white" x-text="pTim.nama"></span>
                                                            <span class="text-slate-600 dark:text-slate-300 font-semibold" x-text="'(' + pTim.peran_label + ')'"></span>
                                                        </div>
                                                        <span class="text-[10px] text-slate-400 font-mono" x-text="pTim.nip !== '-' ? 'NIP: ' + pTim.nip : ''"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!st.tim || st.tim.length === 0">
                                            <p class="text-slate-400 text-[11px] italic p-2">Belum ada rincian susunan tim personil yang didaftarkan pada SPT ini.</p>
                                        </template>
                                    </div>

                                    <!-- 📜 Rincian Dokumen LHP Hasil Pengawasan -->
                                    <div class="p-3.5 bg-blue-50/60 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-900 space-y-2">
                                        <span class="font-bold text-blue-900 dark:text-blue-200 text-xs block">
                                            📜 Rincian Laporan Hasil Pengawasan (LHP):
                                        </span>

                                        <template x-if="st.lhp_items && st.lhp_items.length > 0">
                                            <div class="space-y-2">
                                                <template x-for="lhp in st.lhp_items" :key="lhp.id">
                                                    <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                        <div>
                                                            <span class="font-mono font-bold text-blue-600 dark:text-blue-400 text-xs block" x-text="'Nomor LHP: ' + lhp.no_lhp"></span>
                                                            <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="'Judul: ' + lhp.judul_lhp"></p>
                                                            <span class="text-[10px] text-slate-400" x-text="'Tanggal Terbit LHP: ' + lhp.tgl_lhp"></span>
                                                        </div>

                                                        <a :href="'/tindak-lanjut/' + lhp.id" target="_blank" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs inline-flex items-center gap-1 shrink-0">
                                                            <span>📄 Buka Matriks LHP</span>
                                                        </a>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!st.lhp_items || st.lhp_items.length === 0">
                                            <p class="text-slate-400 text-[11px] italic">Belum ada dokumen LHP yang terbit untuk Surat Tugas (SPT) ini.</p>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!activePkppt?.penugasan || activePkppt.penugasan.length === 0">
                        <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400">
                            Belum ada Surat Tugas (SPT) realisasi yang dikaitkan dengan kegiatan PKPPT ini.
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end">
                    <button type="button" @click="showModalDetail = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
