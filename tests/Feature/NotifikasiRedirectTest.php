<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotifikasiRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalize_url_target_strips_legacy_domain_and_keeps_path_query_fragment(): void
    {
        $this->assertSame(
            '/tindak-lanjut/10',
            Notifikasi::normalizeUrlTarget('https://sipanda.inspektorat.trenggalekkab.go.id/tindak-lanjut/10')
        );

        $this->assertSame(
            '/tindak-lanjut/10?tab=bukti#item-2',
            Notifikasi::normalizeUrlTarget('https://sipanda.inspektorat.trenggalekkab.go.id/tindak-lanjut/10?tab=bukti#item-2')
        );

        $this->assertSame(
            '/tindak-lanjut/10',
            Notifikasi::normalizeUrlTarget('//evil.example/tindak-lanjut/10')
        );

        $this->assertSame(
            '/konsultasi/5',
            Notifikasi::normalizeUrlTarget('konsultasi/5')
        );

        $this->assertNull(Notifikasi::normalizeUrlTarget('   '));
        $this->assertNull(Notifikasi::normalizeUrlTarget(null));
    }

    public function test_mark_as_read_redirects_legacy_sipanda_url_to_current_host(): void
    {
        $user = User::factory()->create();

        // Simulasikan baris lama di DB yang masih menyimpan domain sipanda.*
        $notifId = DB::table('notifikasi')->insertGetId([
            'user_id'      => $user->id,
            'jenis'        => 'info_lain',
            'judul'        => 'Notifikasi Lama',
            'pesan'        => 'Tautan domain lama',
            'url_target'   => 'https://sipanda.inspektorat.trenggalekkab.go.id/tindak-lanjut/10',
            'status'       => 'terkirim',
            'dikirim_pada' => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('https://singawasi.inspektorat.trenggalekkab.go.id/notifikasi/' . $notifId . '/baca');

        $response->assertRedirect('https://singawasi.inspektorat.trenggalekkab.go.id/tindak-lanjut/10');
        $this->assertNotNull(Notifikasi::find($notifId)->dibaca_pada);
    }

    public function test_new_notification_stores_relative_url_target(): void
    {
        $user = User::factory()->create();

        $notif = Notifikasi::create([
            'user_id'      => $user->id,
            'jenis'        => 'info_lain',
            'judul'        => 'Notifikasi Baru',
            'pesan'        => 'Cek normalisasi mutator',
            'url_target'   => 'https://singawasi.inspektorat.trenggalekkab.go.id/tindak-lanjut/25',
            'status'       => 'terkirim',
            'dikirim_pada' => now(),
        ]);

        $this->assertSame('/tindak-lanjut/25', $notif->getRawOriginal('url_target'));
    }
}
