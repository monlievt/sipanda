<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Penugasan;
use App\Models\TindakLanjut;
use App\Notifications\PenugasanReminderNotification;
use App\Notifications\TindakLanjutMandekNotification;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPenugasanReminders extends Command
{
    protected $signature   = 'sipanda:send-reminders';
    protected $description = 'Kirim notifikasi reminder H-3 & H-1 jadwal kegiatan penugasan serta reminder OPD mandek >14 hari.';

    public function handle(): int
    {
        $this->info('Memulai pengecekan reminder penugasan SIPANDA...');

        $in3Days = now()->addDays(3)->toDateString();
        $in1Day  = now()->addDays(1)->toDateString();

        $countH3 = 0; $countH1 = 0; $countOpd = 0; $countError = 0;

        // ─── 1. Reminder H-3 ────────────────────────────────────────────────
        $penugasanH3 = Penugasan::with('timUsers')
            ->whereDate('tanggal_mulai', $in3Days)
            ->where('status', '!=', 'selesai')
            ->get();

        foreach ($penugasanH3 as $p) {
            foreach ($p->timUsers as $user) {
                // Simpan notifikasi in-app (firstOrCreate agar tidak duplikat)
                Notifikasi::firstOrCreate(
                    ['user_id' => $user->id, 'penugasan_id' => $p->id, 'jenis' => 'reminder_h3'],
                    [
                        'pesan'        => "Reminder H-3: Penugasan '{$p->no_spt}' ({$p->uraian_penugasan}) akan dimulai pada {$p->tanggal_mulai->format('d/m/Y')}.",
                        'status'       => 'terkirim',
                        'dikirim_pada' => now(),
                    ]
                );

                // Kirim email — try-catch agar satu kegagalan tidak menghentikan semua
                try {
                    $user->notify(new PenugasanReminderNotification($p, 'h3'));
                    $countH3++;
                } catch (\Throwable $e) {
                    $countError++;
                    Log::warning("[SIPANDA Reminder] Gagal kirim email H-3 ke {$user->email}: " . $e->getMessage());
                }
            }
        }

        // ─── 2. Reminder H-1 ────────────────────────────────────────────────
        $penugasanH1 = Penugasan::with('timUsers')
            ->whereDate('tanggal_mulai', $in1Day)
            ->where('status', '!=', 'selesai')
            ->get();

        foreach ($penugasanH1 as $p) {
            foreach ($p->timUsers as $user) {
                Notifikasi::firstOrCreate(
                    ['user_id' => $user->id, 'penugasan_id' => $p->id, 'jenis' => 'reminder_h1'],
                    [
                        'pesan'        => "Reminder H-1 BESOK: Penugasan '{$p->no_spt}' ({$p->uraian_penugasan}) dimulai besok {$p->tanggal_mulai->format('d/m/Y')}.",
                        'status'       => 'terkirim',
                        'dikirim_pada' => now(),
                    ]
                );

                try {
                    $user->notify(new PenugasanReminderNotification($p, 'h1'));
                    $countH1++;
                } catch (\Throwable $e) {
                    $countError++;
                    Log::warning("[SIPANDA Reminder] Gagal kirim email H-1 ke {$user->email}: " . $e->getMessage());
                }
            }
        }

        // ─── 3. Reminder TL Mandek > 14 Hari ───────────────────────────────
        $overdueTL = TindakLanjut::with(['penugasan.irban'])
            ->whereIn('status_tindak_lanjut', ['belum', 'proses', 'dikembalikan'])
            ->where('created_at', '<=', now()->subDays(14))
            ->get();

        foreach ($overdueTL as $tl) {
            if (! $tl->penugasan?->irban_id) {
                continue;
            }

            $hariMandek = (int) now()->diffInDays($tl->created_at);

            // Kirim hanya ke role irban & admin_irban di Irban terkait
            $usersIrban = \App\Models\User::where('irban_id', $tl->penugasan->irban_id)
                ->aktif()
                ->role(['irban', 'admin_irban'])
                ->get();

            foreach ($usersIrban as $u) {
                // In-app — satu per hari
                Notifikasi::firstOrCreate(
                    [
                        'user_id'      => $u->id,
                        'penugasan_id' => $tl->penugasan_id,
                        'jenis'        => 'info_lain',
                        'created_at'   => today(),
                    ],
                    [
                        'pesan'        => "Peringatan Mandek: Rekomendasi SPT '{$tl->penugasan?->no_spt}' belum direspons OPD selama >{$hariMandek} hari.",
                        'status'       => 'terkirim',
                        'dikirim_pada' => now(),
                    ]
                );

                try {
                    $u->notify(new TindakLanjutMandekNotification($tl, $hariMandek));
                    $countOpd++;
                } catch (\Throwable $e) {
                    $countError++;
                    Log::warning("[SIPANDA Reminder] Gagal kirim email mandek ke {$u->email}: " . $e->getMessage());
                }
            }
        }

        // ─── 4. Reminder Batas Waktu Jatuh Tempo TLHP (H-7 & Hari-H) ────────
        $in7Days = now()->addDays(7)->toDateString();
        $today   = now()->toDateString();
        $countJatuhTempo = 0;

        $targetTL = TindakLanjut::with(['penugasan.irban', 'penugasan.objekPenugasan.users'])
            ->whereIn('status_tindak_lanjut', ['belum', 'proses', 'dikembalikan'])
            ->whereNotNull('tanggal_target')
            ->where(function ($q) use ($in7Days, $today) {
                $q->whereDate('tanggal_target', $in7Days)
                  ->orWhereDate('tanggal_target', $today);
            })
            ->get();

        foreach ($targetTL as $tl) {
            $sisaHari = (int) now()->startOfDay()->diffInDays($tl->tanggal_target->startOfDay(), false);
            if ($sisaHari < 0) {
                continue;
            }

            // Kirim ke seluruh PIC OPD objek penugasan terkait
            foreach ($tl->penugasan->objekPenugasan as $objek) {
                $opdUsers = \App\Models\User::where('objek_penugasan_id', $objek->id)->aktif()->get();
                foreach ($opdUsers as $opdUser) {
                    try {
                        $opdUser->notify(new \App\Notifications\JatuhTempoTlhpNotification($tl, $sisaHari));
                        $countJatuhTempo++;
                    } catch (\Throwable $e) {
                        Log::warning("[SIPANDA Reminder] Gagal kirim reminder jatuh tempo ke OPD {$opdUser->email}: " . $e->getMessage());
                    }
                }
            }
        }

        $pesan = "✓ Selesai. Reminder H-3: {$countH3}, H-1: {$countH1}, Mandek OPD: {$countOpd}, Jatuh Tempo: {$countJatuhTempo}";
        $pesan .= $countError > 0 ? ", Gagal: {$countError} (cek storage/logs/laravel.log)." : '.';
        $this->info($pesan);

        return Command::SUCCESS;
    }
}
