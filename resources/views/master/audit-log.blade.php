<x-app-layout>
    <x-slot name="header">
        Audit Log Sistem (Audit Trail)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Catatan Perubahan Data (Audit Log)</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jejak aktivitas pencatatan, pembaruan, dan penghapusan data di seluruh sistem SIPANDA Web.</p>
        </div>
    </div>

    <!-- Filter Audit Log -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('audit-log.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end text-xs">
            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Tabel Modul</label>
                <select name="tabel" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2">
                    <option value="">-- Semua Tabel --</option>
                    @foreach($tabelList as $t)
                        <option value="{{ $t }}" {{ $tabel === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Jenis Aksi</label>
                <select name="aksi" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2">
                    <option value="">-- Semua Aksi --</option>
                    <option value="create" {{ $aksi === 'create' ? 'selected' : '' }}>CREATE (Tambah)</option>
                    <option value="update" {{ $aksi === 'update' ? 'selected' : '' }}>UPDATE (Ubah)</option>
                    <option value="delete" {{ $aksi === 'delete' ? 'selected' : '' }}>DELETE (Hapus)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Pelaku Aktivitas</label>
                <select name="user_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3 py-2">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($userList as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->nama_display }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3 py-2">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                    Terapkan
                </button>
                @if($tabel || $aksi || $userId || $dari || $sampai || $search)
                    <a href="{{ route('audit-log.index') }}" class="py-2.5 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-center" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
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
                        <th class="py-3.5 px-4 text-center w-24">Detail Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($logs as $index => $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $logs->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $log->user?->nama_display ?? 'Sistem Otomatis' }}</td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400 uppercase">{{ $log->tabel }} (ID: {{ $log->record_id }})</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                    {{ $log->aksi === 'create' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                    {{ $log->aksi === 'update' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                                    {{ $log->aksi === 'delete' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : '' }}">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($log->data_sebelum || $log->data_sesudah)
                                    <button type="button" onclick="showLogDetail('{{ $log->tabel }}', '{{ $log->record_id }}', '{{ $log->aksi }}', {{ json_encode($log->data_sebelum) }}, {{ json_encode($log->data_sesudah) }})" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold cursor-pointer">
                                        Lihat Diff
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada riwayat aktivitas yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Modal Diff Log -->
    <div id="modalLogDetail" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 id="modalLogTitle" class="font-bold text-slate-900 dark:text-white text-base">Detail Riwayat Perubahan Data</h3>
                <button onclick="document.getElementById('modalLogDetail').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-bold text-slate-600 dark:text-slate-400 mb-2 uppercase text-[10px]">Data Sebelum</h4>
                    <pre id="dataSebelumJson" class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[10px] overflow-x-auto max-h-64 whitespace-pre-wrap text-slate-700 dark:text-slate-300"></pre>
                </div>
                <div>
                    <h4 class="font-bold text-slate-600 dark:text-slate-400 mb-2 uppercase text-[10px]">Data Sesudah</h4>
                    <pre id="dataSesudahJson" class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[10px] overflow-x-auto max-h-64 whitespace-pre-wrap text-slate-700 dark:text-slate-300"></pre>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                <button type="button" onclick="document.getElementById('modalLogDetail').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Tutup</button>
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

