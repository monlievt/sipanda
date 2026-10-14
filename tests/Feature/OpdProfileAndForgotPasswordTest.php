<?php

namespace Tests\Feature;

use App\Models\ObjekPenugasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OpdProfileAndForgotPasswordTest extends TestCase
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

    public function test_opd_can_view_edit_data_button_on_dashboard(): void
    {
        $objek = ObjekPenugasan::first() ?? ObjekPenugasan::create(['nama' => 'Inspektorat', 'kategori' => 'opd', 'is_active' => true]);

        $opdUser = User::create([
            'nama'               => 'PIC Inspektorat',
            'email'              => 'inspektorat.trenggalek@gmail.com',
            'password'           => Hash::make('password123'),
            'no_hp'              => '081234567890',
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $objek->id,
            'is_active'          => true,
            'status_undangan'    => 'aktif',
        ]);
        $opdUser->assignRole('opd');

        $response = $this->actingAs($opdUser, 'opd')->get(route('opd.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Edit Data OPD');
        $response->assertSee('Edit Data Mandiri OPD');
        $response->assertSee('inspektorat.trenggalek@gmail.com');
    }

    public function test_opd_can_update_profile_mandiri(): void
    {
        $objek = ObjekPenugasan::first() ?? ObjekPenugasan::create(['nama' => 'Inspektorat', 'kategori' => 'opd', 'is_active' => true]);

        $opdUser = User::create([
            'nama'               => 'PIC Lama',
            'email'              => 'opd.lama@trenggalekkab.go.id',
            'password'           => Hash::make('password123'),
            'no_hp'              => '081111111111',
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $objek->id,
            'is_active'          => true,
            'status_undangan'    => 'aktif',
        ]);
        $opdUser->assignRole('opd');

        $response = $this->actingAs($opdUser, 'opd')->put(route('opd.profil.update'), [
            'nama'                  => 'PIC Baru Diperbarui',
            'email'                 => 'opd.baru@trenggalekkab.go.id',
            'no_hp'                 => '082222222222',
            'password'              => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $opdUser->refresh();
        $this->assertEquals('PIC Baru Diperbarui', $opdUser->nama);
        $this->assertEquals('opd.baru@trenggalekkab.go.id', $opdUser->email);
        $this->assertEquals('082222222222', $opdUser->no_hp);
        $this->assertTrue(Hash::check('passwordBaru123', $opdUser->password));
    }

    public function test_opd_cannot_update_profile_with_existing_other_user_email(): void
    {
        $objek = ObjekPenugasan::first() ?? ObjekPenugasan::create(['nama' => 'Inspektorat', 'kategori' => 'opd', 'is_active' => true]);

        // User lain yang sudah ada
        User::create([
            'nama'               => 'User Lain',
            'email'              => 'sudah_ada@trenggalekkab.go.id',
            'password'           => Hash::make('password123'),
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $objek->id,
            'is_active'          => true,
        ]);

        $opdUser = User::create([
            'nama'               => 'PIC Testing',
            'email'              => 'pic.testing@trenggalekkab.go.id',
            'password'           => Hash::make('password123'),
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $objek->id,
            'is_active'          => true,
        ]);

        $response = $this->actingAs($opdUser, 'opd')->put(route('opd.profil.update'), [
            'nama'  => 'PIC Testing',
            'email' => 'sudah_ada@trenggalekkab.go.id', // email duplikat
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_forgot_password_in_log_mode_informs_simulation_mode(): void
    {
        $objek = ObjekPenugasan::first() ?? ObjekPenugasan::create(['nama' => 'Inspektorat', 'kategori' => 'opd', 'is_active' => true]);

        $opdUser = User::create([
            'nama'               => 'PIC Inspektorat',
            'email'              => 'inspektorat.trenggalek@gmail.com',
            'password'           => Hash::make('password123'),
            'tipe_akun'          => 'opd',
            'objek_penugasan_id' => $objek->id,
            'is_active'          => true,
            'status_undangan'    => 'aktif',
        ]);
        $opdUser->assignRole('opd');

        config(['mail.default' => 'log']);

        $response = $this->post(route('opd.password.email'), [
            'email' => 'inspektorat.trenggalek@gmail.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertStringContainsString('log', session('status'));
    }
}
