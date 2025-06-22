<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiskusiPegawai extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'diskusi_pegawais';
    public $timestamps = true;

    protected $fillable = [
        'ID_PEGAWAI',
        'ID_DISKUSI',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function diskusi()
    {
        return $this->belongsTo(Diskusi::class, 'ID_DISKUSI', 'ID_DISKUSI');
    }
}
