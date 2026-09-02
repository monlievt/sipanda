<x-app-layout>
    <x-slot name="header">
        Detail Bahan Tindak Lanjut (LHP)
    </x-slot>

    <!-- Header Navigation & Action -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('tindak-lanjut.index') }}" class="p-2 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Detail Dokumen LHP</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white leading-tight">
                    {{ $tindakLanjut->no_lhp ? 'No. LHP: ' . $tindakLanjut->no_lhp : 'No. SPT: ' . $tindakLanjut->penugasan?->no_spt }}
                </h2>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('tindak-lanjut.export_lhp', $tindakLanjut->id) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center gap-1.5 transition-all">
                📥 Ekspor Matriks LHP (Excel)
            </a>

            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center gap-1.5 transition-all">
                🖨️ Cetak Matriks
            </button>

            <span class="px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                Total: {{ $lhpItems->count() }} Rekomendasi
            </span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="space-y-6 text-xs">

        <!-- 📊 4 Banner Metric Status Rekapitulasi LHP Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 🟦 1. Sesuai -->
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sesuai</span>
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $countSesuai }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $lhpItems->count() > 0 ? round(($countSesuai / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countSesuai / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- 🟩 2. Belum Sesuai -->
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Sesuai</span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $countBelumSesuai }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $lhpItems->count() > 0 ? round(($countBelumSesuai / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countBelumSesuai / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- 🟨 3. Belum Ditindaklanjuti -->
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Ditindaklanjuti</span>
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $countBelum }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $lhpItems->count() > 0 ? round(($countBelum / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countBelum / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- 🔴 4. Tidak Dapat Ditindaklanjuti (TDT) -->
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tidak Dapat Ditindaklanjuti</span>
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $countTdt }}</span>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $lhpItems->count() > 0 ? round(($countTdt / $lhpItems->count()) * 100, 1) : 0 }}% LHP
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $lhpItems->count() > 0 ? ($countTdt / $lhpItems->count()) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- 💰 Rekapitulasi Ringkasan Pengembalian Finansial LHP Ini -->
        <div class="p-4 bg-emerald-950/80 text-white rounded-2xl border border-emerald-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-800/80 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-emerald-200">Realisasi Pengembalian Finansial Dokumen LHP Ini</h4>
                    <p class="text-slate-300 text-[11px]">Total pengembalian uang daerah yang disetorkan ke Kas Daerah (NTPN).</p>
                </div>
            </div>

            <div class="flex items-center gap-6 font-mono">
                <div>
                    <span class="block text-[10px] text-emerald-400 font-sans">Target Rekomendasi Rp</span>
                    <span class="text-base font-extrabold text-white">Rp {{ number_format($totalNilaiTarget, 0, ',', '.') }}</span>
                </div>
                <div class="h-8 w-px bg-emerald-800"></div>
                <div>
                    <span class="block text-[10px] text-emerald-400 font-sans">Telah Disetor (NTPN)</span>
                    <span class="text-base font-extrabold text-emerald-300">Rp {{ number_format($totalSetorRp, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 1. Header LHP Information -->
        <div class="p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="block font-semibold text-slate-400 text-[11px]">Nomor LHP</span>
                    <span class="font-mono font-bold text-sm text-blue-600 dark:text-blue-400">{{ $tindakLanjut->no_lhp ?? '-' }}</span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-400 text-[11px]">Nomor SPT & Irban</span>
                    <span class="font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400">
                        {{ $tindakLanjut->penugasan?->no_spt }} ({{ $tindakLanjut->penugasan?->irban?->nama_irban }})
                    </span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-400 text-[11px]">Nilai Yang Diawasi (APBD/APBDes)</span>
                    <span class="font-mono font-bold text-sm text-blue-700 dark:text-blue-300">
                        {{ $tindakLanjut->formatted_nilai_diawasi }}
                    </span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-400 text-[11px]">Objek OPD Target</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">
                        {{ $tindakLanjut->penugasan?->objekPenugasan->pluck('nama')->implode(', ') ?? '-' }}
                    </span>
                </div>
            </div>

            <div>
                <span class="block font-semibold text-slate-400 text-[11px] mb-0.5">Judul LHP</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white leading-relaxed">{{ $tindakLanjut->judul_lhp ?? 'Laporan Hasil Pengawasan Terkait SPT ' . $tindakLanjut->penugasan?->no_spt }}</p>
                @if($tindakLanjut->tgl_lhp)
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Tanggal Terbit LHP: {{ $tindakLanjut->tgl_lhp->format('d F Y') }}</span>
                @endif
            </div>

            @if($tindakLanjut->berkas_dasar_lhp)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-semibold">Lampiran Dokumen PDF LHP Resmi:</span>
                    <a href="{{ asset('storage/' . $tindakLanjut->berkas_dasar_lhp) }}" target="_blank" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center gap-1.5">
                        📄 Buka File PDF LHP
                    </a>
                </div>
            @endif
        </div>

        <!-- 2. Loop Seluruh Item Rekomendasi dalam LHP Ini (+ Dialog Interaktif OPD & Tim Pemeriksa) -->
        <div class="space-y-6">
            <h3 class="font-black text-slate-900 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                Daftar Rincian Temuan & Rekomendasi ({{ $lhpItems->count() }} Item):
            </h3>

            @foreach($lhpItems as $idx => $item)
                <div class="p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4" x-data="{ showFormInput: false }">
                    <!-- Item Header, Status Badge & Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                {{ $idx + 1 }}
                            </span>
                            <span class="font-black text-slate-900 dark:text-white text-sm">Item Rekomendasi #{{ $idx + 1 }}</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-slate-400">Target: {{ $item->tanggal_target ? $item->tanggal_target->format('d/m/Y') : '-' }}</span>

                            <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider
                                {{ $item->status_tindak_lanjut === 'selesai' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-200' : '' }}
                                {{ $item->status_tindak_lanjut === 'proses' || $item->status_tindak_lanjut === 'menunggu_verifikasi' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200' : '' }}
                                {{ $item->status_tindak_lanjut === 'belum' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200' : '' }}
                                {{ $item->status_tindak_lanjut === 'tdt' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200' : '' }}">
                                Status: {{ $item->status_label }}
                            </span>

                            <!-- ✏️ Tombol Edit Item Rekomendasi -->
                            <button type="button" onclick="openModalEditTl({
                                id: {{ $item->id }},
                                no_lhp: '{{ addslashes($item->no_lhp ?? '') }}',
                                judul_lhp: '{{ addslashes($item->judul_lhp ?? '') }}',
                                tgl_lhp: '{{ $item->tgl_lhp ? $item->tgl_lhp->format('Y-m-d') : '' }}',
                                uraian_temuan: '{{ addslashes($item->uraian_temuan) }}',
                                rekomendasi: '{{ addslashes($item->rekomendasi) }}',
                                nilai_diawasi_rp: {{ $item->nilai_diawasi_rp ?? 0 }},
                                nilai_rekomendasi_rp: {{ $item->nilai_rekomendasi_rp ?? 0 }},
                                tanggal_target: '{{ $item->tanggal_target ? $item->tanggal_target->format('Y-m-d') : '' }}',
                                status_tindak_lanjut: '{{ $item->status_tindak_lanjut }}'
                            })" class="px-3 py-1.5 bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 hover:bg-blue-100 rounded-xl text-xs font-bold flex items-center gap-1.5 border border-blue-200 dark:border-blue-800 shadow-xs" title="Edit Rincian Temuan & Rekomendasi ini">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit Item Rekomendasi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Temuan vs Rekomendasi Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-amber-50/60 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-900 space-y-1">
                            <span class="font-bold text-amber-900 dark:text-amber-200 text-xs block">Uraian Temuan:</span>
                            <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed whitespace-pre-line">{{ $item->uraian_temuan }}</p>
                        </div>

                        <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/30 rounded-2xl border border-emerald-200 dark:border-emerald-900 space-y-2">
                            <span class="font-bold text-emerald-900 dark:text-emerald-200 text-xs block">Rekomendasi Wajib:</span>
                            <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed whitespace-pre-line">{{ $item->rekomendasi }}</p>
                            <div class="pt-2 border-t border-emerald-200 dark:border-emerald-800 flex justify-between font-mono font-bold text-xs">
                                <span class="text-slate-600 dark:text-slate-400">Target Rp: <span class="text-emerald-700 dark:text-emerald-400">{{ $item->formatted_nilai_rp }}</span></span>
                                <span class="text-slate-600 dark:text-slate-400">Telah Disetor: <span class="text-purple-700 dark:text-purple-400">{{ $item->formatted_total_setor }}</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- 💬 Dialog Interaktif: Uraian Tindak Lanjut OPD & Catatan Evaluasi Pemeriksa -->
                    <div class="p-4 bg-blue-50/50 dark:bg-blue-950/20 rounded-2xl border border-blue-200 dark:border-blue-900 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-blue-900 dark:text-blue-200 text-xs block">
                                💬 Dialog Tindak Lanjut (Jawaban OPD & Evaluasi Pemeriksa):
                            </span>

                            <!-- Tombol Toggle Form Input Tindak Lanjut (OPD / Admin) -->
                            <button type="button" @click="showFormInput = !showFormInput" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>+ Input Jawaban / Perbaikan Tindak Lanjut OPD</span>
                            </button>
                        </div>

                        <!-- 📝 Form Input Uraian Tindak Lanjut, Setoran Kasda, & Upload Berkas Bukti -->
                        <div x-show="showFormInput" x-transition class="p-4.5 bg-white dark:bg-slate-900 rounded-2xl border-2 border-blue-400/80 shadow-md space-y-4">
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <span>✍️ Form Input Jawaban / Perbaikan Tindak Lanjut OPD</span>
                            </h4>

                            <form method="POST" action="{{ route('tindak-lanjut.store_respon', $item->id) }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                                @csrf

                                <!-- Uraian Perbaikan OPD -->
                                <div>
                                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Uraian / Jawaban Tindak Lanjut OPD <span class="text-rose-500">*</span></label>
                                    <textarea name="catatan_opd" required rows="3" placeholder="Tuliskan penjelasan perbaikan / tindak lanjut yang telah dilaksanakan OPD di sini..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-blue-500 font-medium"></textarea>
                                </div>

                                <!-- 💰 Input Rincian Penyetoran Kas Daerah (Pengembalian Finansial) -->
                                <div class="p-3.5 bg-emerald-50/60 dark:bg-emerald-950/30 rounded-xl border border-emerald-200 dark:border-emerald-900 space-y-3">
                                    <span class="font-bold text-emerald-900 dark:text-emerald-300 text-xs flex items-center gap-1.5">
                                        💰 Input Setoran ke Kas Daerah (Optional jika ada pengembalian finansial):
                                    </span>

                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-emerald-800 dark:text-emerald-400 mb-0.5">Nominal Setoran (Rp)</label>
                                            <input type="text" oninput="formatRupiahInput(this)" name="nilai_setor_rp" placeholder="0" class="w-full rounded-xl border-emerald-300 dark:border-emerald-800 bg-white dark:bg-slate-800 text-xs font-bold text-emerald-600">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">No. Referensi / NTPN</label>
                                            <input type="text" name="no_referensi_ntpn" placeholder="mis. NTPN 87123XX" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Nama Bank / Kas</label>
                                            <input type="text" name="nama_bank" placeholder="mis. Bank Jatim / Kasda" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Tanggal Penyetoran</label>
                                            <input type="date" name="tgl_setor" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload File Bukti & Status Update -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Unggah Berkas Bukti (PDF / Gambar / Zip)</label>
                                        <input type="file" name="berkas_bukti" accept=".pdf,.jpg,.jpeg,.png,.zip" class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>

                                    <div>
                                        <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Update Status TL (Optional)</label>
                                        <select name="status_tindak_lanjut" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                                            <option value="">-- Biarkan Status Saat Ini --</option>
                                            <option value="proses">BELUM SESUAI</option>
                                            <option value="selesai">SESUAI</option>
                                            <option value="tdt">TIDAK DAPAT DITINDAKLANJUTI</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="pt-2 flex items-center justify-end gap-2">
                                    <button type="button" @click="showFormInput = false" class="px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs">Simpan Jawaban Tindak Lanjut</button>
                                </div>
                            </form>
                        </div>

                        <!-- Lista Riwayat Tanggapan OPD & Evaluasi Pemeriksa -->
                        @forelse($item->buktiTindakLanjut as $bIdx => $bukti)
                            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3" x-data="{ showFormEvaluasi: false }">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 dark:text-white text-xs">Respon OPD #{{ $bIdx + 1 }}: {{ $bukti->pengunggah?->name ?? 'Pengelola OPD / Pemeriksa' }}</span>
                                        <span class="text-[10px] text-slate-400">({{ $bukti->created_at->format('d/m/Y H:i') }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 font-extrabold rounded-full text-[10px]
                                            {{ $bukti->status_verifikasi === 'diterima' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200' : '' }}
                                            {{ $bukti->status_verifikasi === 'ditolak' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200' : '' }}
                                            {{ $bukti->status_verifikasi === 'tdt' ? 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border border-slate-300' : '' }}
                                            {{ $bukti->status_verifikasi === 'menunggu' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200' : '' }}">
                                            Status Evaluasi: 
                                            @if($bukti->status_verifikasi === 'diterima')
                                                Selesai Evaluasi (Diterima oleh Tim)
                                            @elseif($bukti->status_verifikasi === 'ditolak')
                                                Memerlukan Perbaikan (Catatan Evaluasi Tim)
                                            @elseif($bukti->status_verifikasi === 'tdt')
                                                TIDAK DAPAT DITINDAKLANJUTI (TDT)
                                            @else
                                                Belum Dievaluasi oleh Tim
                                            @endif
                                        </span>

                                        <!-- Tombol Beri Evaluasi Kekurangan bagi Admin/Pemeriksa -->
                                        @if(auth()->user() && !auth()->user()->hasRole(['opd']))
                                            <button type="button" @click="showFormEvaluasi = !showFormEvaluasi" class="px-2.5 py-1 bg-amber-50 text-amber-800 hover:bg-amber-100 font-bold rounded-xl text-[10px] border border-amber-300 flex items-center gap-1 shadow-xs">
                                                <span>✍️ Evaluasi / Catat Kekurangan</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Uraian / Jawaban Tindak Lanjut OPD:</span>
                                    <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed whitespace-pre-line">{{ $bukti->catatan_opd ?? '-' }}</p>
                                </div>

                                @if($bukti->arsipDigital->count() > 0)
                                    <div class="pt-1.5 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-2">
                                        <span class="text-[10px] text-slate-400 font-bold">Berkas Bukti Terlampir:</span>
                                        @foreach($bukti->arsipDigital as $fileBukti)
                                            <a href="{{ route('arsip.download', $fileBukti->id) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950 text-slate-700 dark:text-slate-300 hover:text-blue-600 font-semibold rounded-lg text-[10px] inline-flex items-center gap-1 border border-slate-200 dark:border-slate-700 shadow-2xs">
                                                📎 {{ $fileBukti->nama_file }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- ⚠️ Highlight Keterangan Catatan Evaluasi Kekurangan dari Tim Pemeriksa -->
                                @if($bukti->catatan_verifikasi)
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-300 dark:border-amber-900 space-y-1">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-black text-amber-900 dark:text-amber-300 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <span>Keterangan Evaluasi / Catatan Kekurangan dari Tim Pemeriksa:</span>
                                            </span>
                                            @if($bukti->diverifikasi_pada)
                                                <span class="text-[10px] text-slate-400 font-medium">{{ $bukti->diverifikasi_pada->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-slate-800 dark:text-slate-200 font-bold leading-relaxed whitespace-pre-line text-xs pl-5.5">{{ $bukti->catatan_verifikasi }}</p>
                                    </div>
                                @endif

                                <!-- ✍️ Form Evaluasi / Catat Kekurangan oleh Tim Pemeriksa (Termasuk opsi TDT) -->
                                <div x-show="showFormEvaluasi" x-transition class="p-3.5 bg-amber-50/80 dark:bg-amber-950/60 rounded-xl border-2 border-amber-400 text-xs space-y-3 mt-2">
                                    <h5 class="font-bold text-amber-900 dark:text-amber-200 text-xs">Evaluasi & Beri Catatan Kekurangan untuk OPD</h5>
                                    
                                    <form method="POST" action="{{ route('tindak-lanjut.bukti.verifikasi', $bukti->id) }}" class="space-y-3">
                                        @csrf
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Hasil Evaluasi Pemeriksa</label>
                                                <select name="status_verifikasi" required class="w-full rounded-xl border-amber-300 bg-white dark:bg-slate-800 text-xs font-bold">
                                                    <option value="ditolak" {{ $bukti->status_verifikasi === 'ditolak' ? 'selected' : '' }}>Memerlukan Perbaikan (Catatan Evaluasi Tim)</option>
                                                    <option value="diterima" {{ $bukti->status_verifikasi === 'diterima' ? 'selected' : '' }}>Selesai Evaluasi (Diterima oleh Tim)</option>
                                                    <option value="tdt" {{ $bukti->status_verifikasi === 'tdt' ? 'selected' : '' }}>TIDAK DAPAT DITINDAKLANJUTI (TDT)</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Keterangan Kekurangan untuk OPD <span class="text-rose-500">*</span></label>
                                                <textarea name="catatan_verifikasi" required rows="2" placeholder="Tuliskan secara jelas titik kekurangan OPD yang harus ditindaklanjuti ulang di sini..." class="w-full rounded-xl border-amber-300 bg-white dark:bg-slate-800 text-xs focus:ring-amber-500 font-medium">{{ $bukti->catatan_verifikasi }}</textarea>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <button type="button" @click="showFormEvaluasi = false" class="px-3 py-1 bg-slate-200 text-slate-700 font-semibold rounded-lg">Batal</button>
                                            <button type="submit" class="px-4 py-1 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg shadow-xs">Simpan Catatan Evaluasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs italic">Belum ada uraian tindak lanjut atau tanggapan yang diisi oleh OPD untuk rekomendasi ini.</p>
                        @endforelse
                    </div>

                    <!-- Rincian Penyetoran Kas Daerah (NTPN) untuk Item Ini -->
                    @if($item->rincianPenyetoran->count() > 0)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-xs block">Rincian Penyetoran Kas Daerah (NTPN):</span>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-[11px]">
                                    <thead class="bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold uppercase">
                                        <tr>
                                            <th class="p-1.5">No. Referensi / NTPN</th>
                                            <th class="p-1.5">Bank</th>
                                            <th class="p-1.5">Nilai Setoran</th>
                                            <th class="p-1.5 text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 font-mono">
                                        @foreach($item->rincianPenyetoran as $setor)
                                            <tr>
                                                <td class="p-1.5 font-bold text-slate-800 dark:text-slate-200">{{ $setor->no_referensi_ntpn ?? '-' }}</td>
                                                <td class="p-1.5 text-slate-600 dark:text-slate-300">{{ $setor->nama_bank ?? '-' }}</td>
                                                <td class="p-1.5 font-bold text-emerald-600">{{ $setor->formatted_nilai_setor }}</td>
                                                <td class="p-1.5 text-center text-slate-500">{{ $setor->tgl_setor->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Edit Temuan & Rekomendasi (Wider max-w-4xl) -->
    <div id="modalEditTindakLanjut" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-4xl w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Item Temuan, Rekomendasi & LHP</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Perbarui rincian LHP, uraian temuan, rekomendasi wajib, dan berkas dasar.</p>
                </div>
                <button onclick="document.getElementById('modalEditTindakLanjut').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEditTindakLanjut" method="POST" action="" enctype="multipart/form-data" class="space-y-4 mt-4 text-xs">
                @csrf
                @method('PUT')

                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-4">
                        <label class="block font-bold mb-1 text-blue-700 dark:text-blue-400">Nomor LHP</label>
                        <input type="text" id="editNoLhp" name="no_lhp" placeholder="mis. 700/85/LHP/406.008/2026" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                    </div>

                    <div class="sm:col-span-5">
                        <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Judul LHP</label>
                        <input type="text" id="editJudulLhp" name="judul_lhp" placeholder="mis. LHP atas Pengelolaan Keuangan OPD..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Tanggal LHP</label>
                        <input type="date" id="editTglLhp" name="tgl_lhp" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Uraian Temuan <span class="text-rose-500">*</span></label>
                        <textarea id="editUraianTemuan" name="uraian_temuan" required rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Rekomendasi Wajib <span class="text-rose-500">*</span></label>
                        <textarea id="editRekomendasi" name="rekomendasi" required rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-blue-700 dark:text-blue-400">Nilai Yang Diawasi (Rp)</label>
                        <input type="text" oninput="formatRupiahInput(this)" id="editNilaiDiawasiRp" name="nilai_diawasi_rp" placeholder="0" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-blue-600">
                        <p class="text-[9px] text-slate-400 mt-0.5">(Jika Tidak Ada Berikan Input 0)</p>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-emerald-700 dark:text-emerald-400">Nilai Rekomendasi (Rp)</label>
                        <input type="text" oninput="formatRupiahInput(this)" id="editNilaiRp" name="nilai_rekomendasi_rp" placeholder="0" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-emerald-600">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Target Waktu</label>
                        <input type="date" id="editTanggalTarget" name="tanggal_target" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Status TL</label>
                        <select id="editStatusTl" name="status_tindak_lanjut" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                            <option value="selesai">SESUAI</option>
                            <option value="proses">BELUM SESUAI</option>
                            <option value="belum">BELUM DITINDAKLANJUTI</option>
                            <option value="tdt">TIDAK DAPAT DITINDAKLANJUTI</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Ganti Berkas Dasar LHP (File PDF Optional)</label>
                    <input type="file" name="berkas_dasar_lhp" accept=".pdf" class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditTindakLanjut').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 💰 Dynamic Live Currency Format Input (Ribuan Titik Koma)
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

        function openModalEditTl(data) {
            const form = document.getElementById('formEditTindakLanjut');
            form.action = '/tindak-lanjut/' + data.id;
            document.getElementById('editNoLhp').value = data.no_lhp || '';
            document.getElementById('editJudulLhp').value = data.judul_lhp || '';
            document.getElementById('editTglLhp').value = data.tgl_lhp || '';
            document.getElementById('editUraianTemuan').value = data.uraian_temuan;
            document.getElementById('editRekomendasi').value = data.rekomendasi;
            if (data.nilai_diawasi_rp) {
                document.getElementById('editNilaiDiawasiRp').value = new Intl.NumberFormat('id-ID').format(data.nilai_diawasi_rp);
            } else {
                document.getElementById('editNilaiDiawasiRp').value = '0';
            }
            if (data.nilai_rekomendasi_rp) {
                document.getElementById('editNilaiRp').value = new Intl.NumberFormat('id-ID').format(data.nilai_rekomendasi_rp);
            } else {
                document.getElementById('editNilaiRp').value = '';
            }
            document.getElementById('editTanggalTarget').value = data.tanggal_target;
            document.getElementById('editStatusTl').value = data.status_tindak_lanjut === 'dikembalikan' ? 'proses' : data.status_tindak_lanjut;
            document.getElementById('modalEditTindakLanjut').classList.remove('hidden');
        }
    </script>
</x-app-layout>
