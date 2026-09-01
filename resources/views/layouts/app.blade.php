<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIPANDA') }} — Inspektorat Kabupaten Trenggalek</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased font-sans">
    <div class="min-h-screen flex">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:pl-64 min-w-0">
            <!-- Top Navbar Header -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 shadow-xs">
                <!-- Mobile Sidebar Toggle -->
                <div class="flex items-center gap-3">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="md:hidden p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @isset($header)
                        <div class="font-bold text-slate-800 dark:text-white text-lg tracking-tight">
                            {{ $header }}
                        </div>
                    @endisset
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Irban Badge if any -->
                    @if(auth()->user()?->irban)
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 rounded-full text-xs font-bold text-emerald-700 dark:text-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ auth()->user()->irban->nama_irban }}
                        </span>
                    @endif

                    <!-- Notification Bell Dropdown Component -->
                    <div x-data="notificationDropdown()" x-init="init()" class="relative">
                        <button @click="toggleDropdown()" class="relative p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Pusat Notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Unread Badge Indicator -->
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-1 right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex items-center justify-center rounded-full h-4 w-4 bg-rose-500 text-white font-bold text-[9px]" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                </span>
                            </template>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 z-50 overflow-hidden text-xs" style="display: none;">
                            <div class="p-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800 dark:text-white">Notifikasi</span>
                                    <template x-if="unreadCount > 0">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white" x-text="unreadCount + ' Baru'"></span>
                                    </template>
                                </div>
                                <template x-if="unreadCount > 0">
                                    <button @click="markAllAsRead()" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                                        Tandai dibaca
                                    </button>
                                </template>
                            </div>

                            <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-80 overflow-y-auto">
                                <template x-if="items.length === 0">
                                    <div class="py-8 text-center text-slate-400">
                                        <p class="font-semibold text-xs">Tidak ada notifikasi baru</p>
                                    </div>
                                </template>
                                <template x-for="notif in items" :key="notif.id">
                                    <a :href="notif.url" class="p-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors flex items-start gap-3 block" :class="notif.is_read ? 'opacity-70' : 'bg-emerald-50/20 dark:bg-emerald-950/10'">
                                        <div class="w-2 h-2 mt-1.5 rounded-full shrink-0" :class="notif.is_read ? 'bg-transparent' : 'bg-emerald-500'"></div>
                                        <div class="flex-1 space-y-0.5">
                                            <p class="font-bold text-slate-900 dark:text-white text-[11px]" x-text="notif.judul"></p>
                                            <p class="text-[11px] text-slate-600 dark:text-slate-300 line-clamp-2" x-text="notif.pesan"></p>
                                            <span class="text-[9px] text-slate-400 block pt-0.5" x-text="notif.waktu"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <div class="p-2.5 border-t border-slate-100 dark:border-slate-800 text-center bg-slate-50/50 dark:bg-slate-800/30">
                                <a href="{{ route('notifikasi.index') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline text-[11px]">
                                    Buka Semua Notifikasi &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Quick Dropdown / Profile Link -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                            {{ strtoupper(substr(auth()->user()->nama_display ?? 'U', 0, 1)) }}
                        </div>
                        <span class="hidden lg:inline-block text-xs font-semibold text-slate-700 dark:text-slate-200">
                            {{ auth()->user()->nama_display }}
                        </span>
                    </a>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto">
                @if (session('status'))
                    <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-800 dark:text-emerald-200 text-xs font-medium flex items-center justify-between shadow-xs">
                        <span>{{ session('status') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 p-4 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-800 dark:text-rose-200 text-xs font-medium flex items-center justify-between shadow-xs">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        function notificationDropdown() {
            return {
                isOpen: false,
                unreadCount: 0,
                items: [],
                pollInterval: null,

                init() {
                    this.fetchNotifications();
                    // Polling update unread count otomatis setiap 30 detik
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
                    fetch('{{ route('notifikasi.unread_json') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.unreadCount = data.unread_count || 0;
                        this.items = data.items || [];
                    })
                    .catch(err => {
                        console.warn('[SIPANDA Notifikasi] Gagal memuat notifikasi:', err);
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
</body>
</html>

