<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KurirTransaksiPembelian extends Model
{
    use HasFactory;

    protected $table = 'kurir_transaksi_pembelians';
    protected $primaryKey = 'ID_KURIR_TRANSAKSI';
    protected $guarded = [];

    protected $fillable = [
        'ID_PEGAWAI',
        'ID_TRANSAKSI_PEMBELIAN',
        'TGL_KONFIRMASI',
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
