<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur Ulang Kata Sandi — Portal OPD SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none p-8 border border-slate-200/80 dark:border-slate-800">
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-teal-600 text-white rounded-2xl shadow-lg shadow-teal-600/30 mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <span class="inline-block px-3 py-1 bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 text-xs font-bold rounded-full mb-2 uppercase tracking-wider">KATA SANDI BARU</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Atur Ulang Kata Sandi</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Silakan masukkan kata sandi baru untuk akun Portal OPD Anda.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3.5 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-700 dark:text-rose-300 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('opd.password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Resmi OPD</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-slate-900 dark:text-white"
                    placeholder="dinas@trenggalek.go.id">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Baru</label>
                <input id="password" type="password" name="password" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-slate-900 dark:text-white"
                    placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Kata Sandi Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-slate-900 dark:text-white"
                    placeholder="Ulangi kata sandi baru">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/25 transition-all focus:outline-none focus:ring-2 focus:ring-teal-500 cursor-pointer">
                Simpan Kata Sandi Baru
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <a href="{{ route('opd.login') }}" class="inline-block text-xs font-semibold text-teal-600 hover:text-teal-700 dark:text-teal-400">
                &larr; Kembali ke Halaman Masuk
            </a>
        </div>
    </div>

</body>
</html>
