<x-app-layout>
    <x-slot name="header">
        Ikhtisar Laporan Hasil Pengawasan (ILHP) | Laporan Berkala Bupati
    </x-slot>

    <div class="space-y-6">
        <!-- Header Actions & Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-ink tracking-tight">Daftar Dokumen Ikhtisar Laporan (ILHP)</h2>
                <p class="text-xs text-ink-secondary mt-0.5">Generator otomatis laporan eksekutif Triwulanan, Semesteran, dan Tahunan untuk Bupati Trenggalek.</p>
            </div>

            @hasanyrole('admin|administrator|pelaporan|sekretaris|inspektur|superadmin')
            <div class="flex items-center gap-2">
                <a href="{{ route('ikhtisar-laporan.create', ['tahun' => $tahun]) }}" class="ledger-btn-primary !text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Generate Ikhtisar Baru</span>
                </a>
            </div>
            @endhasanyrole
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[10px] text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Filter Form -->
        <div class="bg-surface p-4 sm:p-5 rounded-[10px] border border-hairline">
            <form method="GET" action="{{ route('ikhtisar-laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                <div class="sm:col-span-4">
                    <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Tahun Anggaran</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2.5">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block font-semibold text-xs text-ink-secondary uppercase mb-1">Periode Laporan</label>
                    <select name="periode" onchange="this.form.submit()" class="w-full text-xs font-semibold rounded-[6px] border-hairline-strong bg-surface text-ink px-3 py-2.5">
                        <option value="">-- Semua Periode --</option>
                        <option value="triwulan_1" {{ $periode === 'triwulan_1' ? 'selected' : '' }}>Triwulan I (Jan - Mar)</option>
                        <option value="triwulan_2" {{ $periode === 'triwulan_2' ? 'selected' : '' }}>Triwulan II (Apr - Jun)</option>
                        <option value="triwulan_3" {{ $periode === 'triwulan_3' ? 'selected' : '' }}>Triwulan III (Jul - Sep)</option>
                        <option value="triwulan_4" {{ $periode === 'triwulan_4' ? 'selected' : '' }}>Triwulan IV (Okt - Des)</option>
                        <option value="semester_1" {{ $periode === 'semester_1' ? 'selected' : '' }}>Semester I (Jan - Jun)</option>
                        <option value="semester_2" {{ $periode === 'semester_2' ? 'selected' : '' }}>Semester II (Jul - Des)</option>
                        <option value="tahunan" {{ $periode === 'tahunan' ? 'selected' : '' }}>Tahunan (Jan - Des)</option>
                    </select>
                </div>

                <div class="sm:col-span-4 flex items-end gap-2 pt-2 sm:pt-0">
                    <button type="submit" class="ledger-btn-primary w-full !text-xs">
                        Terapkan Filter
                    </button>
                    @if($periode)
                        <a href="{{ route('ikhtisar-laporan.index', ['tahun' => $tahun]) }}" class="ledger-btn-secondary !text-xs whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table List Dokumen -->
        <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-ink-secondary">
                    <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider text-[11px] border-b border-hairline">
                        <tr>
                            <th class="px-4 py-3.5">Dokumen Ikhtisar</th>
                            <th class="px-4 py-3.5">Periode & Tahun</th>
                            <th class="px-4 py-3.5">Tanggal Ditetapkan</th>
                            <th class="px-4 py-3.5">Penyusun / Kasubag</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline font-medium">
                        @forelse($listLaporan as $lap)
                            <tr class="hover:bg-surface-subtle/60 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-ink text-sm">
                                        <a href="{{ route('ikhtisar-laporan.show', $lap) }}" class="hover:text-brand-ink">
                                            {{ $lap->judul }}
                                        </a>
                                    </div>
                                    @if($lap->nomor_surat)
                                        <p class="text-[11px] text-ink-muted mt-0.5 font-mono">No: {{ $lap->nomor_surat }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-[4px] font-semibold text-[11px]">
                                        {{ $lap->periode_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-ink tabular-nums">
                                    {{ $lap->tanggal_laporan ? $lap->tanggal_laporan->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-ink">
                                    {{ $lap->pembuat?->nama_display ?? 'Administrator' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($lap->status === 'final')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-[4px] font-semibold text-[11px] uppercase">
                                            Final Resmi
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-[4px] font-semibold text-[11px] uppercase">
                                            Draf Konsep
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-1.5">
                                    <a href="{{ route('ikhtisar-laporan.show', $lap) }}" class="inline-flex items-center px-2.5 py-1.5 bg-brand hover:bg-brand-hover text-white rounded-[6px] text-xs font-semibold transition-colors">
                                        Lihat
                                    </a>
                                    <a href="{{ route('ikhtisar-laporan.cetak', $lap) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-surface hover:bg-surface-subtle text-ink border border-hairline-strong rounded-[6px] text-xs font-semibold transition-colors">
                                        Cetak / PDF
                                    </a>
                                    @hasanyrole('admin|administrator|pelaporan|sekretaris|inspektur|superadmin')
                                    <a href="{{ route('ikhtisar-laporan.edit', $lap) }}" class="inline-flex items-center px-2.5 py-1.5 bg-surface-subtle hover:bg-hairline text-ink rounded-[6px] text-xs font-semibold transition-colors">
                                        Edit
                                    </a>
                                    @endhasanyrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-ink-muted">
                                    <p class="font-semibold text-ink">Belum ada dokumen Ikhtisar Hasil Pengawasan (ILHP) tersimpan untuk tahun {{ $tahun }}.</p>
                                    <p class="text-xs text-ink-secondary mt-1">Klik tombol <span class="font-semibold text-brand-ink">"Generate Ikhtisar Baru"</span> di atas untuk mengompilasi laporan berkala secara instan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listLaporan->hasPages())
                <div class="p-4 border-t border-hairline">
                    {{ $listLaporan->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
