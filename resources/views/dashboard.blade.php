<x-app-layout>
    <x-slot name="header">
        Dashboard Realtime
    </x-slot>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Filter Dashboard -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Ringkasan Pelaksanaan PKPPT</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Data ter-update secara otomatis dari database penugasan.</p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div>
                <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500 shadow-xs">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            @if(!auth()->user()->hasRole(['irban', 'admin_irban']))
            <div>
                <select name="irban_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-emerald-500 shadow-xs">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanId == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>
    </div>

    <!-- Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Penugasan Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Penugasan</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalPenugasan }}</p>
                <span class="text-xs text-slate-500">SPT Terbit</span>
            </div>
        </div>

        <!-- Selesai Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Selesai</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $totalSelesai }}</p>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
                    {{ $persenSelesai }}%
                </span>
            </div>
        </div>

        <!-- Berjalan Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Sedang Berjalan</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $totalBerjalan }}</p>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
                    {{ $persenBerjalan }}%
                </span>
            </div>
        </div>

        <!-- Belum Berjalan Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Berjalan</span>
                <div class="w-8 h-8 rounded-xl bg-slate-500/10 text-slate-500 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-slate-700 dark:text-slate-300">{{ $totalBelum }}</p>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                    {{ $persenBelum }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Charts & Breakdown Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pie Chart Box -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center">
            <h3 class="font-bold text-slate-800 dark:text-white text-sm self-start mb-4">Persentase Status PKPPT {{ $tahun }}</h3>
            <div class="w-56 h-56 relative flex items-center justify-center">
                <canvas id="chartStatusPkppt"></canvas>
            </div>
        </div>

        <!-- Rekap Per Jenis Penugasan Table -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm">Rekap Realisasi Per Jenis Penugasan</h3>
                <span class="text-xs text-slate-500 font-medium">Assurance & Consulting</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Jenis Penugasan</th>
                            <th class="py-3 px-4 text-center">Selesai</th>
                            <th class="py-3 px-4 text-center">Dalam Proses</th>
                            <th class="py-3 px-4 text-center font-black">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($breakdownJenis as $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="py-2.5 px-4 font-semibold {{ $row['kategori'] === 'Assurance' ? 'text-blue-600' : 'text-purple-600' }}">
                                    {{ $row['kategori'] }}
                                </td>
                                <td class="py-2.5 px-4 font-bold text-slate-900 dark:text-white">{{ $row['nama'] }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-emerald-600">{{ $row['selesai'] }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-amber-600">{{ $row['dalam_proses'] }}</td>
                                <td class="py-2.5 px-4 text-center font-extrabold text-slate-900 dark:text-white bg-slate-50/50 dark:bg-slate-800/30">
                                    {{ $row['total'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script Render -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartStatusPkppt');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Berjalan', 'Belum Berjalan'],
                    datasets: [{
                        data: [{{ $totalSelesai }}, {{ $totalBerjalan }}, {{ $totalBelum }}],
                        backgroundColor: ['#10b981', '#f59e0b', '#94a3b8'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 11, family: 'Plus Jakarta Sans' },
                                padding: 15
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</x-app-layout>
