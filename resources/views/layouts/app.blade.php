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
</body>
</html>
