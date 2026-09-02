<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rincian LHP — Portal OPD SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased font-sans min-h-screen flex flex-col">
    <!-- Navbar OPD -->
    <nav class="bg-teal-700 text-white shadow-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('opd.dashboard') }}" class="text-xs font-bold text-teal-100 hover:text-white flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-800/60 hover:bg-teal-800 transition-all">
                        <span>&larr; Kembali ke Daftar LHP</span>
                    </a>
                    <span class="text-sm font-extrabold hidden md:inline-block">SIPANDA <span class="text-xs font-normal opacity-80">| Rincian LHP</span></span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-teal-100">PIC: <strong>{{ $user->nama }}</strong></span>
                    <form method="POST" action="{{ route('opd.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs bg-teal-900 hover:bg-teal-950 px-3.5 py-1.5 rounded-xl text-white font-semibold transition-colors cursor-pointer">
                            Keluar &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-semibold flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Card Header LHP -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-3 py-1 bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-mono font-bold text-xs rounded-lg border border-teal-200 dark:border-teal-800">
                            📄 No. LHP: {{ $lhpSummary->no_lhp }}
                        </span>
                        <span class="text-xs text-slate-400">Tanggal LHP: <strong>{{ \Carbon\Carbon::parse($lhpSummary->tgl_lhp)->format('d F Y') }}</strong></span>
                        @if($lhpSummary->penugasan?->no_spt)
                            <span class="text-xs text-slate-400">| No. SPT: <span class="font-mono">{{ $lhpSummary->penugasan->no_spt }}</span></span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight mt-1">
                        {{ $lhpSummary->judul_lhp }}
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('opd.lhp.berita_acara', $lhpSummary->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 font-bold text-xs rounded-xl shadow-xs transition-all">
                        <span>📑 Cetak Berita Acara (PDF)</span>
                    </a>

                    @if($lhpSummary->berkas_dasar_lhp)
                        <a href="{{ route('dokumen.stream.path', $lhpSummary->berkas_dasar_lhp) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-600/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh Dokumen LHP (PDF)</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Matriks Status & Finansial LHP -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800/60">
                    <span class="font-bold text-emerald-800 dark:text-emerald-300 block uppercase text-[10px]">🟢 Sesuai (SS)</span>
                    <span class="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-0.5 block">{{ $lhpSummary->count_sesuai }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></span>
                </div>
                <div class="p-3.5 bg-blue-50 dark:bg-blue-950/40 rounded-2xl border border-blue-200 dark:border-blue-800/60">
                    <span class="font-bold text-blue-800 dark:text-blue-300 block uppercase text-[10px]">🔵 Belum Sesuai (BS)</span>
                    <span class="text-xl font-black text-blue-700 dark:text-blue-300 mt-0.5 block">{{ $lhpSummary->count_belum_sesuai }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></span>
                </div>
                <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-800/60">
                    <span class="font-bold text-amber-800 dark:text-amber-300 block uppercase text-[10px]">🟡 Belum di-TL (BTL)</span>
                    <span class="text-xl font-black text-amber-700 dark:text-amber-300 mt-0.5 block">{{ $lhpSummary->count_belum }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></span>
                </div>
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <span class="font-bold text-slate-600 dark:text-slate-400 block uppercase text-[10px]">⚪ Tidak Dapat di-TL (TDT)</span>
                    <span class="text-xl font-black text-slate-700 dark:text-slate-300 mt-0.5 block">{{ $lhpSummary->count_tdt }} <span class="text-xs font-semibold text-slate-400">Rekomendasi</span></span>
                </div>
            </div>

            @if($lhpSummary->total_target_rp > 0 || $lhpSummary->total_setor_rp > 0)
            <!-- Financial Tracking Bar -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block text-[10px] uppercase">Kewajiban Setor Kas Daerah</span>
                    <span class="text-base font-black text-slate-900 dark:text-white">{{ $lhpSummary->formatted_total_target }}</span>
                </div>
                <div>
                    <span class="text-emerald-600 font-semibold block text-[10px] uppercase">Telah Disetor ke Kasda</span>
                    <span class="text-base font-black text-emerald-600">{{ $lhpSummary->formatted_total_setor }}</span>
                </div>
                <div>
                    <span class="text-rose-600 font-semibold block text-[10px] uppercase">Sisa Kurang Setor</span>
                    <span class="text-base font-black text-rose-600">{{ $lhpSummary->formatted_sisa_setor }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Daftar Rincian Temuan & Rekomendasi -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋 Daftar Rekomendasi / Saran ({{ $items->count() }} Item)</span>
                </h2>
                <span class="text-xs text-slate-500">Silakan berikan respon dan unggah bukti tindak lanjut di bawah</span>
            </div>

            @foreach($items as $idx => $item)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5" id="rekomendasi-{{ $item->id }}">
                    <!-- Header Baris Rekomendasi -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3.5">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 bg-teal-600 text-white rounded-xl flex items-center justify-center font-black text-xs shrink-0">
                                #{{ $idx + 1 }}
                            </span>
                            <div>
                                <span class="text-[11px] font-bold uppercase text-slate-400">Status Rekomendasi:</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                    {{ $item->status_tindak_lanjut === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                    {{ $item->status_tindak_lanjut === 'menunggu_verifikasi' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                                    {{ $item->status_tindak_lanjut === 'dikembalikan' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : '' }}
                                    {{ in_array($item->status_tindak_lanjut, ['belum', 'proses']) ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : '' }}
                                    {{ $item->status_tindak_lanjut === 'tdt' ? 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300' : '' }}">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                        </div>

                        @if($item->tanggal_target)
                            <span class="text-xs text-slate-500">
                                Target Penyelesaian: <strong>{{ $item->tanggal_target->format('d/m/Y') }}</strong>
                            </span>
                        @endif
                    </div>

                    <!-- Uraian Temuan & Rekomendasi -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/80 space-y-1.5">
                            <span class="font-bold text-slate-500 dark:text-slate-400 block uppercase text-[10px]">🔎 Uraian Temuan Pemeriksaan:</span>
                            <p class="font-medium text-slate-800 dark:text-slate-200 leading-relaxed">{{ $item->uraian_temuan }}</p>
                        </div>

                        <div class="p-4 bg-teal-50/50 dark:bg-teal-950/20 rounded-2xl border border-teal-200/80 dark:border-teal-800/40 space-y-1.5">
                            <span class="font-bold text-teal-700 dark:text-teal-400 block uppercase text-[10px]">🎯 Rekomendasi / Saran Wajib:</span>
                            <p class="font-bold text-slate-900 dark:text-white leading-relaxed">{{ $item->rekomendasi }}</p>
                            @if($item->nilai_rekomendasi_rp > 0)
                                <div class="mt-2 pt-2 border-t border-teal-200/60 dark:border-teal-800/40 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500">Kewajiban Setor: <strong class="text-rose-600">{{ $item->formatted_nilai_rp }}</strong></span>
                                    <span class="text-slate-500">Telah Disetor: <strong class="text-emerald-600">{{ $item->formatted_total_setor }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Riwayat Setoran Finansial (Jika Ada) -->
                    @if($item->rincianPenyetoran->count() > 0)
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2 text-xs">
                        <span class="font-bold text-slate-800 dark:text-slate-200 block uppercase text-[10px]">💰 Rincian Penyetoran Kas Daerah (NTPN):</span>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[11px]">
                                <thead class="bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold uppercase">
                                    <tr>
                                        <th class="p-2">No. Referensi / NTPN</th>
                                        <th class="p-2">Bank</th>
                                        <th class="p-2">Nilai Setoran</th>
                                        <th class="p-2 text-center">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 font-mono">
                                    @foreach($item->rincianPenyetoran as $s)
                                        <tr class="hover:bg-white/50 dark:hover:bg-slate-800/60">
                                            <td class="p-2 font-bold text-slate-800 dark:text-slate-200">{{ $s->no_referensi_ntpn ?: '-' }}</td>
                                            <td class="p-2 text-slate-600 dark:text-slate-300 font-sans">{{ $s->nama_bank ?: 'Bank Jatim / Kas Daerah' }}</td>
                                            <td class="p-2 font-bold text-emerald-600 dark:text-emerald-400">{{ $s->formatted_nilai_setor }}</td>
                                            <td class="p-2 text-center text-slate-500 font-sans">{{ $s->tgl_setor ? $s->tgl_setor->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Riwayat Respon & Catatan Verifikasi Sebelumnya -->
                    @if($item->buktiTindakLanjut->count() > 0)
                    <div class="space-y-2.5 text-xs">
                        <span class="font-bold text-slate-500 dark:text-slate-400 block uppercase text-[10px]">📜 Riwayat Pengajuan Bukti & Verifikasi APIP:</span>
                        <div class="space-y-2">
                            @foreach($item->buktiTindakLanjut as $b)
                                <div class="p-3.5 rounded-2xl border {{ $b->status_verifikasi === 'diterima' ? 'border-emerald-200 bg-emerald-50/30 dark:bg-emerald-950/20' : ($b->status_verifikasi === 'ditolak' ? 'border-rose-200 bg-rose-50/30 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30') }}">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] font-semibold text-slate-400">{{ $b->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase
                                            {{ $b->status_verifikasi === 'diterima' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                            {{ $b->status_verifikasi === 'ditolak' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : '' }}
                                            {{ $b->status_verifikasi === 'menunggu' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : '' }}">
                                            {{ $b->status_verifikasi }}
                                        </span>
                                    </div>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $b->catatan_opd }}</p>
                                    
                                    @if($b->arsipDigital->count() > 0)
                                        <div class="mt-2 flex items-center gap-2">
                                            @foreach($b->arsipDigital as $file)
                                                <a href="{{ route('dokumen.stream.path', $file->path_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-semibold text-teal-600 hover:text-teal-700">
                                                    📎 {{ Str::limit($file->nama_file, 24) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($b->catatan_verifikasi)
                                        <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-700 text-[11px] text-rose-700 dark:text-rose-400 font-semibold">
                                            Catatan APIP (Verifikator): {{ $b->catatan_verifikasi }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Form Respon & Input Setoran Baru -->
                    @if($item->status_tindak_lanjut !== 'selesai' && $item->status_tindak_lanjut !== 'tdt')
                    <div class="p-5 bg-teal-50/40 dark:bg-teal-950/20 rounded-2xl border border-teal-200 dark:border-teal-800/50 space-y-3.5 text-xs">
                        <div class="flex items-center gap-2 text-teal-900 dark:text-teal-300 font-black">
                            <span>✍️ Kirim Tindak Lanjut & Setoran Baru:</span>
                        </div>

                        <form method="POST" action="{{ route('opd.tindak-lanjut.bukti.store', $item->id) }}" enctype="multipart/form-data" class="space-y-3.5">
                            @csrf

                            <div>
                                <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Uraian Langkah Tindak Lanjut OPD <span class="text-rose-500">*</span></label>
                                <textarea name="catatan_opd" rows="3" required placeholder="Jelaskan secara rinci tindakan perbaikan, surat edaran, SOP, atau langkah penyelesaian yang telah dilakukan..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-teal-500"></textarea>
                            </div>

                            <!-- Input Setoran Finansial (Opsional / Kondisional) -->
                            <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">💵 Penyetoran Kas Daerah (Jika Ada Unsur Finansial)</span>
                                    <span class="text-[10px] text-slate-400">Kosongkan jika bukan rekomendasi finansial</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block font-semibold mb-1 text-[11px] text-slate-600 dark:text-slate-400">Nominal Setor (Rp)</label>
                                        <input type="text" name="nilai_setor_rp" oninput="formatRupiahInput(this)" placeholder="mis. 5.000.000" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-teal-500 rupiah-input">
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1 text-[11px] text-slate-600 dark:text-slate-400">Nama Bank / Kasda</label>
                                        <input type="text" name="nama_bank" placeholder="mis. Bank Jatim / Kasda" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1 text-[11px] text-slate-600 dark:text-slate-400">No. STS / Bukti Bank / NTPN</label>
                                        <input type="text" name="no_referensi_ntpn" placeholder="mis. STS-2026/08/01" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1 text-[11px] text-slate-600 dark:text-slate-400">Tanggal Penyetoran</label>
                                        <input type="date" name="tgl_setor" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-teal-500">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Lampiran Berkas Bukti (PDF, DOCX, XLSX, JPG — Max 10MB)</label>
                                <input type="file" name="file" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs p-2">
                                <p class="text-[10px] text-slate-400 mt-1">Unggah berkas bukti pendukung seperti scan STS, slip setoran bank, SK, SOP, atau foto dokumentasi perbaikan.</p>
                            </div>

                            <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-600/20 transition-all cursor-pointer">
                                🚀 Kirim Respon Tindak Lanjut & Bukti
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                        ✅ Rekomendasi ini telah dinyatakan <strong>{{ $item->status_label }}</strong> oleh Inspektorat.
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </main>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />

    <!-- Global PDF & Document Preview Modal -->
    <x-pdf-preview-modal />

    <script>
        function formatRupiahInput(el) {
            if (!el) return;
            let cursorPos = el.selectionStart || 0;
            let originalLen = el.value.length;
            let rawVal = el.value.replace(/\D/g, '');
            if (!rawVal) {
                el.value = '';
                return;
            }
            let formatted = new Intl.NumberFormat('id-ID').format(rawVal);
            el.value = formatted;
            let newLen = formatted.length;
            cursorPos = cursorPos + (newLen - originalLen);
            if (cursorPos > 0 && el.setSelectionRange) {
                el.setSelectionRange(cursorPos, cursorPos);
            }
        }
    </script>
</body>
</html>
