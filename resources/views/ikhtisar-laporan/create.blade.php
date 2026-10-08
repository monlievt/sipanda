<x-app-layout>
    <x-slot name="header">
        Generator Ikhtisar Laporan Hasil Pengawasan (ILHP)
    </x-slot>

    <div class="space-y-6" x-data="ilhpGenerator({
        tahun: {{ $tahun }},
        periode: '{{ $periode }}',
        apiKey: '{{ addslashes($geminiApiKey ?? '') }}',
        model: '{{ addslashes($geminiModel ?? 'gemini-1.5-flash') }}'
    })">
        <!-- Top Title & Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ikhtisar-laporan.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Dokumen Ikhtisar
                </a>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Kompilasi & Pembuatan Dokumen ILHP</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Sistem mengompilasi data transaksi penugasan, SDM definitif, resume AI temuan/rekomendasi, rekapitulasi matriks LHP, dan rincian kluster pengawasan.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showModalApiKey = true" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-700 inline-flex items-center gap-1.5 shadow-xs transition-all cursor-pointer">
                    <span>⚙️ Pengaturan Gemini Key</span>
                    <span class="w-2 h-2 rounded-full" :class="apiKey ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                </button>
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
                        <option value="tahunan" {{ $periode === 'tahunan' ? 'selected' : '' }}>Tahunan Penuh (01 Januari s/d 31 Desember)</option>
                        <option value="semester_1" {{ $periode === 'semester_1' ? 'selected' : '' }}>Semester I (01 Januari s/d 30 Juni)</option>
                        <option value="semester_2" {{ $periode === 'semester_2' ? 'selected' : '' }}>Semester II (01 Juli s/d 31 Desember)</option>
                        <option value="triwulan_1" {{ $periode === 'triwulan_1' ? 'selected' : '' }}>Triwulan I (01 Januari s/d 31 Maret)</option>
                        <option value="triwulan_2" {{ $periode === 'triwulan_2' ? 'selected' : '' }}>Triwulan II (01 April s/d 30 Juni)</option>
                        <option value="triwulan_3" {{ $periode === 'triwulan_3' ? 'selected' : '' }}>Triwulan III (01 Juli s/d 30 September)</option>
                        <option value="triwulan_4" {{ $periode === 'triwulan_4' ? 'selected' : '' }}>Triwulan IV (01 Oktober s/d 31 Desember)</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                        🔄 Refresh Data Kompilasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Simpan ILHP -->
        <form method="POST" action="{{ route('ikhtisar-laporan.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="periode" value="{{ $periode }}">

            <!-- Metadata Dokumen -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Parameter & Metadata Dokumen</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Dokumen Ikhtisar <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $defaultJudul) }}" required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-emerald-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat / Naskah</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat', '700.1.1/' . rand(100, 999) . '/406.008/' . $tahun) }}" placeholder="Nomor Surat..." class="w-full text-xs font-mono rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-emerald-500">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Naskah Laporan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', date('Y-m-d')) }}" required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 1: SUMBER DAYA MANUSIA (SDM) -->
            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-black text-xs">1</span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Sumber Daya Manusia (SDM)</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Jumlah SDM Inspektorat Kabupaten Trenggalek berdasarkan jabatan definitif internal yang aktif.</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-full border border-blue-200 dark:border-blue-800">
                        Total Pegawai Aktif: {{ $compiledData['totalPegawaiAktif'] }} Orang
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Jabatan</th>
                                <th class="py-2.5 px-4 w-40 text-center">Jumlah (Orang)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($compiledData['tabelSdm'] as $sdm)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-500">{{ $sdm['no'] }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800 dark:text-slate-200">{{ $sdm['jabatan'] }}</td>
                                    <td class="py-2.5 px-4 text-center font-extrabold text-slate-900 dark:text-white">{{ $sdm['jumlah'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-700 font-bold">
                            <tr>
                                <td colspan="2" class="py-2.5 px-4 text-right text-slate-700 dark:text-slate-300">TOTAL PEGAWAI INTERNAL:</td>
                                <td class="py-2.5 px-4 text-center font-black text-emerald-600 dark:text-emerald-400">{{ $compiledData['totalPegawaiAktif'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="text-[10px] text-slate-400 italic">
                    * Unsur Kesekretariatan dihitung otomatis: Total Seluruh Pegawai – (Inspektur + Sekretaris + Inspektur Pembantu + Fungsional Auditor + Fungsional PPUPD).
                </p>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 2: RESUME CATATAN/TEMUAN & REKOMENDASI (AI SUMMARY) -->
            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center font-black text-xs">2</span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Resume Catatan/Temuan serta Saran/Rekomendasi Pengawasan Internal</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Ringkasan eksekutif permasalahan dan saran perbaikan yang dianalisis oleh AI dari temuan tim pemeriksa.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="generateAiResume()" :disabled="isGenerating" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-500/20 inline-flex items-center gap-2 cursor-pointer transition-all">
                            <span x-show="!isGenerating">🤖 Buat Resume Otomatis dengan Gemini AI</span>
                            <span x-show="isGenerating" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sedang Menganalisis Temuan...
                            </span>
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Teks Resume Catatan/Temuan & Saran/Rekomendasi (Dapat Diedit Manual) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] text-slate-400">Total Basis Temuan Periode Ini: {{ count($compiledData['listTemuanRekomendasi']) }} Item</span>
                    </div>

                    <textarea name="resume_ai" x-model="resumeText" rows="7" required placeholder="Klik tombol 'Buat Resume Otomatis dengan Gemini AI' di atas atau tuliskan resume eksekutif temuan di sini..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 p-3.5 text-slate-900 dark:text-white leading-relaxed focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 3: REKAPITULASI HASIL PENGAWASAN (MATRIKS TINDAK LANJUT) -->
            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <span class="w-7 h-7 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-black text-xs">3</span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Rekapitulasi Hasil Pengawasan Inspektorat Kabupaten Trenggalek</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Data rekapitulasi bersumber dari agregasi Matriks Tindak Lanjut Hasil Pengawasan (LHP).</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider text-center">
                            <tr>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700 w-10">No</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700 w-16">Tahun</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700 w-20">Total LHP</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700">Nilai yang Dilakukan Pengawasan (Rp)</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700 w-24">Total Saran / Rekomendasi</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700">Nilai Rekomendasi / Saran (Rp)</th>
                                <th colspan="4" class="py-2 px-3 border-b border-r border-slate-200 dark:border-slate-700">Jumlah Status Tindak Lanjut</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 dark:border-slate-700">Nilai Pengembalian (Rp)</th>
                                <th rowspan="2" class="py-2.5 px-3">Sisa Pengembalian (Rp)</th>
                            </tr>
                            <tr>
                                <th class="py-1.5 px-2.5 text-emerald-600 border-r border-slate-200 dark:border-slate-700">Sesuai</th>
                                <th class="py-1.5 px-2.5 text-blue-600 border-r border-slate-200 dark:border-slate-700">Belum Sesuai</th>
                                <th class="py-1.5 px-2.5 text-amber-600 border-r border-slate-200 dark:border-slate-700">Belum di TL</th>
                                <th class="py-1.5 px-2.5 text-slate-500 border-r border-slate-200 dark:border-slate-700">TDT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                            @forelse($compiledData['rekapHasilPengawasanPerTahun'] as $rekap)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-3 text-center text-slate-500 font-bold">{{ $rekap['no'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-slate-800 dark:text-slate-200">{{ $rekap['tahun'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-slate-900 dark:text-white">{{ $rekap['total_lhp'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono">{{ number_format($rekap['nilai_diawasi_rp'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-slate-900 dark:text-white">{{ $rekap['total_rekomendasi'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-slate-900 dark:text-white">{{ number_format($rekap['nilai_rekomendasi_rp'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-bold text-emerald-600 bg-emerald-50/40 dark:bg-emerald-950/20">{{ $rekap['status_sesuai'] }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-bold text-blue-600 bg-blue-50/40 dark:bg-blue-950/20">{{ $rekap['status_belum_sesuai'] }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-bold text-amber-600 bg-amber-50/40 dark:bg-amber-950/20">{{ $rekap['status_belum_tl'] }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-bold text-slate-500 bg-slate-50 dark:bg-slate-800/20">{{ $rekap['status_tdt'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-emerald-600">{{ number_format($rekap['nilai_pengembalian_rp'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-bold">{{ number_format($rekap['sisa_pengembalian_rp'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="py-4 text-center text-slate-400">Belum ada data pengawasan untuk periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 4: RINCIAN PENGAWASAN SESUAI KELOMPOK / KLUSTER PENGAWASAN -->
            <!-- ═══════════════════════════════════════════════════════════════════════ -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <span class="w-7 h-7 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black text-xs">4</span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Rincian Pengawasan sesuai Kelompok / Kluster Pengawasan</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Perbandingan target rencana (PKPPT) dengan realisasi Surat Tugas (SPT perpanjangan & bantuan dihitung 1 dengan SPT induk).</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Kelompok / Kluster Pengawasan</th>
                                <th class="py-2.5 px-4 w-44 text-center">Rencana Tahun {{ $tahun }}</th>
                                <th class="py-2.5 px-4 w-44 text-center">Realisasi Pengawasan Tahun {{ $tahun }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($compiledData['tabelKlusterPengawasan'] as $kluster)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-500">{{ $kluster['no'] }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800 dark:text-slate-200">{{ $kluster['kluster'] }}</td>
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-700 dark:text-slate-300">{{ $kluster['rencana'] }}</td>
                                    <td class="py-2.5 px-4 text-center font-extrabold text-emerald-600 dark:text-emerald-400">{{ $kluster['realisasi'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-700 font-bold">
                            <tr>
                                <td colspan="2" class="py-2.5 px-4 text-right text-slate-700 dark:text-slate-300">TOTAL SELURUH KLUSTER:</td>
                                <td class="py-2.5 px-4 text-center font-black text-slate-800 dark:text-slate-200">{{ $compiledData['totalRencanaKluster'] }}</td>
                                <td class="py-2.5 px-4 text-center font-black text-emerald-600 dark:text-emerald-400">{{ $compiledData['totalRealisasiKluster'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Catatan Tambahan & Status Simpan -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-8">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Khusus Tambahan (Optional)</label>
                        <input type="text" name="catatan_khusus" placeholder="mis. Ikhtisar disusun untuk lampiran Laporan Berkala kepada Bupati..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Finalisasi Dokumen</label>
                        <select name="status" class="w-full text-xs font-bold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-emerald-500">
                            <option value="draft">Draf Laporan (Dapat Disunting Kembali)</option>
                            <option value="final">Final Resmi (Siap Cetak & Didistribusikan)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('ikhtisar-laporan.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-xs shadow-md shadow-emerald-500/20 cursor-pointer transition-all">
                        💾 Simpan Dokumen Ikhtisar Laporan (ILHP)
                    </button>
                </div>
            </div>
        </form>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL PENGATURAN GEMINI API KEY -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="showModalApiKey" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4" x-cloak>
            <div @click.away="showModalApiKey = false" class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2 text-purple-600">
                        <span class="text-xl">⚙️</span>
                        <h3 class="font-black text-slate-900 dark:text-white text-sm">Pengaturan Google Gemini API Key</h3>
                    </div>
                    <button type="button" @click="showModalApiKey = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">&times;</button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        Fitur <strong>AI Summary Temuan & Rekomendasi</strong> menggunakan Google Gemini Flash yang <strong>100% Gratis</strong> tanpa biaya berlangganan.
                    </p>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Google Gemini API Key <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" x-model="apiKeyInput" placeholder="Tempel API Key AI Studio di sini (AIzaSy...)" class="w-full text-xs font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan Model AI</label>
                        <select x-model="modelInput" class="w-full text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                            <option value="gemini-1.5-flash">Gemini 1.5 Flash (Sangat Cepat & Gratis)</option>
                            <option value="gemini-2.0-flash">Gemini 2.0 Flash (Generasi Terbaru & Gratis)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800 text-[11px] text-purple-900 dark:text-purple-300 flex items-start gap-2">
                        <span>💡</span>
                        <div>
                            <span>Belum punya API Key? Dapatkan gratis dalam 1 menit di </span>
                            <a href="https://aistudio.google.com/app/apikey" target="_blank" class="font-bold underline text-purple-700 dark:text-purple-300 hover:text-purple-900">
                                Google AI Studio (aistudio.google.com) &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showModalApiKey = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" @click="saveApiKey()" :disabled="isSavingKey" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs shadow-md cursor-pointer">
                        <span x-show="!isSavingKey">Simpan Pengaturan API Key</span>
                        <span x-show="isSavingKey">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ilhpGenerator(config) {
            return {
                tahun: config.tahun,
                periode: config.periode,
                apiKey: config.apiKey,
                apiKeyInput: config.apiKey,
                modelInput: config.model || 'gemini-1.5-flash',
                resumeText: '',
                isGenerating: false,
                isSavingKey: false,
                showModalApiKey: false,

                async generateAiResume() {
                    this.isGenerating = true;
                    try {
                        const res = await fetch("{{ route('ikhtisar-laporan.generate_resume_ai') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                tahun: this.tahun,
                                periode: this.periode
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.resumeText = data.resume;
                            if (data.note) {
                                alert("✓ Resume temuan berhasil disusun!\n(" + data.note + ")");
                            }
                        } else {
                            alert(data.message || "Gagal memproses analisis AI.");
                            if (data.resume) {
                                this.resumeText = data.resume;
                            }
                        }
                    } catch (e) {
                        alert("Terjadi kesalahan koneksi saat memanggil AI: " + e.message);
                    } finally {
                        this.isGenerating = false;
                    }
                },

                async saveApiKey() {
                    if (!this.apiKeyInput.trim()) {
                        alert("API Key tidak boleh kosong.");
                        return;
                    }

                    this.isSavingKey = true;
                    try {
                        const res = await fetch("{{ route('ikhtisar-laporan.save_gemini_key') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                gemini_api_key: this.apiKeyInput.trim(),
                                gemini_model: this.modelInput
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.apiKey = this.apiKeyInput.trim();
                            this.showModalApiKey = false;
                            alert("✓ Pengaturan Gemini API Key berhasil disimpan!");
                        } else {
                            alert(data.message || "Gagal menyimpan API Key.");
                        }
                    } catch (e) {
                        alert("Kesalahan saat menyimpan: " + e.message);
                    } finally {
                        this.isSavingKey = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
