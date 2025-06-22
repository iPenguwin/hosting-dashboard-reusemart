<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPenitipanBarang extends Model
{
    use HasFactory;
    protected $guarded = []; // Lebih aman untuk menggunakan $fillable secara eksplisit
    protected $table = 'transaksi_penitipan_barangs';
    protected $primaryKey = 'ID_TRANSAKSI_PENITIPAN';
    public $timestamps = true; // created_at dan updated_at akan di-manage otomatis

    protected $fillable = [
        'ID_PENITIP',
        'TGL_MASUK_TITIPAN',
        'TGL_KELUAR_TITIPAN',
        'NO_NOTA_TRANSAKSI_TITIPAN', // Sebenarnya ini akan di-generate, jadi tidak perlu di fillable jika tidak diisi manual
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Generate NO_NOTA_TRANSAKSI_TITIPAN hanya jika belum ada
            if (empty($model->NO_NOTA_TRANSAKSI_TITIPAN)) {
                $now = Carbon::now();
                $tahun = $now->format('y'); // Tahun 2 digit
                $bulan = $now->format('m'); // Bulan 2 digit

                // Menggunakan ID berikutnya dari sequence primary key untuk nomor urut nota
                // PERHATIAN: static::getNextId() akan memberikan ID maks + 1 SEBELUM record ini disimpan.
                // Ini bisa menyebabkan duplikasi jika ada pembuatan bersamaan (concurrent requests)
                // dan ID_TRANSAKSI_PENITIPAN bukan merupakan bagian dari nota yang unik per bulan/tahun.
                // Jika ID_TRANSAKSI_PENITIPAN adalah auto-increment, maka lebih aman generate nota di event 'created'
                // atau menggunakan mekanisme sequence yang lebih robust.
                // Untuk saat ini, kita ikuti logika yang ada dengan static::getNextId().
                $nextIdForNota = static::getNextId(); // Panggil sebagai static method

                // Format: YY.MM.NEXT_ID (misal: 24.05.123)
                $model->NO_NOTA_TRANSAKSI_TITIPAN = "{$tahun}.{$bulan}." . str_pad($nextIdForNota, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Method ini sebaiknya static karena digunakan dalam konteks static creating event
    protected static function getNextId()
    {
        // Mendapatkan ID_TRANSAKSI_PENITIPAN maksimum yang sudah ada di database
        $lastId = static::max('ID_TRANSAKSI_PENITIPAN');
        return $lastId ? $lastId + 1 : 1; // ID berikutnya atau 1 jika tabel kosong
    }


    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function pegawaiQc()
    {
        return $this->hasOneThrough(
            Pegawai::class,
            PegawaiTransaksiPenitipan::class,
            'ID_TRANSAKSI_PENITIPAN', // FK di pegawai_transaksi_penitipans
            'ID_PEGAWAI',             // FK di pegawais
            'ID_TRANSAKSI_PENITIPAN', // Local key di transaksi_penitipan_barangs
            'ID_PEGAWAI'              // Local key di pegawai_transaksi_penitipans
        )->whereHas('jabatans', function ($query) {
            $query->where('NAMA_JABATAN', 'Pegawai Gudang');
        });
    }

    public function detailTransaksiPenitipans()
    {
        return $this->hasMany(DetailTransaksiPenitipBarang::class, 'ID_TRANSAKSI_PENITIPAN', 'ID_TRANSAKSI_PENITIPAN');
    }

    public function pegawaiTransaksiPenitipans()
    {
        return $this->hasMany(PegawaiTransaksiPenitipan::class, 'ID_TRANSAKSI_PENITIPAN', 'ID_TRANSAKSI_PENITIPAN')
            ->with('pegawai'); // Selalu load relasi pegawai
    }
}
