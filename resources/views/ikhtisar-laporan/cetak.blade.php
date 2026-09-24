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
            body { background: white !important; color: black !important; font-size: 11pt; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            .page-break { page-break-before: always; }
            @page {
                size: A4 portrait;
                margin: 20mm 20mm 20mm 20mm;
            }
        }
        body {
            font-family: "Times New Roman", Times, serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #1e293b;
            padding: 6px 8px;
        }
        th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 min-h-screen py-8 print:py-0 print:bg-white">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('ikhtisar-laporan.show', $ikhtisarLaporan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali ke Tampilan Web
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF Resmi
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-12 sm:p-16 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none leading-relaxed text-justify text-xs sm:text-sm">
        
        <!-- Kop Surat Resmi Inspektorat Trenggalek -->
        @php
            $logoPath = public_path('images/logo-trenggalek.png');
            $logoSrc = file_exists($logoPath) 
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) 
                : asset('images/logo-trenggalek.png');
        @endphp
        <div class="relative border-b-4 border-double border-slate-900 pb-3 mb-6">
            <div class="absolute left-0 top-0 bottom-3 flex items-center">
                <img src="{{ $logoSrc }}" alt="Logo Kabupaten Trenggalek" class="h-16 sm:h-20 w-auto object-contain">
            </div>
            <div class="text-center px-16 sm:px-20">
                <h3 class="text-sm font-bold uppercase tracking-wider leading-tight text-slate-900">PEMERINTAH KABUPATEN TRENGGALEK</h3>
                <h2 class="text-lg font-black uppercase tracking-wide leading-tight text-slate-900 mt-0.5">INSPEKTORAT DAERAH</h2>
                <p class="text-[11px] text-slate-700 leading-tight mt-1">
                    Jalan Brigjen Soetran Nomor 9, Telepon (0355) 791407, Fax (0355) 791407<br>
                    Website: https://inspektorat.trenggalekkab.go.id &bull; Pos-el: inspektorat@trenggalekkab.go.id<br>
                    <strong>TRENGGALEK &mdash; 66311</strong>
                </p>
            </div>
        </div>

        <!-- Judul Laporan Resmi -->
        <div class="text-center my-6 space-y-1">
            <h1 class="text-base font-bold uppercase tracking-wide underline underline-offset-4 decoration-1">
                {{ $ikhtisarLaporan->judul }}
            </h1>
            @if($ikhtisarLaporan->nomor_surat)
                <p class="text-xs font-mono">NOMOR : {{ $ikhtisarLaporan->nomor_surat }}</p>
            @endif
            <p class="text-xs font-semibold mt-1">Disampaikan Kepada Yth. : BUPATI TRENGGALEK</p>
        </div>

        <!-- ISI LAPORAN SISTEMATIKA 5 BAB -->
        <div class="space-y-6 mt-8">

            <!-- BAB I: INFORMASI UMUM -->
            <div class="space-y-3">
                <h3 class="font-bold text-sm uppercase">BAB I INFORMASI UMUM</h3>
                
                <div class="space-y-2 pl-4">
                    <p class="font-bold">A. Dasar Hukum</p>
                    <ol class="list-decimal list-outside pl-5 space-y-1 text-xs">
                        @foreach($compiledData['dasarHukum'] as $rb)
                            <li>{{ $rb->format_dasar_spt ?? ($rb->nomor_regulasi . ' tentang ' . $rb->judul) }}</li>
                        @endforeach
                    </ol>

                    <p class="font-bold pt-2">B. Struktur Organisasi & Kapasitas SDM</p>
                    <p class="text-xs">
                        Pelaksanaan pengawasan internal di lingkungan Pemerintah Kabupaten Trenggalek didukung oleh {{ $compiledData['totalPersonilAktif'] }} personil aparatur pengawasan ({{ $compiledData['totalAuditor'] }} Pejabat Fungsional Auditor dan {{ $compiledData['totalPpupd'] }} PPUPD) yang terbagi ke dalam Sekretariat dan {{ count($compiledData['irbans']) }} unit Inspektur Pembantu (Irban).
                    </p>

                    <p class="font-bold pt-2">C. Tujuan Penyusunan Ikhtisar</p>
                    <p class="text-xs">
                        Ikhtisar Laporan Hasil Pengawasan (ILHP) ini disusun sebagai bahan pertanggungjawaban pelaksanaan pengawasan internal kepada Kepala Daerah (Bupati), serta memberikan rekomendasi kebijakan strategis guna penyempurnaan akuntabilitas kinerja, kepatuhan, dan pengelolaan keuangan daerah.
                    </p>

                    <p class="font-bold pt-2">D. Program Pengawasan dan Realisasinya</p>
                    <p class="text-xs">
                        Dari target Program Kerja Pengawasan Tahunan (PKPT) Berbasis Risiko Tahun {{ $ikhtisarLaporan->tahun }} sebanyak {{ $compiledData['totalTargetPkppt'] }} rencana kegiatan, hingga periode {{ $ikhtisarLaporan->periode_label }} telah terealisasi penerbitan sebanyak {{ $compiledData['totalSptTerbit'] }} Surat Perintah Tugas (SPT) dengan tingkat capaian sebesar {{ $compiledData['persenRealisasiPkppt'] }}%.
                    </p>
                </div>
            </div>

            <!-- BAB II: HASIL PENGAWASAN -->
            <div class="space-y-3 pt-4">
                <h3 class="font-bold text-sm uppercase">BAB II HASIL PENGAWASAN</h3>
                
                <div class="space-y-3 pl-4 text-xs">
                    <p>Realisasi kegiatan pengawasan yang dilaksanakan pada periode ini dikelompokkan menurut jenis pengawasan sebagai berikut:</p>

                    <!-- A. Audit -->
                    <p class="font-bold">A. Audit</p>
                    
                    <p class="font-semibold pl-2">1. Audit Kinerja ({{ count($compiledData['kategoriAudit']['audit_kinerja'] ?? []) }} Penugasan)</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">No. SPT</th>
                                <th style="width: 40%;">Uraian Penugasan</th>
                                <th style="width: 20%;">Objek / OPD</th>
                                <th style="width: 15%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compiledData['kategoriAudit']['audit_kinerja'] ?? [] as $it)
                                <tr>
                                    <td class="font-mono">{{ $it->no_spt }}</td>
                                    <td>{{ $it->uraian_penugasan }}</td>
                                    <td>{{ $it->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                    <td style="text-align: center;">{{ $it->status_label }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #64748b;">Nihil pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <p class="font-semibold pl-2 pt-2">2. Audit Dengan Tujuan Tertentu / ADTT ({{ count($compiledData['kategoriAudit']['audit_dtt'] ?? []) }} Penugasan)</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">No. SPT</th>
                                <th style="width: 40%;">Uraian Penugasan</th>
                                <th style="width: 20%;">Objek / OPD</th>
                                <th style="width: 15%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compiledData['kategoriAudit']['audit_dtt'] ?? [] as $it)
                                <tr>
                                    <td class="font-mono">{{ $it->no_spt }}</td>
                                    <td>{{ $it->uraian_penugasan }}</td>
                                    <td>{{ $it->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                    <td style="text-align: center;">{{ $it->status_label }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align: center; color: #64748b;">Nihil pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- B. Reviu -->
                    <p class="font-bold pt-2">B. Reviu ({{ count($compiledData['kategoriReviu']) }} Penugasan)</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">No. SPT</th>
                                <th style="width: 45%;">Uraian Reviu</th>
                                <th style="width: 30%;">Objek Sasaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compiledData['kategoriReviu'] as $it)
                                <tr>
                                    <td class="font-mono">{{ $it->no_spt }}</td>
                                    <td>{{ $it->uraian_penugasan }}</td>
                                    <td>{{ $it->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align: center; color: #64748b;">Nihil pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- C. Evaluasi -->
                    <p class="font-bold pt-2">C. Evaluasi ({{ count($compiledData['kategoriEvaluasi']) }} Penugasan)</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">No. SPT</th>
                                <th style="width: 45%;">Uraian Evaluasi</th>
                                <th style="width: 30%;">Objek Sasaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compiledData['kategoriEvaluasi'] as $it)
                                <tr>
                                    <td class="font-mono">{{ $it->no_spt }}</td>
                                    <td>{{ $it->uraian_penugasan }}</td>
                                    <td>{{ $it->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align: center; color: #64748b;">Nihil pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- D. Pemantauan & Lainnya -->
                    <p class="font-bold pt-2">D. Pemantauan & E. Kegiatan Pengawasan Lainnya ({{ count($compiledData['kategoriPemantauan']) + count($compiledData['kategoriLainnya']) }} Penugasan)</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">No. SPT</th>
                                <th style="width: 40%;">Uraian Penugasan</th>
                                <th style="width: 20%;">Kategori</th>
                                <th style="width: 15%;">Objek Sasaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_merge($compiledData['kategoriPemantauan'], $compiledData['kategoriLainnya']) as $it)
                                <tr>
                                    <td class="font-mono">{{ $it->no_spt }}</td>
                                    <td>{{ $it->uraian_penugasan }}</td>
                                    <td>{{ $it->jenisPenugasan?->nama ?? '-' }}</td>
                                    <td>{{ $it->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BAB III: HASIL PEMANTAUAN TINDAK LANJUT -->
            <div class="space-y-3 pt-4">
                <h3 class="font-bold text-sm uppercase">BAB III HASIL PEMANTAUAN TINDAK LANJUT</h3>
                
                <div class="space-y-3 pl-4 text-xs">
                    <p>
                        Dari total {{ $compiledData['tlCountTotal'] }} rekomendasi hasil pengawasan yang dipantau, sebanyak {{ $compiledData['tlCountSelesai'] }} rekomendasi ({{ $compiledData['tlPersenSelesai'] }}%) telah <strong>Sesuai Rekomendasi (SS)</strong>, {{ $compiledData['tlCountBelumSesuai'] }} rekomendasi masih <strong>Dalam Proses (BS)</strong>, dan {{ $compiledData['tlCountBelum'] }} rekomendasi <strong>Belum Ditindaklanjuti (BTL)</strong>. Realisasi penyetoran ke Kas Daerah tercatat sebesar <strong>Rp {{ number_format($compiledData['tlTotalSetorRp'], 0, ',', '.') }}</strong> dari target pengembalian Rp {{ number_format($compiledData['tlTotalTargetRp'], 0, ',', '.') }}.
                    </p>

                    <p class="font-bold pt-1">Tabel Matriks Kepatuhan Tindak Lanjut per Perangkat Daerah (OPD):</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 35%;">Perangkat Daerah / OPD</th>
                                <th style="width: 10%;">Total</th>
                                <th style="width: 10%;">SS</th>
                                <th style="width: 10%;">BS</th>
                                <th style="width: 10%;">BTL</th>
                                <th style="width: 10%;">% SS</th>
                                <th style="width: 10%;">Setor Kasda (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compiledData['matrixOpd'] as $idx => $opd)
                                <tr>
                                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                                    <td>{{ $opd->nama_opd }}</td>
                                    <td style="text-align: center;">{{ $opd->total }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $opd->ss }}</td>
                                    <td style="text-align: center;">{{ $opd->bs }}</td>
                                    <td style="text-align: center;">{{ $opd->btl }}</td>
                                    <td style="text-align: center; font-weight: bold;">{{ $opd->persen }}%</td>
                                    <td style="text-align: right; font-family: monospace;">{{ number_format($opd->setor_rp, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" style="text-align: center;">Nihil pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BAB IV: HASIL PENANGANAN PENGADUAN MASYARAKAT -->
            <div class="space-y-3 pt-4">
                <h3 class="font-bold text-sm uppercase">BAB IV HASIL PENANGANAN PENGADUAN MASYARAKAT</h3>
                
                <div class="space-y-2 pl-4 text-xs">
                    <p>
                        Pada periode ini, Inspektorat Daerah telah menindaklanjuti sebanyak <strong>{{ count($compiledData['sptDumas']) }} aduan masyarakat / permintaan klarifikasi penegak hukum</strong>:
                    </p>
                    @if(count($compiledData['sptDumas']) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 25%;">No. SPT</th>
                                    <th style="width: 45%;">Materi Aduan / Penugasan</th>
                                    <th style="width: 30%;">Objek Terlapor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($compiledData['sptDumas'] as $spt)
                                    <tr>
                                        <td class="font-mono">{{ $spt->no_spt }}</td>
                                        <td>{{ $spt->uraian_penugasan }}</td>
                                        <td>{{ $spt->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="italic text-slate-500">Tidak ada pengaduan masyarakat yang terdaftar pada periode ini.</p>
                    @endif
                </div>
            </div>

            <!-- BAB V: SIMPULAN, HAMBATAN DAN REKOMENDASI -->
            <div class="space-y-3 pt-4">
                <h3 class="font-bold text-sm uppercase">BAB V SIMPULAN, HAMBATAN DAN REKOMENDASI</h3>
                
                <div class="space-y-3 pl-4 text-xs">
                    <div>
                        <p class="font-bold">A. Simpulan</p>
                        <p class="whitespace-pre-line mt-0.5">{{ $ikhtisarLaporan->simpulan ?: 'Secara umum pengawasan dan pembinaan telah berjalan sesuai target yang ditetapkan.' }}</p>
                    </div>

                    <div class="pt-1">
                        <p class="font-bold">B. Hambatan</p>
                        <p class="whitespace-pre-line mt-0.5">{{ $ikhtisarLaporan->hambatan ?: 'Tidak ada hambatan signifikan yang dihadapi.' }}</p>
                    </div>

                    <div class="pt-1">
                        <p class="font-bold">C. Rekomendasi kepada Bupati Trenggalek</p>
                        <p class="whitespace-pre-line mt-0.5">{{ $ikhtisarLaporan->rekomendasi ?: 'Diharapkan Kepala Perangkat Daerah terus meningkatkan kepatuhan tindak lanjut hasil pengawasan.' }}</p>
                    </div>
                </div>
            </div>

            <!-- TANDA TANGAN INSPEKTUR -->
            <div class="pt-12 flex justify-end">
                <div class="w-64 text-center space-y-16">
                    <div>
                        <p>Trenggalek, {{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : date('d F Y') }}</p>
                        <p class="font-bold mt-0.5">INSPEKTUR KABUPATEN TRENGGALEK</p>
                    </div>

                    <div>
                        <p class="font-bold text-sm underline">{{ $inspektur?->nama ?? 'Drs. PIMPINAN INSPEKTORAT, M.Si' }}</p>
                        <p class="text-xs">NIP. {{ $inspektur?->nip ?? '19700101 199503 1 001' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
