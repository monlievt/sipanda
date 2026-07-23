<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ObjekPenugasan extends Model {
    protected $table = 'objek_penugasan';
    protected $fillable = ['nama', 'kategori', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function penugasan() { return $this->belongsToMany(Penugasan::class, 'penugasan_objek'); }
    public function penilaianRisiko() { return $this->hasMany(PenilaianRisiko::class); }
    public function akunOpd() { return $this->hasMany(User::class)->where('tipe_akun', 'opd'); }
    public function scopeAktif($q) { return $q->where('is_active', true); }
}
