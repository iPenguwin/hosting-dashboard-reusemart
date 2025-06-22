<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTransaksiPembelianBarang extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'detail_transaksi_pembelian_barangs';
    protected $primaryKey = 'ID_DETAIL_TRANSAKSI_PEMBELIAN';

    protected $fillable = [
        'ID_TRANSAKSI_PEMBELIAN',
        'ID_BARANG',    
    ];

    public function transaksiPembelian()
    {
        return $this->belongsTo(TransaksiPembelianBarang::class, 'ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }
}
