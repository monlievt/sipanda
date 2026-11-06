<?php

namespace Tests\Feature;

use App\Models\Irban;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\PenugasanTim;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TindakLanjutExportScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\IrbanSeeder::class, \Database\Seeders\MasterDataSeeder::class]);
    }

    public function test_accessible_by_scope_filters_correctly_per_role(): void
    {
        $irban1 = Irban::find(1);
        $irban4 = Irban::find(4) ?? Irban::create(['nama_irban' => 'Inspektur Pembantu Khusus', 'kode' => 'IRBAN4']);

        $opd1 = ObjekPenugasan::create(['nama' => 'Dinas Pendidikan', 'kategori' => 'opd', 'is_active' => true]);
        $opd2 = ObjekPenugasan::create(['nama' => 'Dinas Kesehatan', 'kategori' => 'opd', 'is_active' => true]);

        // User Auditor 1 (Idham - Irban Khusus)
        $idham = User::create([
            'nama'         => 'Muhammad Idham Firdaus',
            'email'        => 'idham@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'AUDITOR TERAMPIL',
            'status_aktif' => 'aktif',
            'nip'          => '199501012020011001',
            'irban_id'     => $irban4->id,
            'tipe_akun'    => 'internal',
        ]);
        $idham->assignRole('auditor');

        // User Auditor 2 (Lain - Irban 1)
        $auditorLain = User::create([
            'nama'         => 'Auditor Lain',
            'email'        => 'auditor_lain@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'AUDITOR AHLI',
            'status_aktif' => 'aktif',
            'nip'          => '199201012018011001',
            'irban_id'     => $irban1->id,
            'tipe_akun'    => 'internal',
        ]);
        $auditorLain->assignRole('auditor');

        // User Irban 1
        $irbanUser1 = User::create([
            'nama'         => 'Kepala Irban 1',
            'email'        => 'irban1@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'INSPEKTUR PEMBANTU I',
            'status_aktif' => 'aktif',
            'nip'          => '197501012000011001',
            'irban_id'     => $irban1->id,
            'tipe_akun'    => 'internal',
        ]);
        $irbanUser1->assignRole('irban');

        // User Pelaporan
        $pelaporan = User::create([
            'nama'         => 'Staf Tim Pelaporan',
            'email'        => 'pelaporan@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'ANALIS EVALUASI DAN PELAPORAN',
            'status_aktif' => 'aktif',
            'nip'          => '198501012010011001',
            'tipe_akun'    => 'internal',
        ]);
        $pelaporan->assignRole('pelaporan');

        // Penugasan 1: Di Irban Khusus, Idham sebagai anggota tim
        $penugasan1 = Penugasan::create([
            'no_spt'            => '700/716/406.008/2026',
            'uraian_penugasan'  => 'Audit Investigatif Khusus',
            'irban_id'          => $irban4->id,
            'jenis_penugasan_id'=> 1,
            'sumber_penugasan_id'=> 1,
            'tanggal_mulai'     => '2026-10-01',
            'tanggal_selesai'   => '2026-10-10',
            'status_persetujuan'=> 'disetujui',
            'status'            => 'selesai',
            'dibuat_oleh'       => $idham->id,
        ]);
        $penugasan1->objekPenugasan()->attach($opd1->id);
        PenugasanTim::create([
            'penugasan_id' => $penugasan1->id,
            'user_id'      => $idham->id,
            'peran'        => 'anggota_tim',
        ]);

        // Penugasan 2: Di Irban 1, Auditor Lain sebagai tim (Idham tidak ikut)
        $penugasan2 = Penugasan::create([
            'no_spt'            => '700/100/406.008/2026',
            'uraian_penugasan'  => 'Audit Kinerja Dinas Kesehatan',
            'irban_id'          => $irban1->id,
            'jenis_penugasan_id'=> 1,
            'sumber_penugasan_id'=> 1,
            'tanggal_mulai'     => '2026-10-01',
            'tanggal_selesai'   => '2026-10-10',
            'status_persetujuan'=> 'disetujui',
            'status'            => 'selesai',
            'dibuat_oleh'       => $auditorLain->id,
        ]);
        $penugasan2->objekPenugasan()->attach($opd2->id);
        PenugasanTim::create([
            'penugasan_id' => $penugasan2->id,
            'user_id'      => $auditorLain->id,
            'peran'        => 'ketua_tim',
        ]);

        // Buat Tindak Lanjut untuk Penugasan 1 (Idham)
        $tl1 = TindakLanjut::create([
            'penugasan_id'         => $penugasan1->id,
            'objek_penugasan_id'   => $opd1->id,
            'no_lhp'               => '700/01/LHP/2026',
            'judul_lhp'            => 'LHP Penugasan Khusus',
            'uraian_temuan'        => 'Temuan penugasan khusus',
            'rekomendasi'          => 'Rekomendasi tindak lanjut khusus',
            'status_tindak_lanjut' => 'proses',
            'nilai_rekomendasi_rp' => 50000000,
            'dibuat_oleh'          => $idham->id,
        ]);

        // Buat Tindak Lanjut untuk Penugasan 2 (Irban 1 / Auditor Lain)
        $tl2 = TindakLanjut::create([
            'penugasan_id'         => $penugasan2->id,
            'objek_penugasan_id'   => $opd2->id,
            'no_lhp'               => '700/02/LHP/2026',
            'judul_lhp'            => 'LHP Dinas Kesehatan',
            'uraian_temuan'        => 'Temuan pengadaan dinas kesehatan',
            'rekomendasi'          => 'Rekomendasi perbaikan SOP',
            'status_tindak_lanjut' => 'selesai',
            'nilai_rekomendasi_rp' => 25000000,
            'dibuat_oleh'          => $auditorLain->id,
        ]);

        // 1. Uji Scope Idham (Auditor): Hanya boleh melihat TL1
        $idhamTls = TindakLanjut::accessibleBy($idham)->get();
        $this->assertCount(1, $idhamTls);
        $this->assertEquals($tl1->id, $idhamTls->first()->id);
        $this->assertTrue($tl1->canAccess($idham));
        $this->assertFalse($tl2->canAccess($idham));

        // 2. Uji Scope Irban 1: Hanya boleh melihat TL2 (karena berada di bawah Irban 1)
        $irban1Tls = TindakLanjut::accessibleBy($irbanUser1)->get();
        $this->assertCount(1, $irban1Tls);
        $this->assertEquals($tl2->id, $irban1Tls->first()->id);
        $this->assertFalse($tl1->canAccess($irbanUser1));
        $this->assertTrue($tl2->canAccess($irbanUser1));

        // 3. Uji Scope Pelaporan: Harus dapat melihat SELURUH TL (TL1 & TL2)
        $pelaporanTls = TindakLanjut::accessibleBy($pelaporan)->get();
        $this->assertCount(2, $pelaporanTls);
        $this->assertTrue($tl1->canAccess($pelaporan));
        $this->assertTrue($tl2->canAccess($pelaporan));

        // 4. Uji Akses Halaman Web /tindak-lanjut saat login sebagai Idham
        $response = $this->actingAs($idham)->get(route('tindak-lanjut.index'));
        $response->assertStatus(200);
        $response->assertSee('700/01/LHP/2026');
        $response->assertDontSee('700/02/LHP/2026');

        // 5. Uji Ekspor Rekap Excel saat login sebagai Idham
        $exportAllResponse = $this->actingAs($idham)->get(route('tindak-lanjut.export_all'));
        $exportAllResponse->assertStatus(200);
        $exportAllResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // 6. Uji Ekspor Kompilasi BPKP saat login sebagai Idham
        $exportKompilasiResponse = $this->actingAs($idham)->get(route('tindak-lanjut.export_kompilasi_daerah'));
        $exportKompilasiResponse->assertStatus(200);
        $exportKompilasiResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // 7. Uji Ekspor Kompilasi BPKP saat login sebagai Irban 1
        $exportIrbanResponse = $this->actingAs($irbanUser1)->get(route('tindak-lanjut.export_kompilasi_daerah'));
        $exportIrbanResponse->assertStatus(200);
        $exportIrbanResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
