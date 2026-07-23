<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Penugasan;
use App\Models\TindakLanjut;

use Illuminate\Console\Command;

class SendPenugasanReminders extends Command
{
    protected $signature = 'sipanda:send-reminders';
    protected $description = 'Kirim notifikasi reminder H-3 & H-1 jadwal kegiatan penugasan serta reminder OPD mandek >14 hari.';

    public function handle(): int
    {
        $this->info('Memulai pengecekan reminder penugasan SIPANDA...');

        $today = now()->startOfDay();
        $in3Days = now()->addDays(3)->toDateString();
        $in1Day  = now()->addDays(1)->toDateString();

        $countH3 = 0;
        $countH1 = 0;
        $countOpd = 0;

        // 1. Reminder H-3
        $penugasanH3 = Penugasan::with('timUsers')
            ->whereDate('tanggal_mulai', $in3Days)
            ->where('status', '!=', 'selesai')
            ->get();

        foreach ($penugasanH3 as $p) {
            foreach ($p->timUsers as $user) {
                Notifikasi::firstOrCreate([
                    'user_id'      => $user->id,
                    'penugasan_id' => $p->id,
                    'jenis'        => 'reminder_h3',
                ], [
                    'pesan'        => "Reminder H-3: Penugasan '{$p->no_spt}' ({$p->uraian_penugasan}) akan dimulai pada {$p->tanggal_mulai->format('d/m/Y')}.",
                    'status'       => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
                $countH3++;
            }
        }

        // 2. Reminder H-1
        $penugasanH1 = Penugasan::with('timUsers')
            ->whereDate('tanggal_mulai', $in1Day)
            ->where('status', '!=', 'selesai')
            ->get();

        foreach ($penugasanH1 as $p) {
            foreach ($p->timUsers as $user) {
                Notifikasi::firstOrCreate([
                    'user_id'      => $user->id,
                    'penugasan_id' => $p->id,
                    'jenis'        => 'reminder_h1',
                ], [
                    'pesan'        => "Reminder H-1 BESOK: Penugasan '{$p->no_spt}' ({$p->uraian_penugasan}) dimulai besok {$p->tanggal_mulai->format('d/m/Y')}.",
                    'status'       => 'terkirim',
                    'dikirim_pada' => now(),
                ]);
                $countH1++;
            }
        }

        // 3. Reminder OPD belum merespons > 14 hari
        $overdueTL = TindakLanjut::with('penugasan.irban')
            ->whereIn('status_tindak_lanjut', ['belum', 'proses', 'dikembalikan'])
            ->where('created_at', '<=', now()->subDays(14))
            ->get();

        foreach ($overdueTL as $tl) {
            // Notifikasi ke Irban terkait
            if ($tl->penugasan?->irban_id) {
                $usersIrban = \App\Models\User::where('irban_id', $tl->penugasan->irban_id)->get();
                foreach ($usersIrban as $u) {
                    Notifikasi::firstOrCreate([
                        'user_id'      => $u->id,
                        'penugasan_id' => $tl->penugasan_id,
                        'jenis'        => 'info_lain',
                        'created_at'   => today(),
                    ], [
                        'pesan'        => "Peringatan Mandek: Rekomendasi SPT '{$tl->penugasan?->no_spt}' belum direspons OPD selama >14 hari.",
                        'status'       => 'terkirim',
                        'dikirim_pada' => now(),
                    ]);
                    $countOpd++;
                }
            }
        }

        $this->info("✓ Selesai. Reminder H-3: {$countH3}, H-1: {$countH1}, Reminder OPD: {$countOpd}");
        return Command::SUCCESS;
    }
}
