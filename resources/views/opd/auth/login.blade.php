<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal OPD — SIPANDA Inspektorat Trenggalek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none p-8 border border-slate-200/80 dark:border-slate-800">
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-teal-600 text-white rounded-2xl shadow-lg shadow-teal-600/30 mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="inline-block px-3 py-1 bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 text-xs font-bold rounded-full mb-2 uppercase tracking-wider">PORTAL OPD</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Tindak Lanjut Pengawasan</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Akses khusus Perangkat Daerah untuk merespons & mengunggah bukti rekomendasi hasil pengawasan.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-xs font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('opd.login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Resmi OPD</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="dinas@trenggalek.go.id">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between pt-1">
                <label for="remember" class="inline-flex items-center cursor-pointer">
                    <input id="remember" type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span class="ms-2 text-xs text-slate-600 dark:text-slate-400">Ingat sesi ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/25 transition-all focus:outline-none focus:ring-2 focus:ring-teal-500">
                Masuk ke Portal OPD
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <p class="text-xs text-slate-500">Bukan pengguna OPD? / Staf Inspektorat?</p>
            <a href="{{ route('login') }}" class="inline-block mt-1 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600 underline">
                &larr; Masuk ke Aplikasi Internal SIPANDA
            </a>
        </div>
    </div>

</body>
</html>
