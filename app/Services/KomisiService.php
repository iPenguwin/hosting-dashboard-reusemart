<?php

namespace App\Services;

use App\Models\TransaksiPembelianBarang;
use App\Models\Barang;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\Komisi;
use App\Models\Pembeli;
use Carbon\Carbon;

class KomisiService
{
    public function prosesKomisiDanPoin(TransaksiPembelianBarang $transaksi)
    {
        // Only process for completed transactions
        if ($transaksi->STATUS_TRANSAKSI !== 'Selesai') {
            return;
        }

        $barang = $transaksi->barang;
        $penitip = $barang->penitip;
        $hargaBarang = $this->hitungHargaBarang($barang); // Adjust price based on warranty
        $tanggalMasukBarang = Carbon::parse($barang->TGL_MASUK_BARANG);
        $tanggalTerjual = Carbon::parse($transaksi->TGL_PESAN_PEMBELIAN);
        $hariTerjual = $tanggalMasukBarang->diffInDays($tanggalTerjual);

        // 1. Calculate ReuseMart commission
        $komisiReuseMart = $this->hitungKomisiReuseMart($barang, $hargaBarang, $hariTerjual);

        // 2. Calculate Hunter commission (if applicable)
        $komisiHunter = $this->hitungKomisiHunter($barang, $hargaBarang, $hariTerjual);

        // 3. Calculate Penitip earnings (including bonus if sold < 7 days)
        $penghasilanPenitip = $this->hitungPenghasilanPenitip(
            $hargaBarang,
            $komisiReuseMart,
            $komisiHunter,
            $hariTerjual
        );

        // 4. Record Penitip earnings as commission
        $this->catatPenghasilanPenitip($penitip, $penghasilanPenitip, $transaksi);

        // 5. Add points to buyer's account
        $this->tambahPoinPembeli($transaksi->pembeli, $transaksi->TOT_HARGA_PEMBELIAN);

        // 6. Save all commissions to the database
        $this->simpanKomisi($transaksi, $komisiReuseMart, $komisiHunter, $penghasilanPenitip);
    }

    protected function hitungHargaBarang(Barang $barang): float
    {
        $hargaDasar = $barang->HARGA_BARANG;
        // Apply a 10% price increase if the item is still under factory warranty
        if ($this->isUnderWarranty($barang)) {
            return $hargaDasar * 1.10; // 10% higher price for warranted items
        }
        return $hargaDasar;
    }

    protected function isUnderWarranty(Barang $barang): bool
    {
        // Check if the item is still under warranty
        return $barang->GARANSI && Carbon::parse($barang->GARANSI)->isFuture();
    }

    protected function hitungKomisiReuseMart(Barang $barang, float $hargaBarang, int $hariTerjual): float
    {
        $isPerpanjangan = $this->cekPerpanjangan($barang);
        $persenKomisi = $isPerpanjangan ? 0.3 : 0.2; // 30% for extended, 20% otherwise
        return $hargaBarang * $persenKomisi;
    }

    protected function hitungKomisiHunter(Barang $barang, float $hargaBarang, int $hariTerjual): float
    {
        $hunter = $barang->pegawai;
        if ($hunter && strtolower($hunter->jabatan) === 'hunter' && $hariTerjual < 7) {
            return $hargaBarang * 0.05; // 5% commission for Hunter if sold < 7 days
        }
        return 0;
    }

    protected function hitungPenghasilanPenitip(float $hargaBarang, float $komisiReuseMart, float $komisiHunter, int $hariTerjual): float
    {
        $penghasilanDasar = $hargaBarang - ($komisiReuseMart + $komisiHunter);
        if ($hariTerjual < 7) {
            $bonus = $komisiReuseMart * 0.1; // 10% bonus from ReuseMart commission
            return $penghasilanDasar + $bonus;
        }
        return $penghasilanDasar;
    }

    protected function catatPenghasilanPenitip(Penitip $penitip, float $jumlah, TransaksiPembelianBarang $transaksi)
    {
        Komisi::create([
            'JENIS_KOMISI' => 'Penitip',
            'ID_PENITIP' => $penitip->ID_PENITIP,
            'ID_TRANSAKSI_PEMBELIAN' => $transaksi->ID_TRANSAKSI_PEMBELIAN,
            'NOMINAL_KOMISI' => $jumlah,
        ]);

        // Add to Penitip's saldo (assuming Penitip model has a SALDO column)
        $penitip->increment('SALDO_PENITIP', $jumlah);
    }

    protected function tambahPoinPembeli(Pembeli $pembeli, float $totalBelanja)
    {
        $basePoints = floor($totalBelanja / 10000); // 1 point per Rp10,000
        $bonusPoints = $totalBelanja > 500000 ? floor($basePoints * 0.2) : 0; // 20% bonus if > Rp500,000
        $totalPoints = $basePoints + $bonusPoints;
        $pembeli->increment('POINT_LOYALITAS_PEMBELI', $totalPoints);
    }

    protected function simpanKomisi(TransaksiPembelianBarang $transaksi, float $komisiReuseMart, float $komisiHunter, float $penghasilanPenitip)
    {
        $barang = $transaksi->barang;

        // Save ReuseMart commission (after deducting Hunter commission)
        if ($komisiReuseMart > 0) {
            Komisi::create([
                'JENIS_KOMISI' => 'ReuseMart',
                'ID_TRANSAKSI_PEMBELIAN' => $transaksi->ID_TRANSAKSI_PEMBELIAN,
                'NOMINAL_KOMISI' => $komisiReuseMart - $komisiHunter,
            ]);
        }

        // Save Hunter commission if applicable
        if ($komisiHunter > 0 && $barang->pegawai) {
            Komisi::create([
                'JENIS_KOMISI' => 'Hunter',
                'ID_PEGAWAI' => $barang->pegawai->ID_PEGAWAI,
                'ID_TRANSAKSI_PEMBELIAN' => $transaksi->ID_TRANSAKSI_PEMBELIAN,
                'NOMINAL_KOMISI' => $komisiHunter,
            ]);
            $barang->pegawai->increment('KOMISI_PEGAWAI', $komisiHunter);
        }
    }

    protected function cekPerpanjangan(Barang $barang): bool
    {
        return $barang->TGL_PERPANJANGAN !== null &&
            Carbon::parse($barang->TGL_PERPANJANGAN)->gt($barang->TGL_MASUK_BARANG);
    }
}
