<x-app-layout>
    <x-slot name="header">
        Evaluasi Tahunan Capaian PKPT (Siklus N+1)
    </x-slot>

    <!-- Stat Cards Evaluasi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Capaian Objek Terealisasi</span>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ $persenObjek }}%</p>
                <span class="text-xs text-slate-500 font-semibold">{{ $pkpptTerealisasi }} / {{ $totalPkppt }} PKPPT</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full" style="width: {{ $persenObjek }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan Selesai Tepat Waktu</span>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $persenTepatWaktu }}%</p>
                <span class="text-xs text-slate-500 font-semibold">{{ $sptSelesaiTepatWaktu }} / {{ $totalSPT }} SPT</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $persenTepatWaktu }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tindak Lanjut Selesai</span>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-purple-600 dark:text-purple-400">{{ $persenTLSelesai }}%</p>
                <span class="text-xs text-slate-500 font-semibold">{{ $tlSelesai }} / {{ $totalTL }} Temuan</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-purple-600 h-full rounded-full" style="width: {{ $persenTLSelesai }}%"></div>
            </div>
        </div>
    </div>

    <!-- Actions & History -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-base">Riwayat Evaluasi Tahunan</h3>
            <form method="POST" action="{{ route('evaluasi.generate') }}">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-md">
                    Simpan Ringkasan Evaluasi {{ $tahun }}
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tahun Evaluasi</th>
                        <th class="py-3 px-4">Lingkup</th>
                        <th class="py-3 px-4 text-center">% Objek Terealisasi</th>
                        <th class="py-3 px-4 text-center">% Laporan Selesai</th>
                        <th class="py-3 px-4 text-center">% Tindak Lanjut Selesai</th>
                        <th class="py-3 px-4">Catatan Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($evaluasiHistory as $item)
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">Tahun {{ $item->tahun_evaluasi }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-600">{{ $item->irban?->nama_irban ?? 'Seluruh Inspektorat' }}</td>
                            <td class="py-3 px-4 text-center font-bold text-blue-600">{{ $item->persen_objek_terealisasi }}%</td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600">{{ $item->persen_laporan_tepat_waktu }}%</td>
                            <td class="py-3 px-4 text-center font-bold text-purple-600">{{ $item->persen_tindak_lanjut_selesai }}%</td>
                            <td class="py-3 px-4 text-slate-500">{{ $item->catatan_evaluasi }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Belum ada riwayat evaluasi tahunan yang disimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
