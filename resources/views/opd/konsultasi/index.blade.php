<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Consulting QnA APIP - Portal OPD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 dark:text-slate-200 flex flex-col min-h-screen">
    <!-- Navbar OPD -->
    <nav class="bg-teal-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('opd.dashboard') }}" class="font-extrabold text-lg tracking-tight hover:text-teal-200">
                        SIPANDA <span class="text-xs font-normal opacity-80">| Portal OPD</span>
                    </a>
                    <a href="{{ route('opd.konsultasi.index') }}" class="text-xs font-bold px-3 py-1.5 rounded-xl bg-teal-800 text-white flex items-center gap-1.5 transition-all shadow-xs border border-teal-500">
                        💬 E-Consulting (QnA APIP)
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-xs text-teal-100 hidden sm:inline-block">PIC: <strong>{{ $opdUser->nama }}</strong></span>
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

    <!-- Main Container OPD -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 flex-1 w-full space-y-6">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <!-- Header Title & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Layanan E-Consulting & QnA APIP</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ajukan konsultasi online/tatap muka mengenai pengadaan, keuangan APBD/APBDes, aset, atau regulasi ke Inspektorat.</p>
            </div>

            <a href="{{ route('opd.konsultasi.create') }}" class="px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold rounded-xl shadow-xs inline-flex items-center gap-2 transition-all">
                ➕ Ajukan Konsultasi Baru
            </a>
        </div>

        <!-- Table Tiket Konsultasi OPD -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4">No. Tiket / Tanggal</th>
                            <th class="py-3.5 px-4">Area & Permasalahan</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Irban & Status Tiket</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200 font-medium">
                        @forelse($listKonsultasi as $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-teal-700 dark:text-teal-400 block">{{ $item->nomor_tiket }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="px-2 py-0.5 bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-300 rounded font-bold text-[10px] uppercase block w-max mb-1">
                                        {{ $item->area_konsultasi }}
                                    </span>
                                    <p class="font-bold text-slate-900 dark:text-white line-clamp-1">{{ $item->judul_permasalahan }}</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->metode_disetujui)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $item->metode_disetujui === 'online' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $item->metode_disetujui === 'online' ? '💬 Online Chat' : '🤝 Tatap Muka' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Preferensi: {{ strtoupper($item->preferensi_metode) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider block w-max mb-1
                                        {{ $item->status === 'menunggu_disposisi' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $item->status === 'berjalan' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $item->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                        {{ $item->status_label }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold block">{{ $item->irban?->nama_irban ?? 'Irban Pembina' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('opd.konsultasi.show', $item->id) }}" class="px-3 py-1.5 bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center gap-1">
                                        <span>Buka Chat & Detail</span> &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-medium">
                                    Belum ada konsultasi yang diajukan. Klik tombol di atas untuk membuat konsultasi baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listKonsultasi->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $listKonsultasi->links() }}
                </div>
            @endif
        </div>
    </main>

    <!-- Footer OPD -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-4 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Inspektorat Kabupaten Trenggalek — E-Consulting & QnA APIP.
    </footer>
</body>
</html>
