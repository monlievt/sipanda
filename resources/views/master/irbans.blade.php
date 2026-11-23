<x-app-layout>
    <x-slot name="header">
        Kelola Unit Kerja / Inspektur Pembantu (Irban)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Master Unit Kerja / Irban</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Daftar unit kerja Inspektur Pembantu (Irban I, II, III, Khusus, Sekretariat).</p>
        </div>

        @can('master.create')
        <button type="button" onclick="document.getElementById('modalTambahIrban').classList.remove('hidden')" class="ledger-btn-primary !text-xs cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Unit Kerja Baru</span>
        </button>
        @endcan
    </div>

    <!-- Table Irban -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">ID</th>
                        <th class="py-3.5 px-4">Nama Unit Kerja / Irban</th>
                        <th class="py-3.5 px-4">Keterangan / Wilayah Kerja</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Pegawai</th>
                        <th class="py-3.5 px-4 text-center">Jumlah SPT Terkait</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($listIrban as $item)
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-center text-ink-muted">{{ $item->id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-ink text-xs block">{{ $item->nama_irban }}</span>
                            </td>
                            <td class="py-3 px-4 text-ink-secondary">
                                {{ $item->wilayah_keterangan ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-700 dark:text-blue-300 tabular-nums">
                                {{ $item->users_count }} Orang
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-brand-ink tabular-nums">
                                {{ $item->penugasan_count }} SPT
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('master.edit')
                                    <button type="button" onclick="openModalEditIrban({{ $item->id }}, '{{ addslashes($item->nama_irban) }}', '{{ addslashes($item->wilayah_keterangan ?? '') }}')" class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="Edit Nama Unit Kerja">
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
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="Hapus Unit Kerja">
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
                            <td colspan="6" class="py-8 text-center text-ink-muted">Belum ada data unit kerja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Irban -->
    <div id="modalTambahIrban" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Tambah Unit Kerja / Irban Baru</h3>
                <button type="button" onclick="document.getElementById('modalTambahIrban').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.irbans.store') }}" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1 text-ink">Nama Unit Kerja / Irban <span class="text-rose-600">*</span></label>
                    <input type="text" name="nama_irban" required placeholder="mis. Inspektur Pembantu Khusus" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Keterangan / Wilayah Kerja</label>
                    <input type="text" name="wilayah_keterangan" placeholder="mis. Irban Khusus, Wilayah Khusus" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalTambahIrban').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan Unit Kerja</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Irban -->
    <div id="modalEditIrban" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 class="font-bold text-ink text-base">Edit Unit Kerja / Irban</h3>
                <button type="button" onclick="document.getElementById('modalEditIrban').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEditIrban" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-1 text-ink">Nama Unit Kerja / Irban <span class="text-rose-600">*</span></label>
                    <input type="text" id="editNamaIrban" name="nama_irban" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-ink">Keterangan / Wilayah Kerja</label>
                    <input type="text" id="editWilayahKeterangan" name="wilayah_keterangan" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEditIrban').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan Perubahan</button>
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
