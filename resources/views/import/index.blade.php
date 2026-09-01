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
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">
                    Unggah File CSV {{ $tab === 'penugasan' ? 'Penugasan (SPT)' : ($tab === 'tindak_lanjut' ? 'Matriks LHP' : 'Master Objek') }}
                </h3>
                <a href="{{ route('import.template', $tab) }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Template CSV</span>
                </a>
            </div>

            <form method="POST" action="{{ route('import.preview') }}" enctype="multipart/form-data" class="space-y-5 text-xs">
                @csrf
                <input type="hidden" name="tipe" value="{{ $tab }}">

                <div>
                    <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Pilih Berkas CSV / Spreadsheet</label>
                    <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-8 text-center bg-slate-50/50 dark:bg-slate-800/30 transition-all cursor-pointer relative">
                        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white mb-1">Klik atau seret file CSV ke sini</p>
                            <p class="text-[11px] text-slate-400">Mendukung format .csv dengan delimiter koma (,) atau titik koma (;), maks. 10 MB</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] text-slate-500">Pratinjau akan ditampilkan sebelum data benar-benar disimpan.</p>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                        Pratinjau Data CSV &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- Guide & Instructions -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm text-xs space-y-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <span>💡</span> Panduan Format Kolom
            </h3>

            @if($tab === 'penugasan')
                <div class="space-y-2 text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                    <p>Urutan kolom CSV untuk <strong>Penugasan (SPT)</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[10px] bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl">
                        <li>no_spt (wajib)</li>
                        <li>uraian_penugasan (wajib)</li>
                        <li>irban (mis. Irban I)</li>
                        <li>jenis_penugasan (mis. Audit)</li>
                        <li>sumber_penugasan (mis. PKPT)</li>
                        <li>objek_penugasan (pisahkan koma)</li>
                        <li>tanggal_mulai (YYYY-MM-DD)</li>
                        <li>tanggal_selesai (YYYY-MM-DD)</li>
                        <li>status (selesai/berjalan)</li>
                    </ol>
                </div>
            @elseif($tab === 'tindak_lanjut')
                <div class="space-y-2 text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                    <p>Urutan kolom CSV untuk <strong>Matriks Tindak Lanjut (LHP)</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[10px] bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl">
                        <li>no_lhp (wajib)</li>
                        <li>no_spt (relasi SPT)</li>
                        <li>uraian_temuan (wajib)</li>
                        <li>rekomendasi_wajib (wajib)</li>
                        <li>nilai_rekomendasi_rp (angka)</li>
                        <li>target_penyelesaian (YYYY-MM-DD)</li>
                        <li>status_tindak_lanjut (selesai/proses/belum)</li>
                        <li>judul_lhp (opsional)</li>
                        <li>tanggal_lhp (YYYY-MM-DD)</li>
                    </ol>
                </div>
            @else
                <div class="space-y-2 text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                    <p>Urutan kolom CSV untuk <strong>Master Objek Pengawasan</strong>:</p>
                    <ol class="list-decimal list-inside space-y-1 font-mono text-[10px] bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl">
                        <li>nama_instansi_objek (wajib)</li>
                        <li>kategori (opd/kecamatan/desa)</li>
                        <li>status_aktif (aktif/nonaktif)</li>
                    </ol>
                </div>
            @endif

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
                <p><strong>Tips:</strong> Disarankan menggunakan fitur <em>Unduh Template CSV</em> terlebih dahulu, isi dengan Microsoft Excel / Google Sheets, lalu simpan sebagai file CSV (Comma Delimited).</p>
            </div>
        </div>
    </div>
</x-app-layout>
