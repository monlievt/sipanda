<x-app-layout>
    <x-slot name="header">
        Kelola Jenis Penugasan (Assurance & Consulting)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Master Jenis Penugasan</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Daftar kategori penugasan pengawasan (Audit, Reviu, Advisory, dll.).</p>
        </div>

        @can('master.create')
        <button type="button" onclick="document.getElementById('modalTambahJenis').classList.remove('hidden')" class="ledger-btn-primary !text-xs cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Jenis Penugasan Baru</span>
        </button>
        @endcan
    </div>

    <!-- Table Jenis Penugasan -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Jenis Penugasan</th>
                        <th class="py-3.5 px-4">Kategori Utama</th>
                        <th class="py-3.5 px-4 text-center">Penggunaan SPT</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($listJenis as $index => $item)
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3 px-4 font-semibold text-center text-ink-muted">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-ink">{{ $item->nama }}</td>
                            <td class="py-3 px-4 uppercase font-bold {{ $item->kategori === 'assurance' ? 'text-blue-700 dark:text-blue-300' : 'text-purple-700 dark:text-purple-300' }}">
                                {{ $item->kategori }}
                            </td>
                            <td class="py-3 px-4 text-center text-ink-secondary font-semibold tabular-nums">
                                {{ $item->penugasan_count ?? 0 }} SPT
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800">Aktif</span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('master.edit')
                                    <button type="button" onclick="openModalEditJenis({{ $item->id }}, '{{ addslashes($item->nama) }}', '{{ $item->kategori }}')" class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="Edit Jenis Penugasan">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('master.delete')
                                    <form method="POST" action="{{ route('master.jenis-penugasan.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis penugasan \'{{ addslashes($item->nama) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="Hapus Jenis Penugasan">
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
                            <td colspan="6" class="py-8 text-center text-ink-muted">Belum ada jenis penugasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Jenis -->
    <div id="modalTambahJenis" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Tambah Jenis Penugasan Baru</h3>
                <button type="button" onclick="document.getElementById('modalTambahJenis').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.jenis-penugasan.store') }}" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1 text-ink">Nama Jenis Penugasan <span class="text-rose-600">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. Audit Kinerja / Reviu RKPD" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Kategori Utama <span class="text-rose-600">*</span></label>
                    <select name="kategori" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="assurance">Assurance (Audit / Reviu / Monitoring / Evaluasi)</option>
                        <option value="consulting">Consulting (Advisory / Facilitative / Training)</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalTambahJenis').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan Jenis Penugasan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jenis -->
    <div id="modalEditJenis" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Edit Jenis Penugasan</h3>
                <button type="button" onclick="document.getElementById('modalEditJenis').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEditJenis" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold mb-1 text-ink">Nama Jenis Penugasan <span class="text-rose-600">*</span></label>
                    <input type="text" id="editJenisNama" name="nama" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Kategori Utama <span class="text-rose-600">*</span></label>
                    <select id="editJenisKategori" name="kategori" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="assurance">Assurance (Audit / Reviu / Monitoring / Evaluasi)</option>
                        <option value="consulting">Consulting (Advisory / Facilitative / Training)</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEditJenis').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Perbarui Jenis</button>
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
