<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KodeAtributTemuan extends Model
{
    protected $table = 'kode_atribut_temuan';

    protected $fillable = [
        'kode_kelompok',
        'nama_kelompok',
        'kode_sub_kelompok',
        'nama_sub_kelompok',
        'kode_jenis',
        'kode_lengkap',
        'deskripsi',
        'alternatif_rekomendasi',
    ];

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'kode_atribut_temuan_id');
    }

    public function getAlternatifRekomendasiArrayAttribute(): array
    {
        if (empty($this->alternatif_rekomendasi)) {
            return [];
        }

        return array_map(function ($k) {
            $trimmed = trim($k);
            return str_pad($trimmed, 2, '0', STR_PAD_LEFT);
        }, explode(',', $this->alternatif_rekomendasi));
    }

    public function getLabelLengkapAttribute(): string
    {
        return "{$this->kode_lengkap} - {$this->deskripsi}";
    }
}
