<x-app-layout>
    <x-slot name="header">
        Kelola Akun PIC Perangkat Daerah (Portal OPD)
    </x-slot>

    <!-- Header Actions & Modal -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Akun Resmi PIC OPD</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Akun berjenjang untuk akses rekomendasi & unggah bukti tindak lanjut.</p>
        </div>

        <button onclick="document.getElementById('modalTambahOpdUser').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Unduh Akun PIC OPD Baru</span>
        </button>
    </div>

    <!-- Table Akun OPD -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama PIC</th>
                        <th class="py-3.5 px-4">Email Resmi OPD</th>
                        <th class="py-3.5 px-4">Instansi Objek Target</th>
                        <th class="py-3.5 px-4 text-center">Status Undangan</th>
                        <th class="py-3.5 px-4">Link Undangan Aktivasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($opdUsers as $index => $u)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $u->nama }}</td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $u->email }}</td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $u->objekPenugasan?->nama ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($u->status_undangan === 'aktif')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">AKTIF</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">PENDING (MENUNGGU SET PASSWORD)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-[10px] text-slate-500">
                                @if($u->token_undangan)
                                    <input type="text" readonly value="{{ route('opd.undangan', $u->token_undangan) }}" class="w-64 p-1 rounded border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[10px]" onclick="this.select()">
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada akun PIC OPD yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Akun OPD -->
    <div id="modalTambahOpdUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Buat Akun Undangan PIC OPD</h3>
                <button onclick="document.getElementById('modalTambahOpdUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.opd-users.store') }}" class="space-y-4 mt-4">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">Nama PIC OPD <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. PIC Dinas Pendidikan" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Email Resmi OPD <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="disdik@trenggalek.go.id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Instansi Objek Target <span class="text-rose-500">*</span></label>
                    <select name="objek_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <option value="">-- Pilih Instansi OPD / Kecamatan --</option>
                        @foreach($objekList as $obj)
                            <option value="{{ $obj->id }}">{{ $obj->nama }} ({{ strtoupper($obj->kategori) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Nomor WhatsApp / HP (Opsional)</label>
                    <input type="text" name="no_hp" placeholder="628123..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahOpdUser').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md">Kirim Undangan Akun</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
