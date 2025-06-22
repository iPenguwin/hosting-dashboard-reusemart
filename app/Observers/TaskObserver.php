<?php

namespace App\Observers;

use App\Models\TransaksiPembelianBarang;
use App\Models\Penitip;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class TaskObserver
{
    /**
     * Handle the TransaksiPembelianBarang "created" event.
     */
    public function created(TransaksiPembelianBarang $transaksiPembelianBarang): void
    {
        Notification::make()
            ->title('Transaksi Pembelian Barang Berhasil')
            ->body('Transaksi pembelian barang dengan ID ' . $transaksiPembelianBarang->ID_TRANSAKSI_PEMBELIAN . ' telah berhasil dibuat.')
            ->success()
            ->send();
    }

    /**
     * Handle the TransaksiPembelianBarang "updated" event.
     */
    public function updated(TransaksiPembelianBarang $transaksi): void
    {
        // Cek jika TGL_AMBIL_KIRIM diubah dari null ke nilai tertentu
        if (
            $transaksi->DELIVERY_METHOD === 'Ambil Sendiri' &&
            $transaksi->TGL_AMBIL_KIRIM !== null &&
            $transaksi->wasChanged('TGL_AMBIL_KIRIM')
        ) {
            $this->sendPenitipPickupNotification($transaksi);
        }
    }

    /**
     * Mengirim notifikasi ke penitip tentang jadwal pengambilan
     */
    // app/Observers/TaskObserver.php
    protected function sendPenitipPickupNotification(TransaksiPembelianBarang $transaksi): void
    {
        $penitip = $transaksi->barang->penitip;

        if (!$penitip) {
            return;
        }

        $tanggalAmbil = Carbon::parse($transaksi->TGL_AMBIL_KIRIM)->format('d/m/Y H:i');

        // Directly send database notification
        $penitip->notify(
            new \App\Notifications\PenitipPickupNotification(
                $transaksi->barang->NAMA_BARANG,
                $tanggalAmbil,
                $transaksi->barang->ID_BARANG
            )
        );
    }
    /**
     * Handle the TransaksiPembelianBarang "deleted" event.
     */
    public function deleted(TransaksiPembelianBarang $transaksiPembelianBarang): void
    {
        //
    }

    /**
     * Handle the TransaksiPembelianBarang "restored" event.
     */
    public function restored(TransaksiPembelianBarang $transaksiPembelianBarang): void
    {
        //
    }

    /**
     * Handle the TransaksiPembelianBarang "force deleted" event.
     */
    public function forceDeleted(TransaksiPembelianBarang $transaksiPembelianBarang): void
    {
        //
    }
}
