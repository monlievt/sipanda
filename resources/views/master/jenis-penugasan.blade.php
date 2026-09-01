<x-app-layout>
    <x-slot name="header">
        Kelola Jenis Penugasan (Assurance & Consulting)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Master Jenis Penugasan</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar kategori penugasan pengawasan (*Audit, Reviu, Advisory, dll.*).</p>
        </div>

        @can('master.create')
        <button onclick="document.getElementById('modalTambahJenis').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Jenis Penugasan Baru</span>
        </button>
        @endcan
    </div>

    <!-- Table Jenis Penugasan -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Jenis Penugasan</th>
                        <th class="py-3.5 px-4">Kategori Utama</th>
                        <th class="py-3.5 px-4 text-center">Penggunaan SPT</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listJenis as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $item->nama }}</td>
                            <td class="py-3 px-4 uppercase font-bold {{ $item->kategori === 'assurance' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}">
                                {{ $item->kategori }}
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500 font-semibold">
                                {{ $item->penugasan_count ?? 0 }} SPT
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Aktif</span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('master.edit')
                                    <button type="button" onclick="openModalEditJenis({{ $item->id }}, '{{ addslashes($item->nama) }}', '{{ $item->kategori }}')" class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Edit Jenis Penugasan">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('master.delete')
                                    <form method="POST" action="{{ route('master.jenis-penugasan.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis penugasan \'{{ addslashes($item->nama) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Hapus Jenis Penugasan">
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
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada jenis penugasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Jenis -->
    <div id="modalTambahJenis" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Jenis Penugasan Baru</h3>
                <button onclick="document.getElementById('modalTambahJenis').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.jenis-penugasan.store') }}" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Jenis Penugasan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. Audit Kinerja / Reviu RKPD" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Kategori Utama <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        <option value="assurance">Assurance (Audit / Reviu / Monitoring / Evaluasi)</option>
                        <option value="consulting">Consulting (Advisory / Facilitative / Training)</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahJenis').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md cursor-pointer">Simpan Jenis Penugasan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jenis -->
    <div id="modalEditJenis" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Jenis Penugasan</h3>
                <button onclick="document.getElementById('modalEditJenis').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="formEditJenis" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Jenis Penugasan <span class="text-rose-500">*</span></label>
                    <input type="text" id="editJenisNama" name="nama" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Kategori Utama <span class="text-rose-500">*</span></label>
                    <select id="editJenisKategori" name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        <option value="assurance">Assurance (Audit / Reviu / Monitoring / Evaluasi)</option>
                        <option value="consulting">Consulting (Advisory / Facilitative / Training)</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditJenis').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md cursor-pointer">Perbarui Jenis</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalEditJenis(id, nama, kategori) {
            const form = document.getElementById('formEditJenis');
            form.action = "{{ url('/master/jenis-penugasan') }}/" + id;
            document.getElementById('editJenisNama').value = nama;
            document.getElementById('editJenisKategori').value = kategori;
            document.getElementById('modalEditJenis').classList.remove('hidden');
        }
    </script>
</x-app-layout>

