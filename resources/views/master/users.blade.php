<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Data Pegawai Internal</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar Auditor, PPUPD, dan Pejabat Pengawasan Inspektorat Kabupaten Trenggalek</p>
            </div>
            @can('users.create')
            <div>
                <a href="{{ route('master.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pegawai Baru
                </a>
            </div>
            @endcan
        </div>
    </x-slot>

    <!-- Tab Navigasi Manajemen Pengguna -->
    <div class="mb-6 flex border-b border-slate-200 dark:border-slate-800 gap-2">
        <a href="{{ route('master.users.index') }}" class="px-4 py-2.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 border-b-2 border-emerald-600 dark:border-emerald-400 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>👥 Pegawai Internal APIP</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-black">{{ $listUsers->total() }}</span>
        </a>
        <a href="{{ route('master.opd-users.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 border-b-2 border-transparent transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>🏛️ Akun PIC Perangkat Daerah (OPD)</span>
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('master.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5 items-end text-sm">
            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Cari Nama / NIP / Email</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NIP / Email..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Filter Role</label>
                <select name="role" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Filter Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanFilter == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-sm shadow-xs transition-all cursor-pointer">Filter</button>
                @if($search || $roleFilter || $irbanFilter)
                    <a href="{{ route('master.users.index') }}" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm transition-all text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Users -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Pegawai</th>
                        <th class="py-3.5 px-4">NIP</th>
                        <th class="py-3.5 px-4">Jabatan / Golongan</th>
                        <th class="py-3.5 px-4">Kontak (WhatsApp)</th>
                        <th class="py-3.5 px-4">Irban Unit</th>
                        <th class="py-3.5 px-4 text-center">Role Akses</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listUsers as $index => $u)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $listUsers->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $u->nama }}</p>
                                <span class="text-[10px] text-slate-400">{{ $u->email }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-700 dark:text-slate-300">{{ $u->nip ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="space-y-0.5">
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $u->jabatan ?? '-' }}</p>
                                    @if($u->status_jabatan && $u->status_jabatan !== 'definitif')
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                            <span>⚡</span>
                                            <span>
                                                @if($u->hasRole('inspektur'))
                                                    {{ $u->status_jabatan_badge }} Inspektur Daerah
                                                @elseif($u->hasRole('irban'))
                                                    {{ $u->status_jabatan_badge }} {{ $u->irban?->nama_irban ?? 'Irban' }}
                                                @else
                                                    {{ $u->status_jabatan_badge }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    <p class="text-[10px] text-slate-400">{{ $u->pangkat ?? '-' }} {{ $u->golongan ? "({$u->golongan})" : '' }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                                {{ $u->no_hp ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ $u->irban?->nama_irban ?? 'Sekretariat' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">
                                    {{ $u->roles->first()?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @can('users.edit')
                                <form method="POST" action="{{ route('master.users.toggle_status', $u->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status keaktifan {{ addslashes($u->nama_display) }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all cursor-pointer shadow-xs {{ $u->is_active ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 hover:bg-rose-200 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}" title="Klik untuk mengubah status">
                                        {{ $u->is_active ? '● Aktif' : '○ Nonaktif' }}
                                    </button>
                                </form>
                                @else
                                    @if($u->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Nonaktif</span>
                                    @endif
                                @endcan
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @can('users.edit')
                                <a href="{{ route('master.users.edit', $u) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-white rounded-xl text-[11px] font-bold transition-all cursor-pointer inline-flex items-center gap-1 shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada pengguna yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $listUsers->links() }}
        </div>
    </div>
</x-app-layout>

