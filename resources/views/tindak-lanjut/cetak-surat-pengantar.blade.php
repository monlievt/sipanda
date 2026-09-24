<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Pengantar Matriks Tindak Lanjut — {{ $tindakLanjut->no_surat_pengantar ?? $tindakLanjut->no_lhp }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 11pt; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            @page {
                size: A4 portrait;
                margin: 20mm 20mm 20mm 20mm;
            }
        }
        body {
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 min-h-screen py-8 print:py-0 print:bg-white">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('tindak-lanjut.show', $tindakLanjut->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali ke Detail LHP
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF Surat Pengantar
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4 Portrait) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-12 sm:p-16 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none leading-relaxed text-sm">
        
        <!-- Kop Surat Resmi Inspektorat Trenggalek -->
        @php
            $logoPath = public_path('images/logo-trenggalek.png');
            $logoSrc = file_exists($logoPath) 
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) 
                : asset('images/logo-trenggalek.png');
        @endphp
        <div class="relative border-b-4 border-double border-slate-900 pb-4 mb-6">
            <div class="absolute left-0 top-0 bottom-4 flex items-center">
                <img src="{{ $logoSrc }}" alt="Logo Kabupaten Trenggalek" class="h-20 sm:h-24 w-auto object-contain">
            </div>
            <div class="text-center px-16 sm:px-20">
                <h3 class="text-base font-bold uppercase tracking-wider leading-tight text-slate-900">PEMERINTAH KABUPATEN TRENGGALEK</h3>
                <h2 class="text-xl font-extrabold uppercase tracking-wide leading-tight text-slate-900 mt-0.5">INSPEKTORAT DAERAH</h2>
                <p class="text-xs text-slate-700 leading-tight mt-1">
                    Jalan Brigjen Soetran Nomor 9, Telepon (0355) 791407, Fax (0355) 791407<br>
                    Website: https://inspektorat.trenggalekkab.go.id &bull; Pos-el: inspektorat@trenggalekkab.go.id<br>
                    <strong>TRENGGALEK &mdash; 66311</strong>
                </p>
            </div>
        </div>

        <!-- Tanggal Surat di Pojok Kanan Atas -->
        <div class="flex justify-end mb-4">
            <p class="text-right">
                Trenggalek, {{ $tindakLanjut->tgl_surat_pengantar ? $tindakLanjut->tgl_surat_pengantar->format('d F Y') : date('d F Y') }}
            </p>
        </div>

        <!-- Informasi Surat & Tujuan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="space-y-1">
                <div class="flex">
                    <span class="w-24 font-bold shrink-0">Nomor</span>
                    <span class="w-3 shrink-0">:</span>
                    <span class="font-mono font-bold">{{ $tindakLanjut->no_surat_pengantar ?? '700/   /406.008/' . date('Y') }}</span>
                </div>
                <div class="flex">
                    <span class="w-24 font-bold shrink-0">Sifat</span>
                    <span class="w-3 shrink-0">:</span>
                    <span>{{ $tindakLanjut->sifat_surat ?? 'Biasa' }}</span>
                </div>
                <div class="flex">
                    <span class="w-24 font-bold shrink-0">Lampiran</span>
                    <span class="w-3 shrink-0">:</span>
                    <span>1 (satu) Berkas Matriks</span>
                </div>
                <div class="flex">
                    <span class="w-24 font-bold shrink-0">Hal</span>
                    <span class="w-3 shrink-0">:</span>
                    <span class="font-bold leading-tight">{{ $tindakLanjut->hal_surat ?? 'Penyampaian Matriks Hasil Pemantauan Tindak Lanjut Hasil Pengawasan' }}</span>
                </div>
            </div>

            <div class="sm:pl-8 space-y-1">
                <p>Kepada Yth.</p>
                <p class="font-bold leading-snug">
                    Kepala {{ $tindakLanjut->tujuanSuratObjek?->nama ?? $tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->first() ?? 'Perangkat Daerah / Desa Terkait' }}<br>
                    Kabupaten Trenggalek
                </p>
                <p>di &mdash;</p>
                <p class="font-bold pl-4">TRENGGALEK</p>
            </div>
        </div>

        <!-- Isi Naskah Dinas Surat Pengantar -->
        <div class="space-y-4 text-justify leading-relaxed mt-6">
            <p class="indent-8">
                Dalam rangka pelaksanaan fungsi pengawasan internal dan pemantauan tindak lanjut rekomendasi hasil pemeriksaan/pengawasan, bersama ini kami sampaikan <strong>Matriks Hasil Telaah Pemantauan Tindak Lanjut Hasil Pengawasan (TLHP)</strong> atas:
            </p>

            <div class="pl-8 space-y-1 text-xs sm:text-sm bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div class="flex">
                    <span class="w-36 font-bold shrink-0">Laporan Hasil Pengawasan</span>
                    <span class="w-3 shrink-0">:</span>
                    <span class="font-bold">{{ $tindakLanjut->judul_lhp ?? $tindakLanjut->penugasan?->uraian_penugasan }}</span>
                </div>
                <div class="flex">
                    <span class="w-36 font-bold shrink-0">Nomor & Tanggal LHP</span>
                    <span class="w-3 shrink-0">:</span>
                    <span class="font-mono">{{ $tindakLanjut->no_lhp ?? '-' }} ({{ $tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->format('d F Y') : '-' }})</span>
                </div>
                <div class="flex">
                    <span class="w-36 font-bold shrink-0">Dasar Pemantauan TL</span>
                    <span class="w-3 shrink-0">:</span>
                    <span>Surat Tugas Nomor {{ $tindakLanjut->stPemantauan?->no_spt ?? ($tindakLanjut->penugasan?->no_spt ?? '-') }}</span>
                </div>
            </div>

            <p class="indent-8">
                Berdasarkan hasil verifikasi dan telaah bukti pendukung yang telah disampaikan melalui Sistem Informasi Pengawasan Daerah (SIPANDA), kami mengapresiasi rekomendasi yang telah selesai ditindaklanjuti sesuai ketentuan yang berlaku.
            </p>

            <p class="indent-8">
                Adapun terhadap rekomendasi yang statusnya masih dinyatakan <em>Belum Sesuai (Dalam Proses)</em> atau <em>Belum Ditindaklanjuti</em> sebagaimana tercantum dalam lampiran matriks, diminta kepada Saudara untuk segera menyelesaikan dan mengunggah bukti perbaikan/penyetoran kas daerah melalui aplikasi SIPANDA sebelum batas waktu yang telah ditentukan.
            </p>

            <p class="indent-8">
                Demikian untuk menjadi maklum dan atas perhatian serta kerja samanya disampaikan terima kasih.
            </p>
        </div>

        <!-- Tanda Tangan Inspektur Daerah -->
        <div class="mt-10 flex justify-end text-center">
            <div class="w-72">
                <p class="font-bold">
                    INSPEKTUR DAERAH<br>
                    KABUPATEN TRENGGALEK
                </p>
                <div class="h-24"></div>
                <p class="font-bold underline uppercase">
                    {{ $tindakLanjut->inspekturPenyetuju?->nama ?? ($inspektur?->nama ?? 'Drs. AGUS SETIYONO, M.Si.') }}
                </p>
                <p class="text-xs text-slate-700 font-mono">
                    Pembina Utama Muda<br>
                    NIP. {{ $tindakLanjut->inspekturPenyetuju?->nip ?? ($inspektur?->nip ?? '19680815 199303 1 007') }}
                </p>
            </div>
        </div>

        <!-- Tembusan Naskah Dinas -->
        <div class="mt-8 pt-4 border-t border-slate-300 text-xs">
            <p class="font-bold"><u>Tembusan disampaikan kepada Yth.:</u></p>
            <ol class="list-decimal list-inside pl-1 space-y-0.5 mt-1 text-slate-700">
                <li>Bupati Trenggalek (sebagai laporan);</li>
                <li>Wakil Bupati Trenggalek;</li>
                <li>Sekretaris Daerah Kabupaten Trenggalek;</li>
                <li>Arsip / Pertinggal.</li>
            </ol>
        </div>

    </div>

</body>
</html>
