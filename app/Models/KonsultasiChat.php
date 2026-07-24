<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiChat extends Model
{
    protected $table = 'konsultasi_chat';

    protected $fillable = ['konsultasi_id', 'user_id', 'tipe_pengirim', 'pesan', 'lampiran_file'];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
