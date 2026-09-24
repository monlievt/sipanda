<?php

namespace Tests\Feature;

use App\Models\BuktiTindakLanjut;
use App\Models\Irban;
use App\Models\ObjekPenugasan;
use App\Models\Penugasan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TindakLanjutWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_tier_workflow_tim_irban_inspektur_opd(): void
    {
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\IrbanSeeder::class, \Database\Seeders\MasterDataSeeder::class]);

        $irban1 = Irban::first();

        // 1. Create Users (Auditor, Irban, Inspektur)
        $auditor = User::create([
            'nama'         => 'Auditor Tim TL',
            'email'        => 'auditor_tl@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'AUDITOR AHLI PERTAMA',
            'status_aktif' => 'aktif',
            'nip'          => '199001012015011001',
            'irban_id'     => $irban1->id,
        ]);
        $auditor->assignRole('auditor');

        $irbanUser = User::create([
            'nama'         => 'Irban Wilayah 1',
            'email'        => 'irban1@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'INSPEKTUR PEMBANTU I',
            'status_aktif' => 'aktif',
            'nip'          => '197501012000011001',
            'irban_id'     => $irban1->id,
        ]);
        $irbanUser->assignRole('irban');

        $inspektur = User::create([
            'nama'         => 'Inspektur Utama',
            'email'        => 'inspektur@trenggalekkab.go.id',
            'password'     => bcrypt('password'),
            'jabatan'      => 'INSPEKTUR DAERAH',
            'status_aktif' => 'aktif',
            'nip'          => '197001011995011001',
        ]);
        $inspektur->assignRole('inspektur');

        $objek = ObjekPenugasan::create([
            'nama'      => 'Dinas Pendidikan',
            'kategori'  => 'opd',
            'is_active' => true,
        ]);

        // 2. Create Penugasan & Tindak Lanjut
        $penugasan = Penugasan::create([
            'no_spt'              => '700/01/TEST/2026',
            'uraian_penugasan'    => 'Audit Operasional OPD Test',
            'tanggal_mulai'       => now()->subDays(10),
            'tanggal_selesai'     => now()->subDays(5),
            'status'              => 'aktif',
            'status_persetujuan'  => 'disetujui',
            'irban_id'            => $irban1->id,
            'jenis_penugasan_id'  => 1,
            'sumber_penugasan_id' => 1,
            'dibuat_oleh'         => $auditor->id,
        ]);

        $tl = TindakLanjut::create([
            'penugasan_id'          => $penugasan->id,
            'objek_penugasan_id'    => $objek->id,
            'no_lhp'                => 'LHP/TEST/001/2026',
            'judul_lhp'             => 'LHP Audit OPD Test',
            'tgl_lhp'               => now()->subDays(4),
            'uraian_temuan'         => 'Kekurangan Dokumen Pertanggungjawaban',
            'rekomendasi'           => 'Lengkapi dokumen administrasi SPJ',
            'status_tindak_lanjut'  => 'menunggu_verifikasi',
            'status_telaah'         => 'draft',
            'dibuat_oleh'           => $auditor->id,
        ]);

        $bukti = BuktiTindakLanjut::create([
            'tindak_lanjut_id'   => $tl->id,
            'diunggah_oleh'      => $auditor->id,
            'catatan_opd'        => 'Bukti SPJ telah kami susun dan lengkapi.',
            'status_verifikasi'  => 'menunggu',
        ]);

        // STEP 1: Auditor/Tim Inputs Telaah
        $responseTelaah = $this->actingAs($auditor)->post(route('tindak-lanjut.ajukan_telaah', $tl->id), [
            'catatan_telaah_tim' => 'Bukti SPJ telah lengkap dan valid secara substantif.',
            'status_rekomendasi' => 'selesai',
        ]);
        $responseTelaah->assertSessionHasNoErrors();
        $responseTelaah->assertRedirect();

        $tl->refresh();
        // CRITICAL CHECK: status_telaah is 'diajukan_irban', but status_tindak_lanjut is NOT 'selesai' yet!
        $this->assertEquals('diajukan_irban', $tl->status_telaah);
        $this->assertEquals('selesai', $tl->status_rekomendasi_usulan);
        $this->assertEquals('menunggu_verifikasi', $tl->status_tindak_lanjut);

        // STEP 2: Irban Verifies
        $responseIrban = $this->actingAs($irbanUser)->post(route('tindak-lanjut.verifikasi_irban', $tl->id), [
            'aksi'                     => 'setujui',
            'catatan_verifikasi_irban' => 'Setuju dengan telaah tim. Teruskan kepada Inspektur.',
        ]);
        $responseIrban->assertSessionHasNoErrors();
        $responseIrban->assertRedirect();

        $tl->refresh();
        // CRITICAL CHECK: status_telaah is 'diajukan_inspektur', but status_tindak_lanjut is still NOT 'selesai'!
        $this->assertEquals('diajukan_inspektur', $tl->status_telaah);
        $this->assertEquals('menunggu_verifikasi', $tl->status_tindak_lanjut);

        // STEP 3: Inspektur Approves (Final Approval)
        $responseInspektur = $this->actingAs($inspektur)->post(route('tindak-lanjut.persetujuan_inspektur', $tl->id), [
            'aksi'                          => 'setujui',
            'catatan_persetujuan_inspektur' => 'Disetujui. Matriks tindak lanjut dapat disampaikan ke OPD.',
        ]);
        $responseInspektur->assertSessionHasNoErrors();
        $responseInspektur->assertRedirect();

        $tl->refresh();
        $bukti->refresh();

        // FINAL CHECK: status_telaah is 'disetujui_inspektur', status_tindak_lanjut is officially 'selesai', and evidence is 'diterima'!
        $this->assertEquals('disetujui_inspektur', $tl->status_telaah);
        $this->assertEquals('selesai', $tl->status_tindak_lanjut);
        $this->assertEquals('diterima', $bukti->status_verifikasi);
        $this->assertNotNull($tl->tanggal_selesai_aktual);
    }
}
