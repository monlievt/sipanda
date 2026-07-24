<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiTim extends Model
{
    protected $table = 'konsultasi_tim';

    protected $fillable = ['konsultasi_id', 'user_id', 'peran'];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getPeranLabelAttribute(): string
    {
        return match($this->peran) {
            'penanggung_jawab'     => 'Penanggung Jawab',
            'pengendali_teknis'    => 'Pengendali Teknis',
            'ketua_tim'           => 'Ketua Tim',
            'anggota_tim'         => 'Anggota Tim',
            default               => $this->peran,
        };
    }
}
