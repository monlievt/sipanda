<x-app-layout>
    <x-slot name="header">
        Dokumen Ikhtisar Laporan Hasil Pengawasan (ILHP)
    </x-slot>

    <div class="space-y-6">
        <!-- Top Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ikhtisar-laporan.index', ['tahun' => $ikhtisarLaporan->tahun]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Ikhtisar
                </a>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ $ikhtisarLaporan->judul }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Periode: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ikhtisarLaporan->periode_label }}</span> | 
                    Tanggal Naskah: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : '-' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('ikhtisar-laporan.cetak', $ikhtisarLaporan) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-md transition-all">
                    <span>🖨️ Cetak / Unduh PDF Naskah Eksekutif</span>
                </a>

                @hasanyrole('admin|administrator|sekretariat|superadmin')
                <a href="{{ route('ikhtisar-laporan.edit', $ikhtisarLaporan) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl transition-all">
                    <span>✏️ Edit Dokumen & Resume</span>
                </a>
                @endhasanyrole
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- DOKUMEN SISTEMATIKA 4 BAGIAN UTAMA -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
            
            <!-- COVER / HEADER DOKUMEN -->
            <div class="p-6 sm:p-8 bg-slate-50/50 dark:bg-slate-800/30 text-center space-y-2 border-b border-slate-200 dark:border-slate-800">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Inspektorat Daerah Kabupaten Trenggalek</p>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase">{{ $ikhtisarLaporan->judul }}</h1>
                @if($ikhtisarLaporan->nomor_surat)
                    <p class="text-xs font-mono text-slate-500">Nomor: {{ $ikhtisarLaporan->nomor_surat }}</p>
                @endif
                <div class="flex items-center justify-center gap-2 pt-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $ikhtisarLaporan->status === 'final' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                        STATUS: {{ strtoupper($ikhtisarLaporan->status) }}
                    </span>
                    <span class="text-xs text-slate-400">Disampaikan kepada: <strong>Bupati Trenggalek</strong></span>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 1: SUMBER DAYA MANUSIA (SDM) -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-xs shrink-0">1</span>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Sumber Daya Manusia</h3>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-full border border-blue-200 dark:border-blue-800">
                        Total SDM Aktif: {{ $compiledData['totalPegawaiAktif'] }} Orang
                    </span>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Jumlah Sumber Daya Manusia (SDM) yang ada di Inspektorat Kabupaten Trenggalek berdasarkan data pengguna internal aplikasi SIPANDA yang aktif dan menduduki jabatan definitif:
                </p>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Jabatan</th>
                                <th class="py-2.5 px-4 w-44 text-center">Jumlah (Orang)</th>
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
                                <td colspan="2" class="py-2.5 px-4 text-right text-slate-700 dark:text-slate-300">TOTAL SELURUH PEGAWAI:</td>
                                <td class="py-2.5 px-4 text-center font-black text-emerald-600 dark:text-emerald-400">{{ $compiledData['totalPegawaiAktif'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="text-[10.5px] text-slate-400 italic">
                    * Unsur Kesekretariatan dihitung berdasarkan rumus: Jumlah Seluruh Pegawai – (Inspektur + Sekretaris + Inspektur Pembantu + Fungsional Auditor + Fungsional PPUPD).
                </p>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 2: RESUME CATATAN/TEMUAN & SARAN/REKOMENDASI -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-purple-600 text-white flex items-center justify-center font-black text-xs shrink-0">2</span>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Resume Catatan/Temuan serta Saran/Rekomendasi Pengawasan Internal</h3>
                </div>

                <div class="p-5 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200/80 dark:border-purple-800/50">
                    <div class="prose dark:prose-invert max-w-none text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line font-medium">
                        {{ $ikhtisarLaporan->resume_ai ?: 'Belum ada narasi resume temuan & rekomendasi yang disusun.' }}
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 3: REKAPITULASI HASIL PENGAWASAN (MATRIKS TINDAK LANJUT) -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs shrink-0">3</span>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Rekapitulasi Hasil Pengawasan Inspektorat Kabupaten Trenggalek</h3>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Rekapitulasi capaian pelaksanaan pengawasan dan pemantauan tindak lanjut rekomendasi hasil pengawasan (LHP):
                </p>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
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
                                <th class="py-1.5 px-2 text-emerald-600 border-r border-slate-200 dark:border-slate-700">Sesuai</th>
                                <th class="py-1.5 px-2 text-blue-600 border-r border-slate-200 dark:border-slate-700">Belum Sesuai</th>
                                <th class="py-1.5 px-2 text-amber-600 border-r border-slate-200 dark:border-slate-700">Belum di TL</th>
                                <th class="py-1.5 px-2 text-slate-500 border-r border-slate-200 dark:border-slate-700">TDT</th>
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
                                    <td class="py-2.5 px-2 text-center font-bold text-emerald-600 bg-emerald-50/40 dark:bg-emerald-950/20">{{ $rekap['status_sesuai'] }}</td>
                                    <td class="py-2.5 px-2 text-center font-bold text-blue-600 bg-blue-50/40 dark:bg-blue-950/20">{{ $rekap['status_belum_sesuai'] }}</td>
                                    <td class="py-2.5 px-2 text-center font-bold text-amber-600 bg-amber-50/40 dark:bg-amber-950/20">{{ $rekap['status_belum_tl'] }}</td>
                                    <td class="py-2.5 px-2 text-center font-bold text-slate-500 bg-slate-50 dark:bg-slate-800/20">{{ $rekap['status_tdt'] }}</td>
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

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 4: RINCIAN PENGAWASAN SESUAI KELOMPOK / KLUSTER PENGAWASAN -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-amber-600 text-white flex items-center justify-center font-black text-xs shrink-0">4</span>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Rincian Pengawasan sesuai Kelompok / Kluster Pengawasan</h3>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Perbandingan antara target rencana pengawasan (PKPPT) dengan realisasi Surat Tugas (SPT) pada Tahun Anggaran {{ $ikhtisarLaporan->tahun }}:
                </p>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Kelompok / Kluster Pengawasan</th>
                                <th class="py-2.5 px-4 w-48 text-center">Rencana Tahun {{ $ikhtisarLaporan->tahun }}</th>
                                <th class="py-2.5 px-4 w-48 text-center">Realisasi Pengawasan Tahun {{ $ikhtisarLaporan->tahun }}</th>
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

                <p class="text-[10.5px] text-slate-400 italic">
                    * Surat tugas perpanjangan dan surat tugas bantuan untuk objek pengawasan yang sama dihitung 1 kesatuan dengan surat tugas induknya.
                </p>
            </div>

            <!-- TANDA TANGAN & LEGALISASI -->
            <div class="p-6 sm:p-8 bg-slate-50/50 dark:bg-slate-800/30 flex justify-end">
                <div class="w-72 text-center text-xs space-y-1">
                    <p class="text-slate-600 dark:text-slate-400">Trenggalek, {{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : date('d F Y') }}</p>
                    <p class="font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">
                        {{ $inspektur && str_contains(strtolower($inspektur->jabatan ?? ''), 'plt') ? 'Plt. INSPEKTUR' : 'INSPEKTUR' }}<br>
                        KABUPATEN TRENGGALEK
                    </p>
                    <div class="h-16"></div>
                    <p class="font-black text-slate-900 dark:text-white underline">{{ $inspektur?->nama ?? 'Drs. H. INSPEKTUR, M.Si.' }}</p>
                    <p class="text-slate-500 font-semibold">{{ $inspektur?->pangkat ?? 'Pembina Utama Muda' }}</p>
                    <p class="text-slate-500 font-mono text-[11px]">NIP. {{ $inspektur?->nip ?? '19700101 199001 1 001' }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
