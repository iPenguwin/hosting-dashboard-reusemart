<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTransaksiPenitipBarang extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'detail_transaksi_penitip_barangs';
    protected $primaryKey = 'ID_DETAIL_TRANSAKSI_PENITIPAN';

    protected $fillable = [
        'ID_TRANSAKSI_PENITIPAN',
        'ID_BARANG',
        'NAMA_BARANG',
    ];

    public function transaksiPenitipan()
    {
        return $this->belongsTo(TransaksiPenitipanBarang::class, 'ID_TRANSAKSI_PENITIPAN', 'ID_TRANSAKSI_PENITIPAN');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }
}
