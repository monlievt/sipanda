<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Portal OPD — SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased font-sans min-h-screen flex flex-col">
    <!-- Navbar OPD -->
    <nav class="bg-teal-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('opd.dashboard') }}" class="font-extrabold text-lg tracking-tight hover:text-teal-200">
                        SIPANDA <span class="text-xs font-normal opacity-80">| Portal OPD</span>
                    </a>
                    <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-teal-700 hover:bg-teal-600 text-white flex items-center gap-1.5 transition-all shadow-xs">
                        💬 E-Consulting (QnA APIP)
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-teal-100 hidden sm:inline-block">PIC: <strong>{{ $user->nama }}</strong></span>
                    <form method="POST" action="{{ route('opd.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs bg-teal-900 hover:bg-teal-950 px-3.5 py-1.5 rounded-xl text-white font-semibold transition-colors">
                            Keluar &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Body OPD -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stat Cards OPD -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Rekomendasi</p>
                <p class="text-3xl font-black text-slate-900 dark:text-white mt-2">{{ $rekap['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-bold text-amber-600 uppercase">Perlu Tindak Lanjut</p>
                <p class="text-3xl font-black text-amber-600 mt-2">{{ $rekap['belum'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-bold text-blue-600 uppercase">Menunggu Verifikasi</p>
                <p class="text-3xl font-black text-blue-600 mt-2">{{ $rekap['menunggu'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-xs font-bold text-emerald-600 uppercase">Selesai</p>
                <p class="text-3xl font-black text-emerald-600 mt-2">{{ $rekap['selesai'] }}</p>
            </div>
        </div>

        <!-- Table Rekomendasi OPD -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Daftar Rekomendasi Pengawasan Inspektorat</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4 w-10 text-center">No</th>
                            <th class="py-3 px-4">Uraian Temuan</th>
                            <th class="py-3 px-4">Rekomendasi Wajib</th>
                            <th class="py-3 px-4">Target Selesai</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($listRekomendasi as $index => $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="py-3 px-4 text-center font-semibold text-slate-500">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200 max-w-xs">{{ $item->uraian_temuan }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900 dark:text-white max-w-xs">{{ $item->rekomendasi }}</td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">{{ $item->tanggal_target ? $item->tanggal_target->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                        {{ $item->status_tindak_lanjut === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $item->status_tindak_lanjut === 'menunggu_verifikasi' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $item->status_tindak_lanjut === 'dikembalikan' ? 'bg-rose-100 text-rose-800' : '' }}
                                        {{ in_array($item->status_tindak_lanjut, ['belum','proses']) ? 'bg-amber-100 text-amber-800' : '' }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('opd.tindak-lanjut.show', $item->id) }}" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs shadow-xs">
                                        Respons & Bukti &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada rekomendasi pengawasan yang ditujukan untuk instansi ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />
</body>
</html>
