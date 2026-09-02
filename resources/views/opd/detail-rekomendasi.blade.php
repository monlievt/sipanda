<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Rekomendasi OPD — SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased font-sans min-h-screen">
    <!-- Navbar OPD -->
    <nav class="bg-teal-700 text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('opd.dashboard') }}" class="text-xs font-bold text-teal-100 hover:text-white flex items-center gap-1">
                &larr; Kembali ke Dashboard OPD
            </a>
            <span class="text-xs font-black uppercase tracking-wider">PORTAL OPD</span>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto py-8 px-4 sm:px-6 space-y-6">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Card Detail Rekomendasi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4 text-xs">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <span class="font-mono font-bold text-teal-600 text-sm">No. SPT: {{ $tindakLanjut->penugasan?->no_spt }}</span>
                <span class="px-2.5 py-1 rounded-full font-bold uppercase text-[10px] bg-slate-100 dark:bg-slate-800">
                    Status: {{ $tindakLanjut->status_label }}
                </span>
            </div>

            <div>
                <span class="block font-semibold text-slate-400">Uraian Temuan Inspektorat</span>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 mt-1">{{ $tindakLanjut->uraian_temuan }}</p>
            </div>

            <div>
                <span class="block font-semibold text-slate-400">Rekomendasi Wajib ditindaklanjuti</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $tindakLanjut->rekomendasi }}</p>
            </div>
        </div>

        <!-- Form Unggah Bukti Baru -->
        @if($tindakLanjut->status_tindak_lanjut !== 'selesai')
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs">
            <h3 class="font-bold text-slate-900 dark:text-white text-base mb-4">Kirim / Unggah Bukti Tindak Lanjut</h3>

            <form method="POST" action="{{ route('opd.tindak-lanjut.bukti.store', $tindakLanjut->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">Penjelasan / Uraian Tindak Lanjut OPD <span class="text-rose-500">*</span></label>
                    <textarea name="catatan_opd" rows="3" required placeholder="Jelaskan langkah perbaikan / tindak lanjut yang telah dilaksanakan oleh OPD..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                </div>

                <!-- Input Setoran Finansial (Opsional / Kondisional) -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">💵 Penyetoran Kas Daerah (Jika Ada Unsur Finansial)</span>
                        <span class="text-[10px] text-slate-400">Kosongkan jika bukan rekomendasi finansial</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold mb-1 text-[11px] text-slate-600 dark:text-slate-400">Nominal Setor (Rp)</label>
                            <input type="text" name="nilai_setor_rp" oninput="formatRupiahInput(this)" placeholder="mis. 5.000.000" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-teal-500 rupiah-input">
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
                    <label class="block font-semibold mb-1">Lampiran Berkas Bukti (PDF, DOCX, XLSX, JPG — Max 10MB)</label>
                    <input type="file" name="file" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs p-2">
                </div>

                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-600/25 transition-all">
                    Kirim Bukti ke Inspektorat
                </button>
            </form>
        </div>
        @endif

        <!-- Riwayat Pengajuan Bukti -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 text-xs space-y-4">
            <h3 class="font-bold text-slate-900 dark:text-white text-base">Riwayat Pengajuan Bukti & Catatan Verifikasi</h3>

            <div class="space-y-3">
                @forelse($tindakLanjut->buktiTindakLanjut as $b)
                    <div class="p-4 rounded-xl border {{ $b->status_verifikasi === 'diterima' ? 'border-emerald-200 bg-emerald-50/30' : ($b->status_verifikasi === 'ditolak' ? 'border-rose-200 bg-rose-50/30' : 'border-slate-200 bg-slate-50/50') }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-slate-500">{{ $b->created_at->format('d/m/Y H:i') }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $b->status_verifikasi === 'diterima' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $b->status_verifikasi === 'ditolak' ? 'bg-rose-100 text-rose-800' : '' }}
                                {{ $b->status_verifikasi === 'menunggu' ? 'bg-amber-100 text-amber-800' : '' }}">
                                {{ $b->status_verifikasi }}
                            </span>
                        </div>
                        <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $b->catatan_opd }}</p>

                        @if($b->catatan_verifikasi)
                            <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-700 text-rose-700 font-semibold">
                                Catatan Verifikator: {{ $b->catatan_verifikasi }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-4">Belum ada bukti yang diunggah.</p>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />

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
