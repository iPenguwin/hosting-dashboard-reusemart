<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategoribarang extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'kategoribarangs';
    protected $primaryKey = 'ID_KATEGORI';
    public $timestamps = false;

    protected $appends = ['JML_BARANG', 'JML_TERJUAL', 'JML_DIDONASIKAN'];

    protected $fillable = [
        'NAMA_KATEGORI',
        'JML_BARANG',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'ID_KATEGORI', 'ID_KATEGORI');
    }

    public function getJMLBARANGAttribute()
    {
        return $this->barangs()->count();
    }

    public function getJMLTERJUALAttribute()
    {
        return $this->barangs()->where('STATUS_BARANG', 'Terjual')->count();
    }

    public function getJMLDIDONASIKANAttribute()
    {
        return $this->barangs()->where('STATUS_BARANG', 'Didonasikan')->count();
    }

    protected static function booted()
    {
        // Auto-update counts when Barang changes
        static::updated(function ($kategori) {
            $kategori->updateCounts();
        });
    }

    public function updateCounts()
    {
        $this->update([
            'JML_BARANG' => $this->barangs()->count(),
            // Jika Anda ingin kolom lain juga di-update:
            // 'JML_TERJUAL' => $this->barangs()->where('STATUS_BARANG', 'Terjual')->count(),
            // 'JML_DIDONASIKAN' => $this->barangs()->where('STATUS_BARANG', 'Didonasikan')->count(),
        ]);
    }
}
