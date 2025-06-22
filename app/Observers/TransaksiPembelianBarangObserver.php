<?php

namespace App\Observers;

use App\Models\TransaksiPembelianBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransaksiPembelianBarangObserver
{
    public function created(TransaksiPembelianBarang $transaksi)
    {
        // Jika tidak ada bukti transfer, jadwalkan pengecekan setelah 1 menit
        if (empty($transaksi->BUKTI_TRANSFER) && $transaksi->STATUS_PEMBAYARAN === 'Belum dibayar') {
            $this->scheduleHangusCheck($transaksi);
        }
    }

    public function updated(TransaksiPembelianBarang $transaksi)
    {
        // Jika bukti transfer dihapus, jadwalkan pengecekan
        if ($transaksi->isDirty('BUKTI_TRANSFER') && empty($transaksi->BUKTI_TRANSFER)) {
            $this->scheduleHangusCheck($transaksi);
        }
    }

    protected function scheduleHangusCheck(TransaksiPembelianBarang $transaksi)
    {
        // Jadwalkan pengecekan setelah 1 menit
        $checkTime = Carbon::now()->addMinute();

        Log::info("Menjadwalkan pengecekan status Hangus untuk transaksi {$transaksi->ID_TRANSAKSI_PEMBELIAN} pada {$checkTime}");

        dispatch(function () use ($transaksi) {
            $this->checkAndMarkAsHangus($transaksi->ID_TRANSAKSI_PEMBELIAN);
        })->delay($checkTime);
    }

    public function checkAndMarkAsHangus($idTransaksi)
    {
        $transaksi = TransaksiPembelianBarang::find($idTransaksi);

        if (!$transaksi) {
            Log::warning("Transaksi {$idTransaksi} tidak ditemukan saat pengecekan status Hangus");
            return;
        }

        Log::info("Memeriksa transaksi {$idTransaksi} untuk status Hangus", [
            'bukti_transfer' => $transaksi->BUKTI_TRANSFER,
            'status_pembayaran' => $transaksi->STATUS_PEMBAYARAN,
            'created_at' => $transaksi->created_at,
            'waktu_sekarang' => Carbon::now(),
            'selisih_menit' => Carbon::now()->diffInMinutes($transaksi->created_at)
        ]);

        // Cek jika:
        // 1. Tidak ada bukti transfer
        // 2. Status masih Belum dibayar
        // 3. Sudah lebih dari 1 menit sejak dibuat
        if (
            empty($transaksi->BUKTI_TRANSFER) &&
            $transaksi->STATUS_PEMBAYARAN === 'Belum dibayar' &&
            Carbon::now()->diffInMinutes($transaksi->created_at) >= 1
        ) {

            Log::info("Mengubah status transaksi {$idTransaksi} menjadi Hangus");

            $transaksi->update([
                'STATUS_TRANSAKSI' => 'Hangus',
                'STATUS_BUKTI_TRANSFER' => 'Tidak Valid'
            ]);

            // Kembalikan status barang
            if ($transaksi->barang) {
                $transaksi->barang->update(['STATUS_BARANG' => 'Tersedia']);
            }
        } else {
            Log::info("Transaksi {$idTransaksi} tidak memenuhi syarat untuk diubah menjadi Hangus");
        }
    }
}
