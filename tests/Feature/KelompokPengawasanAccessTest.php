<?php

namespace Tests\Feature;

use App\Models\KelompokPengawasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelompokPengawasanAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\MasterDataSeeder::class,
        ]);
    }

    public function test_auditor_can_view_kelompok_pengawasan_in_readonly_mode(): void
    {
        $auditor = User::create([
            'nama'         => 'Auditor Pertama',
            'email'        => 'auditor.test@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'status_aktif' => 'aktif',
        ]);
        $auditor->assignRole('auditor');

        $kelompok = KelompokPengawasan::create([
            'nama_kelompok'     => 'Pengawasan Proyek Strategis Daerah',
            'kode_kelompok'     => 'PSD_TEST',
            'deskripsi_singkat' => 'Deskripsi PSD',
            'urutan'            => 1,
            'is_active'         => true,
        ]);

        $response = $this->actingAs($auditor)->get(route('master.kelompok-pengawasan.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelompok / Kluster Jenis Pengawasan');
        $response->assertSee('Pengawasan Proyek Strategis Daerah');
        $response->assertSee('Mode Pratinjau (Read-Only)');
        $response->assertDontSee('Tambah Kelompok Baru');
        $response->assertDontSee('modalTambahKelompok');
        $response->assertDontSee('modalEditKelompok');
    }

    public function test_auditor_cannot_create_update_or_delete_kelompok_pengawasan(): void
    {
        $auditor = User::create([
            'nama'         => 'Auditor Pertama',
            'email'        => 'auditor.test@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'status_aktif' => 'aktif',
        ]);
        $auditor->assignRole('auditor');

        $kelompok = KelompokPengawasan::create([
            'nama_kelompok'     => 'Pengawasan Keuangan dan Aset',
            'kode_kelompok'     => 'KEU_TEST',
            'urutan'            => 1,
            'is_active'         => true,
        ]);

        // POST harus 403
        $createRes = $this->actingAs($auditor)->post(route('master.kelompok-pengawasan.store'), [
            'nama_kelompok' => 'Kelompok Baru Ilegal',
        ]);
        $createRes->assertStatus(403);

        // PUT harus 403
        $updateRes = $this->actingAs($auditor)->put(route('master.kelompok-pengawasan.update', $kelompok), [
            'nama_kelompok' => 'Update Ilegal',
        ]);
        $updateRes->assertStatus(403);

        // DELETE harus 403
        $deleteRes = $this->actingAs($auditor)->delete(route('master.kelompok-pengawasan.destroy', $kelompok));
        $deleteRes->assertStatus(403);
    }

    public function test_administrator_has_full_crud_access_on_kelompok_pengawasan(): void
    {
        $admin = User::create([
            'nama'         => 'Administrator Sistem',
            'email'        => 'admin.test@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'status_aktif' => 'aktif',
        ]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('master.kelompok-pengawasan.index'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Kelompok Baru');
        $response->assertSee('modalTambahKelompok');

        // Admin berhasil create
        $createRes = $this->actingAs($admin)->post(route('master.kelompok-pengawasan.store'), [
            'nama_kelompok' => 'Pengawasan PSD Khusus',
            'kode_kelompok' => 'PSD_KHUSUS',
            'urutan'        => 99,
            'is_active'     => 1,
        ]);
        $createRes->assertStatus(302);
        $this->assertDatabaseHas('kelompok_pengawasan', [
            'nama_kelompok' => 'Pengawasan PSD Khusus',
            'kode_kelompok' => 'PSD_KHUSUS',
        ]);
    }
}
