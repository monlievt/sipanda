<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\IkhtisarLaporan;
use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\PenugasanTim;
use App\Models\SumberPenugasan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IlhpAndRollingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'inspektur', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'irban', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sekretariat', 'guard_name' => 'web']);

        $permOpd = Permission::firstOrCreate(['name' => 'opd_users.manage', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permOpd);
    }

    public function test_opd_user_search_and_status_filter()
    {
        $admin = User::factory()->create([
            'tipe_akun' => 'internal',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $objek = ObjekPenugasan::create([
            'nama'      => 'Dinas Kesehatan Trenggalek',
            'kategori'  => 'opd',
            'is_active' => true,
        ]);

        $opd1 = User::create([
            'nama'               => 'Budi Santoso PIC Dinkes',
            'email'              => 'budi.dinkes@trenggalekkab.go.id',
            'password'           => bcrypt('password'),
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $objek->id,
            'is_active'          => true,
            'no_hp'              => '08123456789',
        ]);

        $opd2 = User::create([
            'nama'               => 'Siti Rahma PIC Bapenda',
            'email'              => 'siti.bapenda@trenggalekkab.go.id',
            'password'           => bcrypt('password'),
            'tipe_akun'          => 'opd',
            'is_active'          => false,
            'no_hp'              => '08987654321',
        ]);

        // Test search by keyword
        $response = $this->actingAs($admin)->get(route('master.opd-users.index', ['search' => 'Budi']));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Siti Rahma');

        // Test filter by status
        $responseNonaktif = $this->actingAs($admin)->get(route('master.opd-users.index', ['status' => 'nonaktif']));
        $responseNonaktif->assertStatus(200);
        $responseNonaktif->assertSee('Siti Rahma');
        $responseNonaktif->assertDontSee('Budi Santoso');
    }

    public function test_rolling_personnel_can_access_tindak_lanjut_via_st_pemantauan()
    {
        $irban = Irban::create([
            'nama_irban' => 'Inspektur Pembantu Wilayah I',
            'kode_irban' => 'IRBAN1',
        ]);

        $sumber = SumberPenugasan::create([
            'nama' => 'PKPT Tahunan',
            'kode' => 'PKPT',
        ]);

        $jenis = JenisPenugasan::create([
            'nama'     => 'Audit Operasional',
            'kategori' => 'assurance',
            'kode'     => 'AUDIT',
        ]);

        // Personil A (Pemeriksa Awal)
        $userA = User::factory()->create([
            'nama'      => 'Auditor A (Pemeriksa Asli)',
            'tipe_akun' => 'internal',
            'irban_id'  => $irban->id,
            'is_active' => true,
        ]);
        $userA->assignRole('auditor');

        // Personil F (Personil Baru Rolling)
        $userF = User::factory()->create([
            'nama'      => 'Auditor F (Personil Rolling)',
            'tipe_akun' => 'internal',
            'irban_id'  => null, // tidak terikat irban yang sama
            'is_active' => true,
        ]);
        $userF->assignRole('auditor');

        // SPT Audit Awal (Hanya User A)
        $sptAudit = Penugasan::create([
            'no_spt'              => '700/01/SPT-AUDIT/2026',
            'uraian_penugasan'    => 'Pemeriksaan Jalan X',
            'tanggal_mulai'       => '2026-03-01',
            'tanggal_selesai'     => '2026-03-15',
            'status'              => 'selesai',
            'status_persetujuan'  => 'disetujui',
            'sumber_penugasan_id' => $sumber->id,
            'jenis_penugasan_id'  => $jenis->id,
            'irban_id'            => $irban->id,
            'dibuat_oleh'         => $userA->id,
        ]);
        PenugasanTim::create([
            'penugasan_id' => $sptAudit->id,
            'user_id'      => $userA->id,
            'peran'        => 'ketua_tim',
        ]);

        // SPT Pemantauan TL Baru (Berisi User F)
        $sptPemantauan = Penugasan::create([
            'no_spt'              => '700/02/SPT-PEMANTAUAN/2026',
            'uraian_penugasan'    => 'Pemantauan Tindak Lanjut Jalan X',
            'tanggal_mulai'       => '2026-06-01',
            'tanggal_selesai'     => '2026-06-10',
            'status'              => 'sedang_berjalan',
            'status_persetujuan'  => 'disetujui',
            'sumber_penugasan_id' => $sumber->id,
            'jenis_penugasan_id'  => $jenis->id,
            'irban_id'            => $irban->id,
            'dibuat_oleh'         => $userA->id,
        ]);
        PenugasanTim::create([
            'penugasan_id' => $sptPemantauan->id,
            'user_id'      => $userF->id,
            'peran'        => 'anggota_tim',
        ]);

        // LHP dikaitkan ke ST Pemantauan
        $tl = TindakLanjut::create([
            'penugasan_id'          => $sptAudit->id,
            'st_pemantauan_id'      => $sptPemantauan->id,
            'no_lhp'                => 'LHP/700/01/2026',
            'judul_lhp'             => 'LHP Paket Pekerjaan Jalan X',
            'tgl_lhp'               => '2026-03-20',
            'uraian_temuan'         => 'Kekurangan volume aspal',
            'rekomendasi'           => 'Menyetorkan ke Kas Daerah',
            'nilai_rekomendasi_rp'  => 50000000,
            'status_tindak_lanjut'  => 'belum',
            'status_telaah'         => 'draft',
            'dibuat_oleh'           => $userA->id,
        ]);

        // User F (personil baru di SPT Pemantauan) sekarang BISA melihat LHP ini di index TL
        $resIndexF = $this->actingAs($userF)->get(route('tindak-lanjut.index'));
        $resIndexF->assertStatus(200);
        $resIndexF->assertSee('LHP Paket Pekerjaan Jalan X');

        // User F juga dapat membuka halaman detail LHP
        $resShowF = $this->actingAs($userF)->get(route('tindak-lanjut.show', $tl->id));
        $resShowF->assertStatus(200);
        $resShowF->assertSee('700/02/SPT-PEMANTAUAN/2026');
        $resShowF->assertSee('Auditor F (Personil Rolling)');
    }

    public function test_ilhp_new_sections_compilation_and_gemini_settings()
    {
        $admin = User::factory()->create([
            'nama'      => 'Admin Sipanda',
            'tipe_akun' => 'internal',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Test Save Gemini Key
        $resKey = $this->actingAs($admin)->postJson(route('ikhtisar-laporan.save_gemini_key'), [
            'gemini_api_key' => 'AIzaSyFakeTestKey123',
            'gemini_model'   => 'gemini-2.0-flash',
        ]);
        $resKey->assertStatus(200);
        $resKey->assertJson(['success' => true]);
        $this->assertEquals('AIzaSyFakeTestKey123', AppSetting::get('gemini_api_key'));

        // Test Create ILHP Form
        $resCreate = $this->actingAs($admin)->get(route('ikhtisar-laporan.create', ['tahun' => 2026, 'periode' => 'tahunan']));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Sumber Daya Manusia (SDM)');
        $resCreate->assertSee('Resume Catatan/Temuan serta Saran/Rekomendasi');
        $resCreate->assertSee('Rekapitulasi Hasil Pengawasan');
        $resCreate->assertSee('Rincian Pengawasan sesuai Kelompok / Kluster Pengawasan');

        // Test Store ILHP
        $resStore = $this->actingAs($admin)->post(route('ikhtisar-laporan.store'), [
            'tahun'           => 2026,
            'periode'         => 'tahunan',
            'judul'           => 'Ikhtisar Laporan Hasil Pengawasan Tahunan 2026',
            'nomor_surat'     => '700.1/123/406.008/2026',
            'tanggal_laporan' => '2026-10-08',
            'resume_ai'       => 'Resume eksekutif temuan pengawasan internal tahun 2026.',
            'status'          => 'final',
        ]);
        $resStore->assertRedirect();
        $this->assertDatabaseHas('ikhtisar_laporan', [
            'judul'     => 'Ikhtisar Laporan Hasil Pengawasan Tahunan 2026',
            'resume_ai' => 'Resume eksekutif temuan pengawasan internal tahun 2026.',
            'status'    => 'final',
        ]);

        $ilhp = IkhtisarLaporan::where('judul', 'Ikhtisar Laporan Hasil Pengawasan Tahunan 2026')->first();

        // Test Show & Cetak ILHP
        $resShow = $this->actingAs($admin)->get(route('ikhtisar-laporan.show', $ilhp->id));
        $resShow->assertStatus(200);
        $resShow->assertSee('Resume eksekutif temuan pengawasan internal tahun 2026.');

        $resCetak = $this->actingAs($admin)->get(route('ikhtisar-laporan.cetak', $ilhp->id));
        $resCetak->assertStatus(200);
        $resCetak->assertSee('closing-signature-group');
    }
}
