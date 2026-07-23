<x-app-layout>
    <x-slot name="header">
        Modul Beban Kerja Personil
    </x-slot>

    <!-- Filter Form -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('beban-kerja.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ $tglAwal }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tglAkhir }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
            </div>

            @if(!auth()->user()->hasRole(['irban', 'admin_irban']))
            <div>
                <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-1">Pilih Personil</label>
                <select name="user_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                    <option value="">-- Semua Personil --</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ $selectedUserId == $u->id ? 'selected' : '' }}>{{ $u->nama_display }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-md">Tampilkan Rekap</button>
            </div>
        </form>
    </div>

    <!-- Rekap Beban Kerja Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">Rekapitulasi Penugasan Per Personil ({{ \Carbon\Carbon::parse($tglAwal)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($tglAkhir)->format('d/m/Y') }})</h3>
            <span class="text-xs font-semibold text-slate-500">Total Personil: {{ $listPersonil->count() }} Pegawai</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Personil</th>
                        <th class="py-3.5 px-4">NIP / Jabatan</th>
                        <th class="py-3.5 px-4">Unit (Irban)</th>
                        <th class="py-3.5 px-4 text-center">Penugasan Aktif</th>
                        <th class="py-3.5 px-4 text-center">Penugasan Selesai</th>
                        <th class="py-3.5 px-4 text-center font-black">Total Penugasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listPersonil as $index => $p)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $p->nama_display }}</td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                <span class="font-mono block text-[10px]">{{ $p->nip ?? '-' }}</span>
                                <span class="text-slate-500 font-semibold">{{ $p->jabatan }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $p->irban?->nama_irban ?? 'Sekretariat' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-amber-600">
                                {{ $p->penugasan_aktif }} SPT
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600">
                                {{ $p->penugasan_selesai }} SPT
                            </td>
                            <td class="py-3 px-4 text-center font-black text-sm text-slate-900 dark:text-white bg-slate-50/50 dark:bg-slate-800/30">
                                {{ $p->total_penugasan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data personil.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
