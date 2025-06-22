<?php
// app/Models/Alamat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alamat extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'alamats';
    protected $primaryKey = 'ID_ALAMAT';
    public $timestamps = false;
    protected $fillable = [
        'ID_PEMBELI',
        'JUDUL',
        'NAMA_JALAN',
        'PROVINSI',
        'KABUPATEN',
        'KECAMATAN',
        'DESA_KELURAHAN',
    ];
    protected static function booted()
    {
        static::saving(function ($alamat) {
            // Only auto-fill if DESA_KELURAHAN is provided but others are empty
            if ($alamat->isDirty('DESA_KELURAHAN') && empty($alamat->KECAMATAN)) {
                $desa = DesaKelurahan::find($alamat->DESA_KELURAHAN);
                $alamat->KECAMATAN = $desa->id_kecamatan;
                
                $kecamatan = Kecamatan::find($desa->id_kecamatan);
                $alamat->KABUPATEN = $kecamatan->id_kabupaten_kota;
                
                $kabupaten = Kabupaten::find($kecamatan->id_kabupaten_kota);
                $alamat->PROVINSI = $kabupaten->id_provinsi;
            }
        });
    }
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }
    public function provinsiData()
    {
        return $this->belongsTo(Provinsi::class, 'PROVINSI', 'id_provinsi');
    }

    public function kabupatenData()
    {
        return $this->belongsTo(Kabupaten::class, 'KABUPATEN', 'id_kabupaten_kota');
    }

    public function kecamatanData()
    {
        return $this->belongsTo(Kecamatan::class, 'KECAMATAN', 'id_kecamatan');
    }

    public function desaData()
    {
        return $this->belongsTo(DesaKelurahan::class, 'DESA_KELURAHAN', 'id_desa_kelurahan');
    }
}
