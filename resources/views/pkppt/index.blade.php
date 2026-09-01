<x-app-layout>
    <x-slot name="header">
        Program Kerja Pengawasan dan Pembinaan Tahunan (PKPPT)
    </x-slot>

    <!-- Header Actions & Filter -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('pkppt.index') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500 px-3 py-2.5">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="irban_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500 px-3 py-2.5">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500 px-3 py-2.5">
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
            <a href="{{ route('pkppt.export', ['tahun' => $tahun]) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Export CSV PKPPT</span>
            </a>

            @can('pkppt.create')
            <button onclick="document.getElementById('modalTambahPkppt').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Rencana PKPPT</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Table Rencana PKPPT -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                <h3 class="font-bold text-slate-800 dark:text-white text-sm">Rencana Kegiatan Pengawasan Tahun {{ $tahun }}</h3>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-200 dark:bg-slate-800 px-2.5 py-1 rounded-full">
                Total: {{ $listPkppt->count() }} Kegiatan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
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
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listPkppt as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors {{ $item->status === 'diarsipkan' ? 'opacity-60 bg-slate-50/50' : '' }}">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $item->area_pengawasan }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        v{{ $item->versi_revisi }}
                                    </span>
                                </div>
                                @if($item->catatan_revisi)
                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5 italic">
                                        Catatan: {{ Str::limit($item->catatan_revisi, 50) }}
                                    </p>
                                @endif
                                @if($item->pkppt_induk_id)
                                    <span class="text-[9px] text-slate-400 block mt-0.5">Revisi dari PKPPT #{{ $item->pkppt_induk_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $item->jenis_pengawasan }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $item->sasaran ?? '-' }}</td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $item->irban?->nama_irban ?? 'Semua Irban' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                {{ $item->rencana_mulai->format('d/m/Y') }} s.d. {{ $item->rencana_selesai_laporan->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800 dark:text-white">
                                {{ $item->jumlah_laporan_rencana }} Lap
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ $item->penugasan->count() }} SPT
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold tracking-wider uppercase
                                    {{ $item->status === 'ditetapkan' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300' : '' }}
                                    {{ $item->status === 'direviu' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border border-purple-300' : '' }}
                                    {{ $item->status === 'diusulkan' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-300' : '' }}
                                    {{ $item->status === 'draft' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300' : '' }}
                                    {{ $item->status === 'diarsipkan' ? 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : '' }}">
                                    {{ $item->status }}
                                </span>
                                @if($item->ditetapkanOleh && $item->status === 'ditetapkan')
                                    <span class="block text-[9px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5" title="Penetapan Resmi oleh {{ $item->ditetapkanOleh->nama_display }}">
                                        ✓ {{ Str::limit($item->ditetapkanOleh->nama_display, 16) }}
                                    </span>
                                @elseif($item->direviuOleh && $item->status === 'direviu')
                                    <span class="block text-[9px] text-purple-600 dark:text-purple-400 font-bold mt-0.5" title="Direviu oleh {{ $item->direviuOleh->nama_display }}">
                                        🔍 {{ Str::limit($item->direviuOleh->nama_display, 16) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- 1. Tombol Usulkan (Status: draft) -->
                                    @if($item->status === 'draft' && auth()->user()->hasRole(['irban', 'admin_irban', 'sekretariat', 'admin']))
                                        <form method="POST" action="{{ route('pkppt.usulkan', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] rounded-lg shadow-xs transition-all flex items-center gap-1 cursor-pointer" title="Usulkan PKPPT">
                                                <span>📩 Usulkan</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 2. Tombol Reviu (Status: diusulkan) -->
                                    @if($item->status === 'diusulkan' && auth()->user()->hasRole(['sekretariat', 'admin', 'inspektur']))
                                        <button type="button" onclick="openModalReviuPkppt({{ $item->id }}, '{{ addslashes($item->area_pengawasan) }}')" class="px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white font-bold text-[10px] rounded-lg shadow-xs transition-all flex items-center gap-1 cursor-pointer" title="Reviu PKPPT">
                                            <span>🔍 Reviu</span>
                                        </button>
                                    @endif

                                    <!-- 3. Tombol Tetapkan (Status: direviu / diusulkan, khusus Inspektur / Admin) -->
                                    @if(in_array($item->status, ['direviu', 'diusulkan']) && auth()->user()->hasRole(['inspektur', 'admin']))
                                        <form method="POST" action="{{ route('pkppt.tetapkan', $item->id) }}" class="inline" onsubmit="return confirm('Tetapkan PKPPT ini secara resmi oleh Inspektur?')">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg shadow-xs transition-all flex items-center gap-1 cursor-pointer" title="Penetapan Resmi oleh Inspektur">
                                                <span>✍️ Tetapkan</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 4. Tombol Revisi (Status: ditetapkan) -->
                                    @if($item->status === 'ditetapkan')
                                        @can('pkppt.edit')
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
                                        })" class="px-2 py-1 bg-sky-600 hover:bg-sky-700 text-white font-bold text-[10px] rounded-lg shadow-xs transition-all flex items-center gap-1 cursor-pointer" title="Ajukan Revisi / Penyesuaian PKPPT">
                                            <span>🔄 Revisi</span>
                                        </button>
                                        @endcan
                                    @endif

                                    <!-- Tombol Edit (jika belum ditetapkan atau role admin/sekretariat) -->
                                    @can('pkppt.edit')
                                    @if($item->status !== 'diarsipkan')
                                    <button onclick="openModalEditPkppt({
                                        id: {{ $item->id }},
                                        area_pengawasan: '{{ addslashes($item->area_pengawasan) }}',
                                        jenis_pengawasan: '{{ addslashes($item->jenis_pengawasan) }}',
                                        sasaran: '{{ addslashes($item->sasaran ?? '') }}',
                                        irban_id: '{{ $item->irban_id ?? '' }}',
                                        jumlah_laporan_rencana: {{ $item->jumlah_laporan_rencana }},
                                        rencana_mulai: '{{ $item->rencana_mulai->format('Y-m-d') }}',
                                        rencana_selesai_laporan: '{{ $item->rencana_selesai_laporan->format('Y-m-d') }}'
                                    })" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg dark:hover:bg-blue-950/50 cursor-pointer" title="Edit Rencana PKPPT">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @endif
                                    @endcan

                                    <!-- Tombol Hapus -->
                                    @can('pkppt.delete')
                                    @if($item->status !== 'ditetapkan')
                                    <form method="POST" action="{{ route('pkppt.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus baris PKPPT ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg dark:hover:bg-rose-950/50 cursor-pointer" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
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
    <div id="modalTambahPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Rencana PKPPT</h3>
                <button onclick="document.getElementById('modalTambahPkppt').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('pkppt.store') }}" class="space-y-4 mt-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Tahun Rencana</label>
                        <input type="number" name="tahun" value="{{ $tahun }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Irban Pelaksana</label>
                        <select name="irban_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                            <option value="">-- Lintas / Semua Irban --</option>
                            @foreach($irbans as $irban)
                                <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Area Pengawasan</label>
                    <input type="text" name="area_pengawasan" required placeholder="mis. Pengendalian Inflasi Daerah / Audit Kinerja OPD" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Pengawasan <span class="text-rose-500">*</span></label>
                        <select name="jenis_pengawasan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200">
                            <option value="">-- Pilih Jenis Pengawasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->nama }}">{{ $j->nama }} ({{ ucfirst($j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Target Jumlah Laporan</label>
                        <input type="number" name="jumlah_laporan_rencana" value="1" min="1" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Sasaran Pengawasan</label>
                    <input type="text" name="sasaran" placeholder="mis. 1 Desa/Perangkat Daerah di Wilayah Irban III" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Rencana Tanggal Mulai</label>
                        <input type="date" name="rencana_mulai" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Rencana Selesai Laporan</label>
                        <input type="date" name="rencana_selesai_laporan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahPkppt').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md cursor-pointer">Simpan PKPPT</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Modal Edit PKPPT -->
    @can('pkppt.edit')
    <div id="modalEditPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Rencana Kegiatan PKPPT</h3>
                <button onclick="document.getElementById('modalEditPkppt').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="formEditPkppt" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-1">Irban Pelaksana</label>
                    <select id="editIrbanId" name="irban_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        <option value="">-- Lintas / Semua Irban --</option>
                        @foreach($irbans as $irban)
                            <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Area Pengawasan</label>
                    <input type="text" id="editAreaPengawasan" name="area_pengawasan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Pengawasan <span class="text-rose-500">*</span></label>
                        <select id="editJenisPengawasan" name="jenis_pengawasan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200">
                            <option value="">-- Pilih Jenis Pengawasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->nama }}">{{ $j->nama }} ({{ ucfirst($j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Target Jumlah Laporan</label>
                        <input type="number" id="editJumlahLaporan" name="jumlah_laporan_rencana" min="1" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Sasaran Pengawasan</label>
                    <input type="text" id="editSasaran" name="sasaran" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Rencana Tanggal Mulai</label>
                        <input type="date" id="editRencanaMulai" name="rencana_mulai" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Rencana Selesai Laporan</label>
                        <input type="date" id="editRencanaSelesai" name="rencana_selesai_laporan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditPkppt').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md cursor-pointer">Simpan Perubahan PKPPT</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Modal Reviu PKPPT -->
    <div id="modalReviuPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Reviu PKPPT</h3>
                <button onclick="document.getElementById('modalReviuPkppt').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="formReviuPkppt" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                <div>
                    <p class="text-slate-600 dark:text-slate-400 mb-2">Area Pengawasan:</p>
                    <p id="reviuAreaText" class="font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 p-2.5 rounded-xl"></p>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Catatan Reviu (Opsional)</label>
                    <textarea name="catatan_revisi" rows="3" placeholder="Catatan hasil penelaahan atau rekomendasi perbaikan sebelum penetapan..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalReviuPkppt').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl shadow-md cursor-pointer">Selesai Reviu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Revisi PKPPT (Penyesuaian / Versioning) -->
    @can('pkppt.edit')
    <div id="modalRevisiPkppt" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Ajukan Revisi / Penyesuaian PKPPT</h3>
                    <p id="revisiVersiLabel" class="text-[10px] text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('modalRevisiPkppt').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="formRevisiPkppt" method="POST" action="" class="space-y-4 mt-4 text-xs">
                @csrf

                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-[11px]">
                    ⚠️ Versi yang sudah ditetapkan saat ini akan <strong>diarsipkan</strong>, dan sistem akan membuat versi baru sebagai <strong>Draft</strong> untuk diusulkan dan ditetapkan kembali.
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Alasan / Catatan Revisi <span class="text-rose-500">*</span></label>
                    <textarea name="catatan_revisi" required rows="2" placeholder="mis. Penyesuaian jadwal audit semester II atas arahan pimpinan..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Irban Pelaksana</label>
                    <select id="revisiIrbanId" name="irban_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        <option value="">-- Lintas / Semua Irban --</option>
                        @foreach($irbans as $irban)
                            <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Area Pengawasan</label>
                    <input type="text" id="revisiAreaPengawasan" name="area_pengawasan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Jenis Pengawasan <span class="text-rose-500">*</span></label>
                        <select id="revisiJenisPengawasan" name="jenis_pengawasan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200">
                            <option value="">-- Pilih Jenis Pengawasan --</option>
                            @foreach($jenisList as $j)
                                <option value="{{ $j->nama }}">{{ $j->nama }} ({{ ucfirst($j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Target Jumlah Laporan</label>
                        <input type="number" id="revisiJumlahLaporan" name="jumlah_laporan_rencana" min="1" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Sasaran Pengawasan</label>
                    <input type="text" id="revisiSasaran" name="sasaran" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Rencana Tanggal Mulai</label>
                        <input type="date" id="revisiRencanaMulai" name="rencana_mulai" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Rencana Selesai Laporan</label>
                        <input type="date" id="revisiRencanaSelesai" name="rencana_selesai_laporan" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalRevisiPkppt').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl shadow-md cursor-pointer">Buat Versi Revisi</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
        function openModalEditPkppt(data) {
            const form = document.getElementById('formEditPkppt');
            form.action = '/pkppt/' + data.id;
            document.getElementById('editIrbanId').value = data.irban_id;
            document.getElementById('editAreaPengawasan').value = data.area_pengawasan;
            document.getElementById('editJenisPengawasan').value = data.jenis_pengawasan;
            document.getElementById('editJumlahLaporan').value = data.jumlah_laporan_rencana;
            document.getElementById('editSasaran').value = data.sasaran;
            document.getElementById('editRencanaMulai').value = data.rencana_mulai;
            document.getElementById('editRencanaSelesai').value = data.rencana_selesai_laporan;
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
            document.getElementById('revisiIrbanId').value = data.irban_id;
            document.getElementById('revisiAreaPengawasan').value = data.area_pengawasan;
            document.getElementById('revisiJenisPengawasan').value = data.jenis_pengawasan;
            document.getElementById('revisiJumlahLaporan').value = data.jumlah_laporan_rencana;
            document.getElementById('revisiSasaran').value = data.sasaran;
            document.getElementById('revisiRencanaMulai').value = data.rencana_mulai;
            document.getElementById('revisiRencanaSelesai').value = data.rencana_selesai_laporan;
            document.getElementById('modalRevisiPkppt').classList.remove('hidden');
        }
    </script>
</x-app-layout>

