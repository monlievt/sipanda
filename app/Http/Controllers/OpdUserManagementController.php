<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ObjekPenugasan;
use App\Models\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpdUserManagementController extends Controller
{
    /**
     * Tampilkan daftar akun PIC OPD.
     */
    public function index(): View
    {
        $opdUsers = User::with('objekPenugasan')
            ->where('tipe_akun', 'opd')
            ->orderBy('created_at', 'desc')
            ->get();

        $objekList = ObjekPenugasan::aktif()->orderBy('nama')->get();

        return view('master.opd-users', compact('opdUsers', 'objekList'));
    }

    /**
     * Buat akun PIC OPD baru & buatkan token undangan set-password.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'               => ['required', 'string', 'max:150'],
            'email'              => ['required', 'email', 'unique:users,email'],
            'objek_penugasan_id' => ['required', 'exists:objek_penugasan,id'],
            'no_hp'              => ['nullable', 'string', 'max:20'],
        ]);

        $token = Str::random(40);

        $user = User::create([
            'nama'               => $validated['nama'],
            'email'              => $validated['email'],
            'no_hp'              => $validated['no_hp'],
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $validated['objek_penugasan_id'],
            'status_undangan'    => 'pending',
            'token_undangan'     => $token,
            'token_kedaluwarsa'  => now()->addDays(3), // 3x24 jam
            'is_active'          => true,
        ]);

        $user->assignRole('opd');

        ActivityLog::catat('users', $user->id, 'create', null, $user->toArray());

        $linkUndangan = route('opd.undangan', $token);

        return back()->with('status', "Akun PIC OPD '{$user->nama}' berhasil dibuat! Link aktivasi: {$linkUndangan}");
    }
}
