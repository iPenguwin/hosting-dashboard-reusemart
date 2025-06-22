<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiDonasi extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'transaksi_donasis';
    protected $primaryKey = 'ID_TRANSAKSI_DONASI';
    public $timestamps = false;

    protected $fillable = [
        'ID_ORGANISASI',
        'ID_REQUEST',
        'TGL_DONASI',
        'PENERIMA',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'ID_ORGANISASI', 'ID_ORGANISASI');
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'ID_REQUEST', 'ID_REQUEST');
    }
}
