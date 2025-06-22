<?php

namespace App\Filament\Pages;

use App\Models\TransaksiPembelianBarang;
use Carbon\Carbon;
use Filament\Pages\Page;

class LaporanPenjualanBulanan extends Page
{
    protected static string $view = 'filament.pages.laporan-penjualan-bulanan';
    protected static ?string $navigationLabel = 'Laporan Penjualan Bulanan';
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';

    public int   $tahun;
    public array $dataBulanan   = [];
    public array $dataGrafik    = [];
    public int   $totalBarang   = 0;
    public float $totalPenjualan= 0.0;

    public function mount(): void
    {
        // Ambil dari query param ?tahun=...
        $this->tahun = (int) request()->query('tahun', Carbon::now()->year);
        $this->muatDataBulanan();
    }

    protected function muatDataBulanan(): void
    {
        $hasil = TransaksiPembelianBarang::query()
            ->selectRaw('MONTH(created_at) AS bulan_ke, COUNT(*) AS jumlah_barang, SUM(tot_harga_pembelian) AS jumlah_penjualan')
            ->whereYear('created_at', $this->tahun)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        $labelBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $entri = array_map(fn($i) => [
            'bulan'            => $labelBulan[$i],
            'jumlah_barang'    => 0,
            'jumlah_penjualan' => 0,
        ], range(0,11));

        foreach ($hasil as $r) {
            $idx = $r->bulan_ke - 1;
            $entri[$idx] = [
                'bulan'            => $labelBulan[$idx],
                'jumlah_barang'    => (int)$r->jumlah_barang,
                'jumlah_penjualan' => (float)$r->jumlah_penjualan,
            ];
        }

        $this->totalBarang    = array_sum(array_column($entri, 'jumlah_barang'));
        $this->totalPenjualan = array_sum(array_column($entri, 'jumlah_penjualan'));
        $this->dataBulanan    = $entri;
        $this->dataGrafik     = [
            'labels' => $labelBulan,
            'data'   => array_column($entri, 'jumlah_penjualan'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user instanceof \App\Models\Pegawai
            && in_array(strtolower($user->jabatan ?? ''), ['admin','owner']);
    }
}