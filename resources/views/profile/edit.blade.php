<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-ink leading-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="p-6 sm:p-8 bg-surface rounded-[10px] border border-hairline">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-surface rounded-[10px] border border-hairline">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Google Calendar Integration -->
        <div class="p-6 sm:p-8 bg-surface rounded-[10px] border border-hairline">
            <div class="max-w-xl text-xs space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[6px] bg-brand-soft text-brand-ink border border-brand-border flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-ink">Sinkronisasi Google Calendar</h3>
                        <p class="text-ink-secondary text-xs">Hubungkan akun Google Anda untuk otomatisasi sinkronisasi jadwal penugasan (SPT) ke kalender pribadi.</p>
                    </div>
                </div>

                @if(auth()->user()->google_access_token)
                    <div class="p-3 bg-brand-soft border border-brand-border rounded-[6px] flex items-center justify-between">
                        <div class="flex items-center gap-2 text-brand-ink font-semibold">
                            <span class="w-2 h-2 rounded-full bg-brand"></span>
                            <span>Terkoneksi dengan Google Calendar</span>
                        </div>
                        <form method="POST" action="{{ route('google.disconnect') }}">
                            @csrf
                            <button type="submit" class="text-rose-700 dark:text-rose-400 hover:underline font-semibold text-xs cursor-pointer">
                                Putuskan Tautan
                            </button>
                        </form>
                    </div>
                @else
                    <div>
                        <a href="{{ route('google.connect') }}" class="ledger-btn-primary text-xs">
                            <svg class="w-4 h-4" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                            </svg>
                            <span>Hubungkan dengan Google Calendar</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-surface rounded-[10px] border border-hairline">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

