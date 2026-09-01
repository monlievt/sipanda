@php
    $user = auth()->user();
    $roleName = $user?->roles->first()?->name ?? 'User';
    $roleDisplay = match($roleName) {
        'admin' => 'Admin Sistem',
        'sekretariat' => 'Sekretariat',
        'inspektur' => 'Inspektur',
        'admin_irban' => 'Admin Irban',
        'irban' => 'Irban',
        'auditor' => 'Auditor / PPUPD',
        default => ucfirst($roleName),
    };
    $roleColor = match($roleName) {
        'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
        'sekretariat' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
        'inspektur' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
        'admin_irban' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        'irban' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        default => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
    };
@endphp

<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen bg-slate-900 text-slate-300 flex flex-col transition-transform -translate-x-full md:translate-x-0 border-r border-slate-800">
    <!-- Header / Logo -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 bg-slate-900/50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-emerald-600/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-white text-base leading-tight tracking-wide">SIPANDA</h1>
                <p class="text-[10px] text-emerald-400 font-medium tracking-wider uppercase">Inspektorat Trenggalek</p>
            </div>
        </a>
    </div>

    <!-- Profile Box -->
    <div class="p-3 mx-3 my-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-emerald-700 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow">
                {{ strtoupper(substr($user->nama_display ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ $user->nama_display }}</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded border {{ $roleColor }}">
                        {{ $roleDisplay }}
                    </span>
                    @if($user->irban)
                        <span class="text-[9px] text-slate-400 font-medium truncate">({{ $user->irban->nama_irban }})</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto px-3 space-y-5 py-2 text-xs font-medium">

        <!-- DASHBOARD & NOTIFIKASI -->
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Utama</p>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-semibold shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard Realtime</span>
                </a>
                <a href="{{ route('notifikasi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('notifikasi*') ? 'bg-emerald-600 text-white font-semibold shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Pusat Notifikasi</span>
                </a>
            </div>
        </div>

        <!-- PENGAWASAN (PKPPT, Input Penugasan, Data Penugasan, Kegiatan Pengawasan) -->
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pengawasan (PKPPT)</p>

            <div class="space-y-1">
                <a href="/pkppt" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('pkppt*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>PKPPT Tahunan</span>
                </a>

                @can('penugasan.create')
                <a href="/penugasan/create" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('penugasan/create') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Input Penugasan (SPT)</span>
                </a>
                @endcan

                <a href="/penugasan" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('penugasan') && !request()->is('penugasan/create') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Data Penugasan</span>
                </a>

                <a href="/kegiatan-pengawasan" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('kegiatan-pengawasan*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Kegiatan Pengawasan</span>
                </a>
            </div>
        </div>

        <!-- TINDAK LANJUT & ARSIP -->
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tindak Lanjut & Layanan</p>
            <div class="space-y-1">
                <a href="/tindak-lanjut" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('tindak-lanjut*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tindak Lanjut Result</span>
                </a>
                <a href="/konsultasi" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('konsultasi*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>E-Consulting APIP (QnA)</span>
                </a>
                <a href="/faq" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('faq*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Bank FAQ / QnA Publik</span>
                </a>
                <a href="/arsip" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('arsip*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span>Arsip Digital</span>
                </a>
            </div>
        </div>

        <!-- ANALISIS & PERENCANAAN -->
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Analisis & Siklus PKPT</p>
            <div class="space-y-1">
                <a href="/beban-kerja" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('beban-kerja*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Beban Kerja Personil</span>
                </a>
                @can('perencanaan.view')
                <a href="/perencanaan" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('perencanaan*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>Perencanaan PKPT (N-1)</span>
                </a>
                @endcan
                <a href="/evaluasi" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('evaluasi*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Evaluasi Tahunan (N+1)</span>
                </a>
            </div>
        </div>

        <!-- MASTER DATA (Admin & Sekretariat) -->
        @hasanyrole('admin|sekretariat')
        <div>
            <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Master Data & Sistem</p>
            <div class="space-y-1">
                <a href="/master/users" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('master/users*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Kelola Pengguna</span>
                </a>
                <a href="/master/objek-penugasan" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('master/objek-penugasan*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Objek Penugasan (OPD)</span>
                </a>
                <a href="/master/jenis-penugasan" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('master/jenis-penugasan*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10" />
                    </svg>
                    <span>Jenis Penugasan</span>
                </a>
                <a href="/import" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('import*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Import Data CSV</span>
                </a>
                @hasrole('admin')
                <a href="/audit-log" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all {{ request()->is('audit-log*') ? 'bg-emerald-600 text-white font-semibold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Audit Log</span>
                </a>
                @endhasrole
            </div>
        </div>
        @endhasanyrole

    </nav>

    <!-- Footer Logout -->
    <div class="p-3 border-t border-slate-800 bg-slate-900/80">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-slate-800 hover:bg-rose-900/40 text-slate-300 hover:text-rose-300 rounded-lg text-xs font-semibold transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar dari Aplikasi</span>
            </button>
        </form>
    </div>
</aside>
