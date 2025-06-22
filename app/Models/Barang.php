<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Barang extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'barangs';
    protected $primaryKey = 'ID_BARANG';
    public $timestamps = false;

    protected $fillable = [
        'ID_KATEGORI',
        'ID_PENITIP',
        'ID_PEGAWAI',
        'NAMA_BARANG',
        'KODE_BARANG',
        'HARGA_BARANG',
        'TGL_MASUK',
        'TGL_KELUAR',
        'TGL_AMBIL',
        'GARANSI',
        'BERAT',
        'DESKRIPSI',
        'RATING',
        'STATUS_BARANG',
        'FOTO_BARANG',
    ];

    protected $casts = [
        'TGL_MASUK' => 'date',
        'TGL_KELUAR' => 'date',
        'TGL_AMBIL' => 'date',
        'GARANSI' => 'date',
        'FOTO_BARANG' => 'array',
        'RATING' => 'integer',
        'BERAT' => 'float',
    ];

    // ... (getFotoBarangUrlAttribute dan relasi lainnya tetap sama) ...
    public function getFotoBarangUrlAttribute(): ?string
    {
        // Perbaikan: Akses elemen pertama jika FOTO_BARANG adalah array
        // atau gunakan langsung jika sudah string (walaupun cast 'array' menyarankan itu array)
        // Lebih baik gunakan getFotoBarangUrlsAttribute dan ambil yang pertama di controller/resource jika perlu satu.
        // Untuk konsistensi dengan 'array' cast, asumsikan ini mungkin perlu logika berbeda
        // atau dimaksudkan untuk satu gambar utama jika FOTO_BARANG tidak selalu array.
        // Namun, dengan `getFotoBarangUrlsAttribute` yang baru, ini mungkin jadi redundant atau perlu disesuaikan.
        // Jika FOTO_BARANG di-cast ke array, maka $this->FOTO_BARANG akan jadi array.
        // Mungkin yang dimaksud adalah:
        if ($this->FOTO_BARANG && is_array($this->FOTO_BARANG) && !empty($this->FOTO_BARANG[0])) {
            return asset('storage/' . $this->FOTO_BARANG[0]);
        } elseif ($this->FOTO_BARANG && is_string($this->FOTO_BARANG)) { // fallback jika bukan array
            return asset('storage/barang/' . $this->FOTO_BARANG);
        }
        return null;
    }

    public function kategoribarang()
    {
        return $this->belongsTo(Kategoribarang::class, 'ID_KATEGORI', 'ID_KATEGORI');
    }

    public function diskusis()
    {
        return $this->hasMany(Diskusi::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function detailTransaksiPembelians()
    {
        return $this->hasMany(DetailTransaksiPembelianBarang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function detailTransaksiPenitipans()
    {
        return $this->hasMany(DetailTransaksiPenitipBarang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI')
            ->whereHas('jabatans', function ($query) {
                $query->where('NAMA_JABATAN', 'Hunter');
            });
    }

    public function markAsDonated($organisasiId)
    {
        $this->update([
            'STATUS_BARANG' => 'Didonasikan',
            'ID_ORGANISASI' => $organisasiId
        ]);

        if ($this->penitip) {
            $donationPoints = floor($this->HARGA_BARANG / 10000); // 1 point per Rp10,000
            $this->penitip->increment('POINT_LOYALITAS_PENITIP', $donationPoints);
        }
    }

    protected static function booted()
    {
        static::creating(function ($barang) {
            // Set STATUS_BARANG default jika kosong
            if (empty($barang->STATUS_BARANG)) {
                $barang->STATUS_BARANG = 'Tersedia';
            }

            // Otomatis set TGL_KELUAR jika TGL_MASUK ada dan TGL_KELUAR kosong
            // TGL_MASUK akan menjadi instance Carbon karena sudah di-cast
            if ($barang->TGL_MASUK && empty($barang->TGL_KELUAR)) {

                // Pastikan TGL_MASUK adalah instance Carbon, jika tidak (misal dari input mentah), konversi dulu
                $tglMasuk = $barang->TGL_MASUK instanceof Carbon ? $barang->TGL_MASUK : Carbon::parse($barang->TGL_MASUK);
                $barang->TGL_KELUAR = $tglMasuk->copy()->addDays(30);
            }
        });

        static::created(function ($barang) {
            // Generate KODE_BARANG setelah barang dibuat dan ID_BARANG tersedia
            if (empty($barang->KODE_BARANG)) { // Hanya generate jika belum ada
                $prefix = strtoupper(Str::substr($barang->NAMA_BARANG, 0, 1));
                $barang->KODE_BARANG = $prefix . str_pad($barang->ID_BARANG, 4, '0', STR_PAD_LEFT);
                $barang->saveQuietly(); // Gunakan saveQuietly untuk menghindari loop event jika ada event 'saving'/'saved' lain
            }
        });
    }

    public function getFotoBarangUrlsAttribute(): array
    {
        if (!$this->FOTO_BARANG || !is_array($this->FOTO_BARANG)) { // Pastikan FOTO_BARANG adalah array
            return [];
        }

        return array_map(function ($fileName) {
            // Pastikan $fileName adalah string dan tidak kosong
            if (is_string($fileName) && !empty($fileName)) {
                // Cek apakah path sudah termasuk 'storage/' atau subdirektori yang benar
                // Asumsi FileUpload menyimpan nama file saja ke dalam 'barang' directory
                // dan asset() butuh path relatif dari 'public/storage'
                if (Str::startsWith($fileName, 'barang/')) {
                    return asset('storage/' . $fileName);
                }
                return asset('storage/barang/' . $fileName);
            }
            return null;
        }, $this->FOTO_BARANG);
    }

    public function getNamaPegawaiQcAttribute()
    {
        return $this->detailTransaksiPenitipans
            ->first()
            ?->transaksiPenitipan
            ?->pegawaiTransaksiPenitipans
            ->first()
            ?->pegawai
            ?->NAMA_PEGAWAI ?? 'Belum ada QC';
    }

    public function pegawaiTransaksiPenitipans()
    {
        return $this->hasManyThrough(
            PegawaiTransaksiPenitipan::class,
            TransaksiPenitipanBarang::class,
            'ID_TRANSAKSI_PENITIPAN',
            'ID_TRANSAKSI_PENITIPAN',
            'ID_TRANSAKSI_PENITIPAN',
            'ID_TRANSAKSI_PENITIPAN'
        );
    }
}
