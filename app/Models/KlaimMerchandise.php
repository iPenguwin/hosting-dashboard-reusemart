<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KlaimMerchandise extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'klaim_merchandises';
    protected $primaryKey = 'ID_KLAIM';
    public $timestamps = true;


    protected $fillable = [
        'ID_MERCHANDISE',
        'ID_PEMBELI',
        'TGL_KLAIM',
        'TGL_PENGAMBILAN',
    ];

    protected static function booted()
    {
        static::created(function ($klaim) {
            // untuk kurangi jumlah merchandise
            $merchandise = Merchandise::find($klaim->ID_MERCHANDISE);
            if ($merchandise && $merchandise->JUMLAH > 0) {
                $merchandise->decrement('JUMLAH');
            }
        });

        static::deleted(function ($klaim) {
            // untuk kurangi jumlah jika klaim dihapus
            $merchandise = Merchandise::find($klaim->ID_MERCHANDISE);
            if ($merchandise) {
                $merchandise->increment('JUMLAH');
            }
        });
    }

    public function getStatusAttribute(): string
    {
        return is_null($this->TGL_PENGAMBILAN)
            ? 'Belum Diambil'
            : 'Sudah Diambil';
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class, 'ID_MERCHANDISE', 'ID_MERCHANDISE');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }
}
