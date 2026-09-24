<x-app-layout>
    <x-slot name="header">
        Kelola Kelompok / Kluster Pengawasan
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Kelompok / Kluster Jenis Pengawasan</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pengelompokan standar untuk bahan penyusunan PKPPT, Realisasi SPT, dan Ikhtisar Laporan Hasil Pengawasan (ILHP).</p>
        </div>

        <div class="flex items-center gap-3">
            @can('master.create')
            <button onclick="document.getElementById('modalTambahKelompok').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kelompok Baru</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Alert status -->
    @if(session('status'))
        <div class="mb-5 p-3.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 p-3.5 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/80 rounded-xl text-xs font-semibold text-rose-800 dark:text-rose-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('master.kelompok-pengawasan.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kelompok, bentuk pengawasan, atau deskripsi..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-hidden dark:text-white">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-semibold text-xs rounded-xl cursor-pointer">
                    Cari
                </button>
                @if($search)
                <a href="{{ route('master.kelompok-pengawasan.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-xl flex items-center justify-center">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Kelompok Pengawasan -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">Urutan</th>
                        <th class="py-3.5 px-4 w-72">Nama Kelompok & Kode</th>
                        <th class="py-3.5 px-4">Deskripsi Singkat & Bentuk Pengawasan</th>
                        <th class="py-3.5 px-4 text-center w-28">Penggunaan</th>
                        <th class="py-3.5 px-4 text-center w-24">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($kelompoks as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 align-top">
                            <td class="py-3.5 px-4 font-bold text-center text-slate-500">
                                <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-xs font-mono">
                                    {{ $item->urutan }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $item->nama_kelompok }}
                                </div>
                                @if($item->kode_kelompok)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 font-mono text-[10px] font-bold rounded">
                                        {{ $item->kode_kelompok }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 space-y-2">
                                @if($item->deskripsi_singkat)
                                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                                        {{ $item->deskripsi_singkat }}
                                    </p>
                                @endif
                                @if($item->bentuk_pengawasan)
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 text-[11px] text-slate-700 dark:text-slate-300 font-mono whitespace-pre-line leading-relaxed">
                                        {{ $item->bentuk_pengawasan }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center space-y-1">
                                <span class="inline-block px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-semibold text-[11px]">
                                    {{ $item->pkppts_count }} PKPPT
                                </span>
                                <br>
                                <span class="inline-block px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-semibold text-[11px]">
                                    {{ $item->penugasans_count }} SPT
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->is_active)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('master.edit')
                                    <button type="button" 
                                            onclick='openModalEditKelompok(@json($item))' 
                                            class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" 
                                            title="Edit Kelompok Pengawasan">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('master.delete')
                                    <form action="{{ route('master.kelompok-pengawasan.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelompok pengawasan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Hapus Kelompok Pengawasan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada data kelompok pengawasan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kelompoks->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $kelompoks->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah Kelompok -->
    <div id="modalTambahKelompok" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Tambah Kelompok / Kluster Pengawasan</h3>
                <button onclick="document.getElementById('modalTambahKelompok').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('master.kelompok-pengawasan.store') }}" method="POST" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Kelompok Pengawasan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_kelompok" required placeholder="Contoh: Pengawasan Proyek Strategis Daerah (PSD)" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Kode Kelompok</label>
                        <input type="text" name="kode_kelompok" placeholder="Contoh: PSD" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono uppercase">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Urutan Tampilan</label>
                        <input type="number" name="urutan" value="{{ ($kelompoks->total() ?? 0) + 1 }}" min="1" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Deskripsi Singkat / Ruang Lingkup</label>
                    <textarea name="deskripsi_singkat" rows="2" placeholder="Penjelasan singkat ruang lingkup kelompok pengawasan..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Bentuk / Rincian Pengawasan (1 baris per item)</label>
                    <textarea name="bentuk_pengawasan" rows="4" placeholder="• Reviu DAK / DAU&#10;• Monitoring PSD&#10;• Audit PSD" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="tambahIsActive" name="is_active" value="1" checked class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500">
                    <label for="tambahIsActive" class="font-semibold text-slate-700 dark:text-slate-300">Aktifkan kelompok ini untuk pilihan PKPPT dan SPT</label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahKelompok').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Kelompok</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kelompok -->
    <div id="modalEditKelompok" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Edit Kelompok / Kluster Pengawasan</h3>
                <button onclick="document.getElementById('modalEditKelompok').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEditKelompok" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Kelompok Pengawasan <span class="text-rose-500">*</span></label>
                    <input type="text" id="editNamaKelompok" name="nama_kelompok" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Kode Kelompok</label>
                        <input type="text" id="editKodeKelompok" name="kode_kelompok" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono uppercase">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Urutan Tampilan</label>
                        <input type="number" id="editUrutan" name="urutan" min="1" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Deskripsi Singkat / Ruang Lingkup</label>
                    <textarea id="editDeskripsiSingkat" name="deskripsi_singkat" rows="2" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Bentuk / Rincian Pengawasan (1 baris per item)</label>
                    <textarea id="editBentukPengawasan" name="bentuk_pengawasan" rows="4" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="editIsActive" name="is_active" value="1" class="rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                    <label for="editIsActive" class="font-semibold text-slate-700 dark:text-slate-300">Aktifkan kelompok ini untuk pilihan PKPPT dan SPT</label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditKelompok').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalEditKelompok(item) {
            const form = document.getElementById('formEditKelompok');
            form.action = `/master/kelompok-pengawasan/${item.id}`;
            document.getElementById('editNamaKelompok').value = item.nama_kelompok || '';
            document.getElementById('editKodeKelompok').value = item.kode_kelompok || '';
            document.getElementById('editUrutan').value = item.urutan || 1;
            document.getElementById('editDeskripsiSingkat').value = item.deskripsi_singkat || '';
            document.getElementById('editBentukPengawasan').value = item.bentuk_pengawasan || '';
            document.getElementById('editIsActive').checked = item.is_active ? true : false;
            document.getElementById('modalEditKelompok').classList.remove('hidden');
        }
    </script>
</x-app-layout>
