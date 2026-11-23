<x-app-layout>
    <x-slot name="header">
        Pratinjau Data Import Excel / CSV
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('import.index', ['tab' => $type]) }}" class="inline-flex items-center gap-1 text-xs text-ink-secondary hover:text-brand-ink font-semibold">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali ke Unggah File</span>
                </a>
            </div>
            <h2 class="text-xl font-bold text-ink tracking-tight mt-1">
                Pratinjau Data {{ ucfirst(str_replace('_', ' ', $type)) }}
            </h2>
            <p class="text-xs text-ink-secondary mt-0.5">
                Ditemukan total <strong class="text-ink tabular-nums">{{ $totalRows }} baris data</strong> pada file spreadsheet. Menampilkan 10 baris pertama untuk pemeriksaan.
            </p>
        </div>

        <form method="POST" action="{{ route('import.store') }}">
            @csrf
            <input type="hidden" name="tipe" value="{{ $type }}">
            <input type="hidden" name="temp_path" value="{{ $tempPath }}">

            <div class="flex items-center gap-3">
                <a href="{{ route('import.index', ['tab' => $type]) }}" class="ledger-btn-secondary text-xs">
                    Batal
                </a>
                <button type="submit" @disabled($totalRows < 1) class="ledger-btn-primary text-xs disabled:opacity-50">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Import Semua {{ $totalRows }} Data Sekarang</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Table -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="p-4 border-b border-hairline bg-surface-subtle flex items-center justify-between gap-2">
            <h3 class="font-bold text-ink text-xs">
                Contoh 10 Baris Pertama yang Akan Dimasukkan ke Database
            </h3>
            @if($totalRows > 0)
                <span class="text-[11px] font-semibold text-brand-ink bg-brand-soft px-2.5 py-0.5 rounded-[4px] border border-brand-border">
                    Format Berkas Valid
                </span>
            @else
                <span class="text-[11px] font-semibold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 px-2.5 py-0.5 rounded-[4px] border border-amber-200 dark:border-amber-800">
                    Data Kosong
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">#</th>
                        @foreach($header as $col)
                            <th class="py-3 px-4 whitespace-nowrap">{{ str_replace('_', ' ', $col) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($previewRows as $index => $row)
                        <tr class="hover:bg-surface-subtle font-mono text-[11px]">
                            <td class="py-2.5 px-4 font-semibold text-center text-ink-muted tabular-nums">{{ $index + 1 }}</td>
                            @foreach($row as $cell)
                                <td class="py-2.5 px-4 text-ink whitespace-nowrap max-w-xs truncate">
                                    {{ $cell ?: '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ max(1, count($header) + 1) }}" class="py-12 px-6 text-center">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <div class="w-10 h-10 rounded-[6px] bg-surface-subtle border border-hairline text-ink-muted flex items-center justify-center">
                                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-ink">Tidak ada baris data untuk diimpor</p>
                                    <p class="text-xs text-ink-secondary max-w-md">
                                        Berkas yang diunggah tidak memiliki baris data di bawah header kolom. Periksa kembali isi berkas atau gunakan template resmi.
                                    </p>
                                    <div class="pt-2">
                                        <a href="{{ route('import.index', ['tab' => $type]) }}" class="ledger-btn-secondary text-xs">
                                            Unggah Berkas Lain
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($totalRows > 10)
            <div class="p-3 bg-surface-subtle border-t border-hairline text-center text-ink-muted text-xs tabular-nums">
                ... dan {{ $totalRows - 10 }} baris data lainnya akan diproses secara otomatis dalam transaksi database.
            </div>
        @endif
    </div>
</x-app-layout>
