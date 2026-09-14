<x-app-layout>
    <x-slot name="header">
        Dokumen Ikhtisar Laporan Hasil Pengawasan (ILHP)
    </x-slot>

    <div class="space-y-6">
        <!-- Top Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('ikhtisar-laporan.index', ['tahun' => $ikhtisarLaporan->tahun]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Ikhtisar
                </a>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ $ikhtisarLaporan->judul }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Periode: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ikhtisarLaporan->periode_label }}</span> | 
                    Tanggal: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : '-' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('ikhtisar-laporan.cetak', $ikhtisarLaporan) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-md transition-all">
                    <span>🖨️ Cetak / Unduh PDF Resmi</span>
                </a>

                @hasanyrole('admin|sekretariat|superadmin')
                <a href="{{ route('ikhtisar-laporan.edit', $ikhtisarLaporan) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl transition-all">
                    <span>✏️ Edit Narasi Bab V</span>
                </a>
                @endhasanyrole
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- DOKUMEN SISTEMATIKA LENGKAP 5 BAB -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
            
            <!-- COVER / HEADER DOKUMEN -->
            <div class="p-6 sm:p-8 bg-slate-50/50 dark:bg-slate-800/30 text-center space-y-2 border-b border-slate-200 dark:border-slate-800">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Inspektorat Daerah Kabupaten Trenggalek</p>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase">{{ $ikhtisarLaporan->judul }}</h1>
                @if($ikhtisarLaporan->nomor_surat)
                    <p class="text-xs font-mono text-slate-500">Nomor: {{ $ikhtisarLaporan->nomor_surat }}</p>
                @endif
                <p class="text-xs text-slate-400">Disampaikan kepada: <strong>Bupati Trenggalek</strong></p>
            </div>

            <!-- BAB I: INFORMASI UMUM -->
            <div class="p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shrink-0">I</span>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">BAB I INFORMASI UMUM</h3>
                </div>

                <!-- A. Dasar Hukum -->
                <div class="space-y-2 pl-9">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">A. Dasar Hukum Penyelenggaraan Pengawasan</h4>
                    <ol class="list-decimal list-outside pl-4 space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        @foreach($compiledData['dasarHukum'] as $rb)
                            <li>{{ $rb->format_dasar_spt ?? ($rb->nomor_regulasi . ' tentang ' . $rb->judul) }}</li>
                        @endforeach
                    </ol>
                </div>

                <!-- B. Struktur Organisasi & SDM -->
                <div class="space-y-2 pl-9 pt-2">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">B. Struktur Organisasi & Kapasitas SDM Pengawasan</h4>
                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                        Pelaksanaan pembinaan dan pengawasan internal di lingkungan Pemerintah Kabupaten Trenggalek didukung oleh <strong>{{ $compiledData['totalPersonilAktif'] }} personil aparatur pengawasan</strong> yang terbagi ke dalam Sekretariat dan <strong>{{ count($compiledData['irbans']) }} unit Inspektur Pembantu (Irban)</strong> dengan rincian:
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Auditor Fungsional</span>
                            <p class="text-lg font-black text-slate-800 dark:text-white mt-0.5">{{ $compiledData['totalAuditor'] }} Pegawai</p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Pejabat PPUPD</span>
                            <p class="text-lg font-black text-slate-800 dark:text-white mt-0.5">{{ $compiledData['totalPpupd'] }} Pegawai</p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Staf Administrasi / IT</span>
                            <p class="text-lg font-black text-slate-800 dark:text-white mt-0.5">{{ $compiledData['totalStaf'] }} Pegawai</p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Unit Irban Teknis</span>
                            <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ count($compiledData['irbans']) }} Unit Kerja</p>
                        </div>
                    </div>
                </div>

                <!-- C. Tujuan Penyusunan Ikhtisar -->
                <div class="space-y-2 pl-9 pt-2">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">C. Tujuan Penyusunan Ikhtisar Pelaporan</h4>
                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                        Ikhtisar Laporan Hasil Pengawasan (ILHP) ini disusun sebagai wujud pertanggungjawaban akuntabilitas kinerja pengawasan internal Inspektorat Daerah kepada Kepala Daerah (Bupati), serta memberikan rekomendasi kebijakan strategis atas perbaikan tata kelola, efisiensi anggaran, dan pengendalian risiko di seluruh Perangkat Daerah Kabupaten Trenggalek.
                    </p>
                </div>

                <!-- D. Program Pengawasan dan Realisasinya -->
                <div class="space-y-2 pl-9 pt-2">
                    <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">D. Program Pengawasan Tahunan (PKPT) dan Realisasinya</h4>
                    <p class="text-xs text-slate-700 dark:text-slate-300">
                        Dari total rencana program pengawasan PKPT Tahun {{ $ikhtisarLaporan->tahun }} sebanyak <strong>{{ $compiledData['totalTargetPkppt'] }} rencana kegiatan</strong> (target {{ $compiledData['totalTargetLaporan'] }} laporan), hingga periode {{ $ikhtisarLaporan->periode_label }} telah diterbitkan sebanyak <strong>{{ $compiledData['totalSptTerbit'] }} Surat Perintah Tugas (SPT)</strong> dengan tingkat realisasi capaian sebesar <strong>{{ $compiledData['persenRealisasiPkppt'] }}%</strong>.
                    </p>
                </div>
            </div>

            <!-- BAB II: HASIL PENGAWASAN PER JENIS -->
            <div class="p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shrink-0">II</span>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">BAB II HASIL PENGAWASAN (REALISASI PER JENIS PENGAWASAN)</h3>
                </div>

                <div class="pl-9 space-y-6 text-xs">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        Selama periode {{ $ikhtisarLaporan->periode_label }} Tahun Anggaran {{ $ikhtisarLaporan->tahun }}, Inspektorat Daerah telah merealisasikan penugasan pengawasan *Assurance* dan *Consulting* dengan rincian sebagai berikut:
                    </p>

                    <!-- A. Audit -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase text-emerald-600 dark:text-emerald-400">
                            A. AUDIT (Kinerja & Dengan Tujuan Tertentu)
                        </h4>
                        
                        <!-- 1. Audit Kinerja -->
                        <div class="pl-4 space-y-2">
                            <h5 class="font-bold text-slate-800 dark:text-slate-200">1. Audit Kinerja ({{ count($compiledData['kategoriAudit']['audit_kinerja'] ?? []) }} Penugasan)</h5>
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                        <tr>
                                            <th class="px-3 py-2">No. SPT</th>
                                            <th class="px-3 py-2">Uraian Penugasan</th>
                                            <th class="px-3 py-2">Objek / OPD</th>
                                            <th class="px-3 py-2">Irban</th>
                                            <th class="px-3 py-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @forelse($compiledData['kategoriAudit']['audit_kinerja'] ?? [] as $item)
                                            <tr>
                                                <td class="px-3 py-2 font-mono font-bold">{{ $item->no_spt }}</td>
                                                <td class="px-3 py-2">{{ $item->uraian_penugasan }}</td>
                                                <td class="px-3 py-2">{{ $item->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                                <td class="px-3 py-2">{{ $item->irban_list_names }}</td>
                                                <td class="px-3 py-2"><span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $item->status_label }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="px-3 py-3 text-center text-slate-400">Tidak ada penugasan Audit Kinerja pada periode ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 2. Audit Dengan Tujuan Tertentu -->
                        <div class="pl-4 space-y-2 pt-2">
                            <h5 class="font-bold text-slate-800 dark:text-slate-200">2. Audit Dengan Tujuan Tertentu / Probity Audit ({{ count($compiledData['kategoriAudit']['audit_dtt'] ?? []) }} Penugasan)</h5>
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                        <tr>
                                            <th class="px-3 py-2">No. SPT</th>
                                            <th class="px-3 py-2">Uraian Penugasan</th>
                                            <th class="px-3 py-2">Objek / OPD</th>
                                            <th class="px-3 py-2">Irban</th>
                                            <th class="px-3 py-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @forelse($compiledData['kategoriAudit']['audit_dtt'] ?? [] as $item)
                                            <tr>
                                                <td class="px-3 py-2 font-mono font-bold">{{ $item->no_spt }}</td>
                                                <td class="px-3 py-2">{{ $item->uraian_penugasan }}</td>
                                                <td class="px-3 py-2">{{ $item->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                                <td class="px-3 py-2">{{ $item->irban_list_names }}</td>
                                                <td class="px-3 py-2"><span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $item->status_label }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="px-3 py-3 text-center text-slate-400">Tidak ada penugasan Audit ADTT pada periode ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- B. Reviu -->
                    <div class="space-y-2 pt-2">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase text-emerald-600 dark:text-emerald-400">
                            B. REVIU ({{ count($compiledData['kategoriReviu']) }} Penugasan)
                        </h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <th class="px-3 py-2">No. SPT</th>
                                        <th class="px-3 py-2">Uraian Reviu</th>
                                        <th class="px-3 py-2">Objek Sasaran</th>
                                        <th class="px-3 py-2">Irban Pelaksana</th>
                                        <th class="px-3 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($compiledData['kategoriReviu'] as $item)
                                        <tr>
                                            <td class="px-3 py-2 font-mono font-bold">{{ $item->no_spt }}</td>
                                            <td class="px-3 py-2">{{ $item->uraian_penugasan }}</td>
                                            <td class="px-3 py-2">{{ $item->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                            <td class="px-3 py-2">{{ $item->irban_list_names }}</td>
                                            <td class="px-3 py-2"><span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $item->status_label }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-3 py-3 text-center text-slate-400">Tidak ada penugasan Reviu pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- C. Evaluasi -->
                    <div class="space-y-2 pt-2">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase text-emerald-600 dark:text-emerald-400">
                            C. EVALUASI ({{ count($compiledData['kategoriEvaluasi']) }} Penugasan)
                        </h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <th class="px-3 py-2">No. SPT</th>
                                        <th class="px-3 py-2">Uraian Evaluasi</th>
                                        <th class="px-3 py-2">Objek Sasaran</th>
                                        <th class="px-3 py-2">Irban Pelaksana</th>
                                        <th class="px-3 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($compiledData['kategoriEvaluasi'] as $item)
                                        <tr>
                                            <td class="px-3 py-2 font-mono font-bold">{{ $item->no_spt }}</td>
                                            <td class="px-3 py-2">{{ $item->uraian_penugasan }}</td>
                                            <td class="px-3 py-2">{{ $item->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                            <td class="px-3 py-2">{{ $item->irban_list_names }}</td>
                                            <td class="px-3 py-2"><span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $item->status_label }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-3 py-3 text-center text-slate-400">Tidak ada penugasan Evaluasi pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- D. Pemantauan & E. Lainnya -->
                    <div class="space-y-2 pt-2">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase text-emerald-600 dark:text-emerald-400">
                            D. PEMANTAUAN & E. KEGIATAN PENGAWASAN LAINNYA ({{ count($compiledData['kategoriPemantauan']) + count($compiledData['kategoriLainnya']) }} Penugasan)
                        </h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <th class="px-3 py-2">No. SPT</th>
                                        <th class="px-3 py-2">Uraian Penugasan</th>
                                        <th class="px-3 py-2">Kategori</th>
                                        <th class="px-3 py-2">Objek Sasaran</th>
                                        <th class="px-3 py-2">Irban</th>
                                        <th class="px-3 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach(array_merge($compiledData['kategoriPemantauan'], $compiledData['kategoriLainnya']) as $item)
                                        <tr>
                                            <td class="px-3 py-2 font-mono font-bold">{{ $item->no_spt }}</td>
                                            <td class="px-3 py-2">{{ $item->uraian_penugasan }}</td>
                                            <td class="px-3 py-2 font-semibold text-slate-500">{{ $item->jenisPenugasan?->nama ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $item->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                            <td class="px-3 py-2">{{ $item->irban_list_names }}</td>
                                            <td class="px-3 py-2"><span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ $item->status_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAB III: HASIL PEMANTAUAN TINDAK LANJUT -->
            <div class="p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shrink-0">III</span>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">BAB III HASIL PEMANTAUAN TINDAK LANJUT</h3>
                </div>

                <div class="pl-9 space-y-4 text-xs">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        Rekapitulasi perkembangan pemantauan tindak lanjut rekomendasi hasil pengawasan internal (APIP) dan BPK-RI selama periode ini mencatat total <strong>{{ $compiledData['tlCountTotal'] }} butir rekomendasi</strong> dengan capaian:
                    </p>

                    <!-- Summary Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800">
                            <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 uppercase">Selesai (SS)</span>
                            <p class="text-base font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $compiledData['tlCountSelesai'] }} Rekomendasi ({{ $compiledData['tlPersenSelesai'] }}%)</p>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-800">
                            <span class="text-[10px] font-bold text-blue-800 dark:text-blue-300 uppercase">Dalam Proses (BS)</span>
                            <p class="text-base font-black text-blue-600 dark:text-blue-400 mt-0.5">{{ $compiledData['tlCountBelumSesuai'] }} Rekomendasi</p>
                        </div>
                        <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800">
                            <span class="text-[10px] font-bold text-rose-800 dark:text-rose-300 uppercase">Belum TL (BTL)</span>
                            <p class="text-base font-black text-rose-600 dark:text-rose-400 mt-0.5">{{ $compiledData['tlCountBelum'] }} Rekomendasi</p>
                        </div>
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-800">
                            <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300 uppercase">Realisasi Setor Kasda</span>
                            <p class="text-xs font-black text-amber-600 dark:text-amber-400 mt-0.5">Rp {{ number_format($compiledData['tlTotalSetorRp'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Tabel Matriks per OPD -->
                    <div class="pt-2">
                        <h4 class="font-bold text-slate-900 dark:text-white text-xs uppercase mb-2">Matriks Kepatuhan Tindak Lanjut per Perangkat Daerah (OPD)</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <th class="px-3 py-2">No</th>
                                        <th class="px-3 py-2">Nama Perangkat Daerah / OPD</th>
                                        <th class="px-3 py-2 text-center">Total Rekomendasi</th>
                                        <th class="px-3 py-2 text-center text-emerald-600">Selesai (SS)</th>
                                        <th class="px-3 py-2 text-center text-blue-600">Proses (BS)</th>
                                        <th class="px-3 py-2 text-center text-rose-600">Belum (BTL)</th>
                                        <th class="px-3 py-2 text-center">% Selesai</th>
                                        <th class="px-3 py-2 text-right">Target (Rp)</th>
                                        <th class="px-3 py-2 text-right text-emerald-600">Setor Kasda (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($compiledData['matrixOpd'] as $idx => $opd)
                                        <tr>
                                            <td class="px-3 py-2 text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">{{ $opd->nama_opd }}</td>
                                            <td class="px-3 py-2 text-center font-bold">{{ $opd->total }}</td>
                                            <td class="px-3 py-2 text-center text-emerald-600 font-bold">{{ $opd->ss }}</td>
                                            <td class="px-3 py-2 text-center text-blue-600">{{ $opd->bs }}</td>
                                            <td class="px-3 py-2 text-center text-rose-600">{{ $opd->btl }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $opd->persen >= 75 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $opd->persen }}%
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-right font-mono">{{ number_format($opd->target_rp, 0, ',', '.') }}</td>
                                            <td class="px-3 py-2 text-right font-mono font-bold text-emerald-600">{{ number_format($opd->setor_rp, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="px-3 py-3 text-center text-slate-400">Tidak ada temuan tindak lanjut pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAB IV: PENANGANAN PENGADUAN MASYARAKAT -->
            <div class="p-6 sm:p-8 space-y-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shrink-0">IV</span>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">BAB IV HASIL PENANGANAN PENGADUAN MASYARAKAT</h3>
                </div>

                <div class="pl-9 space-y-3 text-xs">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        Selama periode {{ $ikhtisarLaporan->periode_label }} Tahun {{ $ikhtisarLaporan->tahun }}, Inspektorat Daerah telah menindaklanjuti dan mengklarifikasi <strong>{{ count($compiledData['sptDumas']) }} penugasan khusus / aduan masyarakat</strong> yang bersumber dari kanal SP4N LAPOR, surat aduan langsung masyarakat, maupun pelimpahan dari Aparat Penegak Hukum (APH):
                    </p>

                    @if(count($compiledData['sptDumas']) > 0)
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-bold uppercase text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <th class="px-3 py-2">No. SPT</th>
                                        <th class="px-3 py-2">Uraian Aduan / Penugasan</th>
                                        <th class="px-3 py-2">Sumber</th>
                                        <th class="px-3 py-2">Objek yang Diadukan</th>
                                        <th class="px-3 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($compiledData['sptDumas'] as $spt)
                                        <tr>
                                            <td class="px-3 py-2 font-mono font-bold">{{ $spt->no_spt }}</td>
                                            <td class="px-3 py-2">{{ $spt->uraian_penugasan }}</td>
                                            <td class="px-3 py-2 font-semibold text-slate-600">{{ $spt->sumberPenugasan?->nama ?? 'Pengaduan' }}</td>
                                            <td class="px-3 py-2">{{ $spt->objekPenugasan->pluck('nama')->implode(', ') }}</td>
                                            <td class="px-3 py-2"><span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700">{{ $spt->status_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-slate-500 text-xs text-center border border-slate-200 dark:border-slate-700">
                            Tidak ada penugasan investigasi / pengaduan masyarakat yang terdaftar pada periode ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- BAB V: SIMPULAN, HAMBATAN DAN REKOMENDASI -->
            <div class="p-6 sm:p-8 space-y-5 bg-emerald-50/20 dark:bg-emerald-950/10">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs shrink-0">V</span>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">BAB V SIMPULAN, HAMBATAN DAN REKOMENDASI</h3>
                </div>

                <div class="pl-9 space-y-4 text-xs">
                    <!-- A. Simpulan -->
                    <div class="space-y-1">
                        <h4 class="font-bold text-slate-900 dark:text-white uppercase text-emerald-700 dark:text-emerald-400">A. Simpulan</h4>
                        <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 leading-relaxed text-slate-700 dark:text-slate-300 whitespace-pre-line">
                            {{ $ikhtisarLaporan->simpulan ?: 'Belum ada simpulan yang dicatat.' }}
                        </div>
                    </div>

                    <!-- B. Hambatan -->
                    <div class="space-y-1 pt-2">
                        <h4 class="font-bold text-slate-900 dark:text-white uppercase text-emerald-700 dark:text-emerald-400">B. Hambatan</h4>
                        <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 leading-relaxed text-slate-700 dark:text-slate-300 whitespace-pre-line">
                            {{ $ikhtisarLaporan->hambatan ?: 'Tidak ada hambatan signifikan yang dicatat.' }}
                        </div>
                    </div>

                    <!-- C. Rekomendasi -->
                    <div class="space-y-1 pt-2">
                        <h4 class="font-bold text-slate-900 dark:text-white uppercase text-emerald-700 dark:text-emerald-400">C. Rekomendasi untuk Bupati Trenggalek</h4>
                        <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 leading-relaxed text-slate-700 dark:text-slate-300 whitespace-pre-line">
                            {{ $ikhtisarLaporan->rekomendasi ?: 'Belum ada rekomendasi kebijakan yang dicatat.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- TANDA TANGAN INSPEKTUR -->
            <div class="p-8 sm:p-12 text-xs flex justify-end">
                <div class="w-72 text-center space-y-16">
                    <div>
                        <p class="text-slate-500">Trenggalek, {{ $ikhtisarLaporan->tanggal_laporan ? $ikhtisarLaporan->tanggal_laporan->translatedFormat('d F Y') : date('d F Y') }}</p>
                        <p class="font-bold text-slate-900 dark:text-white mt-1">INSPEKTUR KABUPATEN TRENGGALEK</p>
                    </div>

                    <div>
                        <p class="font-black text-slate-900 dark:text-white text-sm underline">{{ $inspektur?->nama ?? 'Drs. PIMPINAN INSPEKTORAT, M.Si' }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">NIP. {{ $inspektur?->nip ?? '19700101 199503 1 001' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
