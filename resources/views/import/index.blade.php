<x-app-layout>
    <x-slot name="header">
        Import Data Historis (Spreadsheet / CSV)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-ink tracking-tight">Import Data Historis Pengawasan</h2>
            <p class="text-xs text-ink-secondary mt-0.5">Migrasi dan unggah data SPT, matriks tindak lanjut LHP, serta master objek dari file spreadsheet.</p>
        </div>
    </div>

    @if (session('import_errors'))
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 rounded-[10px] text-xs text-amber-900 dark:text-amber-200">
            <h4 class="font-bold flex items-center gap-1.5 mb-2">
                <svg class="w-4 h-4 shrink-0 text-amber-700 dark:text-amber-300" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Terdapat beberapa baris data yang dilewati saat proses import:</span>
            </h4>
            <ul class="list-disc list-inside space-y-1 max-h-48 overflow-y-auto font-mono text-[11px]">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tab Selection -->
    <div class="mb-6 flex border-b border-hairline gap-6 text-sm font-semibold overflow-x-auto">
        <a href="{{ route('import.index', ['tab' => 'penugasan']) }}" class="pb-3 border-b-2 transition-colors whitespace-nowrap flex items-center gap-2 {{ $tab === 'penugasan' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-secondary hover:text-ink' }}">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Import Penugasan (SPT)</span>
        </a>
        <a href="{{ route('import.index', ['tab' => 'tindak_lanjut']) }}" class="pb-3 border-b-2 transition-colors whitespace-nowrap flex items-center gap-2 {{ $tab === 'tindak_lanjut' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-secondary hover:text-ink' }}">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Import Matriks LHP (Tindak Lanjut)</span>
        </a>
        <a href="{{ route('import.index', ['tab' => 'objek']) }}" class="pb-3 border-b-2 transition-colors whitespace-nowrap flex items-center gap-2 {{ $tab === 'objek' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-secondary hover:text-ink' }}">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Import Master Objek (OPD/Desa)</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2 bg-surface rounded-[10px] p-6 border border-hairline">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-hairline gap-3">
                <div>
                    <h3 class="font-bold text-ink text-base">
                        Unggah Berkas {{ $tab === 'penugasan' ? 'Penugasan (SPT)' : ($tab === 'tindak_lanjut' ? 'Matriks LHP' : 'Master Objek') }}
                    </h3>
                    <p class="text-xs text-ink-muted">Pilih berkas Excel (.xlsx) yang telah diisi per kolom atau file CSV.</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- 1. Tombol Unduh Template Excel (Rekomendasi Utama) --}}
                    <a href="{{ route('import.template', ['type' => $tab, 'format' => 'xlsx']) }}" class="ledger-btn-primary text-xs">
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Unduh Template Excel (.xlsx)</span>
                    </a>

                    {{-- 2. Tombol Unduh CSV (Alternatif) --}}
                    <a href="{{ route('import.template', ['type' => $tab, 'format' => 'csv']) }}" title="Unduh Format CSV Biasa" class="ledger-btn-secondary text-xs">
                        <span>.CSV</span>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('import.preview') }}" enctype="multipart/form-data" class="space-y-5 text-xs">
                @csrf
                <input type="hidden" name="tipe" value="{{ $tab }}">

                <div>
                    <label for="importFileInput" class="block font-semibold mb-2 text-ink">Pilih Berkas Excel (.xlsx / .xls) atau CSV (.csv)</label>
                    <div class="border-2 border-dashed border-hairline-strong hover:border-brand rounded-[10px] p-8 text-center bg-surface-subtle transition-colors cursor-pointer relative">
                        <input id="importFileInput" type="file" name="file" accept=".xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-11 h-11 bg-brand-soft text-brand-ink border border-brand-border rounded-[6px] flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="font-bold text-sm text-ink mb-1">Klik atau seret file Excel (.xlsx) atau CSV ke sini</p>
                            <p class="text-xs text-ink-muted">Mendukung Microsoft Excel (.xlsx/.xls) dengan kolom terpisah otomatis, atau file .csv (maks. 10 MB)</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-hairline">
                    <p class="text-xs text-ink-muted">Pratinjau pembagian kolom data akan ditampilkan sebelum disimpan ke database.</p>
                    <button type="submit" class="ledger-btn-primary text-xs shrink-0 cursor-pointer">
                        Pratinjau Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Guide & Instructions -->
        <div class="bg-surface rounded-[10px] p-6 border border-hairline text-xs space-y-4">
            <h3 class="font-bold text-ink text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-ink shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Panduan Pengisian Kolom</span>
            </h3>

            <div class="p-3 bg-brand-soft rounded-[10px] border border-brand-border text-xs text-ink space-y-1">
                <span class="font-bold block text-brand-ink">Format Excel Terpisah Kolom (Praktis):</span>
                <p class="leading-relaxed text-ink-secondary">
                    Gunakan tombol hijau <strong class="text-ink">"Unduh Template Excel (.xlsx)"</strong> di atas. Setiap variabel data sudah terbagi ke dalam kolom A, B, C, dst., sehingga Anda dapat mengetik atau <em>copy-paste</em> langsung di Microsoft Excel tanpa ribet memikirkan pemisah koma / titik koma.
                </p>
            </div>

            @if($tab === 'penugasan')
                <div class="space-y-2 text-ink-secondary text-xs leading-relaxed">
                    <p>Struktur kolom pada template <strong class="text-ink">Penugasan (SPT)</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[11px] bg-surface-subtle border border-hairline p-3 rounded-[6px] text-ink">
                        <li><strong>Kolom A:</strong> no_spt (wajib)</li>
                        <li><strong>Kolom B:</strong> uraian_penugasan (wajib)</li>
                        <li><strong>Kolom C:</strong> irban (mis. Irban I)</li>
                        <li><strong>Kolom D:</strong> jenis_penugasan (mis. Audit Kinerja)</li>
                        <li><strong>Kolom E:</strong> sumber_penugasan (mis. PKPT)</li>
                        <li><strong>Kolom F:</strong> objek_penugasan (pisahkan koma jika > 1)</li>
                        <li><strong>Kolom G:</strong> tanggal_mulai (YYYY-MM-DD / DD/MM/YYYY)</li>
                        <li><strong>Kolom H:</strong> tanggal_selesai (YYYY-MM-DD / DD/MM/YYYY)</li>
                        <li><strong>Kolom I:</strong> status (selesai / berjalan)</li>
                    </ol>
                </div>
            @elseif($tab === 'tindak_lanjut')
                <div class="space-y-2 text-ink-secondary text-xs leading-relaxed">
                    <p>Struktur kolom pada template <strong class="text-ink">Matriks Tindak Lanjut (LHP)</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[11px] bg-surface-subtle border border-hairline p-3 rounded-[6px] text-ink">
                        <li><strong>Kolom A:</strong> no_lhp (wajib)</li>
                        <li><strong>Kolom B:</strong> no_spt (relasi SPT)</li>
                        <li><strong>Kolom C:</strong> uraian_temuan (wajib)</li>
                        <li><strong>Kolom D:</strong> rekomendasi_wajib (wajib)</li>
                        <li><strong>Kolom E:</strong> nilai_rekomendasi_rp (angka nominal)</li>
                        <li><strong>Kolom F:</strong> target_penyelesaian (YYYY-MM-DD)</li>
                        <li><strong>Kolom G:</strong> status_tindak_lanjut (selesai/proses/belum)</li>
                        <li><strong>Kolom H:</strong> judul_lhp (opsional)</li>
                        <li><strong>Kolom I:</strong> tanggal_lhp (YYYY-MM-DD)</li>
                    </ol>
                </div>
            @else
                <div class="space-y-2 text-ink-secondary text-xs leading-relaxed">
                    <p>Struktur kolom pada template <strong class="text-ink">Master Objek Pengawasan</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[11px] bg-surface-subtle border border-hairline p-3 rounded-[6px] text-ink">
                        <li><strong>Kolom A:</strong> nama_instansi_objek (wajib)</li>
                        <li><strong>Kolom B:</strong> kategori (opd / kecamatan / desa)</li>
                        <li><strong>Kolom C:</strong> status_aktif (aktif / nonaktif)</li>
                    </ol>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
