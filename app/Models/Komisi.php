<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komisi extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'komisis';
    protected $primaryKey = 'ID_KOMISI';
    public $timestamps = true;

    protected $fillable = [
        'JENIS_KOMISI',
        'ID_PENITIP',
        'ID_PEGAWAI',
        'ID_TRANSAKSI_PEMBELIAN',
        'NOMINAL_KOMISI',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function transaksiPembelian()
    {
        return $this->belongsTo(TransaksiPembelianBarang::class, 'ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN');
    }
}
