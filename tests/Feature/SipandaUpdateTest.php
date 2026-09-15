<?php

namespace Tests\Feature;

use App\Models\IkhtisarLaporan;
use App\Models\Irban;
use App\Models\Penugasan;
use App\Models\User;
use App\Services\SuratTugasDocxService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SipandaUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_irban_does_not_contain_duplicate_iv(): void
    {
        $this->seed(\Database\Seeders\IrbanSeeder::class);
        $irbans = Irban::pluck('nama_irban')->toArray();
        $this->assertNotContains('Inspektur Pembantu IV', $irbans);
        $this->assertContains('Inspektur Pembantu Khusus', $irbans);
    }

    public function test_surat_tugas_docx_service_generates_valid_docx(): void
    {
        $penugasan = new Penugasan();
        $penugasan->no_spt = '800.1.11.1/TEST/406.050/2026';
        $penugasan->uraian_penugasan = 'Uji Coba Pengawasan';
        $penugasan->tanggal_mulai = Carbon::now();
        $penugasan->tanggal_selesai = Carbon::now()->addDays(3);
        $penugasan->dasar_penugasan = "1. Dasar Uji Coba\n2. Dasar Regulasi Kedua";

        $service = new SuratTugasDocxService();
        $file = $service->generate($penugasan);

        $this->assertFileExists($file);
        $this->assertGreaterThan(1000, filesize($file));

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($file));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertStringContainsString('800.1.11.1/TEST/406.050/2026', $xml);
        $this->assertStringContainsString('Uji Coba Pengawasan', $xml);
        $this->assertStringContainsString('Peringatan', $xml);

        @unlink($file);
    }

    public function test_ikhtisar_laporan_access_and_compile(): void
    {
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\IrbanSeeder::class]);

        $sekretariatUser = User::create([
            'nama' => 'Kasubbag Pelaporan',
            'email' => 'pelaporan@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'KASUBBAG EVALUASI DAN PELAPORAN',
            'status_aktif' => 'aktif',
            'nip' => '198001012005011001',
        ]);
        $sekretariatUser->assignRole('sekretariat');

        $auditorUser = User::create([
            'nama' => 'Auditor Muda',
            'email' => 'auditor@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'AUDITOR AHLI MUDA',
            'status_aktif' => 'aktif',
            'nip' => '198501012010011002',
        ]);
        $auditorUser->assignRole('auditor');

        // Sekretariat can access create
        $response = $this->actingAs($sekretariatUser)->get(route('ikhtisar-laporan.create', ['tahun' => 2026]));
        $response->assertStatus(200);

        // Auditor cannot access create (403 Forbidden)
        $responseAuditor = $this->actingAs($auditorUser)->get(route('ikhtisar-laporan.create', ['tahun' => 2026]));
        $responseAuditor->assertStatus(403);
    }

    public function test_tindak_lanjut_show_with_tujuan_surat_objek(): void
    {
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\IrbanSeeder::class, \Database\Seeders\MasterDataSeeder::class]);

        $adminUser = User::create([
            'nama' => 'Administrator',
            'email' => 'admin@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'PRANATA KOMPUTER',
            'status_aktif' => 'aktif',
            'nip' => '198901012015011003',
        ]);
        $adminUser->assignRole('admin');

        $penugasan = \App\Models\Penugasan::create([
            'no_spt' => '800.1.11.1/001/406.050/2026',
            'uraian_penugasan' => 'Audit Keuangan',
            'tanggal_mulai' => Carbon::now(),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'irban_id' => 1,
            'jenis_penugasan_id' => 1,
            'sumber_penugasan_id' => 1,
            'status' => 'selesai',
            'status_persetujuan' => 'disetujui',
            'dibuat_oleh' => $adminUser->id,
        ]);

        $tl = \App\Models\TindakLanjut::create([
            'penugasan_id' => $penugasan->id,
            'no_lhp' => '700/01/LHP/2026',
            'judul_lhp' => 'LHP Audit Keuangan',
            'tgl_lhp' => Carbon::now(),
            'uraian_temuan' => 'Temuan Uji Coba',
            'rekomendasi' => 'Rekomendasi Uji Coba',
            'status_tindak_lanjut' => 'proses',
            'status_telaah' => 'draft',
            'dibuat_oleh' => $adminUser->id,
        ]);

        $response = $this->actingAs($adminUser)->get(route('tindak-lanjut.show', $tl->id));
        $response->assertStatus(200);
    }

    public function test_pkppt_revisi_archives_old_version(): void
    {
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\IrbanSeeder::class]);

        $inspekturUser = User::create([
            'nama' => 'Inspektur',
            'email' => 'inspektur@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'INSPEKTUR DAERAH',
            'status_aktif' => 'aktif',
            'nip' => '197001011995011001',
        ]);
        $inspekturUser->assignRole('inspektur');

        $pkppt = \App\Models\Pkppt::create([
            'tahun' => 2026,
            'area_pengawasan' => 'Pengawasan Keuangan',
            'jenis_pengawasan' => 'Audit Kinerja',
            'sasaran' => 'Dinas Pendidikan',
            'rencana_mulai' => Carbon::now(),
            'rencana_selesai_laporan' => Carbon::now()->addMonth(),
            'jumlah_laporan_rencana' => 1,
            'status' => 'ditetapkan',
            'versi_revisi' => 1,
            'dibuat_oleh' => $inspekturUser->id,
        ]);

        $response = $this->actingAs($inspekturUser)->post(route('pkppt.revisi', $pkppt->id), [
            'catatan_revisi' => 'Penyesuaian jadwal',
            'area_pengawasan' => 'Pengawasan Keuangan dan Aset',
            'jenis_pengawasan' => 'Audit Kinerja',
            'sasaran' => 'Dinas Pendidikan',
            'rencana_mulai' => Carbon::now()->format('Y-m-d'),
            'rencana_selesai_laporan' => Carbon::now()->addMonth()->format('Y-m-d'),
            'jumlah_laporan_rencana' => 1,
        ]);

        $response->assertRedirect(route('pkppt.index', ['tahun' => 2026]));

        $pkppt->refresh();
        $this->assertEquals('diarsipkan', $pkppt->status);

        $this->assertDatabaseHas('pkppt', [
            'tahun' => 2026,
            'area_pengawasan' => 'Pengawasan Keuangan dan Aset',
            'status' => 'draft',
            'versi_revisi' => 2,
            'pkppt_induk_id' => $pkppt->id,
        ]);
    }

    public function test_export_lhp_matrix_download(): void
    {
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\IrbanSeeder::class, \Database\Seeders\MasterDataSeeder::class]);

        $adminUser = User::create([
            'nama' => 'Administrator',
            'email' => 'admin_export@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'PRANATA KOMPUTER',
            'status_aktif' => 'aktif',
            'nip' => '198901012015011099',
        ]);
        $adminUser->assignRole('admin');

        $penugasan = \App\Models\Penugasan::create([
            'no_spt' => '800.1.11.1/002/406.050/2026',
            'uraian_penugasan' => 'Audit Keuangan dan Kinerja',
            'tanggal_mulai' => Carbon::now(),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'irban_id' => 1,
            'jenis_penugasan_id' => 1,
            'sumber_penugasan_id' => 1,
            'status' => 'selesai',
            'status_persetujuan' => 'disetujui',
            'dibuat_oleh' => $adminUser->id,
        ]);

        $tl = \App\Models\TindakLanjut::create([
            'penugasan_id' => $penugasan->id,
            'no_lhp' => '700/02/LHP/2026',
            'judul_lhp' => 'LHP Pemeriksaan Reguler',
            'tgl_lhp' => Carbon::now(),
            'uraian_temuan' => 'Temuan Kasus 1',
            'rekomendasi' => 'Rekomendasi Kasus 1',
            'nilai_rekomendasi' => 15000000,
            'nilai_setor' => 5000000,
            'status_tindak_lanjut' => 'belum_sesuai',
            'status_telaah' => 'disetujui',
            'dibuat_oleh' => $adminUser->id,
        ]);

        $response = $this->actingAs($adminUser)->get(route('tindak-lanjut.export_lhp', $tl->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
