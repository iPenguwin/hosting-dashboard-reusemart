<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diskusi extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'diskusis';
    protected $primaryKey = 'ID_DISKUSI';
    public $timestamps = true;

    protected $fillable = [
        'ID_BARANG',
        'ID_PEMBELI',
        'PERTANYAAN',
        'JAWABAN',
        'ID_PEGAWAI',
        'CREATE_AT'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($diskusi) {
            $diskusi->CREATE_AT = Carbon::today();
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function diskusiPegawais()
    {
        return $this->hasMany(DiskusiPegawai::class, 'ID_DISKUSI', 'ID_DISKUSI');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function jawaban()
    {
        return $this->hasOne(JawabanDiskusi::class, 'ID_DISKUSI');
    }
}
