<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Irban extends Model
{
    protected $fillable = ['nama_irban', 'wilayah_keterangan'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pkppt()
    {
        return $this->hasMany(Pkppt::class);
    }

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class);
    }

    public function penugasanMany()
    {
        return $this->belongsToMany(Penugasan::class, 'penugasan_irban');
    }

    public function kapasitasSdm()
    {
        return $this->hasMany(KapasitasSdm::class);
    }
}
