<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokPengawasan extends Model
{
    use HasFactory;

    protected $table = 'kelompok_pengawasan';

    protected $fillable = [
        'nama_kelompok',
        'kode_kelompok',
        'deskripsi_singkat',
        'bentuk_pengawasan',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function pkppts(): HasMany
    {
        return $this->hasMany(Pkppt::class, 'kelompok_pengawasan_id');
    }

    public function penugasans(): HasMany
    {
        return $this->hasMany(Penugasan::class, 'kelompok_pengawasan_id');
    }
}
