<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Akun PIC Perangkat Daerah (Portal OPD)</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penetapan akun akses, username login, dan kata sandi auditi/OPD untuk menindaklanjuti rekomendasi LHP</p>
            </div>
            @can('opd_users.manage')
            <div>
                <button onclick="openModalTambahOpdUser()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    + Buat Akun PIC OPD Baru
                </button>
            </div>
            @endcan
        </div>
    </x-slot>

    <!-- Tab Navigasi Manajemen Pengguna -->
    <div class="mb-6 flex border-b border-slate-200 dark:border-slate-800 gap-2">
        <a href="{{ route('master.users.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 border-b-2 border-transparent transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>👥 Pegawai Internal APIP</span>
        </a>
        <a href="{{ route('master.opd-users.index') }}" class="px-4 py-2.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 border-b-2 border-emerald-600 dark:border-emerald-400 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>🏛️ Akun PIC Perangkat Daerah (OPD)</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-black">{{ $opdUsers->total() }}</span>
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('master.opd-users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 items-end text-xs">
            <div>
                <label class="block font-semibold text-[11px] text-slate-500 uppercase mb-1">Cari Nama PIC / Email / No. HP</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau email..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-semibold text-[11px] text-slate-500 uppercase mb-1">Filter Instansi / Objek Target</label>
                <select name="objek_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Instansi OPD / Desa --</option>
                    @foreach($objekList as $obj)
                        <option value="{{ $obj->id }}" {{ $objekFilter == $obj->id ? 'selected' : '' }}>{{ $obj->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-xs transition-all cursor-pointer">Filter</button>
                @if($search || $objekFilter)
                    <a href="{{ route('master.opd-users.index') }}" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Akun OPD -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama PIC & Kontak</th>
                        <th class="py-3.5 px-4">Username / Email Login OPD</th>
                        <th class="py-3.5 px-4">Instansi Objek Penugasan</th>
                        <th class="py-3.5 px-4 text-center">Status Akun</th>
                        <th class="py-3.5 px-4">Status Password / Undangan</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($opdUsers as $index => $u)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $opdUsers->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $u->nama }}</p>
                                <span class="text-[11px] font-mono text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $u->no_hp ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                    {{ $u->email }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $u->objekPenugasan?->nama ?? '-' }}
                                </div>
                                <span class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400">
                                    {{ $u->objekPenugasan?->kategori ?? 'OPD' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($u->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                        NONAKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($u->status_undangan === 'aktif' || empty($u->token_undangan))
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Password Telah Diset
                                    </span>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                            ⏳ Menunggu Set Password Mandiri
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <input type="text" readonly value="{{ route('opd.undangan', $u->token_undangan) }}" class="w-48 p-1 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[10px] font-mono" onclick="this.select()">
                                            <button type="button" onclick="navigator.clipboard.writeText('{{ route('opd.undangan', $u->token_undangan) }}'); alert('✓ Link undangan aktivasi disalin!')" class="p-1 text-slate-500 hover:text-emerald-600 rounded" title="Salin Link">
                                                📋
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
                                    })" class="p-1.5 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Edit Data & Reset Password">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('master.opd-users.toggle_status', $u->id) }}" class="inline" onsubmit="return confirm('Ubah status keaktifan akun {{ addslashes($u->nama) }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 {{ $u->is_active ? 'bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 text-amber-600' : 'bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-600' }} rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            @if($u->is_active)
                                                🔒
                                            @else
                                                🔓
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Hapus Akun -->
                                    <form method="POST" action="{{ route('master.opd-users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun PIC OPD {{ addslashes($u->nama) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-semibold transition-colors cursor-pointer" title="Hapus Akun">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold">Belum ada akun PIC Perangkat Daerah (OPD) yang terdaftar.</p>
                                <p class="text-xs text-slate-500 mt-1">Klik tombol "+ Buat Akun PIC OPD Baru" di atas untuk menambahkan akun OPD.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $opdUsers->links() }}
        </div>
    </div>

    <!-- Modal Tambah Akun OPD -->
    <div id="modalTambahOpdUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Buat Akun PIC OPD Baru</h3>
                    <p class="text-[11px] text-slate-500">Input kredensial login akun untuk Perangkat Daerah / Desa</p>
                </div>
                <button onclick="document.getElementById('modalTambahOpdUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.opd-users.store') }}" class="space-y-4 mt-4">
                @csrf

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Nama PIC OPD <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="mis. PIC Pengawasan Dinas Pendidikan" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Username / Email Resmi OPD <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="disdik@trenggalek.go.id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">Digunakan sebagai username saat login di Portal OPD.</p>
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Instansi Objek Penugasan <span class="text-rose-500">*</span></label>
                    <select name="objek_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200">
                        <option value="">-- Pilih Instansi OPD / Kecamatan / Desa --</option>
                        @foreach($objekList as $obj)
                            <option value="{{ $obj->id }}">{{ $obj->nama }} ({{ strtoupper($obj->kategori) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Nomor WhatsApp / HP (Opsional)</label>
                    <input type="text" name="no_hp" placeholder="081234567890" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Untuk notifikasi otomatis verifikasi & tenggat waktu LHP.</p>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                    <label class="block font-bold text-emerald-700 dark:text-emerald-400">Kata Sandi / Password Awal (Opsional)</label>
                    <input type="text" name="password" minlength="6" placeholder="mis. Password123!" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    <p class="text-[10px] text-slate-500">
                        💡 <strong>Tips:</strong> Jika diisi langsung, OPD dapat langsung login menggunakan password ini. Jika dikosongkan, sistem akan membuatkan Link Undangan Aktivasi Mandiri.
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahOpdUser').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan & Terbitkan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Akun OPD & Reset Password -->
    <div id="modalEditOpdUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Data & Reset Password OPD</h3>
                    <p class="text-[11px] text-slate-500">Perbarui profil atau ubah kata sandi akun</p>
                </div>
                <button onclick="document.getElementById('modalEditOpdUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form id="formEditOpdUser" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Nama PIC OPD <span class="text-rose-500">*</span></label>
                    <input type="text" id="editNama" name="nama" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Username / Email Resmi OPD <span class="text-rose-500">*</span></label>
                    <input type="email" id="editEmail" name="email" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Instansi Objek Penugasan <span class="text-rose-500">*</span></label>
                    <select id="editObjekPenugasanId" name="objek_penugasan_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200">
                        @foreach($objekList as $obj)
                            <option value="{{ $obj->id }}">{{ $obj->nama }} ({{ strtoupper($obj->kategori) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Nomor WhatsApp / HP</label>
                    <input type="text" id="editNoHp" name="no_hp" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-bold mb-1 text-slate-800 dark:text-slate-200">Status Akun</label>
                    <select id="editIsActive" name="is_active" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold">
                        <option value="1">AKTIF (Dapat Login)</option>
                        <option value="0">NONAKTIF (Akses Dinonaktifkan)</option>
                    </select>
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-900 space-y-2">
                    <label class="block font-bold text-amber-800 dark:text-amber-300">Reset / Ganti Password Baru (Opsional)</label>
                    <input type="text" name="password" minlength="6" placeholder="Ketik kata sandi baru jika ingin mengganti..." class="w-full rounded-xl border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    <p class="text-[10px] text-amber-700 dark:text-amber-400">
                        Kosongkan kolom ini jika tidak ingin mengubah kata sandi akun.
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditOpdUser').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalTambahOpdUser() {
            document.getElementById('modalTambahOpdUser').classList.remove('hidden');
        }

        function openModalEditOpdUser(data) {
            const form = document.getElementById('formEditOpdUser');
            form.action = '/master/opd-users/' + data.id;
            document.getElementById('editNama').value = data.nama;
            document.getElementById('editEmail').value = data.email;
            document.getElementById('editNoHp').value = data.no_hp || '';
            document.getElementById('editObjekPenugasanId').value = data.objek_penugasan_id;
            document.getElementById('editIsActive').value = data.is_active;
            document.getElementById('modalEditOpdUser').classList.remove('hidden');
        }
    </script>
</x-app-layout>
