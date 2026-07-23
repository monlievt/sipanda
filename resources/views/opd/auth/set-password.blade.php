<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aktivasi Akun Portal OPD — SIPANDA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-xl p-8 border border-slate-200/80 dark:border-slate-800">
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-emerald-600 text-white rounded-2xl shadow-lg shadow-emerald-600/30 mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Aktivasi Akun Portal OPD</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Halo <strong class="text-slate-800 dark:text-slate-200">{{ $user->nama }}</strong> ({{ $user->email }}), buat kata sandi untuk mengaktifkan akun OPD Anda.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('opd.undangan.store', $token) }}" class="space-y-4">
            @csrf

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Baru</label>
                <input id="password" type="password" name="password" required minlength="8"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Ketik ulang kata sandi">
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/25 transition-all">
                Aktifkan & Masuk
            </button>
        </form>
    </div>

</body>
</html>
