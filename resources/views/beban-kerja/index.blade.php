<x-app-layout>
    <x-slot name="header">
        Modul Beban Kerja Personil
    </x-slot>

    <!-- Executive KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface rounded-[10px] p-4 border border-hairline flex items-center gap-3.5">
            <div class="w-10 h-10 bg-surface-subtle text-ink-secondary border border-hairline rounded-[6px] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted">Total Personil</p>
                <p class="text-xl font-bold text-ink tabular-nums">{{ $countTotal }} <span class="text-xs font-medium text-ink-muted">Pegawai</span></p>
            </div>
        </div>

        <a href="{{ request()->fullUrlWithQuery(['status_ketersediaan' => 'Tersedia']) }}"
           class="bg-surface rounded-[10px] p-4 border flex items-center gap-3.5 transition-colors hover:border-hairline-strong {{ $statusFilter === 'Tersedia' ? 'ring-2 ring-brand border-brand' : 'border-hairline' }}">
            <div class="w-10 h-10 bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50 rounded-[6px] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-ink">Siap Ditugaskan</p>
                <p class="text-xl font-bold text-brand-ink tabular-nums">{{ $countTersedia }} <span class="text-xs font-medium text-ink-muted">0 SPT Aktif</span></p>
            </div>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status_ketersediaan' => 'Optimal']) }}"
           class="bg-surface rounded-[10px] p-4 border flex items-center gap-3.5 transition-colors hover:border-hairline-strong {{ $statusFilter === 'Optimal' ? 'ring-2 ring-blue-700 border-blue-700' : 'border-hairline' }}">
            <div class="w-10 h-10 bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50 rounded-[6px] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Sedang Bertugas</p>
                <p class="text-xl font-bold text-blue-700 dark:text-blue-300 tabular-nums">{{ $countOptimal }} <span class="text-xs font-medium text-ink-muted">1-2 SPT Aktif</span></p>
            </div>
        </a>

        <a href="{{ request()->fullUrlWithQuery(['status_ketersediaan' => 'Beban Tinggi']) }}"
           class="bg-surface rounded-[10px] p-4 border flex items-center gap-3.5 transition-colors hover:border-hairline-strong {{ $statusFilter === 'Beban Tinggi' ? 'ring-2 ring-rose-700 border-rose-700' : 'border-hairline' }}">
            <div class="w-10 h-10 bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50 rounded-[6px] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-rose-700 dark:text-rose-300">Beban Tinggi</p>
                <p class="text-xl font-bold text-rose-700 dark:text-rose-300 tabular-nums">{{ $countOverload }} <span class="text-xs font-medium text-ink-muted">&ge;3 SPT Aktif</span></p>
            </div>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="mb-6 bg-surface rounded-[10px] p-5 border border-hairline">
        <form method="GET" action="{{ route('beban-kerja.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end text-xs">
            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ $tglAwal }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-medium text-ink px-3 py-2.5">
            </div>

            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tglAkhir }}" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-medium text-ink px-3 py-2.5">
            </div>

            @if(auth()->user()->isPimpinanOrAdmin())
            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Unit Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-medium text-ink px-3 py-2.5">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Status Ketersediaan</label>
                <select name="status_ketersediaan" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-medium text-ink px-3 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="Tersedia" {{ $statusFilter == 'Tersedia' ? 'selected' : '' }}>Siap Ditugaskan (0 SPT)</option>
                    <option value="Optimal" {{ $statusFilter == 'Optimal' ? 'selected' : '' }}>Sedang Bertugas (1-2 SPT)</option>
                    <option value="Beban Tinggi" {{ $statusFilter == 'Beban Tinggi' ? 'selected' : '' }}>Beban Tinggi (&ge;3 SPT)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-ink-secondary uppercase tracking-wider text-[11px] mb-1.5">Cari Personil</label>
                <select name="user_id" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-xs font-medium text-ink px-3 py-2.5">
                    <option value="">-- Semua Personil --</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ $selectedUserId == $u->id ? 'selected' : '' }}>{{ $u->nama_display }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="ledger-btn-primary w-full text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Filter Matriks</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Rekap Beban Kerja Table -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="p-4 border-b border-hairline bg-surface-subtle/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-semibold text-ink text-[15px]">Matriks Beban Kerja & Ketersediaan Personil</h3>
                <p class="text-xs text-ink-muted tabular-nums mt-0.5">Periode: {{ \Carbon\Carbon::parse($tglAwal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tglAkhir)->format('d/m/Y') }}</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-surface border border-hairline text-ink-secondary rounded-[4px] tabular-nums w-max">
                Menampilkan {{ $listPersonil->count() }} Pegawai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-semibold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Personil</th>
                        <th class="py-3.5 px-4">NIP / Jabatan</th>
                        <th class="py-3.5 px-4">Unit (Irban)</th>
                        <th class="py-3.5 px-4 text-center">Status Ketersediaan</th>
                        <th class="py-3.5 px-4 text-center">Penugasan Aktif</th>
                        <th class="py-3.5 px-4 text-center">Penugasan Selesai</th>
                        <th class="py-3.5 px-4 text-center font-bold">Total SPT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($listPersonil as $index => $p)
                        <tr class="hover:bg-surface-subtle/60 transition-colors">
                            <td class="py-3.5 px-4 font-medium text-center text-ink-muted tabular-nums">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-ink block">{{ $p->nama_display }}</span>
                                <span class="text-[11px] text-ink-muted font-mono">{{ $p->pangkat ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-ink-secondary">
                                <span class="font-mono block text-[11px] text-ink-muted tabular-nums">{{ $p->nip ?? '-' }}</span>
                                <span class="text-ink font-medium">{{ $p->jabatan }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-brand-ink">
                                {{ $p->irban?->nama_irban ?? 'Sekretariat' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[11px] font-semibold {{ $p->status_badge }}">
                                    <span class="w-2 h-2 rounded-full {{ $p->status_dot }}"></span>
                                    <span>{{ $p->status_label }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-bold text-sm tabular-nums {{ $p->penugasan_aktif > 2 ? 'text-rose-700 dark:text-rose-400' : ($p->penugasan_aktif > 0 ? 'text-blue-700 dark:text-blue-400' : 'text-ink-muted') }}">
                                    {{ $p->penugasan_aktif }} SPT
                                </span>
                                @if($p->penugasan_aktif > 0)
                                    <div class="mt-1.5 space-y-1 max-w-[200px] mx-auto text-left">
                                        @foreach($p->daftar_penugasan_aktif as $activeSt)
                                            <a href="{{ route('penugasan.show', $activeSt->id) }}" class="block truncate text-[11px] font-medium text-ink-secondary hover:text-brand-ink bg-surface-subtle px-2 py-0.5 rounded-[4px] border border-hairline" title="{{ $activeSt->no_spt }} | {{ $activeSt->uraian_penugasan }}">
                                                {{ $activeSt->no_spt }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-ink-secondary tabular-nums">
                                {{ $p->penugasan_selesai }} SPT
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-sm text-ink bg-surface-subtle/50 tabular-nums">
                                {{ $p->total_penugasan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-ink-muted font-medium">Tidak ada data personil yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
