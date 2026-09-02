<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Perintah Tugas (SPT) — {{ $penugasan->no_spt }}</title>
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
        <a href="{{ route('penugasan.show', $penugasan->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali ke Detail SPT
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF Resmi
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-12 sm:p-16 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none leading-relaxed">
        
        <!-- Kop Surat Resmi Inspektorat Trenggalek -->
        <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 text-center relative">
            <h3 class="text-base font-bold uppercase tracking-wider leading-tight">PEMERINTAH KABUPATEN TRENGGALEK</h3>
            <h2 class="text-xl font-extrabold uppercase tracking-wide leading-tight">INSPEKTORAT DAERAH</h2>
            <p class="text-xs text-slate-700 leading-tight mt-1">
                Jalan Brigjen Soetran Nomor 9, Telepon (0355) 791407, Fax (0355) 791407<br>
                Website: https://inspektorat.trenggalekkab.go.id &bull; Pos-el: inspektorat@trenggalekkab.go.id<br>
                <strong>TRENGGALEK &mdash; 66311</strong>
            </p>
        </div>

        <!-- Judul & Nomor SPT -->
        <div class="text-center my-6 space-y-1">
            <h1 class="text-base font-bold uppercase tracking-widest underline underline-offset-4 decoration-1">
                SURAT PERINTAH TUGAS
            </h1>
            <p class="text-xs font-bold font-mono tracking-wider">
                NOMOR : {{ $penugasan->no_spt }}
            </p>
        </div>

        <!-- Dasar Surat -->
        <div class="my-6 space-y-2 text-justify text-xs sm:text-sm">
            <div class="flex items-start gap-4">
                <span class="font-bold w-20 shrink-0">Dasar</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1 space-y-1.5 leading-relaxed">
                    @if(!empty($penugasan->dasar_penugasan))
                        @foreach(preg_split('/\r\n|\r|\n/', trim($penugasan->dasar_penugasan)) as $barisDasar)
                            @if(trim($barisDasar))
                                <p>{{ trim($barisDasar) }}</p>
                            @endif
                        @endforeach
                    @else
                        <p>1. Peraturan Daerah Kabupaten Trenggalek tentang Pembentukan dan Susunan Perangkat Daerah;</p>
                        <p>2. Peraturan Bupati Trenggalek tentang Kedudukan, Susunan Organisasi, Tugas dan Fungsi serta Tata Kerja Inspektorat Daerah Kabupaten Trenggalek;</p>
                        <p>3. Program Kerja Pengawasan Tahunan (PKPT) Inspektorat Daerah Kabupaten Trenggalek Tahun Anggaran {{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y') : date('Y') }};</p>
                        @if($penugasan->penugasanInduk)
                            <p>4. Surat Perintah Tugas Induk Nomor: {{ $penugasan->penugasanInduk->no_spt }}.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Memerintahkan -->
        <div class="text-center my-5">
            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-widest">MEMERINTAHKAN:</h2>
        </div>

        <!-- Kepada (Tabel Tim) -->
        <div class="my-4 space-y-2 text-xs sm:text-sm">
            <div class="flex items-start gap-4">
                <span class="font-bold w-20 shrink-0">Kepada</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1">
                    <table class="w-full border-collapse border border-slate-900 text-left text-xs my-2">
                        <thead>
                            <tr class="bg-slate-100 text-center font-bold">
                                <th class="border border-slate-900 py-1.5 px-2 w-8">No</th>
                                <th class="border border-slate-900 py-1.5 px-3">Nama / NIP</th>
                                <th class="border border-slate-900 py-1.5 px-3">Pangkat / Gol.</th>
                                <th class="border border-slate-900 py-1.5 px-3">Jabatan Dinas</th>
                                <th class="border border-slate-900 py-1.5 px-3">Kedudukan Tim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sortedTim = $penugasan->tim->sortBy(function($m) {
                                    $order = [
                                        'penanggung_jawab' => 1,
                                        'wakil_penanggung_jawab' => 2,
                                        'pengendali_teknis' => 3,
                                        'ketua_tim' => 4,
                                        'anggota_tim' => 5,
                                    ];
                                    return $order[$m->peran] ?? 6;
                                });
                            @endphp
                            @forelse($sortedTim as $index => $member)
                                <tr>
                                    <td class="border border-slate-900 py-1.5 px-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-slate-900 py-1.5 px-3 font-semibold">
                                        {{ $member->user->nama ?? '-' }}
                                        <span class="block text-[11px] font-normal font-mono text-slate-700">NIP. {{ $member->user->nip ?? '-' }}</span>
                                    </td>
                                    <td class="border border-slate-900 py-1.5 px-3 text-center">
                                        {{ $member->user->golongan ?? ($member->user->pangkat ?? '-') }}
                                    </td>
                                    <td class="border border-slate-900 py-1.5 px-3">
                                        {{ $member->user->jabatan ?? '-' }}
                                    </td>
                                    <td class="border border-slate-900 py-1.5 px-3 font-bold text-center">
                                        {{ ucwords(str_replace('_', ' ', $member->peran)) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border border-slate-900 py-2 text-center italic text-slate-500">Susunan tim belum ditentukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Untuk -->
        <div class="my-6 space-y-2 text-justify text-xs sm:text-sm">
            <div class="flex items-start gap-4">
                <span class="font-bold w-20 shrink-0">Untuk</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1 space-y-2">
                    <p>
                        1. Melaksanakan <strong>{{ $penugasan->jenisPenugasan->nama ?? 'Pengawasan' }}</strong> perihal <em>"{{ $penugasan->uraian_penugasan }}"</em> pada:
                    </p>
                    <div class="pl-4 font-semibold text-slate-900">
                        Sasaran: 
                        {{ $penugasan->objekPenugasan->pluck('nama')->implode(', ') ?: ($penugasan->irban->nama_irban ?? 'Inspektorat') }}
                    </div>
                    <p>
                        2. Waktu pelaksanaan penugasan selama 
                        <strong>
                            {{ \Carbon\Carbon::parse($penugasan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($penugasan->tanggal_selesai)) + 1 }} 
                            ({{ \Carbon\Carbon::parse($penugasan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($penugasan->tanggal_selesai)) + 1 }}) hari kerja
                        </strong>, 
                        terhitung mulai tanggal 
                        <strong>{{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->translatedFormat('d F Y') : '-' }}</strong> 
                        sampai dengan tanggal 
                        <strong>{{ $penugasan->tanggal_selesai ? $penugasan->tanggal_selesai->translatedFormat('d F Y') : '-' }}</strong>.
                    </p>
                    <p>
                        3. Melaporkan hasil pelaksanaan tugas kepada Inspektur Daerah Kabupaten Trenggalek melalui Laporan Hasil Pengawasan (LHP).
                    </p>
                    <p>
                        4. Melaksanakan tugas dengan penuh tanggung jawab dan mematuhi Kode Etik APIP serta Standar Audit Intern Pemerintah Indonesia (SAIPI).
                    </p>
                </div>
            </div>
        </div>

        <!-- Penutup & Tanda Tangan -->
        <div class="mt-12 text-xs sm:text-sm">
            <div class="flex justify-end">
                <div class="w-72 text-left space-y-1">
                    <p>Ditetapkan di : Trenggalek</p>
                    <p>Pada tanggal : {{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->translatedFormat('d F Y') : date('d F Y') }}</p>
                    
                    <div class="pt-2 font-bold uppercase">
                        Plt. INSPEKTUR DAERAH<br>
                        KABUPATEN TRENGGALEK
                    </div>

                    <!-- Space for signature -->
                    <div class="h-20"></div>

                    <div class="font-bold underline uppercase">
                        {{ $inspektur->nama ?? 'Ir. WIJIONO, S.T., M.MKes.' }}
                    </div>
                    <div class="text-xs">
                        Pembina Utama Muda (IV/c)<br>
                        NIP. {{ $inspektur->nip ?? '197308051997031007' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tembusan -->
        <div class="mt-8 pt-4 border-t border-slate-300 text-[11px] text-slate-700 leading-tight">
            <span class="font-bold">Tembusan disampaikan kepada Yth.:</span>
            <ol class="list-decimal pl-4 mt-0.5 space-y-0.5">
                <li>Bupati Trenggalek (sebagai laporan);</li>
                <li>Sekretaris Daerah Kabupaten Trenggalek;</li>
                <li>Kepala Perangkat Daerah / Auditi yang bersangkutan;</li>
                <li>Pertinggal / Arsip Digital SIPANDA.</li>
            </ol>
        </div>

    </div>

</body>
</html>
