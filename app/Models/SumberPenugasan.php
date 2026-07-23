<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SumberPenugasan extends Model {
    protected $table = 'sumber_penugasan';
    protected $fillable = ['nama'];
    public function penugasan() { return $this->hasMany(Penugasan::class); }
}
