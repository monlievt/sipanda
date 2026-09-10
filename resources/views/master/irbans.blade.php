<x-app-layout>
    <x-slot name="header">
        Kelola Unit Kerja / Inspektur Pembantu (Irban)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Master Unit Kerja / Irban</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar unit kerja Inspektur Pembantu (Irban I, II, III, Khusus, Sekretariat).</p>
        </div>

        @can('master.create')
        <button onclick="document.getElementById('modalTambahIrban').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Unit Kerja Baru</span>
        </button>
        @endcan
    </div>

    <!-- Table Irban -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">ID</th>
                        <th class="py-3.5 px-4">Nama Unit Kerja / Irban</th>
                        <th class="py-3.5 px-4">Keterangan / Wilayah Kerja</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Pegawai</th>
                        <th class="py-3.5 px-4 text-center">Jumlah SPT Terkait</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listIrban as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-mono font-bold text-center text-slate-500">{{ $item->id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 dark:text-white text-xs block">{{ $item->nama_irban }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                {{ $item->wilayah_keterangan ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-600 dark:text-blue-400">
                                {{ $item->users_count }} Orang
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $item->penugasan_count }} SPT
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('master.edit')
                                    <button type="button" onclick="openModalEditIrban({{ $item->id }}, '{{ addslashes($item->nama_irban) }}', '{{ addslashes($item->wilayah_keterangan ?? '') }}')" class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Edit Nama Unit Kerja">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('master.delete')
                                    @if($item->users_count === 0 && $item->penugasan_count === 0)
                                    <form method="POST" action="{{ route('master.irbans.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit kerja \'{{ addslashes($item->nama_irban) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Hapus Unit Kerja">
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
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data unit kerja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Irban -->
    <div id="modalTambahIrban" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Unit Kerja / Irban Baru</h3>
                <button onclick="document.getElementById('modalTambahIrban').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.irbans.store') }}" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Unit Kerja / Irban <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_irban" required placeholder="mis. Inspektur Pembantu Khusus" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Keterangan / Wilayah Kerja</label>
                    <input type="text" name="wilayah_keterangan" placeholder="mis. Irban Khusus — Wilayah Khusus" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahIrban').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Unit Kerja</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Irban -->
    <div id="modalEditIrban" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Unit Kerja / Irban</h3>
                <button onclick="document.getElementById('modalEditIrban').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="formEditIrban" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Unit Kerja / Irban <span class="text-rose-500">*</span></label>
                    <input type="text" id="editNamaIrban" name="nama_irban" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Keterangan / Wilayah Kerja</label>
                    <input type="text" id="editWilayahKeterangan" name="wilayah_keterangan" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditIrban').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalEditIrban(id, nama, wilayah) {
            document.getElementById('formEditIrban').action = '/master/irbans/' + id;
            document.getElementById('editNamaIrban').value = nama;
            document.getElementById('editWilayahKeterangan').value = wilayah;
            document.getElementById('modalEditIrban').classList.remove('hidden');
        }
    </script>
</x-app-layout>
