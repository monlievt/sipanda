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
}
