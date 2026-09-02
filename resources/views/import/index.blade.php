<x-app-layout>
    <x-slot name="header">
        Import Data Historis (Spreadsheet / CSV)
    </x-slot>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Import Data Historis Pengawasan</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Migrasi dan unggah data SPT, matriks tindak lanjut LHP, serta master objek dari file spreadsheet.</p>
        </div>
    </div>

    @if (session('import_errors'))
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 rounded-2xl text-xs text-amber-800 dark:text-amber-200">
            <h4 class="font-bold flex items-center gap-1.5 mb-2">
                <span>⚠️</span> Terdapat beberapa baris data yang dilewati saat proses import:
            </h4>
            <ul class="list-disc list-inside space-y-1 max-h-48 overflow-y-auto font-mono text-[11px]">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tab Selection -->
    <div class="mb-6 flex border-b border-slate-200 dark:border-slate-800 gap-6 text-sm font-bold">
        <a href="{{ route('import.index', ['tab' => 'penugasan']) }}" class="pb-3 border-b-2 transition-all flex items-center gap-2 {{ $tab === 'penugasan' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <span>📋</span>
            <span>Import Penugasan (SPT)</span>
        </a>
        <a href="{{ route('import.index', ['tab' => 'tindak_lanjut']) }}" class="pb-3 border-b-2 transition-all flex items-center gap-2 {{ $tab === 'tindak_lanjut' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <span>📊</span>
            <span>Import Matriks LHP (Tindak Lanjut)</span>
        </a>
        <a href="{{ route('import.index', ['tab' => 'objek']) }}" class="pb-3 border-b-2 transition-all flex items-center gap-2 {{ $tab === 'objek' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <span>🏛️</span>
            <span>Import Master Objek (OPD/Desa)</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800 gap-3">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">
                        Unggah Berkas {{ $tab === 'penugasan' ? 'Penugasan (SPT)' : ($tab === 'tindak_lanjut' ? 'Matriks LHP' : 'Master Objek') }}
                    </h3>
                    <p class="text-[11px] text-slate-400">Pilih berkas Excel (.xlsx) yang telah diisi per kolom atau file CSV.</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- 1. Tombol Unduh Template Excel (Rekomendasi Utama) --}}
                    <a href="{{ route('import.template', ['type' => $tab, 'format' => 'xlsx']) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl flex items-center gap-1.5 shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>🟢 Unduh Template Excel (.xlsx)</span>
                    </a>

                    {{-- 2. Tombol Unduh CSV (Alternatif) --}}
                    <a href="{{ route('import.template', ['type' => $tab, 'format' => 'csv']) }}" title="Unduh Format CSV Biasa" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl flex items-center gap-1 transition-all">
                        <span>📄 .CSV</span>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('import.preview') }}" enctype="multipart/form-data" class="space-y-5 text-xs">
                @csrf
                <input type="hidden" name="tipe" value="{{ $tab }}">

                <div>
                    <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Pilih Berkas Excel (.xlsx / .xls) atau CSV (.csv)</label>
                    <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-8 text-center bg-slate-50/50 dark:bg-slate-800/30 transition-all cursor-pointer relative">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white mb-1">Klik atau seret file Excel (.xlsx) atau CSV ke sini</p>
                            <p class="text-[11px] text-slate-400">Mendukung Microsoft Excel (.xlsx/.xls) dengan kolom terpisah otomatis, atau file .csv (maks. 10 MB)</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] text-slate-500">Pratinjau pembagian kolom data akan ditampilkan sebelum disimpan ke database.</p>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                        Pratinjau Data &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- Guide & Instructions -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm text-xs space-y-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <span>💡</span> Panduan Pengisian Kolom
            </h3>

            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-2xl border border-emerald-200 dark:border-emerald-800 text-[11px] text-emerald-800 dark:text-emerald-200 space-y-1">
                <span class="font-bold block text-emerald-900 dark:text-emerald-300">✨ Format Excel Terpisah Kolom (Praktis):</span>
                <p class="leading-relaxed">
                    Gunakan tombol hijau <strong>"Unduh Template Excel (.xlsx)"</strong> di atas. Setiap variabel data sudah terbagi ke dalam kolom A, B, C, dst., sehingga Anda dapat mengetik atau <em>copy-paste</em> langsung di Microsoft Excel tanpa ribet memikirkan pemisah koma / titik koma.
                </p>
            </div>

            @if($tab === 'penugasan')
                <div class="space-y-2 text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                    <p>Struktur kolom pada template <strong>Penugasan (SPT)</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[10px] bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl">
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
                <div class="space-y-2 text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                    <p>Struktur kolom pada template <strong>Matriks Tindak Lanjut (LHP)</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[10px] bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl">
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
                <div class="space-y-2 text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                    <p>Struktur kolom pada template <strong>Master Objek Pengawasan</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[10px] bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl">
                        <li><strong>Kolom A:</strong> nama_instansi_objek (wajib)</li>
                        <li><strong>Kolom B:</strong> kategori (opd / kecamatan / desa)</li>
                        <li><strong>Kolom C:</strong> status_aktif (aktif / nonaktif)</li>
                    </ol>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
