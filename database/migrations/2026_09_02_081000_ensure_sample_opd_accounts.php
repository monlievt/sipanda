<?php

use App\Models\ObjekPenugasan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memastikan akun-akun PIC OPD sampel siap digunakan untuk login portal OPD
     */
    public function up(): void
    {
        $roleOpd = Role::firstOrCreate(['name' => 'opd', 'guard_name' => 'web']);

        $sampleOpds = [
            [
                'email'         => 'pic.dinkes@trenggalek.go.id',
                'nama'          => 'PIC Dinas Kesehatan',
                'nama_singkat'  => 'PIC Dinkes',
                'keyword_objek' => 'Kesehatan',
            ],
            [
                'email'         => 'pic.dikpora@trenggalek.go.id',
                'nama'          => 'PIC Dinas Pendidikan, Pemuda dan Olahraga',
                'nama_singkat'  => 'PIC Dikpora',
                'keyword_objek' => 'Pendidikan',
            ],
            [
                'email'         => 'pic.disperikanan@trenggalek.go.id',
                'nama'          => 'PIC Dinas Perikanan',
                'nama_singkat'  => 'PIC Disperikanan',
                'keyword_objek' => 'Perikanan',
            ],
            [
                'email'         => 'pic.dispertan@trenggalek.go.id',
                'nama'          => 'PIC Dinas Pertanian dan Pangan',
                'nama_singkat'  => 'PIC Dispertan',
                'keyword_objek' => 'Pertanian',
            ],
            [
                'email'         => 'pic.setwan@trenggalek.go.id',
                'nama'          => 'PIC Sekretariat DPRD',
                'nama_singkat'  => 'PIC Setwan',
                'keyword_objek' => 'DPRD',
            ],
        ];

        foreach ($sampleOpds as $data) {
            $objek = ObjekPenugasan::where('nama', 'like', "%{$data['keyword_objek']}%")->first();
            $objekId = $objek ? $objek->id : null;

            $user = User::firstOrNew(['email' => $data['email']]);
            $user->nama = $data['nama'];
            $user->nama_tanpa_gelar = $data['nama_singkat'];
            $user->password = Hash::make('sipanda2025');
            $user->tipe_akun = 'opd';
            $user->objek_penugasan_id = $objekId;
            $user->is_active = true;
            $user->status_undangan = 'aktif';
            $user->save();

            if (! $user->hasRole('opd')) {
                $user->assignRole('opd');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
