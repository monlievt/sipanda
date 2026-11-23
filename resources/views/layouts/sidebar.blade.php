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
        'admin' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-900/60',
        'sekretariat' => 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-900/60',
        'inspektur' => 'bg-amber-50 text-amber-900 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900/60',
        'admin_irban' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900/60',
        'irban' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900/60',
        default => 'bg-surface-subtle text-ink-secondary border-hairline',
    };

    $navLinkBase = 'flex items-center gap-2.5 min-h-[40px] md:min-h-[36px] px-3 py-2 rounded-[6px] text-[13px] transition-colors border';
    $navLinkActive = 'bg-surface text-brand-ink font-semibold border-hairline border-l-2 border-l-brand';
    $navLinkIdle = 'border-transparent text-ink-secondary hover:bg-surface/80 hover:text-ink font-medium';
    $navDevBtn = 'w-full flex items-center justify-between min-h-[40px] md:min-h-[36px] px-3 py-2 rounded-[6px] text-[13px] text-ink-muted hover:bg-surface/60 hover:text-ink transition-colors border border-transparent group';
@endphp

<aside id="sidebar"
       aria-label="Navigasi utama aplikasi"
       class="fixed top-0 left-0 z-40 w-64 h-screen bg-sidebar text-ink flex flex-col transition-transform duration-150 -translate-x-full md:translate-x-0 border-r border-hairline">
    <!-- Header / Logo -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-hairline shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 bg-brand rounded-[6px] flex items-center justify-center text-white shrink-0" aria-hidden="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="min-w-0">
                <span class="font-bold text-ink text-sm leading-tight tracking-tight block">SINGAWASI</span>
                <span class="text-[11px] text-ink-muted font-medium block truncate">Inspektorat Trenggalek</span>
            </div>
        </a>

        <button type="button"
                onclick="closeMobileSidebar(true)"
                aria-label="Tutup menu navigasi"
                class="md:hidden inline-flex items-center justify-center min-h-[40px] min-w-[40px] rounded-[6px] text-ink-secondary hover:text-ink hover:bg-surface transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Profile Box -->
    <div class="px-3 pt-3 pb-2 shrink-0">
        <div class="p-2.5 bg-surface rounded-[8px] border border-hairline">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-brand-soft text-brand-ink border border-brand-border rounded-[6px] flex items-center justify-center font-bold text-xs shrink-0" aria-hidden="true">
                    {{ strtoupper(substr($user->nama_display ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-ink truncate">{{ $user->nama_display }}</p>
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded-[4px] border {{ $roleColor }}">
                            {{ $roleDisplay }}
                        </span>
                        @if($user->irban)
                            <span class="text-[11px] text-ink-muted font-medium truncate">{{ $user->irban->nama_irban }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto px-3 space-y-5 py-3">

        <!-- DASHBOARD & NOTIFIKASI -->
        <div>
            <p class="px-3 text-[11px] font-semibold text-ink-muted mb-1.5">Utama</p>
            <div class="space-y-0.5">
                <a href="{{ route('dashboard') }}"
                   @if(request()->routeIs('dashboard')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->routeIs('dashboard') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard Realtime</span>
                </a>
                <a href="{{ route('notifikasi.index') }}"
                   @if(request()->is('notifikasi*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('notifikasi*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Pusat Notifikasi</span>
                </a>
            </div>
        </div>

        <!-- PENGAWASAN (PKPPT, Input Penugasan, Data Penugasan, Kegiatan Pengawasan) -->
        <div>
            <p class="px-3 text-[11px] font-semibold text-ink-muted mb-1.5">Pengawasan (PKPPT)</p>
            <div class="space-y-0.5">
                <a href="/pkppt"
                   @if(request()->is('pkppt*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('pkppt*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>PKPPT Tahunan</span>
                </a>

                @can('penugasan.create')
                <a href="/penugasan/create"
                   @if(request()->is('penugasan/create')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('penugasan/create') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Input Penugasan (SPT)</span>
                </a>
                @endcan

                <a href="/penugasan"
                   @if(request()->is('penugasan') && !request()->is('penugasan/create')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('penugasan') && !request()->is('penugasan/create') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Data Penugasan</span>
                </a>

                <a href="/kegiatan-pengawasan"
                   @if(request()->is('kegiatan-pengawasan*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('kegiatan-pengawasan*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Realisasi PKPPT</span>
                </a>

                <a href="{{ route('master.kelompok-pengawasan.index') }}"
                   @if(request()->is('master/kelompok-pengawasan*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/kelompok-pengawasan*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Kluster Jenis Pengawasan</span>
                </a>
            </div>
        </div>

        <!-- TINDAK LANJUT & ARSIP -->
        <div>
            <p class="px-3 text-[11px] font-semibold text-ink-muted mb-1.5">Tindak Lanjut & Layanan</p>
            <div class="space-y-0.5">
                <a href="/tindak-lanjut"
                   @if(request()->is('tindak-lanjut*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('tindak-lanjut*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tindak Lanjut Result</span>
                </a>
                <button type="button"
                        onclick="showFeatureUnderDev('E-Consulting APIP (QnA)', 'Layanan permohonan konsultasi online, penugasan tim advis APIP, dan penerbitan Berita Acara sedang dalam tahap perumusan standar operasional prosedur (SOP).')"
                        class="{{ $navDevBtn }}">
                    <span class="flex items-center gap-2.5 text-left min-w-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span class="truncate">E-Consulting APIP</span>
                    </span>
                    <span class="text-[10px] font-semibold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 px-1.5 py-0.5 rounded-[4px] shrink-0">Segera</span>
                </button>
                <a href="/arsip"
                   @if(request()->is('arsip*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('arsip*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span>Arsip Digital</span>
                </a>
                <a href="{{ route('kamus-atribut.index') }}"
                   @if(request()->is('master/kamus-atribut*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/kamus-atribut*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Kamus Atribut APIP</span>
                </a>
            </div>
        </div>

        <!-- ANALISIS & PERENCANAAN -->
        <div>
            <p class="px-3 text-[11px] font-semibold text-ink-muted mb-1.5">Analisis & Siklus PKPT</p>
            <div class="space-y-0.5">
                <a href="/beban-kerja"
                   @if(request()->is('beban-kerja*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('beban-kerja*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Beban Kerja Personil</span>
                </a>
                @can('perencanaan.view')
                <button type="button"
                        onclick="showFeatureUnderDev('Matriks Penilaian Risiko & Kapasitas SDM', 'Modul perencanaan PKPT berbasis risiko (Siklus N-1) dan kalkulasi kapasitas hari pengawasan SDM sedang dalam tahap sinkronisasi data kuantitatif indikator risiko OPD.')"
                        class="{{ $navDevBtn }}">
                    <span class="flex items-center gap-2.5 text-left min-w-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span class="truncate">Penilaian Risiko (N-1)</span>
                    </span>
                    <span class="text-[10px] font-semibold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 px-1.5 py-0.5 rounded-[4px] shrink-0">Segera</span>
                </button>
                @endcan
                <button type="button"
                        onclick="showFeatureUnderDev('Evaluasi Tahunan Pengawasan (N+1)', 'Modul komparasi target kinerja vs realisasi pengawasan tahunan serta analisis deviasi penugasan sedang disiapkan untuk siklus evaluasi akhir tahun anggaran.')"
                        class="{{ $navDevBtn }}">
                    <span class="flex items-center gap-2.5 text-left min-w-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="truncate">Evaluasi Tahunan (N+1)</span>
                    </span>
                    <span class="text-[10px] font-semibold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 px-1.5 py-0.5 rounded-[4px] shrink-0">Segera</span>
                </button>
                <a href="{{ route('ikhtisar-laporan.index') }}"
                   @if(request()->is('ikhtisar-laporan*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('ikhtisar-laporan*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="flex items-center justify-between flex-1 gap-1.5 min-w-0">
                        <span class="truncate">Ikhtisar Eksekutif (ILHP)</span>
                        <span class="text-[10px] font-semibold bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 px-1.5 py-0.5 rounded-[4px] shrink-0">Bupati</span>
                    </span>
                </a>
            </div>
        </div>

        <!-- MASTER DATA (Admin Sistem) -->
        @hasanyrole('admin|administrator')
        <div>
            <p class="px-3 text-[11px] font-semibold text-ink-muted mb-1.5">Master Data & Sistem</p>
            <div class="space-y-0.5">
                <a href="/master/users"
                   @if(request()->is('master/users*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/users*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Kelola Pengguna Internal</span>
                </a>
                <a href="/master/opd-users"
                   @if(request()->is('master/opd-users*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/opd-users*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Akun PIC OPD (Auditi)</span>
                </a>
                <a href="/master/objek-penugasan"
                   @if(request()->is('master/objek-penugasan*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/objek-penugasan*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Objek Penugasan (OPD)</span>
                </a>
                <a href="/master/jenis-penugasan"
                   @if(request()->is('master/jenis-penugasan*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/jenis-penugasan*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h10M7 11h10M7 15h10" />
                    </svg>
                    <span>Jenis Penugasan</span>
                </a>
                <a href="/master/kelompok-pengawasan"
                   @if(request()->is('master/kelompok-pengawasan*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/kelompok-pengawasan*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Kelompok Pengawasan (ILHP)</span>
                </a>
                <a href="/master/irbans"
                   @if(request()->is('master/irbans*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/irbans*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Unit Kerja / Irban</span>
                </a>
                <a href="/master/regulasi"
                   @if(request()->is('master/regulasi*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/regulasi*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Bank Regulasi & Juknis</span>
                </a>
                <a href="/master/faq"
                   @if(request()->is('master/faq*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/faq*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Kelola FAQ APIP</span>
                </a>
                <a href="/import"
                   @if(request()->is('import*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('import*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Import Data Excel/CSV</span>
                </a>
                <a href="/master/backup"
                   @if(request()->is('master/backup*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('master/backup*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7zm0 5h16M9 4v16" />
                    </svg>
                    <span>Backup & Pemulihan Data</span>
                </a>
                <a href="/master/feedback"
                   @if(request()->is('master/feedback*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} justify-between {{ request()->is('master/feedback*') ? $navLinkActive : $navLinkIdle }}">
                    <span class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span class="truncate">Kotak Saran & Bug UAT</span>
                    </span>
                    @php
                        $unreadFeedbackCount = \App\Models\UatFeedback::where('status', 'baru')->count();
                    @endphp
                    @if($unreadFeedbackCount > 0)
                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-950 dark:bg-amber-900/60 dark:text-amber-200 border border-amber-300 dark:border-amber-700 font-bold text-[10px] rounded-[4px] tabular-nums shrink-0">
                            {{ $unreadFeedbackCount }}
                        </span>
                    @endif
                </a>
                @hasrole('admin')
                <a href="/audit-log"
                   @if(request()->is('audit-log*')) aria-current="page" @endif
                   class="{{ $navLinkBase }} {{ request()->is('audit-log*') ? $navLinkActive : $navLinkIdle }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Audit Log</span>
                </a>
                @endhasrole
            </div>
        </div>
        @endhasanyrole

    </nav>

    <!-- Footer Logout -->
    <div class="p-3 border-t border-hairline shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 min-h-[40px] px-3 py-2 bg-surface hover:bg-red-50 dark:hover:bg-red-950/40 text-ink-secondary hover:text-red-700 dark:hover:text-red-300 border border-hairline hover:border-red-200 dark:hover:border-red-900/60 rounded-[6px] text-xs font-semibold transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar dari Aplikasi</span>
            </button>
        </form>
    </div>
</aside>
