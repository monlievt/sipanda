<x-app-layout>
    <x-slot name="header">
        Generator Ikhtisar Laporan Hasil Pengawasan (ILHP)
    </x-slot>

    <div class="space-y-6">
        <!-- Top Title & Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ikhtisar-laporan.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Dokumen Ikhtisar
                </a>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Kompilasi & Pembuatan Dokumen ILHP</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sistem secara otomatis mengompilasi data transaksi penugasan, capaian PKPPT, dan tindak lanjut rekomendasi ke dalam sistematika baku 5 Bab.</p>
            </div>
        </div>

        <!-- Filter / Pilihan Periode Form -->
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('ikhtisar-laporan.create') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                <div class="sm:col-span-4">
                    <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 uppercase mb-1">1. Pilih Tahun Anggaran</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-emerald-500">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun Anggaran {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-5">
                    <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 uppercase mb-1">2. Pilih Periode Pelaporan</label>
                    <select name="periode" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:ring-emerald-500">
                        <option value="triwulan_1" {{ $periode === 'triwulan_1' ? 'selected' : '' }}>Triwulan I (01 Januari s/d 31 Maret)</option>
                        <option value="triwulan_2" {{ $periode === 'triwulan_2' ? 'selected' : '' }}>Triwulan II (01 April s/d 30 Juni)</option>
                        <option value="triwulan_3" {{ $periode === 'triwulan_3' ? 'selected' : '' }}>Triwulan III (01 Juli s/d 30 September)</option>
                        <option value="triwulan_4" {{ $periode === 'triwulan_4' ? 'selected' : '' }}>Triwulan IV (01 Oktober s/d 31 Desember)</option>
                        <option value="semester_1" {{ $periode === 'semester_1' ? 'selected' : '' }}>Semester I (01 Januari s/d 30 Juni)</option>
                        <option value="semester_2" {{ $periode === 'semester_2' ? 'selected' : '' }}>Semester II (01 Juli s/d 31 Desember)</option>
                        <option value="tahunan" {{ $periode === 'tahunan' ? 'selected' : '' }}>Tahunan Penuh (01 Januari s/d 31 Desember)</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                        🔄 Refresh Data Kompilasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Simpan & Editor Narasi BAB V -->
        <form method="POST" action="{{ route('ikhtisar-laporan.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="periode" value="{{ $periode }}">

            <!-- Parameter Dokumen -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                    <h3 class="font-bold text-slate-800 dark:text-white text-sm">Informasi & Metadata Dokumen</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 text-xs">
                    <div class="sm:col-span-7">
                        <label class="block font-semibold mb-1">Judul Laporan Resmi <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $defaultJudul) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-semibold mb-1">Nomor Surat Dinas (Jika Ada)</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat', '700/   /406.008/' . $tahun) }}" placeholder="mis. 700/01/406.008/2026" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500 font-mono">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold mb-1">Tanggal Dokumen <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', date('Y-m-d')) }}" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- PREVIEW DATA TERKOMPILASI BAB I s/d BAB IV -->
            <div class="bg-slate-50 dark:bg-slate-900/60 p-5 sm:p-6 rounded-3xl border border-slate-200 dark:border-slate-800 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-md">
                            Kompilasi Otomatis Database SIPANDA
                        </span>
                        <h3 class="text-base font-black text-slate-900 dark:text-white mt-1">Pratinjau Sistematika Laporan (BAB I s/d BAB IV)</h3>
                    </div>
                    <span class="text-xs text-slate-500">Rentang: {{ \Carbon\Carbon::parse($range['start'])->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($range['end'])->translatedFormat('d M Y') }}</span>
                </div>

                <!-- BAB I: INFORMASI UMUM -->
                <div class="bg-white dark:bg-slate-800/80 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">I</span>
                        BAB I INFORMASI UMUM
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Dasar Hukum Terbit</span>
                            <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ count($compiledData['dasarHukum']) }} Regulasi Baku & SK PKPT</p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Kekuatan SDM APIP</span>
                            <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $compiledData['totalAuditor'] }} Auditor, {{ $compiledData['totalPpupd'] }} PPUPD ({{ count($compiledData['irbans']) }} Unit Kerja)</p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Realisasi Program PKPPT</span>
                            <p class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $compiledData['totalSptTerbit'] }} SPT Terbit ({{ $compiledData['persenRealisasiPkppt'] }}% dari {{ $compiledData['totalTargetPkppt'] }} Rencana)</p>
                        </div>
                    </div>
                </div>

                <!-- BAB II: HASIL PENGAWASAN PER JENIS -->
                <div class="bg-white dark:bg-slate-800/80 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">II</span>
                        BAB II HASIL PENGAWASAN (REALISASI PER JENIS PENGAWASAN)
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-center text-xs">
                        <div class="p-2.5 bg-blue-50 dark:bg-blue-950/50 rounded-xl border border-blue-200 dark:border-blue-900">
                            <span class="text-[10px] font-bold text-blue-700 dark:text-blue-300 block">A. AUDIT</span>
                            <p class="text-base font-black text-blue-900 dark:text-blue-200 mt-1">
                                {{ (count($compiledData['kategoriAudit']['audit_kinerja'] ?? [])) + (count($compiledData['kategoriAudit']['audit_dtt'] ?? [])) }}
                            </p>
                            <span class="text-[9px] text-slate-500">Kinerja & ADTT</span>
                        </div>
                        <div class="p-2.5 bg-purple-50 dark:bg-purple-950/50 rounded-xl border border-purple-200 dark:border-purple-900">
                            <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 block">B. REVIU</span>
                            <p class="text-base font-black text-purple-900 dark:text-purple-200 mt-1">{{ count($compiledData['kategoriReviu']) }}</p>
                            <span class="text-[9px] text-slate-500">LKPD, RKA, PBJ</span>
                        </div>
                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/50 rounded-xl border border-amber-200 dark:border-amber-900">
                            <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 block">C. EVALUASI</span>
                            <p class="text-base font-black text-amber-900 dark:text-amber-200 mt-1">{{ count($compiledData['kategoriEvaluasi']) }}</p>
                            <span class="text-[9px] text-slate-500">SAKIP, SPIP, RB</span>
                        </div>
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl border border-emerald-200 dark:border-emerald-900">
                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 block">D. PEMANTAUAN</span>
                            <p class="text-base font-black text-emerald-900 dark:text-emerald-200 mt-1">{{ count($compiledData['kategoriPemantauan']) }}</p>
                            <span class="text-[9px] text-slate-500">Kas, Proyek, TL</span>
                        </div>
                        <div class="p-2.5 bg-slate-100 dark:bg-slate-700/50 rounded-xl border border-slate-300 dark:border-slate-600">
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300 block">E. LAINNYA</span>
                            <p class="text-base font-black text-slate-900 dark:text-white mt-1">{{ count($compiledData['kategoriLainnya']) }}</p>
                            <span class="text-[9px] text-slate-500">Konsultasi, Asistensi</span>
                        </div>
                    </div>
                </div>

                <!-- BAB III: HASIL PEMANTAUAN TINDAK LANJUT -->
                <div class="bg-white dark:bg-slate-800/80 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">III</span>
                        BAB III HASIL PEMANTAUAN TINDAK LANJUT (APIP & BPK)
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800">
                            <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 uppercase">Selesai (SS)</span>
                            <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                                {{ $compiledData['tlCountSelesai'] }} <span class="text-xs font-normal text-slate-500">/ {{ $compiledData['tlCountTotal'] }} ({{ $compiledData['tlPersenSelesai'] }}%)</span>
                            </p>
                        </div>
                        <div class="p-3 bg-blue-50/80 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-800">
                            <span class="text-[10px] font-bold text-blue-800 dark:text-blue-300 uppercase">Belum Sesuai (BS)</span>
                            <p class="text-lg font-black text-blue-600 dark:text-blue-400 mt-0.5">{{ $compiledData['tlCountBelumSesuai'] }} Rekomendasi</p>
                        </div>
                        <div class="p-3 bg-rose-50/80 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800">
                            <span class="text-[10px] font-bold text-rose-800 dark:text-rose-300 uppercase">Belum Ditindaklanjuti (BTL)</span>
                            <p class="text-lg font-black text-rose-600 dark:text-rose-400 mt-0.5">{{ $compiledData['tlCountBelum'] }} Rekomendasi</p>
                        </div>
                        <div class="p-3 bg-amber-50/80 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-800">
                            <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300 uppercase">Realisasi Setor Kasda</span>
                            <p class="text-sm font-black text-amber-600 dark:text-amber-400 mt-0.5">Rp {{ number_format($compiledData['tlTotalSetorRp'], 0, ',', '.') }}</p>
                            <span class="text-[9px] text-slate-500">dari Rp {{ number_format($compiledData['tlTotalTargetRp'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- BAB IV: PENANGANAN PENGADUAN MASYARAKAT -->
                <div class="bg-white dark:bg-slate-800/80 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-3">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wide flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px]">IV</span>
                        BAB IV HASIL PENANGANAN PENGADUAN MASYARAKAT (DUMAS / WBS / APH)
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Tercatat sebanyak <strong>{{ count($compiledData['sptDumas']) }} penugasan khusus / klarifikasi pengaduan masyarakat</strong> yang ditangani selama periode {{ $periodeTitle }} Tahun {{ $tahun }}.
                    </p>
                </div>
            </div>

            <!-- INPUT NARRATIVE BAB V (SIMPULAN, HAMBATAN, REKOMENDASI) -->
            <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">V</span>
                        <h3 class="font-black text-slate-900 dark:text-white text-sm">BAB V SIMPULAN, HAMBATAN DAN REKOMENDASI</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Lengkapi rumusan evaluasi kualitatif Kasubag Perencanaan & Pelaporan / Sekretaris untuk disampaikan kepada Bupati.</p>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">A. Simpulan Eksekutif</label>
                        <textarea name="simpulan" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500" placeholder="Jelaskan simpulan umum capaian pengawasan, efektivitas belanja APBD, dan kualitas tata kelola OPD pada periode ini...">{{ old('simpulan', "Secara umum pelaksanaan program pengawasan dan pembinaan pada {$periodeTitle} Tahun {$tahun} telah berjalan sesuai target PKPT Berbasis Risiko. Kepatuhan perangkat daerah dalam menindaklanjuti rekomendasi menunjukkan tren positif dengan tingkat penyelesaian mencapai {$compiledData['tlPersenSelesai']}%.") }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">B. Hambatan & Kendala Lapangan</label>
                        <textarea name="hambatan" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500" placeholder="Uraikan kendala operasional yang dihadapi (misal: keterbatasan personil auditor fungsional, lambatnya respon OPD)...">{{ old('hambatan', "1. Terdapat beberapa Perangkat Daerah yang lambat dalam melengkapi bukti fisik tindak lanjut hasil pengawasan;\n2. Keterbatasan jumlah Pejabat Fungsional Auditor dan PPUPD dibanding jumlah objek pengawasan daerah yang luas.") }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">C. Rekomendasi Kebijakan untuk Bupati</label>
                        <textarea name="rekomendasi" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-emerald-500" placeholder="Uraikan saran kebijakan strategis kepada Bapak Bupati Trenggalek...">{{ old('rekomendasi', "1. Menegur Kepala Perangkat Daerah yang memiliki tingkat kepatuhan tindak lanjut rekomendasi di bawah 75%;\n2. Mendorong optimalisasi sistem pengendalian intern (SPIP) dan mitigasi risiko pada pengadaan barang/jasa bernilai strategis.") }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Dokumen</label>
                        <select name="status" class="w-full sm:w-64 rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-emerald-500">
                            <option value="draft">📝 Simpan Sebagai Draf Konsep</option>
                            <option value="final">✓ Simpan Sebagai Dokumen Final Siap Cetak</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('ikhtisar-laporan.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700 text-xs font-bold rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                    💾 Simpan & Terbitkan Dokumen Ikhtisar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
