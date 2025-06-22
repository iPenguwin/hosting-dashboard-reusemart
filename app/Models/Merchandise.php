<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Merchandise extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'merchandises';
    protected $primaryKey = 'ID_MERCHANDISE';

    protected $fillable = [
        'NAMA_MERCHANDISE',
        'POIN_DIBUTUHKAN',
        'JUMLAH',
        'GAMBAR',
    ];
}
