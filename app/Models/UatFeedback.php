<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UatFeedback extends Model
{
    use HasFactory;

    protected $table = 'uat_feedbacks';

    protected $fillable = [
        'user_id',
        'guard_type',
        'nama_pelapor',
        'email_pelapor',
        'no_hp_pelapor',
        'role_pelapor',
        'kategori',
        'urgensi',
        'url_halaman',
        'judul',
        'deskripsi',
        'screenshot_path',
        'browser_info',
        'status',
        'catatan_admin',
        'ditindaklanjuti_oleh',
        'ditindaklanjuti_pada',
    ];

    protected $casts = [
        'ditindaklanjuti_pada' => 'datetime',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function adminTindakLanjut(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditindaklanjuti_oleh');
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        if (! $this->screenshot_path) {
            return null;
        }

        return Storage::url($this->screenshot_path);
    }

    public function getKategoriBadgeAttribute(): array
    {
        return match ($this->kategori) {
            'bug'        => ['label' => '🐞 Bug / Kendala', 'class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20'],
            'saran'      => ['label' => '💡 Ide & Saran', 'class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20'],
            'pertanyaan' => ['label' => '❓ Pertanyaan Alur', 'class' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20'],
            'apresiasi'  => ['label' => '⭐ Apresiasi / UX', 'class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'],
            default      => ['label' => ucfirst($this->kategori), 'class' => 'bg-slate-500/10 text-slate-400 border border-slate-500/20'],
        };
    }

    public function getUrgensiBadgeAttribute(): array
    {
        return match ($this->urgensi) {
            'kritis' => ['label' => '🔥 Kritis (Blocker)', 'class' => 'bg-rose-600 text-white font-extrabold shadow-sm shadow-rose-600/30'],
            'tinggi' => ['label' => '⚠️ Tinggi', 'class' => 'bg-amber-500/20 text-amber-400 font-bold border border-amber-500/30'],
            'sedang' => ['label' => '🟡 Sedang', 'class' => 'bg-blue-500/20 text-blue-400 font-semibold border border-blue-500/30'],
            'rendah' => ['label' => '🟢 Rendah', 'class' => 'bg-slate-500/20 text-slate-400 font-medium border border-slate-500/30'],
            default  => ['label' => ucfirst($this->urgensi), 'class' => 'bg-slate-500/20 text-slate-400'],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'baru'            => ['label' => '🆕 Baru Masuk', 'class' => 'bg-blue-600 text-white font-bold animate-pulse'],
            'sedang_ditelaah' => ['label' => '🔍 Sedang Ditelaah', 'class' => 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30'],
            'diperbaiki'      => ['label' => '✅ Sudah Diperbaiki', 'class' => 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30'],
            'ditutup'         => ['label' => '📁 Ditutup / Arsip', 'class' => 'bg-slate-800 text-slate-400 font-semibold'],
            default           => ['label' => ucfirst($this->status), 'class' => 'bg-slate-800 text-slate-400'],
        };
    }
}
