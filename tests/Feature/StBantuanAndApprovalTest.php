<?php

namespace Tests\Feature;

use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\SumberPenugasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StBantuanAndApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminIrban;
    protected User $pejabatIrban;
    protected User $anggotaTim;
    protected Irban $irbanKhusus;
    protected JenisPenugasan $jenisPenugasan;
    protected SumberPenugasan $sumberPenugasan;
    protected ObjekPenugasan $objekPenugasan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin_irban']);
        Role::firstOrCreate(['name' => 'irban']);
        Role::firstOrCreate(['name' => 'auditor']);
        Role::firstOrCreate(['name' => 'inspektur']);

        $this->irbanKhusus = Irban::create([
            'nama_irban' => 'Inspektur Pembantu Khusus',
            'kode_irban' => 'IRBAN_KHUSUS',
        ]);

        $this->adminIrban = User::create([
            'nama'     => 'Muhammad Idham Firdaus, S.Ak.',
            'username' => 'idham',
            'email'    => 'idham@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'irban_id' => $this->irbanKhusus->id,
            'is_active'=> true,
        ]);
        $this->adminIrban->assignRole('admin_irban');

        $this->pejabatIrban = User::create([
            'nama'     => 'Pejabat Irban Khusus',
            'username' => 'irban_khusus',
            'email'    => 'irban_khusus@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'irban_id' => $this->irbanKhusus->id,
            'is_active'=> true,
        ]);
        $this->pejabatIrban->assignRole('irban');

        $this->anggotaTim = User::create([
            'nama'     => 'ROEKAN, S.T.',
            'nip'      => '198102052006041010',
            'username' => 'roekan',
            'email'    => 'roekan@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'irban_id' => $this->irbanKhusus->id,
            'is_active'=> true,
        ]);
        $this->anggotaTim->assignRole('auditor');

        $this->jenisPenugasan = JenisPenugasan::create([
            'nama'     => 'Audit Dengan Tujuan Tertentu',
            'kategori' => 'assurance',
        ]);

        $this->sumberPenugasan = SumberPenugasan::create([
            'nama' => 'Permintaan APH',
        ]);

        $this->objekPenugasan = ObjekPenugasan::create([
            'nama'     => 'Desa Nglongsor Kecamatan Tugu',
            'kategori' => 'desa',
        ]);
    }

    public function test_input_st_by_admin_irban_requires_irban_approval(): void
    {
        $response = $this->actingAs($this->adminIrban)->post(route('penugasan.store'), [
            'jenis_st'            => 'reguler',
            'is_perpanjangan'     => '0',
            'no_spt'              => '700/716/406.008/2026',
            'tanggal_st'          => '2026-09-30',
            'uraian_penugasan'    => 'Melaksanakan Penelaahan Informasi Awal (PIA)',
            'sumber_penugasan_id' => $this->sumberPenugasan->id,
            'jenis_penugasan_id'  => $this->jenisPenugasan->id,
            'tanggal_mulai'       => '2026-10-01',
            'tanggal_selesai'     => '2026-10-09',
            'is_sesuai_pkppt'     => '0',
            'irban_ids'           => [$this->irbanKhusus->id],
            'objek_ids'           => [$this->objekPenugasan->id],
            'tim_wakil_pj'        => [$this->pejabatIrban->id],
            'tim_daltek'          => [$this->pejabatIrban->id],
            'tim_ketua'           => [$this->adminIrban->id],
            'tim_anggota'         => [$this->anggotaTim->id],
        ]);

        $response->assertRedirect();
        
        $st = Penugasan::where('no_spt', '700/716/406.008/2026')->first();
        $this->assertNotNull($st);
        
        // Memastikan TIDAK langsung disetujui, melainkan diajukan
        $this->assertEquals('diajukan', $st->status_persetujuan);
        $this->assertEquals('2026-09-30', $st->tanggal_st->format('Y-m-d'));

        // Pejabat Irban melakukan verifikasi setujui
        $verifyResponse = $this->actingAs($this->pejabatIrban)->patch(route('penugasan.verifikasi', $st->id), [
            'status_persetujuan' => 'disetujui',
        ]);
        $verifyResponse->assertRedirect();

        $st->refresh();
        $this->assertEquals('disetujui', $st->status_persetujuan);
    }

    public function test_input_st_bantuan_only_requires_tim_anggota_and_inherits_from_induk(): void
    {
        // 1. Buat ST Utama
        $stUtama = Penugasan::create([
            'no_spt'              => '700/716/406.008/2026',
            'tanggal_st'          => '2026-09-30',
            'jenis_st'            => 'reguler',
            'uraian_penugasan'    => 'Melaksanakan Penelaahan Informasi Awal (PIA)',
            'sumber_penugasan_id' => $this->sumberPenugasan->id,
            'jenis_penugasan_id'  => $this->jenisPenugasan->id,
            'tanggal_mulai'       => '2026-10-01',
            'tanggal_selesai'     => '2026-10-09',
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'is_sesuai_pkppt'     => false,
            'irban_id'            => $this->irbanKhusus->id,
            'dibuat_oleh'         => $this->adminIrban->id,
        ]);
        $stUtama->irbans()->sync([$this->irbanKhusus->id]);
        $stUtama->objekPenugasan()->sync([$this->objekPenugasan->id]);

        // 2. Input ST Bantuan
        $response = $this->actingAs($this->adminIrban)->post(route('penugasan.store'), [
            'jenis_st'           => 'bantuan',
            'penugasan_induk_id' => $stUtama->id,
            'no_spt'             => '700/717/406.008/2026',
            'tanggal_st'         => '2026-09-30',
            'uraian_penugasan'   => 'Membantu Pelaksanaan Penelaahan Informasi Awal (PIA)',
            'tanggal_mulai'      => '2026-10-01',
            'tanggal_selesai'    => '2026-10-09',
            'tim_anggota'        => [$this->anggotaTim->id],
            // Tanpa tim_wakil_pj, tim_daltek, tim_ketua
        ]);

        $response->assertRedirect();
        
        $stBantuan = Penugasan::where('no_spt', '700/717/406.008/2026')->first();
        $this->assertNotNull($stBantuan);
        $this->assertTrue($stBantuan->isBantuan());
        $this->assertEquals($stUtama->id, $stBantuan->penugasan_induk_id);
        $this->assertEquals('2026-09-30', $stBantuan->tanggal_st->format('Y-m-d'));
        
        // Pastikan hanya personil anggota_tim yang tersimpan
        $this->assertEquals(1, $stBantuan->tim()->count());
        $this->assertEquals('anggota_tim', $stBantuan->tim()->first()->peran);
        $this->assertEquals($this->anggotaTim->id, $stBantuan->tim()->first()->user_id);

        // Objek & Irban tersinkron dari Induk
        $this->assertTrue($stBantuan->objekPenugasan->contains($this->objekPenugasan->id));
        $this->assertTrue($stBantuan->irbans->contains($this->irbanKhusus->id));
    }

    public function test_upload_and_revision_of_berkas_st_file(): void
    {
        Storage::fake('public');

        $st = Penugasan::create([
            'no_spt'              => '700/716/406.008/2026',
            'tanggal_st'          => '2026-09-30',
            'jenis_st'            => 'reguler',
            'uraian_penugasan'    => 'Penugasan Uji Berkas ST',
            'sumber_penugasan_id' => $this->sumberPenugasan->id,
            'jenis_penugasan_id'  => $this->jenisPenugasan->id,
            'tanggal_mulai'       => '2026-10-01',
            'tanggal_selesai'     => '2026-10-09',
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'is_sesuai_pkppt'     => false,
            'irban_id'            => $this->irbanKhusus->id,
            'dibuat_oleh'         => $this->adminIrban->id,
        ]);
        $st->irbans()->sync([$this->irbanKhusus->id]);

        // 1. Upload Berkas Pertama
        $filePertama = UploadedFile::fake()->create('ST_700_716_TTD.pdf', 500, 'application/pdf');
        $response1 = $this->actingAs($this->adminIrban)->post(route('penugasan.upload-berkas', $st->id), [
            'berkas_st' => $filePertama,
        ]);

        $response1->assertRedirect();
        $st->refresh();

        $this->assertNotNull($st->file_st);
        $this->assertEquals('ST_700_716_TTD.pdf', $st->file_st_nama);
        Storage::disk('public')->assertExists($st->file_st);

        $oldFilePath = $st->file_st;

        // 2. Upload Revisi Berkas (menimpa file lama dengan nomor SPT yang sama)
        $fileRevisi = UploadedFile::fake()->create('ST_700_716_TTD_Revisi.pdf', 600, 'application/pdf');
        $response2 = $this->actingAs($this->adminIrban)->post(route('penugasan.upload-berkas', $st->id), [
            'berkas_st' => $fileRevisi,
        ]);

        $response2->assertRedirect();
        $st->refresh();

        $this->assertEquals('ST_700_716_TTD_Revisi.pdf', $st->file_st_nama);
        Storage::disk('public')->assertExists($st->file_st);
        // File lama terhapus agar tidak boros storage
        Storage::disk('public')->assertMissing($oldFilePath);

        // 3. Akses download / view berkas
        $downloadResponse = $this->actingAs($this->adminIrban)->get(route('penugasan.berkas-st', $st->id));
        $downloadResponse->assertOk();
    }
}
