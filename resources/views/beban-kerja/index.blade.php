<x-app-layout>
    <x-slot name="header">
        Modul Beban Kerja Personil
    </x-slot>

    <!-- Executive KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl flex items-center justify-center text-xl shrink-0">
                👥
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-slate-400">Total Personil</p>
                <p class="text-xl font-black text-slate-900 dark:text-white">{{ $countTotal }} <span class="text-xs font-semibold text-slate-400">Pegawai</span></p>
            </div>
        </div>

        <a href="{{ request()->fullUrlWithQuery(['status_ketersediaan' => 'Tersedia']) }}" 
           class="bg-white dark:bg-slate-900 rounded-2xl p-4 border shadow-xs flex items-center gap-3.5 transition-all hover:scale-[1.02] {{ $statusFilter === 'Tersedia' ? 'ring-2 ring-emerald-500 border-emerald-500' : 'border-slate-200 dark:border-slate-800' }}">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                🟢
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-emerald-600 dark:text-emerald-400">Siap Ditugaskan</p>
                <p class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $countTersedia }} <span class="text-xs font-semibold text-slate-400">0 SPT Aktif</span></p>
            </div>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status_ketersediaan' => 'Optimal']) }}" 
           class="bg-white dark:bg-slate-900 rounded-2xl p-4 border shadow-xs flex items-center gap-3.5 transition-all hover:scale-[1.02] {{ $statusFilter === 'Optimal' ? 'ring-2 ring-blue-500 border-blue-500' : 'border-slate-200 dark:border-slate-800' }}">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950/60 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                🔵
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-blue-600 dark:text-blue-400">Sedang Bertugas</p>
                <p class="text-xl font-black text-blue-600 dark:text-blue-400">{{ $countOptimal }} <span class="text-xs font-semibold text-slate-400">1–2 SPT Aktif</span></p>
            </div>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status_ketersediaan' => 'Beban Tinggi']) }}" 
           class="bg-white dark:bg-slate-900 rounded-2xl p-4 border shadow-xs flex items-center gap-3.5 transition-all hover:scale-[1.02] {{ $statusFilter === 'Beban Tinggi' ? 'ring-2 ring-rose-500 border-rose-500' : 'border-slate-200 dark:border-slate-800' }}">
            <div class="w-11 h-11 bg-rose-50 dark:bg-rose-950/60 text-rose-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                🔴
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-rose-600 dark:text-rose-400">Beban Tinggi</p>
                <p class="text-xl font-black text-rose-600 dark:text-rose-400">{{ $countOverload }} <span class="text-xs font-semibold text-slate-400">&ge;3 SPT Aktif</span></p>
            </div>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('beban-kerja.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end text-xs">
            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ $tglAwal }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tglAkhir }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            @if(!auth()->user()->hasRole(['irban', 'admin_irban']))
            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Unit Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Status Ketersediaan</label>
                <select name="status_ketersediaan" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status --</option>
                    <option value="Tersedia" {{ $statusFilter == 'Tersedia' ? 'selected' : '' }}>🟢 Siap Ditugaskan (0 SPT)</option>
                    <option value="Optimal" {{ $statusFilter == 'Optimal' ? 'selected' : '' }}>🔵 Sedang Bertugas (1-2 SPT)</option>
                    <option value="Beban Tinggi" {{ $statusFilter == 'Beban Tinggi' ? 'selected' : '' }}>🔴 Beban Tinggi (&ge;3 SPT)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Cari Personil</label>
                <select name="user_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Personil --</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ $selectedUserId == $u->id ? 'selected' : '' }}>{{ $u->nama_display }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md transition-all cursor-pointer">
                    🔍 Filter Matriks
                </button>
            </div>
        </form>
    </div>

    <!-- Rekap Beban Kerja Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white text-sm">Matriks Beban Kerja & Ketersediaan Personil</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Periode: {{ \Carbon\Carbon::parse($tglAwal)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($tglAkhir)->format('d/m/Y') }}</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl">
                Menampilkan {{ $listPersonil->count() }} Pegawai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Personil</th>
                        <th class="py-3.5 px-4">NIP / Jabatan</th>
                        <th class="py-3.5 px-4">Unit (Irban)</th>
                        <th class="py-3.5 px-4 text-center">Status Ketersediaan</th>
                        <th class="py-3.5 px-4 text-center">Penugasan Aktif</th>
                        <th class="py-3.5 px-4 text-center">Penugasan Selesai</th>
                        <th class="py-3.5 px-4 text-center font-black">Total SPT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listPersonil as $index => $p)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $p->nama_display }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $p->pangkat ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                <span class="font-mono block text-[10px] text-slate-400">{{ $p->nip ?? '-' }}</span>
                                <span class="text-slate-700 dark:text-slate-200 font-medium">{{ $p->jabatan }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $p->irban?->nama_irban ?? 'Sekretariat' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold {{ $p->status_badge }}">
                                    <span class="w-2 h-2 rounded-full {{ $p->status_dot }}"></span>
                                    <span>{{ $p->status_label }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-black text-sm {{ $p->penugasan_aktif > 2 ? 'text-rose-600' : ($p->penugasan_aktif > 0 ? 'text-blue-600' : 'text-slate-400') }}">
                                    {{ $p->penugasan_aktif }} SPT
                                </span>
                                @if($p->penugasan_aktif > 0)
                                    <div class="mt-1 space-y-0.5 max-w-[200px] mx-auto text-left">
                                        @foreach($p->daftar_penugasan_aktif as $activeSt)
                                            <a href="{{ route('penugasan.show', $activeSt->id) }}" class="block truncate text-[10px] font-semibold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700" title="{{ $activeSt->no_spt }} — {{ $activeSt->uraian_penugasan }}">
                                                📌 {{ $activeSt->no_spt }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-600 dark:text-slate-300">
                                {{ $p->penugasan_selesai }} SPT
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-sm text-slate-900 dark:text-white bg-slate-50/50 dark:bg-slate-800/30">
                                {{ $p->total_penugasan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 font-medium">Tidak ada data personil yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
