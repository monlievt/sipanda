<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Acara Rekonsiliasi Tindak Lanjut — {{ $tindakLanjut->no_lhp ?? ($opd->nama ?? 'OPD') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 10.5pt; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            @page {
                size: A4 portrait;
                margin: 15mm 20mm 15mm 20mm;
            }
        }
        body {
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 min-h-screen py-8 print:py-0 print:bg-white text-xs leading-relaxed">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF Berita Acara
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-12 sm:p-14 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none">
        
        <!-- Kop Surat Resmi Inspektorat Trenggalek -->
        <div class="border-b-4 border-double border-slate-900 pb-3 mb-5 text-center relative">
            <h3 class="text-sm font-bold uppercase tracking-wider leading-tight">PEMERINTAH KABUPATEN TRENGGALEK</h3>
            <h2 class="text-lg font-extrabold uppercase tracking-wide leading-tight">INSPEKTORAT DAERAH</h2>
            <p class="text-[10px] text-slate-700 leading-tight mt-0.5">
                Jalan Brigjen Soetran Nomor 9, Telepon (0355) 791407, Fax (0355) 791407<br>
                Website: https://inspektorat.trenggalekkab.go.id &bull; Pos-el: inspektorat@trenggalekkab.go.id<br>
                <strong>TRENGGALEK &mdash; 66311</strong>
            </p>
        </div>

        <!-- Judul Berita Acara -->
        <div class="text-center my-4 space-y-1">
            <h1 class="text-sm font-bold uppercase tracking-wide underline underline-offset-4 decoration-1">
                BERITA ACARA HASIL REKONSILIASI TINDAK LANJUT
            </h1>
            <p class="text-[11px] font-bold font-mono">
                NOMOR : BA-TL/{{ date('Y') }}/{{ str_pad($tindakLanjut->id ?? 1, 4, '0', STR_PAD_LEFT) }}/406.008
            </p>
        </div>

        <!-- Paragraf Pembuka -->
        <p class="text-justify indent-8 mb-3">
            Pada hari ini <strong>{{ \Carbon\Carbon::now()->translatedFormat('l') }}</strong>, 
            tanggal <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>, 
            bertempat di Kantor Inspektorat Daerah Kabupaten Trenggalek, telah dilaksanakan rekonsiliasi dan pemutakhiran data tindak lanjut rekomendasi hasil pengawasan antara Tim Evaluator Inspektorat Daerah Kabupaten Trenggalek dengan Pejabat / Perwakilan dari Perangkat Daerah terkait:
        </p>

        <!-- Identitas Para Pihak -->
        <table class="w-full mb-4 ml-4 text-[11px]">
            <tr>
                <td class="w-28 font-bold align-top">Perangkat Daerah</td>
                <td class="w-4 align-top">:</td>
                <td class="font-bold text-slate-900">{{ $opd->nama ?? 'Perangkat Daerah Terkait' }}</td>
            </tr>
            @if($tindakLanjut->no_lhp)
            <tr>
                <td class="font-bold align-top">Nomor LHP</td>
                <td class="align-top">:</td>
                <td class="font-mono font-bold">{{ $tindakLanjut->no_lhp }} (Tanggal: {{ $tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->format('d/m/Y') : '-' }})</td>
            </tr>
            <tr>
                <td class="font-bold align-top">Judul LHP</td>
                <td class="align-top">:</td>
                <td>{{ $tindakLanjut->judul_lhp ?? '-' }}</td>
            </tr>
            @endif
            <tr>
                <td class="font-bold align-top">Irban Pengampu</td>
                <td class="align-top">:</td>
                <td>{{ $irban->nama_irban ?? 'Inspektur Pembantu Wilayah Terkait' }}</td>
            </tr>
        </table>

        <!-- Ringkasan Rekapitulasi Status Standar BPKP -->
        <p class="font-bold mb-1.5">I. REKAPITULASI STATUS PENYELESAIAN REKOMENDASI (STANDAR BPKP/KEMENDAGRI)</p>
        <table class="w-full border-collapse border border-slate-900 text-center text-[10.5px] mb-4">
            <thead class="bg-slate-100 font-bold uppercase">
                <tr>
                    <th class="border border-slate-900 p-1.5">Total Rekomendasi</th>
                    <th class="border border-slate-900 p-1.5">Sesuai (SS)</th>
                    <th class="border border-slate-900 p-1.5">Belum Sesuai (BS)</th>
                    <th class="border border-slate-900 p-1.5">Belum TL (BTL)</th>
                    <th class="border border-slate-900 p-1.5">TDT</th>
                    <th class="border border-slate-900 p-1.5">% Penyelesaian</th>
                </tr>
            </thead>
            <tbody>
                <tr class="font-bold">
                    <td class="border border-slate-900 p-2 font-mono text-sm">{{ $countTotal }}</td>
                    <td class="border border-slate-900 p-2 font-mono text-sm text-emerald-700">{{ $countSesuai }}</td>
                    <td class="border border-slate-900 p-2 font-mono text-sm text-blue-700">{{ $countBelumSesuai }}</td>
                    <td class="border border-slate-900 p-2 font-mono text-sm text-amber-700">{{ $countBelumTl }}</td>
                    <td class="border border-slate-900 p-2 font-mono text-sm text-slate-500">{{ $countTdt }}</td>
                    <td class="border border-slate-900 p-2 font-mono text-sm text-slate-900">
                        {{ $countTotal > 0 ? round(($countSesuai / $countTotal) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Ringkasan Finansial Kerugian Daerah & Setoran Kasda -->
        @if($totalTargetRp > 0 || $totalSetorRp > 0)
        <p class="font-bold mb-1.5">II. REKAPITULASI PENYELAMATAN KEUANGAN DAERAH / SETORAN KASDA</p>
        <table class="w-full border-collapse border border-slate-900 text-[10.5px] mb-4">
            <thead class="bg-slate-100 font-bold text-center uppercase">
                <tr>
                    <th class="border border-slate-900 p-1.5">Total Nilai Rekomendasi (Rp)</th>
                    <th class="border border-slate-900 p-1.5">Telah Disetor ke Kas Daerah (Rp)</th>
                    <th class="border border-slate-900 p-1.5">Sisa Kewajiban Setor (Rp)</th>
                    <th class="border border-slate-900 p-1.5">Persentase Pemulihan (%)</th>
                </tr>
            </thead>
            <tbody class="text-center font-mono font-bold">
                <tr>
                    <td class="border border-slate-900 p-2">Rp {{ number_format($totalTargetRp, 0, ',', '.') }}</td>
                    <td class="border border-slate-900 p-2 text-emerald-700">Rp {{ number_format($totalSetorRp, 0, ',', '.') }}</td>
                    <td class="border border-slate-900 p-2 text-rose-700">Rp {{ number_format($sisaKurangSetorRp, 0, ',', '.') }}</td>
                    <td class="border border-slate-900 p-2">
                        {{ $totalTargetRp > 0 ? round(($totalSetorRp / $totalTargetRp) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- Rincian Matriks Rekomendasi & Catatan Rekonsiliasi -->
        <p class="font-bold mb-1.5">III. RINCIAN MATRIKS STATUS PER ITEM REKOMENDASI</p>
        <table class="w-full border-collapse border border-slate-900 text-left text-[10px] mb-5">
            <thead class="bg-slate-100 font-bold uppercase text-center">
                <tr>
                    <th class="border border-slate-900 p-1.5 w-8">No</th>
                    <th class="border border-slate-900 p-1.5 w-44">Uraian Temuan</th>
                    <th class="border border-slate-900 p-1.5">Rekomendasi Wajib & Nilai (Rp)</th>
                    <th class="border border-slate-900 p-1.5 w-24">Status TL</th>
                    <th class="border border-slate-900 p-1.5 w-32">Catatan Kesepakatan Rekonsiliasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $it)
                <tr class="align-top">
                    <td class="border border-slate-900 p-1.5 text-center font-mono font-bold">{{ $idx + 1 }}</td>
                    <td class="border border-slate-900 p-1.5">{{ $it->temuan_uraian ?: '-' }}</td>
                    <td class="border border-slate-900 p-1.5">
                        <p class="font-semibold">{{ $it->rekomendasi_uraian ?: '-' }}</p>
                        @if($it->nilai_rekomendasi_rp > 0)
                            <p class="font-mono text-emerald-800 font-bold text-[9.5px] mt-0.5">
                                Target Setor: Rp {{ number_format($it->nilai_rekomendasi_rp, 0, ',', '.') }}
                            </p>
                        @endif
                    </td>
                    <td class="border border-slate-900 p-1.5 text-center font-bold font-mono uppercase text-[9.5px]">
                        @if($it->status_tindak_lanjut === 'selesai')
                            [SESUAI]
                        @elseif($it->status_tindak_lanjut === 'dalam_proses')
                            [BELUM SESUAI]
                        @elseif($it->status_tindak_lanjut === 'tdt')
                            [TDT]
                        @else
                            [BELUM TL]
                        @endif
                    </td>
                    <td class="border border-slate-900 p-1.5 text-[9.5px] leading-snug">
                        @php
                            $latestBukti = $it->buktiTindakLanjut->last();
                        @endphp
                        {{ $latestBukti?->catatan_verifikasi ?: 'Telah direkonsiliasi bersama dan disepakati tindak lanjut pemenuhannya.' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border border-slate-900 p-4 text-center text-slate-500 italic">Tidak ada item rekomendasi yang tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Paragraf Penutup -->
        <p class="text-justify indent-8 mb-8">
            Demikian Berita Acara Rekonsiliasi Tindak Lanjut Hasil Pengawasan ini dibuat dan ditandatangani oleh kedua belah pihak dengan sebenarnya, untuk dipergunakan sebagaimana mestinya dan menjadi pedoman penyelesaian kewajiban tindak lanjut rekomendasi pengawasan berikutnya.
        </p>

        <!-- Tanda Tangan Para Pihak (Saling Berdampingan) -->
        <div class="grid grid-cols-2 text-center text-xs leading-snug break-inside-avoid">
            <div>
                <p class="font-bold">PIHAK PERTAMA,</p>
                <p class="font-semibold text-slate-700">Tim Evaluator / Irban Pengampu<br>Inspektorat Daerah Kab. Trenggalek</p>
                <div class="h-20"></div>
                <p class="font-bold underline uppercase tracking-wide">
                    {{ $irban->penanggungJawab?->nama ?? 'INSPEKTUR PEMBANTU' }}
                </p>
                <p class="text-[10px] font-mono text-slate-600">
                    NIP. {{ $irban->penanggungJawab?->nip ?? '....................................' }}
                </p>
            </div>

            <div>
                <p class="font-bold">PIHAK KEDUA,</p>
                <p class="font-semibold text-slate-700">Kepala Perangkat Daerah / PIC<br>{{ $opd->nama ?? 'Perangkat Daerah' }}</p>
                <div class="h-20"></div>
                <p class="font-bold underline uppercase tracking-wide">
                    ( .................................................... )
                </p>
                <p class="text-[10px] font-mono text-slate-600">
                    NIP. ....................................................
                </p>
            </div>
        </div>

    </div>

</body>
</html>
