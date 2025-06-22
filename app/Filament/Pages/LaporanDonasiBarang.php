<?php

namespace App\Filament\Pages;

use App\Models\TransaksiDonasi;
use Carbon\Carbon;
use Filament\Pages\Page;

class LaporanDonasiBarang extends Page
{
    protected static string $view = 'filament.pages.laporan-donasi-barang';
    protected static ?string $navigationLabel = 'Laporan Donasi Barang';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';

    public $tahun;
    public $dataDonasi = [];

    public function mount()
    {
        $this->tahun = Carbon::now()->year;
        $this->loadDataDonasi();
    }

    public function loadDataDonasi()
    {
        $this->dataDonasi = TransaksiDonasi::with(['organisasi', 'request.barang.penitip'])
            ->whereYear('TGL_DONASI', $this->tahun)
            ->get()
            ->map(function ($donasi) {
                $barang = $donasi->request->barang;
                return [
                    'kode_barang' => $barang?->KODE_BARANG ?? '-',
                    'nama_barang' => $barang?->NAMA_BARANG ?? '-',
                    'id_penitip' => $barang?->penitip?->ID_PENITIP ?? '-',
                    'nama_penitip' => $barang?->penitip?->NAMA_PENITIP ?? '-',
                    'tgl_donasi' => $donasi->TGL_DONASI ? Carbon::parse($donasi->TGL_DONASI)->format('d/m/Y') : '-',
                    'organisasi' => $donasi->organisasi?->NAMA_ORGANISASI ?? '-',
                    'penerima' => $donasi->PENERIMA ?? '-',
                ];
            })
            ->toArray();
    }

    public function updatedTahun()
    {
        $this->loadDataDonasi();
    }
}
