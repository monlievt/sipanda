<x-app-layout>
    <x-slot name="header">
        Kelola Pengguna Internal (69 Pegawai Inspektorat)
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
                                <span class="block font-semibold text-slate-800 dark:text-slate-200">{{ $u->jabatan }}</span>
                                <span class="text-[10px] text-slate-400">{{ $u->pangkat }} ({{ $u->golongan }})</span>
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
                                <button onclick="openModalEditUser({{ $u->id }}, '{{ $u->roles->first()?->name }}', '{{ $u->irban_id }}', {{ $u->is_active ? 1 : 0 }})" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-[10px] font-bold transition-colors cursor-pointer">
                                    Edit Role
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada pengguna yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $listUsers->links() }}
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="modalEditUser" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 text-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Role & Unit Irban Pengguna</h3>
                <button onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="formEditUser" method="POST" action="" class="space-y-4 mt-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold mb-1">Role Hak Akses <span class="text-rose-500">*</span></label>
                    <select id="userRoleSelect" name="role" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Unit Irban</label>
                    <select id="userIrbanSelect" name="irban_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <option value="">-- Sekretariat / Lintas Irban --</option>
                        @foreach($irbans as $irban)
                            <option value="{{ $irban->id }}">{{ $irban->nama_irban }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Status Akun</label>
                    <select id="userActiveSelect" name="is_active" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalEditUser(id, role, irbanId, isActive) {
            document.getElementById('formEditUser').action = '/master/users/' + id;
            document.getElementById('userRoleSelect').value = role;
            document.getElementById('userIrbanSelect').value = irbanId || '';
            document.getElementById('userActiveSelect').value = isActive;
            document.getElementById('modalEditUser').classList.remove('hidden');
        }
    </script>
</x-app-layout>
