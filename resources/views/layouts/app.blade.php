<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SINGAWASI') }} | Inspektorat Kabupaten Trenggalek</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Theme Initialization & Global Toggle Function (Default: Light Mode) -->
    <script>
        if (localStorage.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleThemeMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</head>
<body class="bg-canvas text-ink antialiased font-sans">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand focus:text-white focus:rounded-[6px] focus:text-sm focus:font-semibold">
        Lewati ke konten utama
    </a>

    <div class="min-h-screen flex">
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop"
             onclick="closeMobileSidebar()"
             class="fixed inset-0 z-30 bg-black/40 hidden md:hidden transition-opacity"
             aria-hidden="true"></div>

        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:pl-64 min-w-0">
            <!-- Top Navbar Header -->
            <header class="h-16 bg-surface border-b border-hairline sticky top-0 z-20 flex items-center justify-between px-4 sm:px-6">
                <!-- Mobile Sidebar Toggle & Page Header -->
                <div class="flex items-center gap-3 min-w-0">
                    <button id="mobile-sidebar-toggle"
                            type="button"
                            onclick="toggleMobileSidebar()"
                            aria-controls="sidebar"
                            aria-expanded="false"
                            aria-label="Buka menu navigasi"
                            class="md:hidden inline-flex items-center gap-1.5 min-h-[44px] min-w-[44px] px-2.5 text-ink-secondary hover:text-ink hover:bg-surface-subtle rounded-[6px] border border-hairline transition-colors">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="text-xs font-semibold">Menu</span>
                    </button>

                    @isset($header)
                        <div class="font-bold text-ink text-base sm:text-lg tracking-tight truncate">
                            {{ $header }}
                        </div>
                    @endisset
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                    <!-- Irban Badge if any -->
                    @if(auth()->user()?->irban)
                        <span class="hidden sm:inline-flex items-center px-2.5 py-1 bg-brand-soft border border-brand-border rounded-[4px] text-xs font-semibold text-brand-ink">
                            {{ auth()->user()->irban->nama_irban }}
                        </span>
                    @endif

                    <!-- Dark / Light Theme Toggle Button -->
                    <button type="button"
                            onclick="toggleThemeMode()"
                            aria-label="Ganti mode terang atau gelap"
                            title="Ganti Mode Terang / Gelap"
                            class="inline-flex items-center justify-center min-h-[40px] min-w-[40px] text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle border border-transparent hover:border-hairline transition-colors">
                        <!-- Moon Icon (Light mode -> Dark) -->
                        <svg class="w-[18px] h-[18px] block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <!-- Sun Icon (Dark mode -> Light) -->
                        <svg class="w-[18px] h-[18px] hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <!-- Notification Bell Dropdown Component -->
                    <div x-data="notificationDropdown()" x-init="init()" @keydown.escape.window="isOpen = false" class="relative">
                        <button type="button"
                                @click="toggleDropdown()"
                                :aria-expanded="isOpen.toString()"
                                aria-label="Pusat Notifikasi"
                                title="Pusat Notifikasi"
                                class="relative inline-flex items-center justify-center min-h-[40px] min-w-[40px] text-ink-secondary hover:text-ink rounded-[6px] hover:bg-surface-subtle border border-transparent hover:border-hairline transition-colors">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Unread Badge Indicator -->
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-1 right-1 inline-flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-700 text-white font-bold text-[10px] tabular-nums" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                            </template>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="isOpen"
                             @click.outside="isOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-surface rounded-[10px] shadow-lg border border-hairline z-50 overflow-hidden text-xs"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-hairline flex items-center justify-between bg-surface-subtle">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-ink">Notifikasi</span>
                                    <template x-if="unreadCount > 0">
                                        <span class="px-1.5 py-0.5 rounded-[4px] text-[10px] font-semibold bg-red-700 text-white tabular-nums" x-text="unreadCount + ' Baru'"></span>
                                    </template>
                                </div>
                                <template x-if="unreadCount > 0">
                                    <button type="button" @click="markAllAsRead()" class="text-[11px] font-semibold text-brand-ink hover:underline">
                                        Tandai dibaca
                                    </button>
                                </template>
                            </div>

                            <div class="divide-y divide-hairline max-h-80 overflow-y-auto">
                                <template x-if="isLoading && items.length === 0">
                                    <div class="py-8 text-center text-ink-muted">
                                        <p class="font-medium text-xs">Memuat notifikasi...</p>
                                    </div>
                                </template>
                                <template x-if="hasError && items.length === 0">
                                    <div class="py-8 px-4 text-center text-red-700 dark:text-red-300">
                                        <p class="font-semibold text-xs">Gagal memuat notifikasi.</p>
                                        <button type="button" @click="fetchNotifications()" class="mt-2 text-xs font-semibold text-brand-ink hover:underline">
                                            Coba muat ulang
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!isLoading && !hasError && items.length === 0">
                                    <div class="py-8 px-4 text-center text-ink-muted">
                                        <p class="font-semibold text-xs text-ink-secondary">Belum ada notifikasi baru</p>
                                        <p class="text-[11px] mt-1">Pemberitahuan penugasan dan tindak lanjut akan tampil di sini.</p>
                                    </div>
                                </template>
                                <template x-for="notif in items" :key="notif.id">
                                    <a :href="notif.url"
                                       class="px-4 py-3 hover:bg-surface-subtle transition-colors flex items-start gap-3 block"
                                       :class="notif.is_read ? 'opacity-75' : 'bg-brand-soft/60'">
                                        <span class="w-2 h-2 mt-1.5 rounded-full shrink-0" :class="notif.is_read ? 'bg-transparent' : 'bg-brand'" aria-hidden="true"></span>
                                        <div class="flex-1 min-w-0 space-y-0.5">
                                            <p class="font-semibold text-ink text-xs" x-text="notif.judul"></p>
                                            <p class="text-xs text-ink-secondary line-clamp-2" x-text="notif.pesan"></p>
                                            <span class="text-[11px] text-ink-muted block pt-0.5 tabular-nums" x-text="notif.waktu"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <div class="px-4 py-2.5 border-t border-hairline text-center bg-surface-subtle">
                                <a href="{{ route('notifikasi.index') }}" class="font-semibold text-brand-ink hover:underline text-xs">
                                    Lihat semua notifikasi
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Quick Link -->
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 min-h-[40px] px-2 py-1 rounded-[6px] hover:bg-surface-subtle border border-transparent hover:border-hairline transition-colors">
                        <div class="w-7 h-7 rounded-[6px] bg-brand text-white font-semibold flex items-center justify-center text-xs shrink-0" aria-hidden="true">
                            {{ strtoupper(substr(auth()->user()->nama_display ?? 'U', 0, 1)) }}
                        </div>
                        <span class="hidden lg:inline-block text-xs font-semibold text-ink max-w-[140px] truncate">
                            {{ auth()->user()->nama_display }}
                        </span>
                    </a>
                </div>
            </header>

            <!-- Page Body -->
            <main id="main-content" class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto">
                @if (session('status'))
                    <div role="status" class="mb-5 px-4 py-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-[8px] text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm font-medium flex items-center justify-between gap-3">
                        <span>{{ session('status') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup pesan status" class="min-h-[32px] min-w-[32px] inline-flex items-center justify-center rounded-[4px] text-emerald-800 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50">
                            &times;
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert" class="mb-5 px-4 py-3 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-[8px] text-red-800 dark:text-red-200 text-xs sm:text-sm font-medium flex items-center justify-between gap-3">
                        <span>{{ session('error') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup pesan galat" class="min-h-[32px] min-w-[32px] inline-flex items-center justify-center rounded-[4px] text-red-800 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50">
                            &times;
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        function openMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggle = document.getElementById('mobile-sidebar-toggle');
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }

        function closeMobileSidebar(returnFocus = false) {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggle = document.getElementById('mobile-sidebar-toggle');
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                if (returnFocus) toggle.focus();
            }
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            if (sidebar.classList.contains('-translate-x-full')) {
                openMobileSidebar();
            } else {
                closeMobileSidebar(false);
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 768) {
                    closeMobileSidebar(true);
                }
            }
        });

        function notificationDropdown() {
            return {
                isOpen: false,
                isLoading: false,
                hasError: false,
                unreadCount: 0,
                items: [],
                pollInterval: null,

                init() {
                    this.fetchNotifications();
                    this.pollInterval = setInterval(() => {
                        this.fetchNotifications();
                    }, 30000);
                },

                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.fetchNotifications();
                    }
                },

                fetchNotifications() {
                    this.isLoading = true;
                    this.hasError = false;
                    fetch('{{ route('notifikasi.unread_json') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        this.unreadCount = data.unread_count || 0;
                        this.items = data.items || [];
                    })
                    .catch(err => {
                        this.hasError = true;
                        console.warn('[SINGAWASI Notifikasi] Gagal memuat notifikasi:', err);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                markAllAsRead() {
                    fetch('{{ route('notifikasi.mark_all_read') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(() => {
                        this.unreadCount = 0;
                        this.items.forEach(i => i.is_read = true);
                    })
                    .catch(err => console.warn(err));
                }
            }
        }
    </script>

    <!-- Global UAT Feedback & Bug Report Widget -->
    <x-uat-feedback-widget />

    <!-- Global PDF & Document Preview Modal -->
    <x-pdf-preview-modal />

    <!-- Global Under Development Modal -->
    <x-under-development-modal />
</body>
</html>
