<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DesaKelurahan extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'desa_kelurahans';
    protected $primaryKey = 'id_desa_kelurahan';
    public $timestamps = false;

    protected $fillable = ['id_kecamatan', 'nama_desa_kelurahan'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    }
}
