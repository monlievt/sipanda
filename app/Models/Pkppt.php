<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkppt extends Model {
    protected $table = 'pkppt';

    protected $fillable = [
        'tahun',
        'area_pengawasan',
        'jenis_pengawasan',
        'sasaran',
        'rencana_mulai',
        'rencana_selesai_laporan',
        'jumlah_laporan_rencana',
        'irban_id',
        'status',
        'skor_risiko_acuan',
        'ditetapkan_oleh',
        'tanggal_ditetapkan',
        'versi_revisi',
        'pkppt_induk_id',
        'catatan_revisi',
        'direviu_pada',
        'direviu_oleh',
        'dibuat_oleh',
    ];

    protected $casts = [
        'rencana_mulai'              => 'date',
        'rencana_selesai_laporan'    => 'date',
        'tanggal_ditetapkan'         => 'date',
        'direviu_pada'               => 'datetime',
        'skor_risiko_acuan'          => 'decimal:2',
        'versi_revisi'               => 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────

    public function irban()
    {
        return $this->belongsTo(Irban::class);
    }

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class);
    }

    public function pembuatData()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function direviuOleh()
    {
        return $this->belongsTo(User::class, 'direviu_oleh');
    }

    public function ditetapkanOleh()
    {
        return $this->belongsTo(User::class, 'ditetapkan_oleh');
    }

    public function pkpptInduk()
    {
        return $this->belongsTo(Pkppt::class, 'pkppt_induk_id');
    }

    public function riwayatRevisi()
    {
        return $this->hasMany(Pkppt::class, 'pkppt_induk_id')->orderBy('versi_revisi', 'desc');
    }

    // ─── Scope ────────────────────────────────────────────

    public function scopeTahun($q, $tahun)
    {
        return $q->where('tahun', $tahun);
    }

    public function scopeDitetapkan($q)
    {
        return $q->where('status', 'ditetapkan');
    }

    public function scopeAktif($q)
    {
        return $q->where('status', '!=', 'diarsipkan');
    }
}
