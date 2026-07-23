<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PenugasanTim extends Model {
    protected $table = 'penugasan_tim';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = ['penugasan_id', 'user_id', 'peran'];

    public function penugasan() { return $this->belongsTo(Penugasan::class); }
    public function user()      { return $this->belongsTo(User::class); }

    public function getPeranLabelAttribute(): string
    {
        return match($this->peran) {
            'wakil_penanggung_jawab' => 'Wakil Penanggung Jawab',
            'pengendali_teknis'      => 'Pengendali Teknis',
            'ketua_tim'              => 'Ketua Tim',
            'anggota_tim'            => 'Anggota Tim',
            default                  => $this->peran,
        };
    }
}
