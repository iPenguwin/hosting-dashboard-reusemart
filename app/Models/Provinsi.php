<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provinsi extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'provinsis';
    protected $primaryKey = 'id_provinsi';
    public $timestamps = false;

    protected $fillable = ['nama_provinsi'];

    public function kabupatenKota()
    {
        return $this->hasMany(Kabupaten::class, 'id_provinsi');
    }
}
