<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kelola Data Pegawai Internal</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar Auditor, PPUPD, dan Pejabat Pengawasan Inspektorat Kabupaten Trenggalek</p>
            </div>
            @can('users.create')
            <div>
                <button onclick="openModalCreateUser()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pegawai Baru
                </button>
            </div>
            @endcan
        </div>
    </x-slot>

    <!-- Filter & Search -->
    <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('master.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5 items-end text-sm">
            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Cari Nama / NIP / Email</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NIP / Email..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Filter Role</label>
                <select name="role" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-xs text-slate-500 uppercase mb-1">Filter Irban</label>
                <select name="irban_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Irban --</option>
                    @foreach($irbans as $irban)
                        <option value="{{ $irban->id }}" {{ $irbanFilter == $irban->id ? 'selected' : '' }}>{{ $irban->nama_irban }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-sm shadow-xs transition-all cursor-pointer">Filter</button>
                @if($search || $roleFilter || $irbanFilter)
                    <a href="{{ route('master.users.index') }}" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm transition-all text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Users -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Pegawai</th>
                        <th class="py-3.5 px-4">NIP</th>
                        <th class="py-3.5 px-4">Jabatan / Golongan</th>
                        <th class="py-3.5 px-4">Kontak (WhatsApp)</th>
                        <th class="py-3.5 px-4">Irban Unit</th>
                        <th class="py-3.5 px-4 text-center">Role Akses</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($listUsers as $index => $u)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-semibold text-center text-slate-500">{{ $listUsers->firstItem() + $index }}</td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $u->nama }}</p>
                                <span class="text-[10px] text-slate-400">{{ $u->email }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-700 dark:text-slate-300">{{ $u->nip ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $u->jabatan ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $u->pangkat ?? '-' }} {{ $u->golongan ? "({$u->golongan})" : '' }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                                {{ $u->no_hp ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ $u->irban?->nama_irban ?? 'Sekretariat' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">
                                    {{ $u->roles->first()?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @can('users.edit')
                                <form method="POST" action="{{ route('master.users.toggle_status', $u->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status keaktifan {{ addslashes($u->nama_display) }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all cursor-pointer shadow-xs {{ $u->is_active ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 hover:bg-rose-200 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}" title="Klik untuk mengubah status">
                                        {{ $u->is_active ? '● Aktif' : '○ Nonaktif' }}
                                    </button>
                                </form>
                                @else
                                    @if($u->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Nonaktif</span>
                                    @endif
                                @endcan
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @can('users.edit')
                                <button onclick="openModalEditUser({{ json_encode($u) }}, '{{ $u->roles->first()?->name }}')" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-[10px] font-bold transition-colors cursor-pointer inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada pengguna yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $listUsers->links() }}
        </div>
    </div>

    <!-- Modal Tambah Pegawai Baru -->
    <div id="modalCreateUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Pegawai Internal Baru</h3>
                    <p class="text-[11px] text-slate-500">Daftarkan data auditor/pejabat pengawasan baru ke dalam database.</p>
                </div>
                <button onclick="document.getElementById('modalCreateUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('master.users.store') }}" class="space-y-3.5 mt-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Nama Lengkap (dg Gelar) <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Contoh: AHMAD FAUZI, SE.M.Si" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Nama Panggilan / Sapaan</label>
                        <input type="text" name="nama_tanpa_gelar" placeholder="Contoh: Pak Fauzi" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">NIP Pegawai <span class="text-rose-500">*</span></label>
                        <input type="text" name="nip" required placeholder="1980xxxxxxxxxxxxxx" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="nama@trenggalek.go.id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" placeholder="081234567890" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <span class="text-[10px] text-slate-400">Untuk pengiriman notifikasi penugasan via WA.</span>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jabatan Fungsional/Struktural</label>
                        <input type="text" name="jabatan" placeholder="Contoh: AUDITOR AHLI MUDA" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Pangkat</label>
                        <input type="text" name="pangkat" placeholder="Contoh: Penata Tingkat I" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Golongan Ruang</label>
                        <input type="text" name="golongan" placeholder="Contoh: III/d" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Unit Kerja / Irban</label>
                        <select name="irban_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="">-- Sekretariat / Umum --</option>
                            @foreach($irbans as $irban)
                                <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Role Hak Akses <span class="text-rose-500">*</span></label>
                        <select name="role" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ $r->name === 'anggota' ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Password Awal (Opsional)</label>
                    <input type="text" name="password" placeholder="Default: Sesuai NIP Pegawai" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalCreateUser').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Pegawai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="modalEditUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Data Pegawai & Role</h3>
                    <p class="text-[11px] text-slate-500">Perbarui profil, nomor kontak, jabatan, unit, atau hak akses.</p>
                </div>
                <button onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEditUser" method="POST" action="" class="space-y-3.5 mt-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Nama Pegawai <span class="text-rose-500">*</span></label>
                        <input type="text" id="editNama" name="nama" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">NIP Pegawai <span class="text-rose-500">*</span></label>
                        <input type="text" id="editNip" name="nip" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" id="editEmail" name="email" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">No. WhatsApp / HP</label>
                        <input type="text" id="editNoHp" name="no_hp" placeholder="081234567890" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Jabatan</label>
                        <input type="text" id="editJabatan" name="jabatan" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Pangkat</label>
                        <input type="text" id="editPangkat" name="pangkat" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Golongan</label>
                        <input type="text" id="editGolongan" name="golongan" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Unit Kerja / Irban</label>
                        <select id="editIrbanId" name="irban_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="">-- Sekretariat --</option>
                            @foreach($irbans as $irban)
                                <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Role Akses <span class="text-rose-500">*</span></label>
                        <select id="editRole" name="role" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Status Akun <span class="text-rose-500">*</span></label>
                        <select id="editIsActive" name="is_active" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalCreateUser() {
            document.getElementById('modalCreateUser').classList.remove('hidden');
        }

        function openModalEditUser(user, role) {
            document.getElementById('formEditUser').action = '/master/users/' + user.id;
            document.getElementById('editNama').value = user.nama || '';
            document.getElementById('editNip').value = user.nip || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editNoHp').value = user.no_hp || '';
            document.getElementById('editJabatan').value = user.jabatan || '';
            document.getElementById('editPangkat').value = user.pangkat || '';
            document.getElementById('editGolongan').value = user.golongan || '';
            document.getElementById('editIrbanId').value = user.irban_id || '';
            document.getElementById('editRole').value = role || '';
            document.getElementById('editIsActive').value = user.is_active ? 1 : 0;
            document.getElementById('modalEditUser').classList.remove('hidden');
        }
    </script>
</x-app-layout>

