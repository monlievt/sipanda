<x-app-layout>
    <x-slot name="header">
        Evaluasi Tahunan Capaian PKPT (Siklus N+1)
    </x-slot>

    <!-- Stat Cards Evaluasi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Capaian Objek Terealisasi</span>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-bold text-blue-700 dark:text-blue-400 tabular-nums">{{ $persenObjek }}%</p>
                <span class="text-xs text-ink-secondary font-medium tabular-nums">{{ $pkpptTerealisasi }} / {{ $totalPkppt }} PKPPT</span>
            </div>
            <div class="w-full bg-surface-subtle h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-700 dark:bg-blue-400 h-full rounded-full" style="width: {{ $persenObjek }}%"></div>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Laporan Selesai Tepat Waktu</span>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-bold text-brand-ink tabular-nums">{{ $persenTepatWaktu }}%</p>
                <span class="text-xs text-ink-secondary font-medium tabular-nums">{{ $sptSelesaiTepatWaktu }} / {{ $totalSPT }} SPT</span>
            </div>
            <div class="w-full bg-surface-subtle h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-brand h-full rounded-full" style="width: {{ $persenTepatWaktu }}%"></div>
            </div>
        </div>

        <div class="bg-surface rounded-[10px] p-5 border border-hairline">
            <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Tindak Lanjut Selesai</span>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-bold text-purple-700 dark:text-purple-400 tabular-nums">{{ $persenTLSelesai }}%</p>
                <span class="text-xs text-ink-secondary font-medium tabular-nums">{{ $tlSelesai }} / {{ $totalTL }} Temuan</span>
            </div>
            <div class="w-full bg-surface-subtle h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-purple-700 dark:bg-purple-400 h-full rounded-full" style="width: {{ $persenTLSelesai }}%"></div>
            </div>
        </div>
    </div>

    <!-- Actions & History -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="p-5 border-b border-hairline flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="font-semibold text-ink text-[15px]">Riwayat Evaluasi Tahunan</h3>
            <form method="POST" action="{{ route('evaluasi.generate') }}">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="ledger-btn-primary text-xs">
                    Simpan Ringkasan Evaluasi {{ $tahun }}
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4">Tahun Evaluasi</th>
                        <th class="py-3.5 px-4">Lingkup</th>
                        <th class="py-3.5 px-4 text-center">% Objek Terealisasi</th>
                        <th class="py-3.5 px-4 text-center">% Laporan Selesai</th>
                        <th class="py-3.5 px-4 text-center">% Tindak Lanjut Selesai</th>
                        <th class="py-3.5 px-4">Catatan Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($evaluasiHistory as $item)
                        <tr class="hover:bg-surface-subtle/60 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-ink tabular-nums">Tahun {{ $item->tahun_evaluasi }}</td>
                            <td class="py-3.5 px-4 font-medium text-ink-secondary">{{ $item->irban?->nama_irban ?? 'Seluruh Inspektorat' }}</td>
                            <td class="py-3.5 px-4 text-center font-semibold text-blue-700 dark:text-blue-400 tabular-nums">{{ $item->persen_objek_terealisasi }}%</td>
                            <td class="py-3.5 px-4 text-center font-semibold text-brand-ink tabular-nums">{{ $item->persen_laporan_tepat_waktu }}%</td>
                            <td class="py-3.5 px-4 text-center font-semibold text-purple-700 dark:text-purple-400 tabular-nums">{{ $item->persen_tindak_lanjut_selesai }}%</td>
                            <td class="py-3.5 px-4 text-ink-secondary">{{ $item->catatan_evaluasi }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-ink-muted">Belum ada riwayat evaluasi tahunan yang disimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
