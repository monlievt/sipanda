<x-app-layout>
    <x-slot name="header">
        Dashboard Realtime
    </x-slot>

    <!-- Chart.js (deferred so it never blocks first paint) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    <!-- Page Title & Filter Bar -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Ringkasan Pelaksanaan PKPPT {{ $tahun }}</h1>
            <p class="text-xs sm:text-sm text-ink-secondary mt-1">Rekapitulasi langsung dari basis data penugasan pengawasan.</p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="filter-tahun" class="block text-[11px] font-semibold text-ink-muted mb-1">Tahun Anggaran</label>
                <select id="filter-tahun" name="tahun" onchange="this.form.submit()" class="text-xs font-semibold min-w-[130px]">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isPimpinanOrAdmin())
            <div>
                <label for="filter-irban" class="block text-[11px] font-semibold text-ink-muted mb-1">Unit Kerja / Irban</label>
                <select id="filter-irban" name="irban_id" onchange="this.form.submit()" class="text-xs font-semibold min-w-[170px]">
                    <option value="">Semua Irban</option>
                    @foreach($irbans as $irban)
                        @php
                            $iId = is_object($irban) ? $irban->id : ($irban['id'] ?? null);
                            $iNama = is_object($irban) ? $irban->nama_irban : ($irban['nama_irban'] ?? (string)$irban);
                        @endphp
                        @if($iId)
                            <option value="{{ $iId }}" {{ $irbanId == $iId ? 'selected' : '' }}>{{ $iNama }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @endif
        </form>
    </div>

    <!-- Unified Ledger KPI Strip (Hairline Divided, Zero Floating Shadow Noise) -->
    <section aria-label="Ringkasan angka penugasan" class="bg-surface border border-hairline rounded-[10px] mb-6 overflow-hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-hairline">
            <!-- Total Penugasan -->
            <div class="p-5 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-ink-muted">Total Penugasan</span>
                    <span class="text-[11px] font-medium text-ink-muted">SPT Terbit</span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-bold text-ink tabular-nums">{{ $totalPenugasan }}</p>
                    <span class="text-xs text-ink-secondary">Penugasan</span>
                </div>
            </div>

            <!-- Selesai -->
            <div class="p-5 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Selesai</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded-[4px] bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 tabular-nums">
                        {{ $persenSelesai }}%
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-bold text-emerald-800 dark:text-emerald-300 tabular-nums">{{ $totalSelesai }}</p>
                    <span class="text-xs text-ink-secondary">Tuntas</span>
                </div>
            </div>

            <!-- Sedang Berjalan -->
            <div class="p-5 flex flex-col justify-between gap-3 sm:border-t lg:border-t-0 border-hairline">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-amber-900 dark:text-amber-300">Sedang Berjalan</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded-[4px] bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 tabular-nums">
                        {{ $persenBerjalan }}%
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-bold text-amber-900 dark:text-amber-300 tabular-nums">{{ $totalBerjalan }}</p>
                    <span class="text-xs text-ink-secondary">Dalam proses</span>
                </div>
            </div>

            <!-- Belum Berjalan -->
            <div class="p-5 flex flex-col justify-between gap-3 sm:border-t lg:border-t-0 border-hairline">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-ink-muted">Belum Berjalan</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded-[4px] bg-surface-subtle text-ink-secondary border border-hairline tabular-nums">
                        {{ $persenBelum }}%
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-bold text-ink-secondary tabular-nums">{{ $totalBelum }}</p>
                    <span class="text-xs text-ink-secondary">Terjadwal</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Primary Decision Grid: Breakdown Ledger Table First + Status Composition Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Rekap Per Jenis Penugasan Table (Primary Focal Area) -->
        <section aria-labelledby="rekap-jenis-heading" class="lg:col-span-8 bg-surface rounded-[10px] border border-hairline overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-hairline bg-surface-subtle flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                <div>
                    <h2 id="rekap-jenis-heading" class="font-bold text-ink text-sm sm:text-[15px]">Rekap Realisasi per Jenis Penugasan</h2>
                    <p class="text-xs text-ink-muted">Perbandingan penugasan selesai dan dalam proses untuk kategori Assurance dan Consulting.</p>
                </div>
                <a href="/penugasan" class="text-xs font-semibold text-brand-ink hover:underline shrink-0">
                    Lihat semua SPT
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs sm:text-[13px]">
                    <thead class="bg-surface-subtle text-ink-muted font-semibold border-b border-hairline">
                        <tr>
                            <th scope="col" class="py-3 px-4">Kategori</th>
                            <th scope="col" class="py-3 px-4">Jenis Penugasan</th>
                            <th scope="col" class="py-3 px-4 text-right">Selesai</th>
                            <th scope="col" class="py-3 px-4 text-right">Dalam Proses</th>
                            <th scope="col" class="py-3 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline">
                        @forelse($breakdownJenis as $row)
                            <tr class="hover:bg-surface-subtle/70 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[11px] font-semibold border {{ $row['kategori'] === 'Assurance' ? 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800/60' : 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800/60' }}">
                                        {{ $row['kategori'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-ink">{{ $row['nama'] }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-emerald-800 dark:text-emerald-300 tabular-nums">{{ $row['selesai'] }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-amber-900 dark:text-amber-300 tabular-nums">{{ $row['dalam_proses'] }}</td>
                                <td class="py-3 px-4 text-right font-bold text-ink bg-surface-subtle/50 tabular-nums">
                                    {{ $row['total'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 px-4 text-center">
                                    <p class="text-sm font-semibold text-ink">Belum ada penugasan tercatat untuk filter ini</p>
                                    <p class="text-xs text-ink-muted mt-1">Pilih tahun anggaran lain atau tambahkan penugasan SPT baru untuk mulai memantau realisasi.</p>
                                    @can('penugasan.create')
                                        <a href="/penugasan/create" class="ledger-btn-primary mt-4 text-xs">
                                            Input Penugasan (SPT)
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Status Composition Chart -->
        <section aria-labelledby="chart-status-heading" class="lg:col-span-4 bg-surface rounded-[10px] p-5 border border-hairline flex flex-col">
            <div class="mb-4">
                <h2 id="chart-status-heading" class="font-bold text-ink text-sm sm:text-[15px]">Proporsi Status PKPPT {{ $tahun }}</h2>
                <p class="text-xs text-ink-muted mt-0.5">Sebaran SPT selesai, berjalan, dan belum berjalan.</p>
            </div>

            @if($totalPenugasan > 0)
                <div class="w-full max-w-[240px] h-56 mx-auto relative flex items-center justify-center">
                    <canvas id="chartStatusPkppt" aria-label="Grafik proporsi status PKPPT {{ $tahun }}" role="img"></canvas>
                </div>

                <dl class="mt-5 pt-4 border-t border-hairline space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <dt class="flex items-center gap-2 text-ink-secondary">
                            <span class="w-2.5 h-2.5 rounded-[2px] bg-[#047857]" aria-hidden="true"></span>
                            <span>Selesai</span>
                        </dt>
                        <dd class="font-semibold text-ink tabular-nums">{{ $totalSelesai }} ({{ $persenSelesai }}%)</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="flex items-center gap-2 text-ink-secondary">
                            <span class="w-2.5 h-2.5 rounded-[2px] bg-[#d97706]" aria-hidden="true"></span>
                            <span>Sedang Berjalan</span>
                        </dt>
                        <dd class="font-semibold text-ink tabular-nums">{{ $totalBerjalan }} ({{ $persenBerjalan }}%)</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="flex items-center gap-2 text-ink-secondary">
                            <span class="w-2.5 h-2.5 rounded-[2px] bg-[#94a3b8]" aria-hidden="true"></span>
                            <span>Belum Berjalan</span>
                        </dt>
                        <dd class="font-semibold text-ink tabular-nums">{{ $totalBelum }} ({{ $persenBelum }}%)</dd>
                    </div>
                </dl>
            @else
                <div class="py-10 text-center text-xs text-ink-muted border border-dashed border-hairline rounded-[8px]">
                    <p class="font-semibold text-ink-secondary">Grafik belum tersedia</p>
                    <p class="mt-1">Data proporsi muncul otomatis setelah ada SPT terbit pada tahun {{ $tahun }}.</p>
                </div>
            @endif
        </section>
    </div>

    <!-- Chart.js Script Render -->
    @if($totalPenugasan > 0)
    <script>
        window.addEventListener('load', function() {
            const ctx = document.getElementById('chartStatusPkppt');
            if (!ctx || typeof Chart === 'undefined') return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Sedang Berjalan', 'Belum Berjalan'],
                    datasets: [{
                        data: [{{ $totalSelesai }}, {{ $totalBerjalan }}, {{ $totalBelum }}],
                        backgroundColor: ['#047857', '#d97706', '#94a3b8'],
                        borderColor: document.documentElement.classList.contains('dark') ? '#161a1b' : '#ffffff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: '72%'
                }
            });
        });
    </script>
    @endif
</x-app-layout>
