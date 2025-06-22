<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jabatan extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'jabatans';
    protected $primaryKey = 'ID_JABATAN';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_JABATAN',
    ];
}
