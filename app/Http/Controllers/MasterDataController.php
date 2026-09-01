<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class MasterDataController extends Controller
{
    /**
     * Master Data: Kelola Pengguna Internal (69 Pegawai).
     */
    public function users(Request $request): View
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $irbanFilter = $request->input('irban_id');

        $query = User::internal()->with(['roles', 'irban']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->role($roleFilter);
        }

        if ($irbanFilter) {
            $query->where('irban_id', $irbanFilter);
        }

        $listUsers = $query->orderBy('nama')->paginate(15)->withQueryString();

        $roles = Role::where('guard_name', 'web')->get();
        $irbans = Irban::all();

        return view('master.users', compact('listUsers', 'roles', 'irbans', 'search', 'roleFilter', 'irbanFilter'));
    }

    /** Tambah Pegawai Internal Baru */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'             => ['required', 'string', 'max:150'],
            'nama_tanpa_gelar' => ['nullable', 'string', 'max:150'],
            'nip'              => ['required', 'string', 'max:30', 'unique:users,nip'],
            'email'            => ['required', 'email', 'max:150', 'unique:users,email'],
            'no_hp'            => ['nullable', 'string', 'max:25'],
            'jabatan'          => ['nullable', 'string', 'max:150'],
            'pangkat'          => ['nullable', 'string', 'max:100'],
            'golongan'         => ['nullable', 'string', 'max:50'],
            'irban_id'         => ['nullable', 'exists:irbans,id'],
            'role'             => ['required', 'exists:roles,name'],
            'password'         => ['nullable', 'string', 'min:6'],
        ], [
            'nama.required'  => 'Nama pegawai wajib diisi.',
            'nip.required'   => 'NIP wajib diisi.',
            'nip.unique'     => 'NIP tersebut sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email tersebut sudah terdaftar.',
            'role.required'  => 'Role akses wajib dipilih.',
        ]);

        $defaultPassword = $validated['password'] ?: ($validated['nip'] ?: 'password123');

        $user = User::create([
            'nama'             => $validated['nama'],
            'nama_tanpa_gelar' => $validated['nama_tanpa_gelar'] ?: preg_replace('/,.*$/', '', $validated['nama']),
            'nip'              => $validated['nip'],
            'email'            => $validated['email'],
            'no_hp'            => $validated['no_hp'],
            'jabatan'          => $validated['jabatan'],
            'pangkat'          => $validated['pangkat'],
            'golongan'         => $validated['golongan'],
            'irban_id'         => $validated['irban_id'],
            'password'         => \Illuminate\Support\Facades\Hash::make($defaultPassword),
            'tipe_akun'        => 'internal',
            'is_active'        => true,
        ]);

        $user->assignRole($validated['role']);

        ActivityLog::catat('users', $user->id, 'create', null, $user->toArray());

        return back()->with('status', "✓ Pegawai baru '{$user->nama}' berhasil ditambahkan ke database!");
    }

    /** Update Lengkap Data Pegawai & Role */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:150'],
            'nip'       => ['required', 'string', 'max:30', 'unique:users,nip,' . $user->id],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'no_hp'     => ['nullable', 'string', 'max:25'],
            'jabatan'   => ['nullable', 'string', 'max:150'],
            'pangkat'   => ['nullable', 'string', 'max:100'],
            'golongan'  => ['nullable', 'string', 'max:50'],
            'role'      => ['required', 'exists:roles,name'],
            'irban_id'  => ['nullable', 'exists:irbans,id'],
            'is_active' => ['required', 'boolean'],
            'password'  => ['nullable', 'string', 'min:6'],
        ]);

        $sebelum = $user->toArray();

        $updateData = [
            'nama'      => $validated['nama'],
            'nip'       => $validated['nip'],
            'email'     => $validated['email'],
            'no_hp'     => $validated['no_hp'],
            'jabatan'   => $validated['jabatan'],
            'pangkat'   => $validated['pangkat'],
            'golongan'  => $validated['golongan'],
            'irban_id'  => $validated['irban_id'],
            'is_active' => $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        ActivityLog::catat('users', $user->id, 'update', $sebelum, $user->toArray());

        return back()->with('status', "Data pegawai '{$user->nama_display}' berhasil diperbarui.");
    }

    /**
     * Master Data: Objek Penugasan (OPD/Kecamatan/Desa).
     */
    public function objekPenugasan(): View
    {
        $listObjek = ObjekPenugasan::withCount(['penugasan', 'akunOpd'])->orderBy('kategori')->orderBy('nama')->paginate(20);
        return view('master.objek-penugasan', compact('listObjek'));
    }

    public function storeObjekPenugasan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:150', 'unique:objek_penugasan,nama'],
            'kategori' => ['required', 'in:opd,kecamatan,desa,kelurahan,lainnya'],
        ]);

        $objek = ObjekPenugasan::create($validated);
        ActivityLog::catat('objek_penugasan', $objek->id, 'create', null, $objek->toArray());

        return back()->with('status', "Objek penugasan '{$objek->nama}' berhasil ditambahkan.");
    }

    public function updateObjekPenugasan(Request $request, ObjekPenugasan $objek): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:150', 'unique:objek_penugasan,nama,' . $objek->id],
            'kategori'  => ['required', 'in:opd,kecamatan,desa,kelurahan,lainnya'],
            'is_active' => ['required', 'boolean'],
        ]);

        $sebelum = $objek->toArray();
        $objek->update($validated);
        ActivityLog::catat('objek_penugasan', $objek->id, 'update', $sebelum, $objek->toArray());

        return back()->with('status', "Objek penugasan '{$objek->nama}' berhasil diperbarui.");
    }

    public function destroyObjekPenugasan(ObjekPenugasan $objek): RedirectResponse
    {
        if ($objek->penugasan()->exists() || $objek->akunOpd()->exists()) {
            return back()->with('error', "Objek '{$objek->nama}' tidak dapat dihapus karena sudah memiliki riwayat penugasan atau akun OPD terkait. Anda dapat menonaktifkannya.");
        }

        $sebelum = $objek->toArray();
        $nama = $objek->nama;
        $objek->delete();
        ActivityLog::catat('objek_penugasan', $sebelum['id'], 'delete', $sebelum, null);

        return back()->with('status', "Objek penugasan '{$nama}' berhasil dihapus.");
    }

    /**
     * Master Data: Jenis Penugasan (Assurance / Consulting).
     */
    public function jenisPenugasan(): View
    {
        $listJenis = JenisPenugasan::withCount('penugasan')->orderBy('kategori')->orderBy('nama')->get();
        return view('master.jenis-penugasan', compact('listJenis'));
    }

    public function storeJenisPenugasan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'unique:jenis_penugasan,nama'],
            'kategori' => ['required', 'in:assurance,consulting'],
        ]);

        $jenis = JenisPenugasan::create($validated);
        ActivityLog::catat('jenis_penugasan', $jenis->id, 'create', null, $jenis->toArray());

        return back()->with('status', "Jenis penugasan baru '{$jenis->nama}' ({$jenis->kategori}) berhasil ditambahkan!");
    }

    public function updateJenisPenugasan(Request $request, JenisPenugasan $jenis): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100', 'unique:jenis_penugasan,nama,' . $jenis->id],
            'kategori' => ['required', 'in:assurance,consulting'],
        ]);

        $sebelum = $jenis->toArray();
        $jenis->update($validated);
        ActivityLog::catat('jenis_penugasan', $jenis->id, 'update', $sebelum, $jenis->toArray());

        return back()->with('status', "Jenis penugasan '{$jenis->nama}' berhasil diperbarui.");
    }

    public function destroyJenisPenugasan(JenisPenugasan $jenis): RedirectResponse
    {
        if ($jenis->penugasan()->exists()) {
            return back()->with('error', "Jenis penugasan '{$jenis->nama}' tidak dapat dihapus karena telah digunakan pada SPT penugasan.");
        }

        $sebelum = $jenis->toArray();
        $nama = $jenis->nama;
        $jenis->delete();
        ActivityLog::catat('jenis_penugasan', $sebelum['id'], 'delete', $sebelum, null);

        return back()->with('status', "Jenis penugasan '{$nama}' berhasil dihapus.");
    }

    /**
     * Quick Toggle Status Aktif / Nonaktif Pengguna
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        $sebelum = $user->toArray();
        $user->is_active = ! $user->is_active;
        $user->save();

        ActivityLog::catat('users', $user->id, 'update', $sebelum, $user->toArray());

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Status pengguna '{$user->nama_display}' berhasil {$statusText}.");
    }

    /**
     * Audit Log Viewer (Admin & Inspektur) dengan Fitur Filter Lengkap.
     */
    public function auditLog(Request $request): View
    {
        $tabel = $request->input('tabel');
        $aksi  = $request->input('aksi');
        $dari  = $request->input('dari');
        $sampai = $request->input('sampai');
        $userId = $request->input('user_id');
        $search = $request->input('search');

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($tabel) {
            $query->where('tabel', $tabel);
        }
        if ($aksi) {
            $query->where('aksi', $aksi);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($dari) {
            $query->whereDate('created_at', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('created_at', '<=', $sampai);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('record_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $tabelList = ActivityLog::distinct()->orderBy('tabel')->pluck('tabel');
        $userList  = User::internal()->orderBy('nama')->get();

        return view('master.audit-log', compact('logs', 'tabelList', 'userList', 'tabel', 'aksi', 'dari', 'sampai', 'userId', 'search'));
    }
}
