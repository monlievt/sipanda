<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ObjekPenugasan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpdUserManagementController extends Controller
{
    /**
     * Tampilkan daftar akun PIC OPD.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $objekFilter = $request->input('objek_id');

        $query = User::with('objekPenugasan')
            ->where('tipe_akun', 'opd')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhereHas('objekPenugasan', fn($oq) => $oq->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($objekFilter) {
            $query->where('objek_penugasan_id', $objekFilter);
        }

        $opdUsers = $query->paginate(20)->withQueryString();
        $objekList = ObjekPenugasan::aktif()->orderBy('nama')->get();

        return view('master.opd-users', compact('opdUsers', 'objekList', 'search', 'objekFilter'));
    }

    /**
     * Buat akun PIC OPD baru (Mendukung Input Password Langsung atau Token Undangan Aktivasi).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'               => ['required', 'string', 'max:150'],
            'email'              => ['required', 'email', 'unique:users,email'],
            'objek_penugasan_id' => ['required', 'exists:objek_penugasan,id'],
            'no_hp'              => ['nullable', 'string', 'max:20'],
            'password'           => ['nullable', 'string', 'min:6'],
        ]);

        $passwordInput = $request->input('password');
        $token = null;
        $statusUndangan = 'aktif';

        if (! empty($passwordInput)) {
            $passwordHash = Hash::make($passwordInput);
            $tokenKedaluwarsa = null;
        } else {
            // Jika password kosong, buat token undangan aktivasi mandiri
            $token = Str::random(40);
            $passwordHash = Hash::make(Str::random(16)); // temporary random password
            $statusUndangan = 'pending';
            $tokenKedaluwarsa = now()->addDays(3);
        }

        $user = User::create([
            'nama'               => $validated['nama'],
            'email'              => strtolower(trim($validated['email'])),
            'no_hp'              => $validated['no_hp'],
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $validated['objek_penugasan_id'],
            'password'           => $passwordHash,
            'status_undangan'    => $statusUndangan,
            'token_undangan'     => $token,
            'token_kedaluwarsa'  => $tokenKedaluwarsa,
            'is_active'          => true,
        ]);

        $user->assignRole('opd');

        ActivityLog::catat('users', $user->id, 'create', null, $user->toArray());

        if ($token) {
            $linkUndangan = route('opd.undangan', $token);
            return back()->with('status', "✓ Akun PIC OPD '{$user->nama}' berhasil dibuat! Link aktivasi: {$linkUndangan}");
        }

        return back()->with('status', "✓ Akun PIC OPD '{$user->nama}' ({$user->email}) berhasil dibuat dan dapat langsung digunakan untuk login!");
    }

    /**
     * Update Data Akun PIC OPD & Ganti Password.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->tipe_akun !== 'opd') {
            return back()->with('error', 'Aksi ini hanya untuk akun OPD.');
        }

        $validated = $request->validate([
            'nama'               => ['required', 'string', 'max:150'],
            'email'              => ['required', 'email', 'unique:users,email,' . $user->id],
            'objek_penugasan_id' => ['required', 'exists:objek_penugasan,id'],
            'no_hp'              => ['nullable', 'string', 'max:20'],
            'password'           => ['nullable', 'string', 'min:6'],
            'is_active'          => ['required', 'boolean'],
        ]);

        $sebelum = $user->toArray();

        $updateData = [
            'nama'               => $validated['nama'],
            'email'              => strtolower(trim($validated['email'])),
            'objek_penugasan_id' => $validated['objek_penugasan_id'],
            'no_hp'              => $validated['no_hp'],
            'is_active'          => $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
            $updateData['status_undangan'] = 'aktif';
            $updateData['token_undangan'] = null;
        }

        $user->update($updateData);
        ActivityLog::catat('users', $user->id, 'update', $sebelum, $user->toArray());

        return back()->with('status', "✓ Data akun PIC OPD '{$user->nama}' berhasil diperbarui.");
    }

    /**
     * Quick Toggle Aktif / Nonaktif Akun OPD.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->tipe_akun !== 'opd') {
            return back()->with('error', 'Aksi ini hanya untuk akun OPD.');
        }

        $sebelum = $user->toArray();
        $user->is_active = ! $user->is_active;
        $user->save();

        ActivityLog::catat('users', $user->id, 'update', $sebelum, $user->toArray());

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Status akun PIC OPD '{$user->nama}' berhasil {$statusText}.");
    }

    /**
     * Buat Ulang Link Undangan Aktivasi (Jika Token Kedaluwarsa).
     */
    public function regenerateToken(User $user): RedirectResponse
    {
        if ($user->tipe_akun !== 'opd') {
            return back()->with('error', 'Aksi ini hanya untuk akun OPD.');
        }

        $token = Str::random(40);
        $user->update([
            'token_undangan'    => $token,
            'status_undangan'   => 'pending',
            'token_kedaluwarsa' => now()->addDays(3),
        ]);

        $linkUndangan = route('opd.undangan', $token);
        return back()->with('status', "✓ Link aktivasi baru untuk '{$user->nama}': {$linkUndangan}");
    }

    /**
     * Hapus Akun PIC OPD.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->tipe_akun !== 'opd') {
            return back()->with('error', 'Aksi ini hanya untuk akun OPD.');
        }

        $sebelum = $user->toArray();
        $nama = $user->nama;
        $user->delete();

        ActivityLog::catat('users', $sebelum['id'], 'delete', $sebelum, null);

        return back()->with('status', "Akun PIC OPD '{$nama}' berhasil dihapus.");
    }
}

