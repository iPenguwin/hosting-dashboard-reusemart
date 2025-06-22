<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kecamatan extends Model
{
    protected $table = 'kecamatans';
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = 'id_kecamatan';
    public $timestamps = false;

    protected $fillable = ['id_kabupaten_kota', 'nama_kecamatan'];

    public function kabupatenKota()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten_kota');
    }

    public function desaKelurahan()
    {
        return $this->hasMany(DesaKelurahan::class, 'id_kecamatan');
    }
}
