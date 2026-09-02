<x-app-layout>
    <x-slot name="header">
        Data Penugasan (SPT) — Hasil Input PKPPT & Non-PKPPT
    </x-slot>

    <!-- Header Actions & Search/Filters -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Daftar Penugasan Pengawasan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tabel terpadu penugasan PKPPT & Non-PKPPT.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('penugasan.export', ['tahun' => $tahun, 'irban_id' => $irbanId]) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-sm transition-all">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export CSV</span>
                </a>

                @can('penugasan.create')
                <a href="{{ route('penugasan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Input Penugasan Baru</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Filter Box -->
        <form method="GET" action="{{ route('penugasan.index') }}" class="p-4 sm:p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
            <div>
                <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Cari SPT / Uraian</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="No. SPT / Uraian..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Tahun</label>
                <select name="tahun" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(!auth()->user()->hasRole(['irban', 'admin_irban']))
            <div>
                <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status --</option>
                    <option value="belum_berjalan" {{ $status === 'belum_berjalan' ? 'selected' : '' }}>Belum Berjalan</option>
                    <option value="berjalan" {{ $status === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 dark:text-slate-400 uppercase mb-1">Jenis Penugasan</label>
                <select name="jenis_penugasan_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($jenisList as $j)
                        <option value="{{ $j->id }}" {{ $jenisId == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                    Cari
                </button>
                @if($search || $status || $jenisId || $irbanId)
                <a href="{{ route('penugasan.index') }}" class="px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700 text-sm font-bold rounded-xl">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table (Kolom Progres & Aksi Dihapus) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">No. SPT</th>
                        <th class="py-3.5 px-4">Uraian & Objek Target</th>
                        <th class="py-3.5 px-4">Jenis & Sumber</th>
                        <th class="py-3.5 px-4">Irban Penanggung Jawab</th>
                        <th class="py-3.5 px-4">Tanggal Pelaksanaan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listPenugasan as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold whitespace-nowrap">
                                <a href="{{ route('penugasan.show', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline block" title="Klik untuk membuka Rincian Lengkap Surat Tugas ini">
                                    📄 {{ $item->no_spt }}
                                </a>
                                @if($item->penugasan_induk_id)
                                    <span class="block text-[9px] font-sans font-extrabold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-1.5 py-0.5 rounded border border-purple-200 mt-0.5">
                                        🔄 ST Perpanjangan (Induk: {{ $item->penugasanInduk?->no_spt }})
                                    </span>
                                @endif
                                @if($item->is_sesuai_pkppt)
                                    <span class="block text-[9px] font-sans font-bold text-blue-600 dark:text-blue-400 mt-0.5">✓ PKPPT</span>
                                @else
                                    <span class="block text-[9px] font-sans font-semibold text-slate-400 mt-0.5">Non-PKPPT</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <a href="{{ route('penugasan.show', $item->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-blue-600 line-clamp-2">
                                    {{ $item->uraian_penugasan }}
                                </a>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($item->objekPenugasan as $objek)
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded">
                                            {{ $objek->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $item->jenisPenugasan?->nama }}</span>
                                <span class="text-[10px] text-slate-400">Sumber: {{ $item->sumberPenugasan?->nama }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $item->irban_list_names }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                {{ $item->tanggal_mulai->format('d/m/Y') }} — {{ $item->tanggal_selesai->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                    {{ $item->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                    {{ $item->status === 'berjalan' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : '' }}
                                    {{ $item->status === 'belum_berjalan' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : '' }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('penugasan.show', $item->id) }}" class="px-2.5 py-1 bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-100 rounded-lg text-[10px] font-bold border border-slate-300 shadow-xs" title="Lihat Rincian Isi Surat Tugas">
                                        👁️ Detail
                                    </a>
                                    <a href="{{ route('penugasan.cetak', $item->id) }}" target="_blank" class="px-2.5 py-1 bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 hover:bg-blue-100 rounded-lg text-[10px] font-bold border border-blue-300 shadow-xs" title="Cetak Naskah Dinas SPT Resmi">
                                        🖨️ Cetak
                                    </a>
                                    @can('penugasan.edit')
                                    <a href="{{ route('penugasan.edit', $item->id) }}" class="px-2.5 py-1 bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300 hover:bg-amber-100 rounded-lg text-[10px] font-bold border border-amber-300 shadow-xs" title="Edit Surat Tugas">
                                        ✏️ Edit
                                    </a>
                                    @endcan
                                    @can('penugasan.delete')
                                    <form method="POST" action="{{ route('penugasan.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Surat Tugas {{ addslashes($item->no_spt) }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 hover:bg-rose-100 rounded-lg text-[10px] font-bold border border-rose-300 shadow-xs cursor-pointer" title="Hapus Surat Tugas">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada data penugasan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listPenugasan->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $listPenugasan->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
