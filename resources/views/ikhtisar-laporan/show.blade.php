<x-app-layout>
    <x-slot name="header">
        Dokumen Ikhtisar Laporan Hasil Pengawasan (ILHP)
    </x-slot>

    <div class="space-y-6">
        <!-- Top Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ikhtisar-laporan.index', ['tahun' => $ikhtisarLaporan->tahun]) }}" class="text-xs text-brand-ink hover:underline font-semibold inline-flex items-center gap-1.5 mb-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Ikhtisar</span>
                </a>
                <h2 class="text-xl font-bold text-ink tracking-tight">{{ $ikhtisarLaporan->judul }}</h2>
                <p class="text-xs text-ink-secondary mt-0.5">
                    Periode: <span class="font-semibold text-ink">{{ $ikhtisarLaporan->periode_label }}</span> |
                    Tanggal Naskah: <span class="font-semibold text-ink tabular-nums">{{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : '-' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('ikhtisar-laporan.cetak', $ikhtisarLaporan) }}" target="_blank" class="ledger-btn-primary !text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak / Unduh PDF Naskah Eksekutif</span>
                </a>

                @hasanyrole('admin|administrator|pelaporan|sekretaris|inspektur|superadmin')
                <a href="{{ route('ikhtisar-laporan.edit', $ikhtisarLaporan) }}" class="ledger-btn-secondary !text-xs">
                    <span>Edit Dokumen & Resume</span>
                </a>
                @endhasanyrole
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- DOKUMEN SISTEMATIKA 4 BAGIAN UTAMA -->
        <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden divide-y divide-hairline">

            <!-- COVER / HEADER DOKUMEN -->
            <div class="p-6 sm:p-8 bg-surface-subtle/60 text-center space-y-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-ink">Inspektorat Daerah Kabupaten Trenggalek</p>
                <h1 class="text-2xl font-bold text-ink uppercase">{{ $ikhtisarLaporan->judul }}</h1>
                @if($ikhtisarLaporan->nomor_surat)
                    <p class="text-xs font-mono text-ink-secondary">Nomor: {{ $ikhtisarLaporan->nomor_surat }}</p>
                @endif
                <div class="flex items-center justify-center gap-2.5 pt-1 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-[4px] text-[11px] font-semibold uppercase border {{ $ikhtisarLaporan->status === 'final' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800' }}">
                        Status: {{ strtoupper($ikhtisarLaporan->status) }}
                    </span>
                    <span class="text-xs text-ink-secondary">Disampaikan kepada: <strong class="text-ink">Bupati Trenggalek</strong></span>
                </div>
            </div>

            <!-- BAGIAN 1: SUMBER DAYA MANUSIA (SDM) -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-[6px] bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800 flex items-center justify-center font-bold text-xs shrink-0">1</span>
                        <h3 class="text-base font-semibold text-ink">Sumber Daya Manusia</h3>
                    </div>
                    <span class="text-xs font-semibold text-blue-800 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-[4px] border border-blue-200 dark:border-blue-800 tabular-nums">
                        Total SDM Aktif: {{ $compiledData['totalPegawaiAktif'] }} Orang
                    </span>
                </div>

                <p class="text-xs text-ink-secondary leading-relaxed">
                    Jumlah Sumber Daya Manusia (SDM) yang ada di Inspektorat Kabupaten Trenggalek berdasarkan data pengguna internal aplikasi SINGAWASI yang aktif dan menduduki jabatan definitif:
                </p>

                <div class="overflow-x-auto rounded-[6px] border border-hairline">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Jabatan</th>
                                <th class="py-2.5 px-4 w-44 text-center">Jumlah (Orang)</th>
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
                                <td colspan="2" class="py-2.5 px-4 text-right text-ink-secondary">TOTAL SELURUH PEGAWAI:</td>
                                <td class="py-2.5 px-4 text-center font-bold text-brand-ink tabular-nums">{{ $compiledData['totalPegawaiAktif'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="text-[11px] text-ink-muted italic">
                    * Unsur Kesekretariatan dihitung berdasarkan rumus: Jumlah Seluruh Pegawai - (Inspektur + Sekretaris + Inspektur Pembantu + Fungsional Auditor + Fungsional PPUPD).
                </p>
            </div>

            <!-- BAGIAN 2: RESUME CATATAN/TEMUAN & SARAN/REKOMENDASI -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border flex items-center justify-center font-bold text-xs shrink-0">2</span>
                    <h3 class="text-base font-semibold text-ink">Resume Catatan/Temuan serta Saran/Rekomendasi Pengawasan Internal</h3>
                </div>

                <div class="p-5 rounded-[10px] bg-surface-subtle border border-hairline">
                    <div class="prose dark:prose-invert max-w-none text-xs text-ink leading-relaxed whitespace-pre-line font-normal">
                        {{ $ikhtisarLaporan->resume_ai ?: 'Belum ada narasi resume temuan & rekomendasi yang disusun.' }}
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: REKAPITULASI HASIL PENGAWASAN (MATRIKS TINDAK LANJUT) -->
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-[6px] bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">3</span>
                    <h3 class="text-base font-semibold text-ink">Rekapitulasi Hasil Pengawasan Inspektorat Kabupaten Trenggalek</h3>
                </div>

                <p class="text-xs text-ink-secondary leading-relaxed">
                    Rekapitulasi capaian pelaksanaan pengawasan dan pemantauan tindak lanjut rekomendasi hasil pengawasan (LHP):
                </p>

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
                                <th class="py-1.5 px-2 text-emerald-800 dark:text-emerald-300 border-r border-hairline">Sesuai</th>
                                <th class="py-1.5 px-2 text-blue-800 dark:text-blue-300 border-r border-hairline">Belum Sesuai</th>
                                <th class="py-1.5 px-2 text-amber-900 dark:text-amber-300 border-r border-hairline">Belum di TL</th>
                                <th class="py-1.5 px-2 text-ink-secondary border-r border-hairline">TDT</th>
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
                                    <td class="py-2.5 px-2 text-center font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50/50 dark:bg-emerald-950/20 tabular-nums">{{ $rekap['status_sesuai'] }}</td>
                                    <td class="py-2.5 px-2 text-center font-semibold text-blue-800 dark:text-blue-300 bg-blue-50/50 dark:bg-blue-950/20 tabular-nums">{{ $rekap['status_belum_sesuai'] }}</td>
                                    <td class="py-2.5 px-2 text-center font-semibold text-amber-900 dark:text-amber-300 bg-amber-50/50 dark:bg-amber-950/20 tabular-nums">{{ $rekap['status_belum_tl'] }}</td>
                                    <td class="py-2.5 px-2 text-center font-semibold text-ink-secondary bg-surface-subtle/50 tabular-nums">{{ $rekap['status_tdt'] }}</td>
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
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-[6px] bg-amber-50 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-center justify-center font-bold text-xs shrink-0">4</span>
                    <h3 class="text-base font-semibold text-ink">Rincian Pengawasan sesuai Kelompok / Kluster Pengawasan</h3>
                </div>

                <p class="text-xs text-ink-secondary leading-relaxed">
                    Perbandingan antara target rencana pengawasan (PKPPT) dengan realisasi Surat Tugas (SPT) pada Tahun Anggaran {{ $ikhtisarLaporan->tahun }}:
                </p>

                <div class="overflow-x-auto rounded-[6px] border border-hairline">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                            <tr>
                                <th class="py-2.5 px-4 w-16 text-center">No.</th>
                                <th class="py-2.5 px-4">Kelompok / Kluster Pengawasan</th>
                                <th class="py-2.5 px-4 w-48 text-center">Rencana Tahun {{ $ikhtisarLaporan->tahun }}</th>
                                <th class="py-2.5 px-4 w-48 text-center">Realisasi Pengawasan Tahun {{ $ikhtisarLaporan->tahun }}</th>
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

                <p class="text-[11px] text-ink-muted italic">
                    * Surat tugas perpanjangan dan surat tugas bantuan untuk objek pengawasan yang sama dihitung 1 kesatuan dengan surat tugas induknya.
                </p>
            </div>

            <!-- TANDA TANGAN & LEGALISASI -->
            <div class="p-6 sm:p-8 bg-surface-subtle/60 flex justify-end">
                <div class="w-72 text-center text-xs space-y-1">
                    <p class="text-ink-secondary">Trenggalek, {{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : date('d F Y') }}</p>
                    <p class="font-bold text-ink uppercase tracking-wider">
                        {{ $inspektur && str_contains(strtolower($inspektur->jabatan ?? ''), 'plt') ? 'Plt. INSPEKTUR' : 'INSPEKTUR' }}<br>
                        KABUPATEN TRENGGALEK
                    </p>
                    <div class="h-16"></div>
                    <p class="font-bold text-ink underline">{{ $inspektur?->nama ?? 'Drs. H. INSPEKTUR, M.Si.' }}</p>
                    <p class="text-ink-secondary font-medium">{{ $inspektur?->pangkat ?? 'Pembina Utama Muda' }}</p>
                    <p class="text-ink-muted font-mono text-[11px] tabular-nums">NIP. {{ $inspektur?->nip ?? '19700101 199001 1 001' }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
