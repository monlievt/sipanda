<x-app-layout>
    <x-slot name="header">
        Audit Log Sistem (Audit Trail)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Catatan Perubahan Data (Audit Log)</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Jejak aktivitas pencatatan, pembaruan, dan penghapusan data di seluruh sistem SINGAWASI Web.</p>
        </div>
    </div>

    <!-- Filter Audit Log -->
    <div class="mb-6 bg-surface rounded-[10px] p-4 sm:p-5 border border-hairline">
        <form method="GET" action="{{ route('audit-log.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end text-xs">
            <div>
                <label class="block font-semibold text-ink-secondary uppercase mb-1">Tabel Modul</label>
                <select name="tabel" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3 py-2">
                    <option value="">-- Semua Tabel --</option>
                    @foreach($tabelList as $t)
                        <option value="{{ $t }}" {{ $tabel === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-ink-secondary uppercase mb-1">Jenis Aksi</label>
                <select name="aksi" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3 py-2">
                    <option value="">-- Semua Aksi --</option>
                    <option value="create" {{ $aksi === 'create' ? 'selected' : '' }}>CREATE (Tambah)</option>
                    <option value="update" {{ $aksi === 'update' ? 'selected' : '' }}>UPDATE (Ubah)</option>
                    <option value="delete" {{ $aksi === 'delete' ? 'selected' : '' }}>DELETE (Hapus)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-ink-secondary uppercase mb-1">Pelaku Aktivitas</label>
                <select name="user_id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3 py-2">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($userList as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->nama_display }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-ink-secondary uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold text-ink-secondary uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs px-3 py-2">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="ledger-btn-primary w-full !text-xs">
                    Terapkan
                </button>
                @if($tabel || $aksi || $userId || $dari || $sampai || $search)
                    <a href="{{ route('audit-log.index') }}" class="ledger-btn-secondary !px-3 !text-xs shrink-0" title="Reset Filter" aria-label="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Audit Log -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Pelaku Aktivitas</th>
                        <th class="py-3.5 px-4">Tabel Modul</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                        <th class="py-3.5 px-4">IP Address</th>
                        <th class="py-3.5 px-4 text-center w-24">Detail Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($logs as $index => $log)
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3 px-4 font-semibold text-center text-ink-muted">{{ $logs->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono whitespace-nowrap text-ink-secondary">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4 font-bold text-ink">{{ $log->user?->nama_display ?? 'Sistem Otomatis' }}</td>
                            <td class="py-3 px-4 font-semibold text-brand-ink uppercase">{{ $log->tabel }} (ID: {{ $log->record_id }})</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold uppercase
                                    {{ $log->aksi === 'create' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : '' }}
                                    {{ $log->aksi === 'update' ? 'bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800' : '' }}
                                    {{ $log->aksi === 'delete' ? 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800' : '' }}">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-ink-muted">{{ $log->ip_address ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($log->data_sebelum || $log->data_sesudah)
                                    <button type="button" onclick="showLogDetail('{{ $log->tabel }}', '{{ $log->record_id }}', '{{ $log->aksi }}', {{ json_encode($log->data_sebelum) }}, {{ json_encode($log->data_sesudah) }})" class="px-2.5 py-1 bg-surface-subtle hover:bg-hairline text-ink border border-hairline-strong rounded-[6px] text-[10px] font-bold transition-colors cursor-pointer">
                                        Lihat Diff
                                    </button>
                                @else
                                    <span class="text-ink-muted text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-ink-muted">Tidak ada riwayat aktivitas yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Modal Diff Log -->
    <div id="modalLogDetail" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-2xl w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <h3 id="modalLogTitle" class="font-bold text-ink text-base">Detail Riwayat Perubahan Data</h3>
                <button type="button" onclick="document.getElementById('modalLogDetail').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-bold text-ink-secondary mb-2 uppercase text-[10px]">Data Sebelum</h4>
                    <pre id="dataSebelumJson" class="p-3 bg-surface-subtle rounded-[6px] border border-hairline font-mono text-[10px] overflow-x-auto max-h-64 whitespace-pre-wrap text-ink"></pre>
                </div>
                <div>
                    <h4 class="font-bold text-ink-secondary mb-2 uppercase text-[10px]">Data Sesudah</h4>
                    <pre id="dataSesudahJson" class="p-3 bg-surface-subtle rounded-[6px] border border-hairline font-mono text-[10px] overflow-x-auto max-h-64 whitespace-pre-wrap text-ink"></pre>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-hairline flex justify-end">
                <button type="button" onclick="document.getElementById('modalLogDetail').classList.add('hidden')" class="ledger-btn-secondary !text-xs">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function showLogDetail(tabel, recordId, aksi, sebelum, sesudah) {
            document.getElementById('modalLogTitle').innerText = 'Detail ' + aksi.toUpperCase() + ' - ' + tabel.toUpperCase() + ' (ID: ' + recordId + ')';
            document.getElementById('dataSebelumJson').innerText = sebelum ? JSON.stringify(sebelum, null, 2) : 'Tidak ada data awal (Create)';
            document.getElementById('dataSesudahJson').innerText = sesudah ? JSON.stringify(sesudah, null, 2) : 'Data dihapus (Delete)';
            document.getElementById('modalLogDetail').classList.remove('hidden');
        }
    </script>
</x-app-layout>
