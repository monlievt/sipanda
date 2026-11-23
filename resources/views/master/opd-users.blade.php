<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-ink">Kelola Akun PIC Perangkat Daerah (Portal OPD)</h1>
                <p class="text-xs text-ink-secondary mt-0.5">Penetapan akun akses, username login, dan kata sandi auditi/OPD untuk menindaklanjuti rekomendasi LHP</p>
            </div>
            @can('opd_users.manage')
            <div>
                <button type="button" onclick="openModalTambahOpdUser()" class="ledger-btn-primary !text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Buat Akun PIC OPD Baru
                </button>
            </div>
            @endcan
        </div>
    </x-slot>

    <!-- Tab Navigasi Manajemen Pengguna -->
    <div class="mb-6 flex border-b border-hairline gap-2">
        <a href="{{ route('master.users.index') }}" class="px-4 py-2.5 text-xs font-bold text-ink-secondary hover:text-ink border-b-2 border-transparent transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Pegawai Internal APIP</span>
        </a>
        <a href="{{ route('master.opd-users.index') }}" class="px-4 py-2.5 text-xs font-bold text-brand-ink border-b-2 border-brand transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Akun PIC Perangkat Daerah (OPD)</span>
            <span class="px-2 py-0.5 rounded-full bg-brand-soft text-brand-ink border border-brand-border text-[10px] font-bold tabular-nums">{{ $opdUsers->total() }}</span>
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="mb-6 bg-surface rounded-[10px] p-4 sm:p-5 border border-hairline">
        <form method="GET" action="{{ route('master.opd-users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5 items-end text-xs">
            <div>
                <label class="block font-semibold text-[11px] text-ink-secondary uppercase mb-1">Cari PIC / Email / HP / Instansi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama, instansi, email..." class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs !pl-9 pr-3.5 py-2.5">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-[11px] text-ink-secondary uppercase mb-1">Filter Instansi / Objek Target</label>
                <select name="objek_id" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3.5 py-2.5">
                    <option value="">-- Semua Instansi OPD / Desa --</option>
                    @foreach($objekList as $obj)
                        <option value="{{ $obj->id }}" {{ $objekFilter == $obj->id ? 'selected' : '' }}>{{ $obj->nama }} ({{ $obj->kategori }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-[11px] text-ink-secondary uppercase mb-1">Filter Status Akun</label>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold px-3.5 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Menunggu Aktivasi</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="ledger-btn-primary w-full !text-xs cursor-pointer">Cari & Filter</button>
                @if($search || $objekFilter || $statusFilter)
                    <a href="{{ route('master.opd-users.index') }}" class="ledger-btn-secondary !text-xs text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Akun OPD -->
    <div class="bg-surface rounded-[10px] border border-hairline overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-subtle text-ink-secondary font-bold uppercase tracking-wider border-b border-hairline">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama PIC & Kontak</th>
                        <th class="py-3.5 px-4">Email Objek Pengawasan / Entitas</th>
                        <th class="py-3.5 px-4">Instansi Objek Penugasan</th>
                        <th class="py-3.5 px-4 text-center">Status Akun</th>
                        <th class="py-3.5 px-4">Status Password / Undangan</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($opdUsers as $index => $u)
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3 px-4 font-semibold text-center text-ink-muted">{{ $opdUsers->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-ink">{{ $u->nama }}</p>
                                <span class="text-[11px] font-mono text-ink-secondary flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-brand-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $u->no_hp ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-brand-ink bg-brand-soft px-2 py-0.5 rounded-[4px] border border-brand-border">
                                    {{ $u->email }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-ink">
                                    {{ $u->objekPenugasan?->nama ?? '-' }}
                                </div>
                                <span class="text-[10px] uppercase font-bold text-blue-700 dark:text-blue-300">
                                    {{ $u->objekPenugasan?->kategori ?? 'OPD' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($u->is_active)
                                    <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold bg-surface-subtle text-ink-secondary border border-hairline">
                                        NONAKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($u->status_undangan === 'aktif' || empty($u->token_undangan))
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-ink">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Password Telah Diset
                                    </span>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 dark:text-amber-300">
                                            Menunggu Set Password Mandiri
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <input type="text" readonly value="{{ route('opd.undangan', $u->token_undangan) }}" class="w-48 !min-h-0 !py-1 !px-2 rounded-[6px] border border-hairline-strong bg-surface-subtle text-[10px] font-mono" onclick="this.select()">
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ route('opd.undangan', $u->token_undangan) }}'); alert('Link undangan aktivasi disalin!')" class="p-1.5 text-ink-secondary hover:text-brand-ink bg-surface-subtle hover:bg-hairline border border-hairline rounded-[6px] transition-colors cursor-pointer" title="Salin Link" aria-label="Salin Link">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit & Reset Password -->
                                    <button type="button" onclick="openModalEditOpdUser({
                                        id: {{ $u->id }},
                                        nama: '{{ addslashes($u->nama) }}',
                                        email: '{{ addslashes($u->email) }}',
                                        no_hp: '{{ addslashes($u->no_hp ?? '') }}',
                                        objek_penugasan_id: '{{ $u->objek_penugasan_id }}',
                                        is_active: {{ $u->is_active ? 1 : 0 }}
                                    })" class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="Edit Data & Reset Password">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('master.opd-users.toggle_status', $u->id) }}" class="inline" onsubmit="return confirm('Ubah status keaktifan akun {{ addslashes($u->nama) }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 {{ $u->is_active ? 'bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' }} rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}" aria-label="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            @if($u->is_active)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Hapus Akun -->
                                    <form method="POST" action="{{ route('master.opd-users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun PIC OPD {{ addslashes($u->nama) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-[6px] text-xs font-semibold transition-colors cursor-pointer" title="Hapus Akun">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-muted">
                                <p class="text-sm font-semibold text-ink">Belum ada akun PIC Perangkat Daerah (OPD) yang terdaftar.</p>
                                <p class="text-xs text-ink-secondary mt-1">Klik tombol "Buat Akun PIC OPD Baru" di atas untuk menambahkan akun OPD.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline">
            {{ $opdUsers->links() }}
        </div>
    </div>

    <!-- Modal Tambah Akun OPD -->
    <div id="modalTambahOpdUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-bold text-ink text-base">Buat Akun PIC OPD Baru</h3>
                    <p class="text-[11px] text-ink-secondary">Input kredensial login akun untuk Perangkat Daerah / Desa</p>
                </div>
                <button type="button" onclick="document.getElementById('modalTambahOpdUser').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.opd-users.store') }}" class="space-y-4 mt-4">
                @csrf

                <div>
                    <label class="block font-bold mb-1 text-ink">Nama PIC OPD <span class="text-rose-600">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. PIC Pengawasan Dinas Pendidikan" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-ink">Email Objek Pengawasan / Entitas <span class="text-rose-600">*</span></label>
                    <input type="email" name="email" required placeholder="disdik@trenggalek.go.id" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                    <p class="text-[10px] text-ink-muted mt-1">Digunakan sebagai username saat login di Portal OPD.</p>
                </div>

                <!-- Searchable Select Instansi Objek Penugasan (Modal Tambah) -->
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    selectedLabel: '',
                    options: [
                        @foreach($objekList as $obj)
                            { id: '{{ $obj->id }}', nama: '{{ addslashes($obj->nama) }}', kategori: '{{ strtoupper($obj->kategori) }}', label: '{{ addslashes($obj->nama) }} ({{ strtoupper($obj->kategori) }})' },
                        @endforeach
                    ],
                    get filteredOptions() {
                        if (!this.search.trim()) return this.options;
                        const q = this.search.toLowerCase();
                        return this.options.filter(o => o.nama.toLowerCase().includes(q) || o.kategori.toLowerCase().includes(q));
                    },
                    select(opt) {
                        this.selectedId = opt.id;
                        this.selectedLabel = opt.label;
                        this.open = false;
                        this.search = '';
                    },
                    reset() {
                        this.selectedId = '';
                        this.selectedLabel = '';
                        this.search = '';
                        this.open = false;
                    }
                }" id="tambahObjekDropdownWrapper">
                    <label class="block font-bold mb-1 text-ink">Instansi Objek Penugasan <span class="text-rose-600">*</span></label>
                    <input type="hidden" name="objek_penugasan_id" :value="selectedId" required id="tambahObjekPenugasanId">

                    <div class="relative">
                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.objSearchInput.focus())" @click.outside="open = false"
                            class="w-full text-left px-3.5 py-2.5 rounded-[6px] border border-hairline-strong bg-surface text-xs font-semibold flex items-center justify-between cursor-pointer">
                            <span x-text="selectedId ? selectedLabel : '-- Cari & Pilih Instansi OPD / Kecamatan / Desa --'" :class="selectedId ? 'text-ink font-bold' : 'text-ink-muted font-medium'" class="truncate"></span>
                            <svg class="w-4 h-4 text-ink-muted shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute z-50 mt-1 w-full bg-surface border border-hairline rounded-[10px] shadow-lg p-2.5 space-y-2 max-w-full">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-ink-muted">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" x-model="search" x-ref="objSearchInput" placeholder="Ketik nama instansi / dinas / desa untuk mencari..."
                                    class="w-full rounded-[6px] border-hairline-strong bg-surface-subtle text-ink text-xs !pl-8 pr-3 py-2 font-medium">
                            </div>

                            <div class="max-h-48 overflow-y-auto divide-y divide-hairline custom-scrollbar">
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="select(opt)"
                                        class="w-full text-left px-3 py-2 text-xs font-medium text-ink hover:bg-brand-soft hover:text-brand-ink rounded-[6px] cursor-pointer transition-colors flex items-center justify-between gap-2"
                                        :class="{ 'bg-brand-soft text-brand-ink font-bold': selectedId == opt.id }">
                                        <span x-text="opt.nama" class="truncate"></span>
                                        <span x-text="opt.kategori" class="text-[9px] uppercase px-1.5 py-0.5 rounded-[4px] bg-surface-subtle text-ink-secondary font-bold shrink-0"></span>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="p-3 text-center text-ink-muted text-xs">
                                    Tidak ada instansi yang cocok dengan kata kunci.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold mb-1 text-ink">Nomor WhatsApp / HP (Opsional)</label>
                    <input type="text" name="no_hp" placeholder="Nomor WhatsApp / HP aktif" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-mono">
                    <p class="text-[10px] text-ink-muted mt-1">Untuk notifikasi otomatis verifikasi & tenggat waktu LHP.</p>
                </div>

                <div class="p-3 bg-surface-subtle rounded-[6px] border border-hairline space-y-2">
                    <label class="block font-bold text-brand-ink">Kata Sandi / Password Awal (Opsional)</label>
                    <input type="text" name="password" minlength="6" placeholder="mis. Password123!" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-mono">
                    <p class="text-[10px] text-ink-secondary">
                        <strong>Tips:</strong> Jika diisi langsung, OPD dapat langsung login menggunakan password ini. Jika dikosongkan, sistem akan membuatkan Link Undangan Aktivasi Mandiri.
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalTambahOpdUser').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan & Terbitkan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Akun OPD & Reset Password -->
    <div id="modalEditOpdUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-surface rounded-[10px] shadow-lg max-w-md w-full p-6 border border-hairline text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-hairline">
                <div>
                    <h3 class="font-bold text-ink text-base">Edit Data Objek Pengawasan / Entitas</h3>
                    <p class="text-[11px] text-ink-secondary">Perbarui profil atau ubah kata sandi akun</p>
                </div>
                <button type="button" onclick="document.getElementById('modalEditOpdUser').classList.add('hidden')" class="text-ink-muted hover:text-ink text-xl font-bold cursor-pointer" aria-label="Tutup">&times;</button>
            </div>

            <form id="formEditOpdUser" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold mb-1 text-ink">Nama PIC OPD <span class="text-rose-600">*</span></label>
                    <input type="text" id="editNama" name="nama" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-ink">Email Objek Pengawasan / Entitas <span class="text-rose-600">*</span></label>
                    <input type="email" id="editEmail" name="email" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                </div>

                <!-- Searchable Select Instansi Objek Penugasan (Modal Edit) -->
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    selectedLabel: '',
                    options: [
                        @foreach($objekList as $obj)
                            { id: '{{ $obj->id }}', nama: '{{ addslashes($obj->nama) }}', kategori: '{{ strtoupper($obj->kategori) }}', label: '{{ addslashes($obj->nama) }} ({{ strtoupper($obj->kategori) }})' },
                        @endforeach
                    ],
                    get filteredOptions() {
                        if (!this.search.trim()) return this.options;
                        const q = this.search.toLowerCase();
                        return this.options.filter(o => o.nama.toLowerCase().includes(q) || o.kategori.toLowerCase().includes(q));
                    },
                    select(opt) {
                        this.selectedId = opt.id;
                        this.selectedLabel = opt.label;
                        this.open = false;
                        this.search = '';
                    },
                    setSelectedById(id) {
                        this.selectedId = id;
                        const found = this.options.find(o => o.id == id);
                        this.selectedLabel = found ? found.label : '';
                        this.search = '';
                        this.open = false;
                    }
                }" id="editObjekDropdownWrapper">
                    <label class="block font-bold mb-1 text-ink">Instansi Objek Penugasan <span class="text-rose-600">*</span></label>
                    <input type="hidden" name="objek_penugasan_id" :value="selectedId" required id="editObjekPenugasanId">

                    <div class="relative">
                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.editObjSearchInput.focus())" @click.outside="open = false"
                            class="w-full text-left px-3.5 py-2.5 rounded-[6px] border border-hairline-strong bg-surface text-xs font-semibold flex items-center justify-between cursor-pointer">
                            <span x-text="selectedId ? selectedLabel : '-- Cari & Pilih Instansi OPD / Kecamatan / Desa --'" :class="selectedId ? 'text-ink font-bold' : 'text-ink-muted font-medium'" class="truncate"></span>
                            <svg class="w-4 h-4 text-ink-muted shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute z-50 mt-1 w-full bg-surface border border-hairline rounded-[10px] shadow-lg p-2.5 space-y-2 max-w-full">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-ink-muted">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" x-model="search" x-ref="editObjSearchInput" placeholder="Ketik nama instansi / dinas / desa untuk mencari..."
                                    class="w-full rounded-[6px] border-hairline-strong bg-surface-subtle text-ink text-xs !pl-8 pr-3 py-2 font-medium">
                            </div>

                            <div class="max-h-48 overflow-y-auto divide-y divide-hairline custom-scrollbar">
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="select(opt)"
                                        class="w-full text-left px-3 py-2 text-xs font-medium text-ink hover:bg-brand-soft hover:text-brand-ink rounded-[6px] cursor-pointer transition-colors flex items-center justify-between gap-2"
                                        :class="{ 'bg-brand-soft text-brand-ink font-bold': selectedId == opt.id }">
                                        <span x-text="opt.nama" class="truncate"></span>
                                        <span x-text="opt.kategori" class="text-[9px] uppercase px-1.5 py-0.5 rounded-[4px] bg-surface-subtle text-ink-secondary font-bold shrink-0"></span>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="p-3 text-center text-ink-muted text-xs">
                                    Tidak ada instansi yang cocok dengan kata kunci.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold mb-1 text-ink">Nomor WhatsApp / HP</label>
                    <input type="text" id="editNoHp" name="no_hp" placeholder="Nomor WhatsApp / HP aktif" class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-mono">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-ink">Status Akun</label>
                    <select id="editIsActive" name="is_active" required class="w-full rounded-[6px] border-hairline-strong bg-surface text-ink text-xs font-semibold">
                        <option value="1">AKTIF (Dapat Login)</option>
                        <option value="0">NONAKTIF (Akses Dinonaktifkan)</option>
                    </select>
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-[6px] border border-amber-200 dark:border-amber-900 space-y-2">
                    <label class="block font-bold text-amber-900 dark:text-amber-300">Reset / Ganti Password Baru (Opsional)</label>
                    <input type="text" name="password" minlength="6" placeholder="Ketik kata sandi baru jika ingin mengganti..." class="w-full rounded-[6px] border-amber-300 dark:border-amber-700 bg-surface text-ink text-xs font-mono">
                    <p class="text-[10px] text-amber-800 dark:text-amber-300">
                        Kosongkan kolom ini jika tidak ingin mengubah kata sandi akun.
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-hairline">
                    <button type="button" onclick="document.getElementById('modalEditOpdUser').classList.add('hidden')" class="ledger-btn-secondary !text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="ledger-btn-primary !text-xs cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalTambahOpdUser() {
            const tambahWrapper = document.getElementById('tambahObjekDropdownWrapper');
            if (tambahWrapper && window.Alpine) {
                const alpineData = Alpine.$data(tambahWrapper);
                if (alpineData && alpineData.reset) {
                    alpineData.reset();
                }
            }
            document.getElementById('modalTambahOpdUser').classList.remove('hidden');
        }

        function openModalEditOpdUser(data) {
            const form = document.getElementById('formEditOpdUser');
            form.action = '/master/opd-users/' + data.id;
            document.getElementById('editNama').value = data.nama;
            document.getElementById('editEmail').value = data.email;
            document.getElementById('editNoHp').value = data.no_hp || '';
            document.getElementById('editIsActive').value = data.is_active;

            const editWrapper = document.getElementById('editObjekDropdownWrapper');
            if (editWrapper && window.Alpine) {
                const alpineData = Alpine.$data(editWrapper);
                if (alpineData && alpineData.setSelectedById) {
                    alpineData.setSelectedById(data.objek_penugasan_id);
                }
            }

            document.getElementById('modalEditOpdUser').classList.remove('hidden');
        }
    </script>
</x-app-layout>
