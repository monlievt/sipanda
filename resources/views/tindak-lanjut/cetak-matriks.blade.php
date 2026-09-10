<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matriks Tindak Lanjut Hasil Pengawasan — {{ $tindakLanjut->no_lhp ?? $tindakLanjut->penugasan?->no_spt }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 10pt; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            @page {
                size: A4 landscape;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
        body {
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 min-h-screen py-8 print:py-0 print:bg-white">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-6xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('tindak-lanjut.show', $tindakLanjut->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
            &larr; Kembali ke Detail LHP
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF Matriks
            </button>
        </div>
    </div>

    <!-- Official Document Sheet (A4 Landscape) -->
    <div class="page-sheet max-w-6xl mx-auto bg-white p-10 sm:p-14 shadow-2xl rounded-2xl print:rounded-none border border-slate-300 print:border-none leading-relaxed">
        
        <!-- Kop Surat Resmi Inspektorat Trenggalek -->
        <div class="border-b-4 border-double border-slate-900 pb-3 mb-6 text-center relative">
            <h3 class="text-sm sm:text-base font-bold uppercase tracking-wider leading-tight">PEMERINTAH KABUPATEN TRENGGALEK</h3>
            <h2 class="text-lg sm:text-xl font-extrabold uppercase tracking-wide leading-tight">INSPEKTORAT DAERAH</h2>
            <p class="text-[11px] text-slate-700 leading-tight mt-1">
                Jalan Brigjen Soetran Nomor 9, Telepon (0355) 791407, Fax (0355) 791407<br>
                Website: https://inspektorat.trenggalekkab.go.id &bull; Pos-el: inspektorat@trenggalekkab.go.id<br>
                <strong>TRENGGALEK &mdash; 66311</strong>
            </p>
        </div>

        <!-- Judul Matriks -->
        <div class="text-center my-4 space-y-1">
            <h1 class="text-base sm:text-lg font-bold uppercase tracking-wide underline underline-offset-4 decoration-1">
                MATRIKS HASIL TELAAH TINDAK LANJUT HASIL PENGAWASAN
            </h1>
            <p class="text-xs font-bold text-slate-800">
                PADA {{ strtoupper($tindakLanjut->tujuanSuratObjek?->nama ?? $tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? 'PERANGKAT DAERAH / INSTANSI TERPERIKSA') }}
            </p>
        </div>

        <!-- Metadata Dokumen Pengawasan -->
        <div class="my-4 p-3 bg-slate-50 print:bg-transparent border border-slate-300 rounded-xl text-xs space-y-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1">
                <div class="flex">
                    <span class="w-44 font-bold shrink-0">Nomor & Tanggal LHP</span>
                    <span class="w-3 shrink-0">:</span>
                    <span class="font-mono font-bold">{{ $tindakLanjut->no_lhp ?? '-' }} ({{ $tindakLanjut->tgl_lhp ? $tindakLanjut->tgl_lhp->format('d/m/Y') : '-' }})</span>
                </div>
                <div class="flex">
                    <span class="w-44 font-bold shrink-0">Irban Penanggung Jawab</span>
                    <span class="w-3 shrink-0">:</span>
                    <span>{{ $tindakLanjut->penugasan?->irban_list_names ?? $tindakLanjut->penugasan?->irban?->nama_irban ?? '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-44 font-bold shrink-0">Judul LHP / Penugasan</span>
                    <span class="w-3 shrink-0">:</span>
                    <span>{{ $tindakLanjut->judul_lhp ?? $tindakLanjut->penugasan?->uraian_penugasan ?? '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-44 font-bold shrink-0">ST Pemantauan TL</span>
                    <span class="w-3 shrink-0">:</span>
                    <span class="font-mono">{{ $tindakLanjut->stPemantauan?->no_spt ?? ($tindakLanjut->penugasan?->no_spt ?? '-') }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Matriks Tindak Lanjut -->
        <table class="w-full border-collapse border border-slate-900 text-left text-[11px] my-4 leading-normal">
            <thead>
                <tr class="bg-slate-100 text-center font-bold">
                    <th class="border border-slate-900 p-2 w-8">No</th>
                    <th class="border border-slate-900 p-2 w-44">Uraian Temuan / Kondisi</th>
                    <th class="border border-slate-900 p-2 w-44">Rekomendasi Wajib APIP</th>
                    <th class="border border-slate-900 p-2 w-28">Nilai Rekomendasi (Rp)</th>
                    <th class="border border-slate-900 p-2">Tindak Lanjut Perangkat Daerah</th>
                    <th class="border border-slate-900 p-2 w-28">Setoran Kasda (NTPN)</th>
                    <th class="border border-slate-900 p-2 w-40">Hasil Telaah Tim APIP</th>
                    <th class="border border-slate-900 p-2 w-20">Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lhpItems as $index => $item)
                    <tr class="align-top">
                        <td class="border border-slate-900 p-2 text-center font-bold">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-2">
                            @if($item->nama_objek_sasaran)
                                <span class="font-bold text-[10px] text-purple-700 block mb-1">[{{ $item->nama_objek_sasaran }}]</span>
                            @endif
                            <p class="leading-relaxed">{{ $item->uraian_temuan }}</p>
                        </td>
                        <td class="border border-slate-900 p-2 leading-relaxed">
                            {{ $item->rekomendasi }}
                        </td>
                        <td class="border border-slate-900 p-2 font-mono text-right whitespace-nowrap font-semibold">
                            {{ $item->nilai_rekomendasi_rp > 0 ? 'Rp ' . number_format($item->nilai_rekomendasi_rp, 0, ',', '.') : '-' }}
                        </td>
                        <td class="border border-slate-900 p-2 space-y-1 leading-relaxed">
                            @forelse($item->buktiTindakLanjut as $b)
                                <div class="text-[10px]">
                                    &bull; {{ $b->catatan_opd }}
                                </div>
                            @empty
                                <span class="text-slate-400 italic text-[10px]">Belum ada uraian tindak lanjut.</span>
                            @endforelse
                        </td>
                        <td class="border border-slate-900 p-2 font-mono text-right text-[10px] space-y-1">
                            @php $itemSetor = $item->rincianPenyetoran->sum('nilai_setor_rp'); @endphp
                            @if($itemSetor > 0)
                                <span class="font-bold block">Rp {{ number_format($itemSetor, 0, ',', '.') }}</span>
                                @foreach($item->rincianPenyetoran as $setor)
                                    @if($setor->no_referensi_ntpn)
                                        <span class="block text-[9px] text-slate-500">NTPN: {{ $setor->no_referensi_ntpn }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="border border-slate-900 p-2 text-[10px] leading-relaxed">
                            {{ $item->catatan_telaah_tim ?? ($tindakLanjut->catatan_telaah_tim ?? 'Telah ditelaah sesuai bukti pendukung.') }}
                        </td>
                        <td class="border border-slate-900 p-2 text-center font-bold whitespace-nowrap">
                            @if($item->status_tindak_lanjut === 'selesai')
                                <span class="text-emerald-700">SESUAI</span>
                            @elseif(in_array($item->status_tindak_lanjut, ['proses', 'menunggu_verifikasi']))
                                <span class="text-blue-700">BELUM SESUAI</span>
                            @elseif($item->status_tindak_lanjut === 'belum')
                                <span class="text-amber-700">BELUM TL</span>
                            @else
                                <span class="text-rose-700">TDT</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-slate-900 p-4 text-center italic text-slate-500">Tidak ada catatan rekomendasi.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-bold">
                    <td colspan="3" class="border border-slate-900 p-2 text-center uppercase">Total Rekapitulasi</td>
                    <td class="border border-slate-900 p-2 font-mono text-right whitespace-nowrap">
                        Rp {{ number_format($lhpItems->sum('nilai_rekomendasi_rp'), 0, ',', '.') }}
                    </td>
                    <td class="border border-slate-900 p-2 text-center text-[10px]">
                        S: {{ $lhpItems->where('status_tindak_lanjut', 'selesai')->count() }} | 
                        BS: {{ $lhpItems->whereIn('status_tindak_lanjut', ['proses', 'menunggu_verifikasi'])->count() }} | 
                        BT: {{ $lhpItems->where('status_tindak_lanjut', 'belum')->count() }} | 
                        TDT: {{ $lhpItems->where('status_tindak_lanjut', 'tdt')->count() }}
                    </td>
                    <td class="border border-slate-900 p-2 font-mono text-right whitespace-nowrap">
                        Rp {{ number_format($lhpItems->sum(fn($tl) => $tl->rincianPenyetoran->sum('nilai_setor_rp')), 0, ',', '.') }}
                    </td>
                    <td colspan="2" class="border border-slate-900 p-2 text-center text-[10px]">
                        Status Telaah: Disetujui
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Lembar Tanda Tangan & Pengesahan Bertingkat -->
        <div class="mt-8 pt-4 grid grid-cols-3 gap-6 text-xs text-center leading-normal">
            <div>
                <p class="font-bold">Ditelaah oleh,<br>Tim Pemantauan TL</p>
                <div class="h-20"></div>
                <p class="font-bold underline uppercase">{{ $tindakLanjut->penelaah?->nama ?? auth()->user()->nama }}</p>
                <p class="text-[10px] text-slate-600 font-mono">NIP. {{ $tindakLanjut->penelaah?->nip ?? auth()->user()->nip ?? '-' }}</p>
            </div>

            <div>
                <p class="font-bold">Diverifikasi oleh,<br>Inspektur Pembantu (Irban)</p>
                <div class="h-20"></div>
                <p class="font-bold underline uppercase">{{ $tindakLanjut->irbanPenyetuju?->nama ?? ($tindakLanjut->penugasan?->irban?->nama_irban ?? 'Inspektur Pembantu') }}</p>
                <p class="text-[10px] text-slate-600 font-mono">NIP. {{ $tindakLanjut->irbanPenyetuju?->nip ?? '-' }}</p>
            </div>

            <div>
                <p class="font-bold">Trenggalek, {{ $tindakLanjut->disetujui_inspektur_pada ? $tindakLanjut->disetujui_inspektur_pada->format('d F Y') : date('d F Y') }}<br>Disetujui oleh,<br>INSPEKTUR DAERAH KABUPATEN TRENGGALEK</p>
                <div class="h-16"></div>
                <p class="font-bold underline uppercase">{{ $tindakLanjut->inspekturPenyetuju?->nama ?? ($inspektur?->nama ?? 'Drs. AGUS SETIYONO, M.Si.') }}</p>
                <p class="text-[10px] text-slate-600 font-mono">NIP. {{ $tindakLanjut->inspekturPenyetuju?->nip ?? ($inspektur?->nip ?? '19680815 199303 1 007') }}</p>
            </div>
        </div>

    </div>

</body>
</html>
