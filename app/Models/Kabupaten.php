<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kabupaten extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'kabupatens';
    protected $primaryKey = 'id_kabupaten_kota';
    public $timestamps = false;

    protected $fillable = ['id_provinsi', 'nama_kabupaten_kota'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi');
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'id_kabupaten_kota');
    }
}
