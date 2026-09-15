<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Tugas — {{ $penugasan->no_spt }}</title>
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
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 min-h-screen py-8 print:py-0 print:bg-white text-[12pt] leading-normal">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('penugasan.show', $penugasan->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali ke Detail Penugasan
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('penugasan.export-docx', $penugasan->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh Format Word (.docx)
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4) -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-12 sm:p-16 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none">
        
        <!-- Kop Surat Resmi Template -->
        <div class="border-b-4 border-double border-slate-900 pb-3 mb-6 text-center">
            <h3 class="text-sm font-bold uppercase tracking-wider leading-tight">PEMERINTAH KABUPATEN TRENGGALEK</h3>
            <h2 class="text-lg font-bold uppercase tracking-wide leading-tight">INSPEKTORAT</h2>
            <p class="text-xs text-slate-700 leading-tight mt-1">
                Jalan KH. Wakhid Hasyim No. 5 Telp. 0355-791472 Kode Pos 66311<br>
                https://inspektorat.trenggalekkab.go.id
            </p>
        </div>

        <!-- Judul & Nomor Surat Tugas -->
        <div class="text-center my-6 space-y-1">
            <h1 class="text-base font-bold uppercase tracking-wide underline underline-offset-4 decoration-1">
                SURAT TUGAS
            </h1>
            <p class="text-sm">
                Nomor : {{ $penugasan->no_spt }}
            </p>
        </div>

        <!-- Dasar Surat -->
        <div class="my-5 text-justify text-sm">
            <div class="flex items-start gap-4">
                <span class="w-20 shrink-0">Dasar</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1 space-y-1.5 leading-relaxed">
                    @if(!empty($penugasan->dasar_penugasan))
                        @foreach(preg_split('/\r\n|\r|\n/', trim($penugasan->dasar_penugasan)) as $idx => $barisDasar)
                            @if(trim($barisDasar))
                                <p class="pl-5 -indent-5">{{ preg_match('/^\d+[\.\)]/', trim($barisDasar)) ? trim($barisDasar) : ($idx + 1) . '. ' . trim($barisDasar) }}</p>
                            @endif
                        @endforeach
                    @else
                        <p class="pl-5 -indent-5">1. Peraturan Daerah Kabupaten Trenggalek tentang Pembentukan dan Susunan Perangkat Daerah;</p>
                        <p class="pl-5 -indent-5">2. Peraturan Bupati Trenggalek tentang Kedudukan, Susunan Organisasi, Tugas dan Fungsi serta Tata Kerja Inspektorat Daerah Kabupaten Trenggalek;</p>
                        <p class="pl-5 -indent-5">3. Program Kerja Pengawasan Tahunan (PKPT) Inspektorat Daerah Kabupaten Trenggalek Tahun Anggaran {{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y') : date('Y') }};</p>
                        @if($penugasan->penugasanInduk)
                            <p class="pl-5 -indent-5">4. Surat Perintah Tugas Induk Nomor: {{ $penugasan->penugasanInduk->no_spt }};</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Memerintahkan -->
        <div class="text-center my-5">
            <h2 class="text-sm font-bold uppercase tracking-wider">MEMERINTAHKAN :</h2>
        </div>

        <!-- Kepada (Tabel Tim Sesuai Template) -->
        <div class="my-4 text-sm">
            <div class="flex items-start gap-4">
                <span class="w-20 shrink-0">Kepada</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1">
                    <table class="w-full border-collapse border border-slate-900 text-left text-xs sm:text-sm my-2">
                        <thead>
                            <tr class="font-bold text-center">
                                <th class="border border-slate-900 py-1.5 px-2 w-10">No.</th>
                                <th class="border border-slate-900 py-1.5 px-3">Nama</th>
                                <th class="border border-slate-900 py-1.5 px-3">NIP</th>
                                <th class="border border-slate-900 py-1.5 px-3">Jabatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sortedTim = $penugasan->tim->sortBy(function($m) {
                                    $order = [
                                        'penanggung_jawab'       => 1,
                                        'wakil_penanggung_jawab' => 2,
                                        'pengendali_teknis'      => 3,
                                        'ketua_tim'              => 4,
                                        'anggota_tim'            => 5,
                                    ];
                                    return $order[$m->peran] ?? 6;
                                });

                                $inspekturNama = $inspektur?->nama ?? 'Ir. WIJIONO, S.T., M.MKes.';
                                $inspekturNip  = $inspektur?->nip ?? '197308051997031007';
                                $inspekturPangkat = $inspektur?->pangkat ?? ($inspektur?->golongan ? 'Pembina Utama Muda (' . $inspektur->golongan . ')' : 'Pembina Utama Muda (IV/c)');

                                $timList = [];
                                $hasPj = $sortedTim->contains(fn($m) => $m->peran === 'penanggung_jawab');
                                if (!$hasPj) {
                                    $timList[] = [
                                        'nama'  => $inspekturNama,
                                        'nip'   => $inspekturNip,
                                        'peran' => 'Penanggung Jawab',
                                    ];
                                }

                                $hasWpj = $sortedTim->contains(fn($m) => $m->peran === 'wakil_penanggung_jawab');
                                if (!$hasWpj && $penugasan->irban) {
                                    $irbanUser = $penugasan->irban->users()->whereHas('roles', fn($q) => $q->where('name', 'irban'))->first();
                                    if ($irbanUser) {
                                        $timList[] = [
                                            'nama'  => $irbanUser->nama,
                                            'nip'   => $irbanUser->nip,
                                            'peran' => 'Wakil Penanggungjawab',
                                        ];
                                    }
                                }

                                foreach ($sortedTim as $member) {
                                    $peranLabel = match($member->peran) {
                                        'penanggung_jawab'       => 'Penanggung Jawab',
                                        'wakil_penanggung_jawab' => 'Wakil Penanggungjawab',
                                        'pengendali_teknis'      => 'Pengendali Teknis',
                                        'ketua_tim'              => 'Ketua Tim',
                                        'anggota_tim'            => 'Anggota Tim',
                                        default                  => ucwords(str_replace('_', ' ', $member->peran)),
                                    };

                                    $timList[] = [
                                        'nama'  => $member->user?->nama ?? '-',
                                        'nip'   => $member->user?->nip ?? '-',
                                        'peran' => $peranLabel,
                                    ];
                                }
                            @endphp

                            @foreach($timList as $index => $person)
                                <tr>
                                    <td class="border border-slate-900 py-1.5 px-2 text-center">{{ $index + 1 }}.</td>
                                    <td class="border border-slate-900 py-1.5 px-3">{{ $person['nama'] }}</td>
                                    <td class="border border-slate-900 py-1.5 px-3 text-center">{{ $person['nip'] }}</td>
                                    <td class="border border-slate-900 py-1.5 px-3">{{ $person['peran'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Untuk -->
        <div class="my-5 text-justify text-sm">
            <div class="flex items-start gap-4">
                <span class="w-20 shrink-0">Untuk</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1 leading-relaxed">
                    <p>
                        {{ $penugasan->uraian_penugasan }} pada {{ $penugasan->objekPenugasan->pluck('nama')->implode(', ') ?: ($penugasan->irban->nama_irban ?? 'Perangkat Daerah Kabupaten Trenggalek') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Waktu -->
        <div class="my-5 text-justify text-sm">
            <div class="flex items-start gap-4">
                <span class="w-20 shrink-0">Waktu</span>
                <span class="w-3 shrink-0">:</span>
                <div class="flex-1 leading-relaxed">
                    <p>
                        {{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->translatedFormat('d F Y') : '-' }} s.d. {{ $penugasan->tanggal_selesai ? $penugasan->tanggal_selesai->translatedFormat('d F Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Peringatan -->
        <div class="my-5 text-justify text-sm">
            <p class="leading-relaxed">
                <strong>Peringatan :</strong> Kegiatan ini dibiayai dari APBD Kabupaten Trenggalek Tahun Anggaran {{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->format('Y') : date('Y') }}, selanjutnya dalam rangka penegakan Kode Etik APIP dan implementasi pakta integritas maka tidak diperkenankan memberi dan/atau menerima uang, barang dan/atau jasa dalam bentuk apapun sejenis gratifikasi.
            </p>
        </div>

        <!-- Kalimat Penutup -->
        <div class="my-5 text-justify text-sm">
            <p>Demikian untuk dilaksanakan sebaik-baiknya dengan penuh tanggung jawab.</p>
        </div>

        <!-- Penutup & Tanda Tangan -->
        <div class="mt-8 text-sm">
            <div class="flex justify-end">
                <div class="w-72 text-left space-y-1">
                    <p>Trenggalek, {{ $penugasan->tanggal_mulai ? $penugasan->tanggal_mulai->translatedFormat('d F Y') : date('d F Y') }}</p>
                    
                    <div class="pt-2 font-bold uppercase leading-tight">
                        Plt. INSPEKTUR<br>
                        KABUPATEN TRENGGALEK
                    </div>

                    <!-- Ruang tanda tangan -->
                    <div class="h-20"></div>

                    <div class="font-bold underline uppercase">
                        {{ $inspekturNama }}
                    </div>
                    <div class="text-xs text-slate-800">
                        {{ $inspekturPangkat }}<br>
                        NIP. {{ $inspekturNip }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

