<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PegawaiTransaksiPembelian extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'pegawai_transaksi_pembelians';
    public $timestamps = true;

    protected $fillable = [
        'ID_PEGAWAI',
        'ID_TRANSAKSI_PEMBELIAN',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function transaksiPembelian()
    {
        return $this->belongsTo(TransaksiPembelianBarang::class, 'ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN');
    }
}
