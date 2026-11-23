<x-app-layout>
    <x-slot name="header">
        Program Kerja Pengawasan dan Pembinaan Tahunan (PKPPT)
    </x-slot>

    <!-- Header Actions & Filter -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('pkppt.index') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isPimpinanOrAdmin())
            <div>
                <select name="irban_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2">
                    <option value="">-- Semua Status Aktif --</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="diusulkan" {{ $status === 'diusulkan' ? 'selected' : '' }}>Diusulkan</option>
                    <option value="direviu" {{ $status === 'direviu' ? 'selected' : '' }}>Direviu</option>
                    <option value="ditetapkan" {{ $status === 'ditetapkan' ? 'selected' : '' }}>Ditetapkan</option>
                    <option value="diarsipkan" {{ $status === 'diarsipkan' ? 'selected' : '' }}>Diarsipkan (Versi Lama)</option>
                </select>
            </div>
        </form>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <a href="{{ route('pkppt.export', ['tahun' => $tahun]) }}" class="ledger-btn-secondary !text-xs">
                <svg class="w-4 h-4 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Export CSV PKPPT</span>
            </a>

            @can('pkppt.create')
            <button type="button" onclick="document.getElementById('modalTambahPkppt').classList.remove('hidden')" class="ledger-btn-primary !text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Rencana PKPPT</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Table Rencana PKPPT -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="p-4 border-b border-hairline bg-surface-subtle flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-brand rounded-full" aria-hidden="true"></span>
                <h3 class="font-semibold text-ink text-sm">Rencana Kegiatan Pengawasan Tahun {{ $tahun }}</h3>
            </div>
            <span class="text-xs font-semibold text-ink-secondary bg-surface border border-hairline px-2.5 py-1 rounded-[4px] tabular-nums">
                Total: {{ $listPkppt->count() }} Kegiatan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Area Pengawasan & Versi</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4">Sasaran</th>
                        <th class="py-3.5 px-4">Pelaksana</th>
                        <th class="py-3.5 px-4">Jadwal Rencana</th>
                        <th class="py-3.5 px-4 text-center">Target</th>
                        <th class="py-3.5 px-4 text-center">Realisasi</th>
                        <th class="py-3.5 px-4 text-center">Status Alur</th>
                        <th class="py-3.5 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($listPkppt as $index => $item)
                        <tr class="hover:bg-surface-subtle/70 transition-colors {{ $item->status === 'diarsipkan' ? 'opacity-60 bg-surface-subtle/50' : '' }}">
                            <td class="py-3 px-4 font-semibold text-center text-ink-muted tabular-nums">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-semibold text-ink">{{ $item->area_pengawasan }}</span>
                                    <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-surface-subtle border border-hairline text-ink-secondary tabular-nums">
                                        v{{ $item->versi_revisi }}
                                    </span>
                                </div>
                                @if($item->kelompokPengawasan)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-[4px] text-[10px] font-semibold bg-indigo-50 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        {{ $item->kelompokPengawasan->nama_kelompok }}
                                    </span>
                                @endif
                                @if($item->catatan_revisi)
                                    <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5 italic">
                                        Catatan: {{ Str::limit($item->catatan_revisi, 50) }}
                                    </p>
                                @endif
                                @if($item->pkppt_induk_id)
                                    <span class="text-[10px] text-ink-muted block mt-0.5 tabular-nums">Revisi dari PKPPT #{{ $item->pkppt_induk_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-[4px] bg-surface-subtle border border-hairline text-ink-secondary font-medium">
                                    {{ $item->jenis_pengawasan }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-ink-secondary">{{ $item->sasaran ?? '-' }}</td>
                            <td class="py-3 px-4 font-semibold text-brand-ink">
                                {{ $item->irban?->nama_irban ?? 'Semua Irban' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-ink-secondary tabular-nums">
                                {{ $item->rencana_mulai->format('d/m/Y') }} s.d. {{ $item->rencana_selesai_laporan->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center font-semibold text-ink tabular-nums">
                                {{ $item->jumlah_laporan_rencana }} Lap
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-[4px] text-xs font-semibold bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800 tabular-nums">
                                    {{ $item->penugasan->count() }} SPT
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-0.5 rounded-[4px] text-[10px] font-bold tracking-wider uppercase
                                    {{ $item->status === 'ditetapkan' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                                    {{ $item->status === 'direviu' ? 'bg-purple-50 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : '' }}
                                    {{ $item->status === 'diusulkan' ? 'bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}
                                    {{ $item->status === 'draft' ? 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : '' }}
                                    {{ $item->status === 'diarsipkan' ? 'bg-surface-subtle text-ink-secondary border border-hairline' : '' }}">
                                    {{ $item->status }}
                                </span>
                                @if($item->ditetapkanOleh && $item->status === 'ditetapkan')
                                    <span class="block text-[10px] text-brand-ink font-semibold mt-0.5" title="Penetapan Resmi oleh {{ $item->ditetapkanOleh->nama_display }}">
                                        {{ Str::limit($item->ditetapkanOleh->nama_display, 16) }}
                                    </span>
                                @elseif($item->direviuOleh && $item->status === 'direviu')
                                    <span class="block text-[10px] text-purple-800 dark:text-purple-300 font-semibold mt-0.5" title="Direviu oleh {{ $item->direviuOleh->nama_display }}">
                                        {{ Str::limit($item->direviuOleh->nama_display, 16) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- 1. Tombol Usulkan (Status: draft) -->
                                    @if($item->status === 'draft' && auth()->user()->hasRole(['irban', 'admin_irban', 'sekretariat', 'admin']))
                                        <form method="POST" action="{{ route('pkppt.usulkan', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-amber-700 hover:bg-amber-800 text-white font-semibold text-[10px] rounded-[6px] transition-colors flex items-center gap-1" title="Usulkan PKPPT">
                                                <span>Usulkan</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 2. Tombol Reviu (Status: diusulkan) -->
                                    @if($item->status === 'diusulkan' && auth()->user()->hasRole(['sekretariat', 'admin', 'inspektur']))
                                        <button type="button" onclick="openModalReviuPkppt({{ $item->id }}, '{{ addslashes($item->area_pengawasan) }}')" class="px-2.5 py-1 bg-purple-700 hover:bg-purple-800 text-white font-semibold text-[10px] rounded-[6px] transition-colors flex items-center gap-1" title="Reviu PKPPT">
                                            <span>Reviu</span>
                                        </button>
                                    @endif

                                    <!-- 3. Tombol Tetapkan (Status: direviu / diusulkan, khusus Inspektur / Admin) -->
                                    @if(in_array($item->status, ['direviu', 'diusulkan']) && auth()->user()->hasRole(['inspektur', 'admin']))
                                        <form method="POST" action="{{ route('pkppt.tetapkan', $item->id) }}" class="inline" onsubmit="return confirm('Tetapkan PKPPT ini secara resmi oleh Inspektur?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-brand hover:bg-brand-hover text-white font-semibold text-[10px] rounded-[6px] transition-colors flex items-center gap-1" title="Penetapan Resmi oleh Inspektur">
                                                <span>Tetapkan</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 4. Tombol Revisi (Status: ditetapkan) -->
                                    @if($item->status === 'ditetapkan')
                                        @if(auth()->user()->hasRole(['admin', 'sekretariat', 'inspektur', 'irban', 'admin_irban']) || auth()->user()->can('pkppt.edit'))
                                        <button type="button" onclick="openModalRevisiPkppt({
                                            id: {{ $item->id }},
                                            versi: {{ $item->versi_revisi }},
                                            area_pengawasan: '{{ addslashes($item->area_pengawasan) }}',
                                            jenis_pengawasan: '{{ addslashes($item->jenis_pengawasan) }}',
                                            sasaran: '{{ addslashes($item->sasaran ?? '') }}',
                                            irban_id: '{{ $item->irban_id ?? '' }}',
                                            jumlah_laporan_rencana: {{ $item->jumlah_laporan_rencana }},
                                            rencana_mulai: '{{ $item->rencana_mulai->format('Y-m-d') }}',
                                            rencana_selesai_laporan: '{{ $item->rencana_selesai_laporan->format('Y-m-d') }}'
                                        })" class="px-2.5 py-1 bg-sky-700 hover:bg-sky-800 text-white font-semibold text-[10px] rounded-[6px] transition-colors flex items-center gap-1" title="Ajukan Revisi / Penyesuaian PKPPT">
                                            <span>Revisi PKPT</span>
                                        </button>
                                        @endif
                                    @endif

                                    <!-- Tombol Edit (jika belum ditetapkan atau role admin/sekretariat) -->
                                    @can('pkppt.edit')
                                    @if($item->status !== 'diarsipkan')
                                    <button type="button" onclick="openModalEditPkppt({
                                        id: {{ $item->id }},
                                        area_pengawasan: '{{ addslashes($item->area_pengawasan) }}',
                                        jenis_pengawasan: '{{ addslashes($item->jenis_pengawasan) }}',
                                        sasaran: '{{ addslashes($item->sasaran ?? '') }}',
                                        irban_id: '{{ $item->irban_id ?? '' }}',
                                        jumlah_laporan_rencana: {{ $item->jumlah_laporan_rencana }},
                                        rencana_mulai: '{{ $item->rencana_mulai->format('Y-m-d') }}',
                                        rencana_selesai_laporan: '{{ $item->rencana_selesai_laporan->format('Y-m-d') }}'
                                    })" class="p-1.5 text-blue-700 dark:text-blue-300 hover:bg-blue-50 rounded-[6px] dark:hover:bg-blue-950/50 transition-colors" title="Edit Rencana PKPPT" aria-label="Edit Rencana PKPPT">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @endif
                                    @endcan

                                    <!-- Tombol Hapus (Khusus status draft untuk Irban/Admin/Sekretariat, atau non-ditetapkan untuk Admin) -->
                                    @if(($item->status === 'draft' && (auth()->user()->hasRole(['irban', 'admin_irban', 'sekretariat', 'admin', 'inspektur']) || auth()->user()->can('pkppt.delete'))) || ($item->status !== 'ditetapkan' && auth()->user()->can('pkppt.delete')))
                                    <form method="POST" action="{{ route('pkppt.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana PKPPT ini: {{ addslashes($item->area_pengawasan) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-950/50 rounded-[6px] transition-colors" title="Hapus Rencana PKPPT" aria-label="Hapus Rencana PKPPT">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-ink-muted">
                                Belum ada rencana PKPPT untuk tahun {{ $tahun }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah PKPPT -->
    @can('pkppt.create')
    <div id="modalTambahPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-lg w-full p-6 border border-hairline">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Tambah Rencana PKPPT</h3>
                <button type="button" onclick="document.getElementById('modalTambahPkppt').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" action="{{ route('pkppt.store') }}" class="space-y-4 mt-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Tahun Rencana</label>
                        <input type="number" name="tahun" value="{{ $tahun }}" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Irban Pelaksana</label>
                        <select name="irban_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                            <option value="">-- Lintas / Semua Irban --</option>
                            @foreach($irbans as $irban)
                                <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Area Pengawasan</label>
                    <input type="text" name="area_pengawasan" required placeholder="mis. Pengendalian Inflasi Daerah / Audit Kinerja OPD" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Kelompok / Kluster Pengawasan (Bahan ILHP)</label>
                    <select name="kelompok_pengawasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Pilih Kelompok Pengawasan (Opsional) --</option>
                        @foreach($kelompokList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Jenis Pengawasan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select name="jenis_pengawasan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                            <option value="">-- Pilih Jenis Pengawasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->nama }}">{{ $j->nama }} ({{ ucfirst($j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Target Jumlah Laporan</label>
                        <input type="number" name="jumlah_laporan_rencana" value="1" min="1" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Sasaran Pengawasan</label>
                    <input type="text" name="sasaran" placeholder="mis. 1 Desa/Perangkat Daerah di Wilayah Irban III" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Rencana Tanggal Mulai</label>
                        <input type="date" name="rencana_mulai" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Rencana Selesai Laporan</label>
                        <input type="date" name="rencana_selesai_laporan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalTambahPkppt').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs">Simpan PKPPT</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Modal Edit PKPPT -->
    @can('pkppt.edit')
    <div id="modalEditPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-lg w-full p-6 border border-hairline">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Edit Rencana Kegiatan PKPPT</h3>
                <button type="button" onclick="document.getElementById('modalEditPkppt').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEditPkppt" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-ink mb-1">Irban Pelaksana</label>
                    <select id="editIrbanId" name="irban_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Lintas / Semua Irban --</option>
                        @foreach($irbans as $irban)
                            <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Area Pengawasan</label>
                    <input type="text" id="editAreaPengawasan" name="area_pengawasan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Kelompok / Kluster Pengawasan (Bahan ILHP)</label>
                    <select id="editKelompokPengawasanId" name="kelompok_pengawasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Pilih Kelompok Pengawasan (Opsional) --</option>
                        @foreach($kelompokList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Jenis Pengawasan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select id="editJenisPengawasan" name="jenis_pengawasan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                            <option value="">-- Pilih Jenis Pengawasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->nama }}">{{ $j->nama }} ({{ ucfirst($j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Target Jumlah Laporan</label>
                        <input type="number" id="editJumlahLaporan" name="jumlah_laporan_rencana" min="1" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Sasaran Pengawasan</label>
                    <input type="text" id="editSasaran" name="sasaran" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Rencana Tanggal Mulai</label>
                        <input type="date" id="editRencanaMulai" name="rencana_mulai" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Rencana Selesai Laporan</label>
                        <input type="date" id="editRencanaSelesai" name="rencana_selesai_laporan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEditPkppt').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs">Simpan Perubahan PKPPT</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Modal Reviu PKPPT -->
    <div id="modalReviuPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Reviu PKPPT</h3>
                <button type="button" onclick="document.getElementById('modalReviuPkppt').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form id="formReviuPkppt" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                <div>
                    <p class="text-ink-secondary mb-2">Area Pengawasan:</p>
                    <p id="reviuAreaText" class="font-semibold text-ink bg-surface-subtle border border-hairline p-2.5 rounded-[6px]"></p>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Catatan Reviu (Opsional)</label>
                    <textarea name="catatan_revisi" rows="3" placeholder="Catatan hasil penelaahan atau rekomendasi perbaikan sebelum penetapan..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalReviuPkppt').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white font-semibold rounded-[6px] text-xs transition-colors">Selesai Reviu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Revisi PKPPT (Penyesuaian / Versioning) -->
    @if(auth()->user()->hasRole(['admin', 'sekretariat', 'inspektur', 'irban', 'admin_irban']) || auth()->user()->can('pkppt.edit'))
    <div id="modalRevisiPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-lg w-full p-6 border border-hairline">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-bold text-ink text-base">Ajukan Revisi / Penyesuaian PKPPT</h3>
                    <p id="revisiVersiLabel" class="text-[11px] text-ink-muted mt-0.5"></p>
                </div>
                <button type="button" onclick="document.getElementById('modalRevisiPkppt').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form id="formRevisiPkppt" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf

                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-[6px] text-amber-900 dark:text-amber-300 text-[11px]">
                    Perhatian: Versi yang sudah ditetapkan saat ini akan <strong>diarsipkan</strong>, dan sistem akan membuat versi baru sebagai <strong>Draft</strong> untuk diusulkan dan ditetapkan kembali.
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Alasan / Catatan Revisi <span class="text-red-700 dark:text-red-400">*</span></label>
                    <textarea name="catatan_revisi" required rows="2" placeholder="mis. Penyesuaian jadwal audit semester II atas arahan pimpinan..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Irban Pelaksana</label>
                    <select id="revisiIrbanId" name="irban_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Lintas / Semua Irban --</option>
                        @foreach($irbans as $irban)
                            <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Area Pengawasan</label>
                    <input type="text" id="revisiAreaPengawasan" name="area_pengawasan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Kelompok / Kluster Pengawasan (Bahan ILHP)</label>
                    <select id="revisiKelompokPengawasanId" name="kelompok_pengawasan_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="">-- Pilih Kelompok Pengawasan (Opsional) --</option>
                        @foreach($kelompokList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Jenis Pengawasan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <select id="revisiJenisPengawasan" name="jenis_pengawasan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                            <option value="">-- Pilih Jenis Pengawasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->nama }}">{{ $j->nama }} ({{ ucfirst($j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Target Jumlah Laporan</label>
                        <input type="number" id="revisiJumlahLaporan" name="jumlah_laporan_rencana" min="1" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-ink mb-1">Sasaran Pengawasan</label>
                    <input type="text" id="revisiSasaran" name="sasaran" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-ink mb-1">Rencana Tanggal Mulai</label>
                        <input type="date" id="revisiRencanaMulai" name="rencana_mulai" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink mb-1">Rencana Selesai Laporan</label>
                        <input type="date" id="revisiRencanaSelesai" name="rencana_selesai_laporan" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalRevisiPkppt').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-sky-700 hover:bg-sky-800 text-white font-semibold rounded-[6px] text-xs transition-colors">Buat Versi Revisi</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function openModalEditPkppt(data) {
            const form = document.getElementById('formEditPkppt');
            form.action = '/pkppt/' + data.id;
            document.getElementById('editIrbanId').value = data.irban_id || '';
            document.getElementById('editAreaPengawasan').value = data.area_pengawasan || '';
            document.getElementById('editKelompokPengawasanId').value = data.kelompok_pengawasan_id || '';
            document.getElementById('editJenisPengawasan').value = data.jenis_pengawasan || '';
            document.getElementById('editJumlahLaporan').value = data.jumlah_laporan_rencana || 1;
            document.getElementById('editSasaran').value = data.sasaran || '';
            document.getElementById('editRencanaMulai').value = data.rencana_mulai || '';
            document.getElementById('editRencanaSelesai').value = data.rencana_selesai_laporan || '';
            document.getElementById('modalEditPkppt').classList.remove('hidden');
        }

        function openModalReviuPkppt(id, area) {
            const form = document.getElementById('formReviuPkppt');
            form.action = '/pkppt/' + id + '/reviu';
            document.getElementById('reviuAreaText').innerText = area;
            document.getElementById('modalReviuPkppt').classList.remove('hidden');
        }

        function openModalRevisiPkppt(data) {
            const form = document.getElementById('formRevisiPkppt');
            form.action = '/pkppt/' + data.id + '/revisi';
            document.getElementById('revisiVersiLabel').innerText = 'Membuat Versi ' + (data.versi + 1) + ' dari Versi ' + data.versi;
            document.getElementById('revisiIrbanId').value = data.irban_id || '';
            document.getElementById('revisiAreaPengawasan').value = data.area_pengawasan || '';
            document.getElementById('revisiKelompokPengawasanId').value = data.kelompok_pengawasan_id || '';
            document.getElementById('revisiJenisPengawasan').value = data.jenis_pengawasan || '';
            document.getElementById('revisiJumlahLaporan').value = data.jumlah_laporan_rencana || 1;
            document.getElementById('revisiSasaran').value = data.sasaran || '';
            document.getElementById('revisiRencanaMulai').value = data.rencana_mulai || '';
            document.getElementById('revisiRencanaSelesai').value = data.rencana_selesai_laporan || '';
            document.getElementById('modalRevisiPkppt').classList.remove('hidden');
        }
    </script>
</x-app-layout>
