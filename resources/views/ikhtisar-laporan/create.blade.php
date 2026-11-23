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
                <a href="{{ route('ikhtisar-laporan.index') }}" class="text-xs text-brand-ink hover:underline font-semibold inline-flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Dokumen Ikhtisar</span>
                </a>
                <h2 class="text-xl font-bold text-ink tracking-tight">Kompilasi & Pembuatan Dokumen ILHP</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Sistem mengompilasi data transaksi penugasan, SDM definitif, resume AI temuan/rekomendasi, rekapitulasi matriks LHP, dan rincian kluster pengawasan.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showModalApiKey = true" class="ledger-btn-secondary !text-xs !py-2 !px-3.5 !min-h-0">
                    <svg class="w-4 h-4 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan Gemini Key</span>
                    <span class="w-2 h-2 rounded-full" :class="apiKey ? 'bg-emerald-600' : 'bg-amber-600'"></span>
                </button>
            </div>
        </div>

        <!-- Filter / Pilihan Periode Form -->
        <div class="bg-surface p-4 sm:p-5 rounded-[10px] border border-hairline">
            <form method="GET" action="{{ route('ikhtisar-laporan.create') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                <div class="sm:col-span-4">
                    <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">1. Pilih Tahun Anggaran</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2.5">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun Anggaran {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-5">
                    <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">2. Pilih Periode Pelaporan</label>
                    <select name="periode" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2.5">
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
                    <button type="submit" class="ledger-btn-secondary w-full !text-xs">
                        Refresh Data Kompilasi
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
            <div class="bg-surface p-5 rounded-[10px] border border-hairline space-y-4">
                <div class="flex items-center gap-2 border-b border-hairline pb-3">
                    <span class="w-2 h-2 bg-brand rounded-full"></span>
                    <h3 class="text-sm font-semibold text-ink uppercase tracking-wider">Parameter & Metadata Dokumen</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-semibold text-ink mb-1">Judul Dokumen Ikhtisar <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $defaultJudul) }}" required class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-ink mb-1">Nomor Surat / Naskah</label>
                        <input type="text" name="nomor_surat" value="{{ old('nomor_surat', '700.1.1/' . rand(100, 999) . '/406.008/' . $tahun) }}" placeholder="Nomor Surat..." class="w-full text-xs font-mono rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-ink mb-1">Tanggal Naskah Laporan <span class="text-red-700 dark:text-red-400">*</span></label>
                        <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', date('Y-m-d')) }}" required class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                    </div>
                </div>
            </div>

            <!-- BAGIAN 1: SUMBER DAYA MANUSIA (SDM) -->
            <div class="bg-surface p-5 rounded-[10px] border border-hairline space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-hairline pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-[6px] bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800 flex items-center justify-center font-bold text-xs shrink-0">1</span>
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Sumber Daya Manusia (SDM)</h3>
                            <p class="text-xs text-ink-secondary">Jumlah SDM Inspektorat Kabupaten Trenggalek berdasarkan jabatan definitif internal yang aktif.</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-blue-800 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-[4px] border border-blue-200 dark:border-blue-800 tabular-nums">
                        Total Pegawai Aktif: {{ $compiledData['totalPegawaiAktif'] }} Orang
                    </span>
                </div>

                <div class="overflow-x-auto rounded-[6px] border border-hairline">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Jabatan</th>
                                <th class="py-2.5 px-4 w-40 text-center">Jumlah (Orang)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline">
                            @foreach($compiledData['tabelSdm'] as $sdm)
                                <tr class="hover:bg-surface-subtle/60">
                                    <td class="py-2.5 px-4 text-center font-semibold text-ink-muted tabular-nums">{{ $sdm['no'] }}</td>
                                    <td class="py-2.5 px-4 font-medium text-ink">{{ $sdm['jabatan'] }}</td>
                                    <td class="py-2.5 px-4 text-center font-bold text-ink tabular-nums">{{ $sdm['jumlah'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-surface-subtle border-t border-hairline font-semibold">
                            <tr>
                                <td colspan="2" class="py-2.5 px-4 text-right text-ink-secondary">TOTAL PEGAWAI INTERNAL:</td>
                                <td class="py-2.5 px-4 text-center font-bold text-brand-ink tabular-nums">{{ $compiledData['totalPegawaiAktif'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="text-[11px] text-ink-muted italic">
                    * Unsur Kesekretariatan dihitung otomatis: Total Seluruh Pegawai - (Inspektur + Sekretaris + Inspektur Pembantu + Fungsional Auditor + Fungsional PPUPD).
                </p>
            </div>

            <!-- BAGIAN 2: RESUME CATATAN/TEMUAN & REKOMENDASI (AI SUMMARY) -->
            <div class="bg-surface p-5 rounded-[10px] border border-hairline space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-hairline pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border flex items-center justify-center font-bold text-xs shrink-0">2</span>
                        <div>
                            <h3 class="text-sm font-semibold text-ink">Resume Catatan/Temuan serta Saran/Rekomendasi Pengawasan Internal</h3>
                            <p class="text-xs text-ink-secondary">Ringkasan eksekutif permasalahan dan saran perbaikan yang dianalisis oleh AI dari temuan tim pemeriksa.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="generateAiResume()" :disabled="isGenerating" class="ledger-btn-primary !text-xs disabled:opacity-50">
                            <span x-show="!isGenerating">Buat Resume Otomatis dengan Gemini AI</span>
                            <span x-show="isGenerating" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sedang Menganalisis Temuan...
                            </span>
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-ink">
                            Teks Resume Catatan/Temuan & Saran/Rekomendasi (Dapat Diedit Manual) <span class="text-red-700 dark:text-red-400">*</span>
                        </label>
                        <span class="text-[11px] text-ink-muted tabular-nums">Total Basis Temuan Periode Ini: {{ count($compiledData['listTemuanRekomendasi']) }} Item</span>
                    </div>

                    <textarea name="resume_ai" x-model="resumeText" rows="7" required placeholder="Klik tombol 'Buat Resume Otomatis dengan Gemini AI' di atas atau tuliskan resume eksekutif temuan di sini..." class="w-full text-xs rounded-[6px] border border-hairline-strong bg-surface p-3.5 text-ink leading-relaxed"></textarea>
                </div>
            </div>

            <!-- BAGIAN 3: REKAPITULASI HASIL PENGAWASAN (MATRIKS TINDAK LANJUT) -->
            <div class="bg-surface p-5 rounded-[10px] border border-hairline space-y-4">
                <div class="flex items-center gap-2.5 border-b border-hairline pb-3">
                    <span class="w-7 h-7 rounded-[6px] bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">3</span>
                    <div>
                        <h3 class="text-sm font-semibold text-ink">Rekapitulasi Hasil Pengawasan Inspektorat Kabupaten Trenggalek</h3>
                        <p class="text-xs text-ink-secondary">Data rekapitulasi bersumber dari agregasi Matriks Tindak Lanjut Hasil Pengawasan (LHP).</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-[6px] border border-hairline">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider text-center border-b border-hairline">
                            <tr>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline w-10">No</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline w-16">Tahun</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline w-20">Total LHP</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline">Nilai yang Dilakukan Pengawasan (Rp)</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline w-24">Total Saran / Rekomendasi</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline">Nilai Rekomendasi / Saran (Rp)</th>
                                <th colspan="4" class="py-2 px-3 border-b border-r border-hairline">Jumlah Status Tindak Lanjut</th>
                                <th rowspan="2" class="py-2.5 px-3 border-r border-hairline">Nilai Pengembalian (Rp)</th>
                                <th rowspan="2" class="py-2.5 px-3">Sisa Pengembalian (Rp)</th>
                            </tr>
                            <tr>
                                <th class="py-1.5 px-2.5 text-emerald-800 dark:text-emerald-300 border-r border-hairline">Sesuai</th>
                                <th class="py-1.5 px-2.5 text-blue-800 dark:text-blue-300 border-r border-hairline">Belum Sesuai</th>
                                <th class="py-1.5 px-2.5 text-amber-900 dark:text-amber-300 border-r border-hairline">Belum di TL</th>
                                <th class="py-1.5 px-2.5 text-ink-secondary border-r border-hairline">TDT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline font-medium">
                            @forelse($compiledData['rekapHasilPengawasanPerTahun'] as $rekap)
                                <tr class="hover:bg-surface-subtle/60">
                                    <td class="py-2.5 px-3 text-center text-ink-muted font-semibold tabular-nums">{{ $rekap['no'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-semibold text-ink tabular-nums">{{ $rekap['tahun'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-semibold text-ink tabular-nums">{{ $rekap['total_lhp'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-ink tabular-nums">{{ number_format($rekap['nilai_diawasi_rp'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-center font-semibold text-ink tabular-nums">{{ $rekap['total_rekomendasi'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-ink tabular-nums">{{ number_format($rekap['nilai_rekomendasi_rp'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50/50 dark:bg-emerald-950/20 tabular-nums">{{ $rekap['status_sesuai'] }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-semibold text-blue-800 dark:text-blue-300 bg-blue-50/50 dark:bg-blue-950/20 tabular-nums">{{ $rekap['status_belum_sesuai'] }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-semibold text-amber-900 dark:text-amber-300 bg-amber-50/50 dark:bg-amber-950/20 tabular-nums">{{ $rekap['status_belum_tl'] }}</td>
                                    <td class="py-2.5 px-2.5 text-center font-semibold text-ink-secondary bg-surface-subtle/50 tabular-nums">{{ $rekap['status_tdt'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-emerald-800 dark:text-emerald-300 tabular-nums">{{ number_format($rekap['nilai_pengembalian_rp'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-red-800 dark:text-red-300 font-semibold tabular-nums">{{ number_format($rekap['sisa_pengembalian_rp'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="py-6 text-center text-ink-muted">Belum ada data pengawasan untuk periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BAGIAN 4: RINCIAN PENGAWASAN SESUAI KELOMPOK / KLUSTER PENGAWASAN -->
            <div class="bg-surface p-5 rounded-[10px] border border-hairline space-y-4">
                <div class="flex items-center gap-2.5 border-b border-hairline pb-3">
                    <span class="w-7 h-7 rounded-[6px] bg-amber-50 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-center justify-center font-bold text-xs shrink-0">4</span>
                    <div>
                        <h3 class="text-sm font-semibold text-ink">Rincian Pengawasan sesuai Kelompok / Kluster Pengawasan</h3>
                        <p class="text-xs text-ink-secondary">Perbandingan target rencana (PKPPT) dengan realisasi Surat Tugas (SPT perpanjangan & bantuan dihitung 1 dengan SPT induk).</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-[6px] border border-hairline">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Kelompok / Kluster Pengawasan</th>
                                <th class="py-2.5 px-4 w-44 text-center">Rencana Tahun {{ $tahun }}</th>
                                <th class="py-2.5 px-4 w-44 text-center">Realisasi Pengawasan Tahun {{ $tahun }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline">
                            @foreach($compiledData['tabelKlusterPengawasan'] as $kluster)
                                <tr class="hover:bg-surface-subtle/60">
                                    <td class="py-2.5 px-4 text-center font-semibold text-ink-muted tabular-nums">{{ $kluster['no'] }}</td>
                                    <td class="py-2.5 px-4 font-medium text-ink">{{ $kluster['kluster'] }}</td>
                                    <td class="py-2.5 px-4 text-center font-semibold text-ink-secondary tabular-nums">{{ $kluster['rencana'] }}</td>
                                    <td class="py-2.5 px-4 text-center font-bold text-brand-ink tabular-nums">{{ $kluster['realisasi'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-surface-subtle border-t border-hairline font-semibold">
                            <tr>
                                <td colspan="2" class="py-2.5 px-4 text-right text-ink-secondary">TOTAL SELURUH KLUSTER:</td>
                                <td class="py-2.5 px-4 text-center font-bold text-ink tabular-nums">{{ $compiledData['totalRencanaKluster'] }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-brand-ink tabular-nums">{{ $compiledData['totalRealisasiKluster'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Catatan Tambahan & Status Simpan -->
            <div class="bg-surface p-5 rounded-[10px] border border-hairline space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-8">
                        <label class="block text-xs font-semibold text-ink mb-1">Catatan Khusus Tambahan (Optional)</label>
                        <input type="text" name="catatan_khusus" placeholder="mis. Ikhtisar disusun untuk lampiran Laporan Berkala kepada Bupati..." class="w-full text-xs rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-xs font-semibold text-ink mb-1">Status Finalisasi Dokumen</label>
                        <select name="status" class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3.5 py-2.5">
                            <option value="draft">Draf Laporan (Dapat Disunting Kembali)</option>
                            <option value="final">Final Resmi (Siap Cetak & Didistribusikan)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-hairline">
                    <a href="{{ route('ikhtisar-laporan.index') }}" class="ledger-btn-secondary !text-xs">
                        Batal
                    </a>
                    <button type="submit" class="ledger-btn-primary !text-xs">
                        Simpan Dokumen Ikhtisar Laporan (ILHP)
                    </button>
                </div>
            </div>
        </form>

        <!-- MODAL PENGATURAN GEMINI API KEY -->
        <div x-show="showModalApiKey" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
            <div @click.away="showModalApiKey = false" class="bg-surface rounded-[10px] p-6 shadow-lg border border-hairline max-w-lg w-full space-y-4">
                <div class="flex items-center justify-between border-b border-hairline pb-3">
                    <h3 class="font-semibold text-ink text-sm">Pengaturan Google Gemini API Key</h3>
                    <button type="button" @click="showModalApiKey = false" class="text-ink-muted hover:text-ink text-xl font-bold leading-none px-1" aria-label="Tutup">&times;</button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <p class="text-ink-secondary leading-relaxed">
                        Fitur <strong>AI Summary Temuan & Rekomendasi</strong> menggunakan Google Gemini Flash yang <strong>100% Gratis</strong> tanpa biaya berlangganan.
                    </p>

                    <div>
                        <label class="block font-semibold text-ink mb-1">
                            Google Gemini API Key <span class="text-red-700 dark:text-red-400">*</span>
                        </label>
                        <input type="password" x-model="apiKeyInput" placeholder="Tempel API Key AI Studio di sini (AIzaSy...)" class="w-full text-xs font-mono rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink">
                    </div>

                    <div>
                        <label class="block font-semibold text-ink mb-1">Pilihan Model AI</label>
                        <select x-model="modelInput" class="w-full text-xs font-semibold rounded-[6px] border border-hairline-strong bg-surface p-2.5 text-ink">
                            <option value="gemini-1.5-flash">Gemini 1.5 Flash (Sangat Cepat & Gratis)</option>
                            <option value="gemini-2.0-flash">Gemini 2.0 Flash (Generasi Terbaru & Gratis)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-[6px] border border-blue-200 dark:border-blue-800 text-xs text-blue-900 dark:text-blue-200">
                        <span>Belum punya API Key? Dapatkan gratis dalam 1 menit di </span>
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="font-semibold underline text-blue-800 dark:text-blue-300 hover:text-blue-900">
                            Google AI Studio (aistudio.google.com)
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline">
                    <button type="button" @click="showModalApiKey = false" class="ledger-btn-secondary !text-xs !py-2 !px-4 !min-h-0">Batal</button>
                    <button type="button" @click="saveApiKey()" :disabled="isSavingKey" class="ledger-btn-primary !text-xs !py-2 !px-5 !min-h-0">
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
                                alert("Resume temuan berhasil disusun!\n(" + data.note + ")");
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
                            alert("Pengaturan Gemini API Key berhasil disimpan!");
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
