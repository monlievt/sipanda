<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JenisPenugasan extends Model {
    protected $table = 'jenis_penugasan';
    protected $fillable = ['kategori', 'nama'];
    public function penugasan() { return $this->hasMany(Penugasan::class); }
    public function scopeAssurance($q) { return $q->where('kategori', 'assurance'); }
    public function scopeConsulting($q) { return $q->where('kategori', 'consulting'); }
}
