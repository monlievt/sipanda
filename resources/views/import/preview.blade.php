<x-app-layout>
    <x-slot name="header">
        Pratinjau Data Import CSV
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('import.index', ['tab' => $type]) }}" class="text-xs text-slate-500 hover:text-emerald-600 font-semibold">&larr; Kembali ke Unggah File</a>
            </div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                Pratinjau Data {{ ucfirst(str_replace('_', ' ', $type)) }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Ditemukan total <strong>{{ $totalRows }} baris data</strong> pada file CSV. Menampilkan 10 baris pertama untuk pemeriksaan.
            </p>
        </div>

        <form method="POST" action="{{ route('import.store') }}">
            @csrf
            <input type="hidden" name="tipe" value="{{ $type }}">
            <input type="hidden" name="temp_path" value="{{ $tempPath }}">

            <div class="flex items-center gap-3">
                <a href="{{ route('import.index', ['tab' => $type]) }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Import Semua {{ $totalRows }} Data Sekarang</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 dark:text-white text-xs">
                Contoh 10 Baris Pertama yang Akan Dimasukkan ke Database
            </h3>
            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800">
                Format CSV Valid
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">#</th>
                        @foreach($header as $col)
                            <th class="py-3 px-4 whitespace-nowrap">{{ str_replace('_', ' ', $col) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($previewRows as $index => $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 font-mono text-[11px]">
                            <td class="py-2.5 px-4 font-bold text-center text-slate-400">{{ $index + 1 }}</td>
                            @foreach($row as $cell)
                                <td class="py-2.5 px-4 text-slate-800 dark:text-slate-200 whitespace-nowrap max-w-xs truncate">
                                    {{ $cell ?: '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($totalRows > 10)
            <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 text-center text-slate-500 text-[11px]">
                ... dan {{ $totalRows - 10 }} baris data lainnya akan diproses secara otomatis dalam transaksi database.
            </div>
        @endif
    </div>
</x-app-layout>
