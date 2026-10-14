<?php

namespace Tests\Feature;

use App\Models\Irban;
use App\Models\JenisPenugasan;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\PenugasanTim;
use App\Models\SumberPenugasan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsAndAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\IrbanSeeder::class,
            \Database\Seeders\MasterDataSeeder::class,
        ]);
    }

    public function test_auditor_cannot_see_or_complete_or_input_lhp_on_other_team_penugasan(): void
    {
        $irban3 = Irban::find(3) ?? Irban::create(['nama_irban' => 'Inspektur Pembantu Wilayah III', 'kode_irban' => 'IRBAN-3']);

        // 1. Akun Abyadh (Auditor Irban 3, bukan anggota tim SPT ini)
        $abyadh = User::create([
            'nama'         => 'ABYADH NUR ROKHMAN',
            'email'        => 'abyadh@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'AUDITOR PERTAMA',
            'status_aktif' => 'aktif',
            'nip'          => '199601012020011003',
            'irban_id'     => $irban3->id,
        ]);
        $abyadh->assignRole('auditor');

        // 2. Akun Choirunnisa (Auditor Irban 3, Ketua Tim)
        $choirunnisa = User::create([
            'nama'         => 'CHOIRUNNISA NURUL AINI',
            'email'        => 'choirunnisa@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'AUDITOR MUDA',
            'status_aktif' => 'aktif',
            'nip'          => '199201012015012001',
            'irban_id'     => $irban3->id,
        ]);
        $choirunnisa->assignRole('auditor');

        // 3. Akun Admin Irban 3
        $adminIrban3 = User::create([
            'nama'         => 'ADMIN IRBAN III',
            'email'        => 'adminirban3@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'ADMINISTRATOR IRBAN III',
            'status_aktif' => 'aktif',
            'nip'          => '199501012020011013',
            'irban_id'     => $irban3->id,
        ]);
        $adminIrban3->assignRole('admin_irban');

        $objek = ObjekPenugasan::first() ?? ObjekPenugasan::create(['nama' => 'Dinas Pendidikan', 'kategori' => 'opd', 'is_active' => true]);

        // SPT Tim Choirunnisa
        $penugasan = Penugasan::create([
            'no_spt'              => '700/999/406.008/2026',
            'uraian_penugasan'    => 'Pemeriksaan Kinerja Pengelolaan Dana BOS',
            'tanggal_mulai'       => now()->subDays(5),
            'tanggal_selesai'     => now()->addDays(5),
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'irban_id'            => $irban3->id,
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'dibuat_oleh'         => 1,
        ]);

        PenugasanTim::create([
            'penugasan_id' => $penugasan->id,
            'user_id'      => $choirunnisa->id,
            'peran'        => 'ketua_tim',
        ]);

        // 1. Abyadh tidak melihat penugasan Choirunnisa di halaman indeks
        $resIndex = $this->actingAs($abyadh)->get(route('penugasan.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertDontSee('700/999/406.008/2026');
        $resIndex->assertDontSee('Pemeriksaan Kinerja Pengelolaan Dana BOS');

        // 2. Abyadh ditolak saat mencoba membuka detail penugasan tim lain (403)
        $resShow = $this->actingAs($abyadh)->get(route('penugasan.show', $penugasan->id));
        $resShow->assertStatus(403);

        // 3. Abyadh ditolak saat mencoba menandai penugasan selesai (403)
        $resComplete = $this->actingAs($abyadh)->patch(route('penugasan.update_status', $penugasan->id), [
            'status' => 'selesai',
        ]);
        $resComplete->assertStatus(403);

        // 4. Abyadh ditolak saat mencoba menginput temuan LHP (403)
        $resStoreLhp = $this->actingAs($abyadh)->post(route('tindak-lanjut.store'), [
            'penugasan_id'  => $penugasan->id,
            'no_lhp'        => '700.1.2.3/999/406.008/2026',
            'uraian_temuan' => 'Temuan ilegal oleh personil non-tim',
            'rekomendasi'   => 'Rekomendasi ilegal',
        ]);
        $resStoreLhp->assertStatus(403);

        // 5. Choirunnisa (Auditor / Ketua Tim) juga TIDAK berhak menandai selesai (karena hak menandai selesai khusus Admin Irban)
        $this->assertFalse($penugasan->canComplete($choirunnisa));
        $resKetuaComplete = $this->actingAs($choirunnisa)->patch(route('penugasan.update_status', $penugasan->id), [
            'status' => 'selesai',
        ]);
        $resKetuaComplete->assertStatus(403);

        // 6. Admin Irban 3 memiliki wewenang menyelesaikan penugasan
        $this->assertTrue($penugasan->canComplete($adminIrban3));
        $resAdminComplete = $this->actingAs($adminIrban3)->patch(route('penugasan.update_status', $penugasan->id), [
            'status'           => 'selesai',
            'progres_persen'   => 100,
            'keterangan_hasil' => 'Pemeriksaan lapangan telah selesai 100%.',
        ]);
        $resAdminComplete->assertRedirect();
        $this->assertEquals('selesai', $penugasan->fresh()->status);
    }

    public function test_admin_irban_and_irban_workflow_and_approval(): void
    {
        $irban2 = Irban::find(2) ?? Irban::create(['nama_irban' => 'Inspektur Pembantu Wilayah II', 'kode_irban' => 'IRBAN-2']);

        $adminIrban = User::create([
            'nama'         => 'ADMIN IRBAN II',
            'email'        => 'adminirban2@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'ADMINISTRATOR IRBAN II',
            'status_aktif' => 'aktif',
            'nip'          => '199401012019011002',
            'irban_id'     => $irban2->id,
        ]);
        $adminIrban->assignRole('admin_irban');

        $pejabatIrban = User::create([
            'nama'         => 'PEJABAT IRBAN II',
            'email'        => 'irban2@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'INSPEKTUR PEMBANTU WILAYAH II',
            'status_aktif' => 'aktif',
            'nip'          => '197501012000011001',
            'irban_id'     => $irban2->id,
        ]);
        $pejabatIrban->assignRole('irban');

        $objek = ObjekPenugasan::first() ?? ObjekPenugasan::create(['nama' => 'Puskesmas Panggul', 'kategori' => 'opd', 'is_active' => true]);

        // Admin Irban dapat menginput SPT baru
        $resStoreSpt = $this->actingAs($adminIrban)->post(route('penugasan.store'), [
            'no_spt'              => '700/888/406.008/2026',
            'uraian_penugasan'    => 'Audit Operasional Puskesmas',
            'tanggal_mulai'       => now()->toDateString(),
            'tanggal_selesai'     => now()->addDays(10)->toDateString(),
            'is_sesuai_pkppt'     => 0,
            'irban_ids'           => [$irban2->id],
            'objek_ids'           => [$objek->id],
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'tim_wakil_pj'        => [$pejabatIrban->id],
            'tim_daltek'          => [$pejabatIrban->id],
            'tim_ketua'           => [$adminIrban->id],
            'tim_anggota'         => [$adminIrban->id],
        ]);
        $resStoreSpt->assertRedirect();
        $penugasan = Penugasan::where('no_spt', '700/888/406.008/2026')->first();
        $this->assertNotNull($penugasan);

        // Admin Irban TIDAK berhak memverifikasi/menyetujui SPT-nya sendiri
        $resVerifByAdmin = $this->actingAs($adminIrban)->patch(route('penugasan.verifikasi', $penugasan->id), [
            'status_persetujuan' => 'disetujui',
        ]);
        $resVerifByAdmin->assertSessionHas('error');

        // Pejabat Irban berhak memverifikasi/menyetujui konsep SPT
        $resVerifByIrban = $this->actingAs($pejabatIrban)->patch(route('penugasan.verifikasi', $penugasan->id), [
            'status_persetujuan' => 'disetujui',
        ]);
        $resVerifByIrban->assertSessionHas('status');
        $this->assertEquals('disetujui', $penugasan->fresh()->status_persetujuan);

        // Admin Irban berhak menginput Temuan LHP
        $resStoreLhp = $this->actingAs($adminIrban)->post(route('tindak-lanjut.store'), [
            'penugasan_id'         => $penugasan->id,
            'no_lhp'               => '700.1.2.2/888/406.008/2026',
            'judul_lhp'            => 'LHP Audit Operasional Puskesmas',
            'tgl_lhp'              => now()->toDateString(),
            'uraian_temuan'        => 'Penyimpanan Obat Kadaluarsa',
            'rekomendasi'          => 'Pemusnahan obat dan revisi SOP',
            'nilai_rekomendasi_rp' => '5.000.000',
        ]);
        $resStoreLhp->assertRedirect();
        $this->assertDatabaseHas('tindak_lanjut', [
            'no_lhp' => '700.1.2.2/888/406.008/2026',
        ]);
    }

    public function test_matriks_lhp_top_banner_metrics_calculated_from_filtered_records(): void
    {
        $irban1 = Irban::find(1);
        $irbanKhusus = Irban::find(4) ?? Irban::create(['nama_irban' => 'Inspektur Pembantu Khusus', 'kode_irban' => 'IRBAN-KHUSUS']);

        $idham = User::create([
            'nama'         => 'MUHAMMAD IDHAM FIRDAUS',
            'email'        => 'idham2@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'AUDITOR TERAMPIL',
            'status_aktif' => 'aktif',
            'nip'          => '199501012020011004',
            'irban_id'     => $irbanKhusus->id,
        ]);
        $idham->assignRole('auditor');

        // Penugasan 1 di Irban 1 (bukan tim Idham)
        $spt1 = Penugasan::create([
            'no_spt'              => '700/111/406.008/2026',
            'uraian_penugasan'    => 'Penugasan Irban 1',
            'tanggal_mulai'       => now(),
            'tanggal_selesai'     => now()->addDays(5),
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'irban_id'            => $irban1->id,
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'dibuat_oleh'         => 1,
        ]);

        TindakLanjut::create([
            'penugasan_id'         => $spt1->id,
            'no_lhp'               => 'LHP-IRBAN-1',
            'uraian_temuan'        => 'Temuan Irban 1',
            'rekomendasi'          => 'Rekomendasi 1',
            'status_tindak_lanjut' => 'selesai',
            'nilai_rekomendasi_rp' => 10000000,
            'dibuat_oleh'          => 1,
        ]);

        // Penugasan 2 di mana Idham terdaftar sebagai anggota tim
        $spt2 = Penugasan::create([
            'no_spt'              => '700/444/406.008/2026',
            'uraian_penugasan'    => 'Penugasan Khusus Tim Idham',
            'tanggal_mulai'       => now(),
            'tanggal_selesai'     => now()->addDays(5),
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'irban_id'            => $irbanKhusus->id,
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'dibuat_oleh'         => 1,
        ]);

        PenugasanTim::create([
            'penugasan_id' => $spt2->id,
            'user_id'      => $idham->id,
            'peran'        => 'anggota_tim',
        ]);

        TindakLanjut::create([
            'penugasan_id'         => $spt2->id,
            'no_lhp'               => 'LHP-KHUSUS-IDHAM',
            'uraian_temuan'        => 'Temuan Khusus',
            'rekomendasi'          => 'Rekomendasi Khusus',
            'status_tindak_lanjut' => 'belum',
            'nilai_rekomendasi_rp' => 2000000,
            'dibuat_oleh'          => 1,
        ]);

        // Saat Idham membuka /tindak-lanjut:
        // Total rekomendasi di banner metrik harus 1 (bukan 2 se-Kabupaten!)
        // Sesuai = 0, Belum = 1
        $res = $this->actingAs($idham)->get(route('tindak-lanjut.index'));
        $res->assertStatus(200);
        $res->assertViewHas('totalRekomendasi', 1);
        $res->assertViewHas('countSesuai', 0);
        $res->assertViewHas('countBelum', 1);
        $res->assertViewHas('totalNilaiRekomendasi', 2000000.0);
        $res->assertSee('Penugasan Tim Anda'); // Scope badge
    }

    public function test_sekretariat_is_view_only_and_pelaporan_sekretaris_have_global_access(): void
    {
        $irban1 = Irban::find(1);
        $irban3 = Irban::find(3) ?? Irban::create(['nama_irban' => 'Inspektur Pembantu Wilayah III', 'kode_irban' => 'IRBAN-3']);

        // Buat penugasan di Irban 1 dan Irban 3
        $spt1 = Penugasan::create([
            'no_spt'              => '700/01/406.008/2026',
            'uraian_penugasan'    => 'Pengawasan Wilayah Irban 1',
            'tanggal_mulai'       => now(),
            'tanggal_selesai'     => now()->addDays(5),
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'irban_id'            => $irban1->id,
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'dibuat_oleh'         => 1,
        ]);

        $spt3 = Penugasan::create([
            'no_spt'              => '700/03/406.008/2026',
            'uraian_penugasan'    => 'Pengawasan Wilayah Irban 3',
            'tanggal_mulai'       => now(),
            'tanggal_selesai'     => now()->addDays(5),
            'status'              => 'berjalan',
            'status_persetujuan'  => 'disetujui',
            'irban_id'            => $irban3->id,
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'dibuat_oleh'         => 1,
        ]);

        // 1. Role Sekretariat (View-Only, tidak dalam tim manapun)
        $sekretariatUser = User::create([
            'nama'         => 'STAF SEKRETARIAT UMUM',
            'email'        => 'sekretariat_staf@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'PENGADMINISTRASI UMUM',
            'status_aktif' => 'aktif',
            'nip'          => '199001012015011099',
            'irban_id'     => 5,
        ]);
        $sekretariatUser->assignRole('sekretariat');

        // Sekretariat tidak melihat penugasan tim lain
        $resSekretariat = $this->actingAs($sekretariatUser)->get(route('penugasan.index'));
        $resSekretariat->assertStatus(200);
        $resSekretariat->assertDontSee('700/01/406.008/2026');
        $resSekretariat->assertDontSee('700/03/406.008/2026');

        // Sekretariat ditolak input SPT (403)
        $resSekretariatStore = $this->actingAs($sekretariatUser)->post(route('penugasan.store'), []);
        $resSekretariatStore->assertStatus(403);

        // 2. Role Pelaporan (Melihat seluruh penugasan se-Inspektorat)
        $pelaporanUser = User::create([
            'nama'         => 'TIM EVALUASI DAN PELAPORAN',
            'email'        => 'eval_pelaporan@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'PENELAAH TEKNIS KEBIJAKAN PELAPORAN',
            'status_aktif' => 'aktif',
            'nip'          => '198801012012011005',
            'irban_id'     => 5,
        ]);
        $pelaporanUser->assignRole('pelaporan');

        $resPelaporan = $this->actingAs($pelaporanUser)->get(route('penugasan.index'));
        $resPelaporan->assertStatus(200);
        $resPelaporan->assertSee('700/01/406.008/2026');
        $resPelaporan->assertSee('700/03/406.008/2026');

        // 3. Role Sekretaris (Melihat seluruh penugasan se-Inspektorat)
        $sekretarisUser = User::create([
            'nama'         => 'SEKRETARIS INSPEKTORAT',
            'email'        => 'sekretaris_inspektorat@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'SEKRETARIS INSPEKTORAT',
            'status_aktif' => 'aktif',
            'nip'          => '197601012000011001',
            'irban_id'     => 5,
        ]);
        $sekretarisUser->assignRole('sekretaris');

        $resSekretaris = $this->actingAs($sekretarisUser)->get(route('penugasan.index'));
        $resSekretaris->assertStatus(200);
        $resSekretaris->assertSee('700/01/406.008/2026');
        $resSekretaris->assertSee('700/03/406.008/2026');
    }

    public function test_pelaporan_can_generate_ilhp_and_sekretaris_inspektur_can_view_draft(): void
    {
        // 1. Akun Sulikah (Role Pelaporan)
        $sulikah = User::create([
            'nama'         => 'SULIKAH, S.TP., M.A.P',
            'email'        => 'sulikah@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'PENYUSUN LAPORAN KEUANGAN & KINERJA',
            'status_aktif' => 'aktif',
            'nip'          => '198001012005012001',
            'irban_id'     => 5,
        ]);
        $sulikah->assignRole('pelaporan');

        // Akun Sekretaris
        $sekretaris = User::create([
            'nama'         => 'SEKRETARIS INSPEKTORAT',
            'email'        => 'sekretaris@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'SEKRETARIS',
            'status_aktif' => 'aktif',
            'nip'          => '197501012000011001',
            'irban_id'     => 5,
        ]);
        $sekretaris->assignRole('sekretaris');

        // Akun Inspektur
        $inspektur = User::create([
            'nama'         => 'INSPEKTUR KABUPATEN TRENGGALEK',
            'email'        => 'inspektur@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'INSPEKTUR DAERAH',
            'status_aktif' => 'aktif',
            'nip'          => '197001011995011001',
            'irban_id'     => 5,
        ]);
        $inspektur->assignRole('inspektur');

        // Sulikah membuka halaman ikhtisar laporan -> Tombol Generate muncul
        $resIndex = $this->actingAs($sulikah)->get(route('ikhtisar-laporan.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Generate Ikhtisar Baru');

        // Sulikah membuka form create
        $resCreate = $this->actingAs($sulikah)->get(route('ikhtisar-laporan.create', ['tahun' => 2026]));
        $resCreate->assertStatus(200);

        // Sulikah menyimpan Draft ILHP
        $resStore = $this->actingAs($sulikah)->post(route('ikhtisar-laporan.store'), [
            'tahun'           => 2026,
            'periode'         => 'tahunan',
            'judul'           => 'Ikhtisar Laporan Hasil Pengawasan Tahunan Tahun Anggaran 2026',
            'nomor_surat'     => '700/ILHP-01/406.008/2026',
            'tanggal_laporan' => '2026-10-14',
            'status'          => 'draft',
        ]);
        $resStore->assertStatus(302);

        $ilhp = \App\Models\IkhtisarLaporan::where('nomor_surat', '700/ILHP-01/406.008/2026')->first();
        $this->assertNotNull($ilhp);
        $this->assertEquals('draft', $ilhp->status);

        // Sekretaris membuka index ILHP -> melihat draft dan label DRAF KONSEP
        $resSekretarisIndex = $this->actingAs($sekretaris)->get(route('ikhtisar-laporan.index', ['tahun' => 2026]));
        $resSekretarisIndex->assertStatus(200);
        $resSekretarisIndex->assertSee('Ikhtisar Laporan Hasil Pengawasan Tahunan Tahun Anggaran 2026');
        $resSekretarisIndex->assertSee('DRAF KONSEP');

        // Sekretaris membuka detail & cetak draft
        $this->actingAs($sekretaris)->get(route('ikhtisar-laporan.show', $ilhp))->assertStatus(200);
        $this->actingAs($sekretaris)->get(route('ikhtisar-laporan.cetak', $ilhp))->assertStatus(200);

        // Inspektur membuka index ILHP -> melihat draft dan label DRAF KONSEP
        $resInspekturIndex = $this->actingAs($inspektur)->get(route('ikhtisar-laporan.index', ['tahun' => 2026]));
        $resInspekturIndex->assertStatus(200);
        $resInspekturIndex->assertSee('Ikhtisar Laporan Hasil Pengawasan Tahunan Tahun Anggaran 2026');
        $resInspekturIndex->assertSee('DRAF KONSEP');

        // Inspektur membuka detail & cetak draft
        $this->actingAs($inspektur)->get(route('ikhtisar-laporan.show', $ilhp))->assertStatus(200);
        $this->actingAs($inspektur)->get(route('ikhtisar-laporan.cetak', $ilhp))->assertStatus(200);
    }
}

