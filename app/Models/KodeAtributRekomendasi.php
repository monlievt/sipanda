<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KodeAtributRekomendasi extends Model
{
    protected $table = 'kode_atribut_rekomendasi';

    protected $fillable = [
        'kode',
        'deskripsi',
    ];

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'kode_atribut_rekomendasi_id');
    }

    public function getLabelLengkapAttribute(): string
    {
        return "{$this->kode} - {$this->deskripsi}";
    }
}
