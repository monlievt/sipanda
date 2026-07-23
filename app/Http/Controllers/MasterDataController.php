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

    /** Update Role & Irban Pengguna */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role'      => ['required', 'exists:roles,name'],
            'irban_id'  => ['nullable', 'exists:irbans,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->syncRoles([$validated['role']]);
        $user->update([
            'irban_id'  => $validated['irban_id'],
            'is_active' => $validated['is_active'],
        ]);

        ActivityLog::catat('users', $user->id, 'update', null, $user->toArray());

        return back()->with('status', "Data & role pengguna '{$user->nama_display}' berhasil diperbarui.");
    }

    /**
     * Master Data: Objek Penugasan (OPD/Kecamatan/Desa).
     */
    public function objekPenugasan(): View
    {
        $listObjek = ObjekPenugasan::orderBy('kategori')->orderBy('nama')->paginate(20);
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

    /**
     * Master Data: Jenis Penugasan (Assurance / Consulting).
     */
    public function jenisPenugasan(): View
    {
        $listJenis = JenisPenugasan::orderBy('kategori')->orderBy('nama')->get();
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

    /**
     * Audit Log Viewer (Admin & Inspektur).
     */
    public function auditLog(): View
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(25);
        return view('master.audit-log', compact('logs'));
    }
}
