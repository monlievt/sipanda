<x-app-layout>
    <x-slot name="header">
        Audit Log Sistem (Audit Trail)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Catatan Perubahan Data (Audit Log)</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jejak aktivitas pencatatan, pembaruan, dan penghapusan data di SIPANDA Web.</p>
        </div>
    </div>

    <!-- Table Audit Log -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Pelaku Aktivitas</th>
                        <th class="py-3.5 px-4">Tabel Modul</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                        <th class="py-3.5 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($logs as $index => $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $logs->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $log->user?->nama_display ?? 'Sistem' }}</td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400 uppercase">{{ $log->tabel }} (ID: {{ $log->record_id }})</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                    {{ $log->aksi === 'create' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $log->aksi === 'update' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->aksi === 'delete' ? 'bg-rose-100 text-rose-800' : '' }}">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada riwayat aktivitas di audit log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
