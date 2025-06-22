<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PegawaiTransaksiPenitipan extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'pegawai_transaksi_penitipans';

    protected $fillable = [
        'ID_TRANSAKSI_PENITIPAN',
        'ID_PEGAWAI',
    ];

    public function transaksiPenitipan()
    {
        return $this->belongsTo(TransaksiPenitipanBarang::class, 'ID_TRANSAKSI_PENITIPAN', 'ID_TRANSAKSI_PENITIPAN');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }
}
