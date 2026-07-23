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

        <button onclick="document.getElementById('modalTambahJenis').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Jenis Penugasan Baru</span>
        </button>
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
                        <th class="py-3.5 px-4 text-center">Status</th>
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
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">Belum ada jenis penugasan.</td>
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
                <button onclick="document.getElementById('modalTambahJenis').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.jenis-penugasan.store') }}" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block font-semibold mb-1">Nama Jenis Penugasan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. Audit Stunting / Reviu RKPD" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Kategori Utama <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        <option value="assurance">Assurance (Audit / Reviu / Monitoring / Evaluasi)</option>
                        <option value="consulting">Consulting (Advisory / Facilitative / Training)</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahJenis').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md">Simpan Jenis Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
