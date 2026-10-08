<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ikhtisarLaporan->judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 11pt; margin: 0 !important; padding: 0 !important; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            .closing-signature-group, .signature-block, .avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            @page {
                size: A4 portrait;
                margin: 20mm 20mm 20mm 20mm;
            }
        }
        body {
            font-family: "Times New Roman", Times, serif;
        }
        table.formal-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 6px;
            margin-bottom: 6px;
        }
        table.formal-table th, table.formal-table td {
            border: 1px solid #1e293b;
            padding: 5px 7px;
        }
        table.formal-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 12px;
        }
        .kop-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .kop-border {
            border-bottom: 3px double #0f172a;
            margin-bottom: 16px;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 min-h-screen py-8 print:py-0 print:bg-white">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali ke Tampilan Dokumen
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Unduh PDF Resmi
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-10 sm:p-14 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none leading-relaxed text-justify text-xs sm:text-sm">
        
        <!-- Kop Surat Resmi Inspektorat Trenggalek -->
        @php
            $logoPath = public_path('images/logo-trenggalek.png');
            $logoSrc = file_exists($logoPath) 
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) 
                : asset('images/logo-trenggalek.png');
        @endphp
        <table class="kop-table">
            <tr>
                <td style="width: 85px; text-align: center; padding-right: 12px;">
                    <img src="{{ $logoSrc }}" alt="Logo Kabupaten Trenggalek" style="width: 80px; max-height: 85px; object-fit: contain; display: inline-block;">
                </td>
                <td style="text-align: center;">
                    <h3 style="font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; line-height: 1.2;">PEMERINTAH KABUPATEN TRENGGALEK</h3>
                    <h2 style="font-size: 15pt; font-weight: bold; margin: 2px 0 0 0; text-transform: uppercase; line-height: 1.2;">INSPEKTORAT DAERAH</h2>
                    <p style="font-size: 9pt; margin: 3px 0 0 0; line-height: 1.3;">
                        Jalan Brigjen Soetran Nomor 9, Telepon (0355) 791407, Fax (0355) 791407<br>
                        Website: https://inspektorat.trenggalekkab.go.id &bull; Pos-el: inspektorat@trenggalekkab.go.id<br>
                        <strong>TRENGGALEK &mdash; 66311</strong>
                    </p>
                </td>
            </tr>
        </table>
        <div class="kop-border"></div>

        <!-- Judul Laporan Resmi -->
        <div class="text-center my-4 space-y-0.5">
            <h1 class="text-sm sm:text-base font-bold uppercase tracking-wide underline underline-offset-4">
                {{ $ikhtisarLaporan->judul }}
            </h1>
            @if($ikhtisarLaporan->nomor_surat)
                <p class="text-xs font-mono font-bold">NOMOR : {{ $ikhtisarLaporan->nomor_surat }}</p>
            @endif
            <p class="text-xs font-semibold mt-1">Disampaikan Kepada Yth. : BUPATI TRENGGALEK</p>
        </div>

        <!-- ISI LAPORAN 4 BAGIAN UTAMA -->
        <div class="space-y-6 mt-6">

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 1: SUMBER DAYA MANUSIA (SDM) -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="space-y-2">
                <h3 class="font-bold text-xs sm:text-sm uppercase tracking-wide">1. Sumber Daya Manusia</h3>
                <p class="text-xs leading-relaxed">
                    Jumlah Sumber Daya Manusia (SDM) yang ada di Inspektorat Kabupaten Trenggalek berdasarkan data pengguna internal aplikasi SIPANDA yang aktif dan menduduki jabatan definitif adalah sebagai berikut:
                </p>

                <table class="formal-table text-xs">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No.</th>
                            <th>Jabatan</th>
                            <th style="width: 140px;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($compiledData['tabelSdm'] as $sdm)
                            <tr>
                                <td class="text-center">{{ $sdm['no'] }}</td>
                                <td>{{ $sdm['jabatan'] }}</td>
                                <td class="text-center font-bold">{{ $sdm['jumlah'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8fafc; font-weight: bold;">
                            <td colspan="2" class="text-right">TOTAL :</td>
                            <td class="text-center">{{ $compiledData['totalPegawaiAktif'] }}</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="text-[10px] text-slate-500 italic mt-1">
                    *) Unsur Kesekretariatan dihitung berdasarkan rumus: Jumlah Seluruh Pegawai – (Inspektur + Sekretaris + Inspektur Pembantu + Fungsional Auditor + Fungsional PPUPD).
                </p>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 2: RESUME CATATAN/TEMUAN & SARAN/REKOMENDASI -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="space-y-2">
                <h3 class="font-bold text-xs sm:text-sm uppercase tracking-wide">2. Resume Catatan/Temuan serta Saran/Rekomendasi Pengawasan Internal</h3>
                <div class="text-xs leading-relaxed whitespace-pre-line text-justify pl-1">
                    {{ $ikhtisarLaporan->resume_ai ?: 'Berdasarkan pelaksanaan pengawasan internal yang telah dilaksanakan pada periode berjalan, tim pengawas merumuskan catatan temuan serta saran/rekomendasi perbaikan tata kelola sebagaimana terlampir dalam matriks LHP.' }}
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 3: REKAPITULASI HASIL PENGAWASAN (MATRIKS TINDAK LANJUT) -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="space-y-2">
                <h3 class="font-bold text-xs sm:text-sm uppercase tracking-wide">3. Rekapitulasi Hasil Pengawasan Inspektorat Kabupaten Trenggalek</h3>
                <p class="text-xs leading-relaxed">
                    Data Rekapitulasi Hasil Pengawasan dan Pemantauan Tindak Lanjut Rekomendasi Hasil Pengawasan (LHP) disajikan dalam tabel sebagai berikut:
                </p>

                <table class="formal-table text-[10px]">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 25px;">No</th>
                            <th rowspan="2" style="width: 45px;">Tahun</th>
                            <th rowspan="2" style="width: 45px;">Total LHP</th>
                            <th rowspan="2">Nilai yang Dilakukan Pengawasan (Rp)</th>
                            <th rowspan="2" style="width: 55px;">Total Saran / Rekomendasi</th>
                            <th rowspan="2">Nilai Rekomendasi / Saran (Rp) *Jika Ada</th>
                            <th colspan="4">Jumlah Status Tindak Lanjut</th>
                            <th rowspan="2">Nilai Pengembalian (Rp) *Jika Ada</th>
                            <th rowspan="2">Sisa Pengembalian (Rp)</th>
                        </tr>
                        <tr>
                            <th style="width: 38px;">Sesuai</th>
                            <th style="width: 45px;">Belum Sesuai</th>
                            <th style="width: 45px;">Belum di TL</th>
                            <th style="width: 38px;">TDT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compiledData['rekapHasilPengawasanPerTahun'] as $rekap)
                            <tr>
                                <td class="text-center">{{ $rekap['no'] }}</td>
                                <td class="text-center font-bold">{{ $rekap['tahun'] }}</td>
                                <td class="text-center font-bold">{{ $rekap['total_lhp'] }}</td>
                                <td class="text-right">{{ number_format($rekap['nilai_diawasi_rp'], 0, ',', '.') }}</td>
                                <td class="text-center font-bold">{{ $rekap['total_rekomendasi'] }}</td>
                                <td class="text-right">{{ number_format($rekap['nilai_rekomendasi_rp'], 0, ',', '.') }}</td>
                                <td class="text-center">{{ $rekap['status_sesuai'] }}</td>
                                <td class="text-center">{{ $rekap['status_belum_sesuai'] }}</td>
                                <td class="text-center">{{ $rekap['status_belum_tl'] }}</td>
                                <td class="text-center">{{ $rekap['status_tdt'] }}</td>
                                <td class="text-right">{{ number_format($rekap['nilai_pengembalian_rp'], 0, ',', '.') }}</td>
                                <td class="text-right font-bold">{{ number_format($rekap['sisa_pengembalian_rp'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-2">Belum ada data pengawasan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- BAGIAN 4: RINCIAN PENGAWASAN SESUAI KELOMPOK / KLUSTER PENGAWASAN -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="space-y-2">
                <h3 class="font-bold text-xs sm:text-sm uppercase tracking-wide">4. Rincian Pengawasan sesuai Kelompok / Kluster Pengawasan</h3>
                <p class="text-xs leading-relaxed">
                    Realisasi pelaksanaan pengawasan berdasarkan Kelompok / Kluster Pengawasan dibandingkan dengan target rencana PKPPT Tahun Anggaran {{ $ikhtisarLaporan->tahun }}:
                </p>

                <table class="formal-table text-xs">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No.</th>
                            <th>Kelompok / Kluster Pengawasan</th>
                            <th style="width: 140px;">Rencana Tahun {{ $ikhtisarLaporan->tahun }}</th>
                            <th style="width: 160px;">Realisasi Pengawasan Tahun {{ $ikhtisarLaporan->tahun }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($compiledData['tabelKlusterPengawasan'] as $kluster)
                            <tr>
                                <td class="text-center">{{ $kluster['no'] }}</td>
                                <td>{{ $kluster['kluster'] }}</td>
                                <td class="text-center">{{ $kluster['rencana'] }}</td>
                                <td class="text-center font-bold">{{ $kluster['realisasi'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8fafc; font-weight: bold;">
                            <td colspan="2" class="text-right">TOTAL :</td>
                            <td class="text-center">{{ $compiledData['totalRencanaKluster'] }}</td>
                            <td class="text-center">{{ $compiledData['totalRealisasiKluster'] }}</td>
                        </tr>
                    </tfoot>
                </table>

                <p class="text-[10px] text-slate-500 italic mt-1">
                    *) Surat tugas perpanjangan dan surat tugas bantuan untuk objek pengawasan yang sama dihitung 1 kesatuan dengan surat tugas induknya.
                </p>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- PENUTUP & TANDA TANGAN LEGALISASI RESMI -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="closing-signature-group pt-4 space-y-4">
                <p class="text-xs leading-relaxed">
                    Demikian Ikhtisar Laporan Hasil Pengawasan ini disusun untuk dapat dipergunakan sebagai bahan pertimbangan pimpinan dalam pengambilan kebijakan dan evaluasi kinerja pengawasan di lingkungan Pemerintah Kabupaten Trenggalek.
                </p>

                <div class="signature-block flex justify-end pt-2">
                    <div class="w-72 text-center text-xs space-y-1">
                        <p>Trenggalek, {{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : date('d F Y') }}</p>
                        <p class="font-bold uppercase tracking-wider">
                            {{ $inspektur && str_contains(strtolower($inspektur->jabatan ?? ''), 'plt') ? 'Plt. INSPEKTUR' : 'INSPEKTUR' }}<br>
                            KABUPATEN TRENGGALEK
                        </p>
                        <div style="height: 60px;"></div>
                        <p class="font-bold underline uppercase">{{ $inspektur?->nama ?? 'Ir. WIJIONO, S.T., M.MKes.' }}</p>
                        <p>{{ $inspektur?->pangkat ?? 'Pembina Utama Muda' }}</p>
                        <p class="font-mono text-[11px]">NIP. {{ $inspektur?->nip ?? '197308051997031007' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
