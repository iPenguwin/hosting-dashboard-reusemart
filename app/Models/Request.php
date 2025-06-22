<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Request extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'requests';
    protected $primaryKey = 'ID_REQUEST';
    public $timestamps = true;

    protected $fillable = [
        'ID_ORGANISASI',
        'NAMA_BARANG_REQUEST',
        'CREATE_AT',
        'DESKRIPSI_REQUEST',
        'STATUS_REQUEST',
        'ID_BARANG',
    ];

    protected $attributes = [
        'STATUS_REQUEST' => 'Menunggu',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'ID_ORGANISASI', 'ID_ORGANISASI');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function transaksiDonasis()
    {
        return $this->hasMany(TransaksiDonasi::class, 'ID_REQUEST', 'ID_REQUEST');
    }

    protected static function booted()
    {
        static::creating(function ($request) {
            $request->CREATE_AT = $request->CREATE_AT ?? now();
            // $request->ID_ORGANISASI = auth()->user()->ID_ORGANISASI;
        });
    }

    public function allocateBarang($barangId)
    {
        $barang = Barang::findOrFail($barangId);

        $this->update(['ID_BARANG' => $barangId]);

        $this->update(['STATUS_REQUEST' => 'Diterima']);

        $barang->markAsDonated($this->ID_ORGANISASI);

        return $this;
    }

    
}
