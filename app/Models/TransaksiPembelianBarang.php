<?php

namespace App\Models;

use App\Observers\TaskObserver;
use App\Services\KomisiService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPembelianBarang extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'transaksi_pembelian_barangs';
    protected $primaryKey = 'ID_TRANSAKSI_PEMBELIAN';
    public $timestamps = true;

    protected $fillable = [
        'ID_PEMBELI',
        'BUKTI_TRANSFER',
        'ID_BARANG',
        'DELIVERY_METHOD',
        'ID_ALAMAT_PENGIRIMAN',
        'TGL_PESAN_PEMBELIAN',
        'TGL_AMBIL_KIRIM',
        'TGL_LUNAS_PEMBELIAN',
        'TOT_HARGA_PEMBELIAN',
        'HARGA_BARANG_SEBELUM_ONGKIR',
        'STATUS_PEMBAYARAN',
        'DELIVERY_METHOD',
        'ONGKOS_KIRIM',
        'POTONGAN_GRATIS_ONGKIR',
        'POIN_DIDAPAT',
        'POIN_POTONGAN',
        'STATUS_BUKTI_TRANSFER',
        'STATUS_TRANSAKSI',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function detailTransaksiPembelians()
    {
        return $this->hasMany(DetailTransaksiPembelianBarang::class, 'ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN')->with('barang');;
    }

    public function alamatPengiriman()
    {
        return $this->belongsTo(Alamat::class, 'ID_ALAMAT_PENGIRIMAN', 'ID_ALAMAT');
    }

    public function pegawaiTransaksiPembelians()
    {
        return $this->hasMany(PegawaiTransaksiPembelian::class, 'ID_TRANSAKSI_PEMBELIAN');
    }

    public function pegawaiQc()
    {
        return $this->hasOneThrough(
            Pegawai::class,
            PegawaiTransaksiPembelian::class,
            'ID_TRANSAKSI_PEMBELIAN',
            'ID_PEGAWAI',
            'ID_TRANSAKSI_PEMBELIAN',
            'ID_PEGAWAI'
        )->whereHas('jabatans', function ($query) {
            $query->where('NAMA_JABATAN', 'Pegawai Gudang');
        });
    }

    public function komisis()
    {
        return $this->hasMany(Komisi::class, 'ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN');
    }

    protected static function booted()
    {
        parent::booted();

        static::observe(TaskObserver::class);
        static::observe(\App\Observers\TransaksiPembelianBarangObserver::class);

        static::creating(function ($transaksi) {
            // Set default values
            if (empty($transaksi->STATUS_PEMBAYARAN)) {
                $transaksi->STATUS_PEMBAYARAN = 'Belum dibayar';
            }
            if (empty($transaksi->STATUS_BUKTI_TRANSFER)) {
                $transaksi->STATUS_BUKTI_TRANSFER = 'N/A';
            }
            if (empty($transaksi->STATUS_TRANSAKSI)) {
                $transaksi->STATUS_TRANSAKSI = 'Menunggu Pembayaran';
            }
            if (empty($transaksi->POIN_DIDAPAT)) {
                $transaksi->POIN_DIDAPAT = 0;
            }
        });

        static::created(function ($transaksi) {
            // Update status barang to "Dipesan"
            Barang::where('ID_BARANG', $transaksi->ID_BARANG)
                ->update(['STATUS_BARANG' => 'Dipesan']);

            // Schedule expiration check after 1 minute
            $transaksi->scheduleExpirationCheck();
        });

        static::updated(function ($transaksi) {
            if ($transaksi->isDirty('STATUS_BUKTI_TRANSFER') && $transaksi->STATUS_PEMBAYARAN === 'Sudah dibayar') {
                $transaksi->TGL_LUNAS_PEMBELIAN = now();
            }

            // Update status barang when payment is complete and delivery is pickup
            if (
                $transaksi->STATUS_PEMBAYARAN === 'Sudah dibayar' &&
                $transaksi->DELIVERY_METHOD === 'Ambil Sendiri' &&
                $transaksi->STATUS_TRANSAKSI === 'Perlu Diambil'
            ) {
                Barang::where('ID_BARANG', $transaksi->ID_BARANG)
                    ->update(['STATUS_BARANG' => 'Menunggu Diambil']);
            }

            // Update status barang when transaction is completed
            if ($transaksi->isDirty('STATUS_TRANSAKSI') && $transaksi->STATUS_TRANSAKSI === 'Selesai') {
                Barang::where('ID_BARANG', $transaksi->ID_BARANG)
                    ->update(['STATUS_BARANG' => 'Terkirim']);

                // Update pembeli's points
                if ($transaksi->pembeli) {
                    $transaksi->pembeli->increment('POINT_LOYALITAS_PEMBELI', $transaksi->POIN_DIDAPAT);
                    if ($transaksi->POIN_POTONGAN > 0) {
                        $transaksi->pembeli->decrement('POINT_LOYALITAS_PEMBELI', $transaksi->POIN_POTONGAN);
                    }
                }

                $transaksi->hitungDanDistribusikanKomisi();

                // Schedule donation check for pickup orders
                if ($transaksi->DELIVERY_METHOD === 'Ambil Sendiri') {
                    $transaksi->scheduleDonationCheck();
                }
            }
        });
    }

    public function scheduleExpirationCheck(): void
    {
        // Schedule the check to run exactly 1 minute after creation
        dispatch(function () {
            $trx = self::find($this->ID_TRANSAKSI_PEMBELIAN);
            if (!$trx) return;

            if ($trx->isExpired()) {
                $trx->update([
                    'STATUS_TRANSAKSI' => 'Hangus',
                    'STATUS_BUKTI_TRANSFER' => 'Tidak Valid',
                ]);

                // Return item status to available
                Barang::where('ID_BARANG', $trx->ID_BARANG)
                    ->update(['STATUS_BARANG' => 'Tersedia']);

                // Send notification
                Notification::make()
                    ->title('Transaksi dibatalkan karena melewati batas waktu pembayaran (1 menit)')
                    ->danger()
                    ->send();
            }
        })->delay(now()->addMinute());
    }

    public function checkPaymentProof()
    {
        // Schedule a check after 1 minute
        dispatch(function () {
            // Reload the transaction to get current status
            $freshTransaction = self::find($this->ID_TRANSAKSI_PEMBELIAN);

            if (
                $freshTransaction &&
                $freshTransaction->STATUS_PEMBAYARAN === 'Belum dibayar' &&
                $freshTransaction->STATUS_BUKTI_TRANSFER !== 'Valid'
            ) {

                // Update transaction status to Hangus
                $freshTransaction->update([
                    'STATUS_TRANSAKSI' => 'Hangus'
                ]);

                // Revert item status back to Tersedia
                Barang::where('ID_BARANG', $freshTransaction->ID_BARANG)
                    ->update(['STATUS_BARANG' => 'Tersedia']);
            }
        })->delay(now()->addMinute());
    }

    // Di dalam model TransaksiPembelianBarang
    public function isExpired(): bool
    {
        // Check if transaction is older than 1 minute
        $ageInSeconds = $this->created_at->diffInSeconds(now());

        // Transaction expires if:
        // 1. More than 1 minute old
        // 2. Payment status is still "Belum dibayar"
        // 3. Payment proof is not valid
        // 4. Transaction status is still "Menunggu Pembayaran"
        return $ageInSeconds > 60 &&
            $this->STATUS_PEMBAYARAN === 'Belum dibayar' &&
            $this->STATUS_BUKTI_TRANSFER !== 'Valid' &&
            $this->STATUS_TRANSAKSI === 'Menunggu Pembayaran';
    }

    public function scheduleStatusChecks(): void
    {
        // Jalankan job segera (tanpa delay) — cukup sekali saja
        dispatch(function () {
            $trx = self::find($this->ID_TRANSAKSI_PEMBELIAN);
            if (!$trx) return;

            // Hitung selisih waktu dengan created_at
            if ($trx->isExpired()) {
                $trx->update([
                    'STATUS_TRANSAKSI'      => 'Hangus',
                    'STATUS_BUKTI_TRANSFER' => 'Tidak Valid',
                    'STATUS_PEMBAYARAN'     => 'Belum dibayar',
                ]);

                // Kembalikan status barang
                Barang::where('ID_BARANG', $trx->ID_BARANG)
                    ->update(['STATUS_BARANG' => 'Tersedia']);

                // (opsional) notifikasi
                Notification::make()
                    ->title('Transaksi dibatalkan karena melewati batas 1 menit.')
                    ->danger()
                    ->send();
            }
        });
    }


    // method baru untuk menghitung komisi
    public function hitungDanDistribusikanKomisi()
    {
        $komisiService = new KomisiService();
        $komisiService->prosesKomisiDanPoin($this);
    }

    public function getTglPesanPembelianAttribute($value)
    {
        return $value ?? $this->created_at;
    }

    public function scheduleDonationCheck()
    {
        if (
            $this->STATUS_TRANSAKSI === 'Transaksi Berhasil' &&
            $this->DELIVERY_METHOD === 'Ambil Sendiri' &&
            empty($this->TGL_AMBIL_KIRIM)
        ) {
            $orderDate = Carbon::parse($this->TGL_PESAN_PEMBELIAN);
            $checkDate = $orderDate->copy()->addDays(2)->startOfDay();

            if (Carbon::now()->greaterThanOrEqualTo($checkDate)) {
                $this->processDonationCheck();
            } else {
                dispatch(function () {
                    $freshTransaction = self::find($this->ID_TRANSAKSI_PEMBELIAN);
                    if ($freshTransaction) {
                        $freshTransaction->processDonationCheck();
                    }
                })->delay($checkDate);
            }
        }
    }

    // public function getPenitipNameAttribute()
    // {
    //     if ($this->request && $this->request->barang && $this->request->barang->penitip) {
    //         return $this->request->barang->penitip->NAMA_PENITIP;
    //     }

    //     return null;
    // }

    public function kurirTransaksiPembelians()
    {
        return $this->hasMany(KurirTransaksiPembelian::class, 'ID_TRANSAKSI_PEMBELIAN', 'ID_TRANSAKSI_PEMBELIAN');
    }

    public function getNamaPegawaiKurirAttribute()
    {
        $kurir = $this->kurirTransaksiPembelians()
            ->whereHas('pegawai.jabatans', fn($query) => $query->where('NAMA_JABATAN', 'Kurir'))
            ->first();

        return $kurir?->pegawai?->NAMA_PEGAWAI ?? '-';
    }

    public function confirmShipmentByKurir($pegawaiId)
    {
        $pegawai = Pegawai::where('ID_PEGAWAI', $pegawaiId)
            ->whereHas('jabatans', fn($query) => $query->where('NAMA_JABATAN', 'Kurir'))
            ->first();

        if (!$pegawai) {
            Notification::make()
                ->title('Pegawai bukan kurir atau tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $this->update([
            'STATUS_TRANSAKSI' => 'Selesai',
            'TGL_AMBIL_KIRIM' => Carbon::now(),
        ]);

        $this->barang->update([
            'STATUS_BARANG' => 'Terkirim'
        ]);

        $this->kurirTransaksiPembelians()->create([
            'ID_PEGAWAI' => $pegawaiId,
            'ID_TRANSAKSI_PEMBELIAN' => $this->ID_TRANSAKSI_PEMBELIAN,
            'TGL_KONFIRMASI' => Carbon::now(),
        ]);

        Notification::make()
            ->title('Status transaksi diperbarui: Selesai')
            ->success()
            ->send();
    }

    protected function processDonationCheck()
    {
        // Reload fresh data
        $freshTransaction = self::find($this->ID_TRANSAKSI_PEMBELIAN);
        if (!$freshTransaction) return;

        // Check conditions again in case something changed
        if (
            $freshTransaction->STATUS_TRANSAKSI === 'Transaksi Berhasil' &&
            $freshTransaction->DELIVERY_METHOD === 'Ambil Sendiri' &&
            empty($freshTransaction->TGL_AMBIL_KIRIM)
        ) {

            // Update transaction status
            $freshTransaction->update([
                'STATUS_TRANSAKSI' => 'Hangus'
            ]);

            // Update item status
            Barang::where('ID_BARANG', $freshTransaction->ID_BARANG)
                ->update(['STATUS_BARANG' => 'Untuk Donasi']);
        }
    }
}
