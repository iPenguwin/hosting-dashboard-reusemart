<?php

namespace App\Filament\Widgets;

use App\Models\Penitip;
use App\Models\Barang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class TestStatsOverview extends BaseWidget
{
    // Atur refresh setiap 60 detik (1 menit) agar statistik selalu up to date
    protected static ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        // Mendapatkan jumlah data saat ini
        $jumlahPenitip = Penitip::count();
        $jumlahBarang = Barang::count();
        $jumlahBarangDidonasikan = Barang::where('STATUS_BARANG', 'Didonasikan')->count();

        // Mendapatkan data dari periode sebelumnya (misalnya 7 hari sebelumnya)
        $lastWeek = Carbon::now()->subDays(7);

        // Untuk penitip, kita hanya bisa menampilkan jumlah total karena
        // tidak ada kolom tanggal pembuatan di model
        $peningkatanPenitip = 0; // Default ke 0% jika tidak bisa dihitung

        // Jumlah barang minggu sebelumnya
        $jumlahBarangSebelumnya = Barang::where('TGL_MASUK', '<', $lastWeek)->count();
        $peningkatanBarang = $jumlahBarangSebelumnya > 0
            ? round((($jumlahBarang - $jumlahBarangSebelumnya) / $jumlahBarangSebelumnya) * 100, 1)
            : 0;

        // Jumlah barang didonasikan minggu sebelumnya
        $jumlahBarangDidonasikanSebelumnya = Barang::where('STATUS_BARANG', 'Didonasikan')
            ->where('TGL_MASUK', '<', $lastWeek)
            ->count();
        $peningkatanBarangDidonasikan = $jumlahBarangDidonasikanSebelumnya > 0
            ? round((($jumlahBarangDidonasikan - $jumlahBarangDidonasikanSebelumnya) / $jumlahBarangDidonasikanSebelumnya) * 100, 1)
            : 0;

        // Mendapatkan data tren untuk chart (7 hari terakhir)
        // Untuk Penitip, kita tidak bisa menunjukkan tren berdasarkan tanggal karena
        // tidak ada kolom tanggal, jadi kita akan menampilkan tren datar
        $trendPenitip = [1, 1, 1, 1, 1, 1, 1]; // Tren datar karena tidak ada data historis
        $trendBarang = $this->getTrendData('Barang');
        $trendBarangDidonasikan = $this->getTrendData('BarangDidonasikan');

        return [
            Card::make('Jumlah Penitip', $jumlahPenitip)
                ->color('success'),

            Card::make('Jumlah Barang', $jumlahBarang)
                ->description($peningkatanBarang . '% ' . ($peningkatanBarang >= 0 ? 'peningkatan' : 'penurunan'))
                ->descriptionIcon($peningkatanBarang >= 0 ? 'heroicon-s-arrow-trending-up' : 'heroicon-s-arrow-trending-down')
                ->chart($trendBarang)
                ->color($peningkatanBarang >= 0 ? 'success' : 'danger'),

            Card::make('Barang Didonasikan', $jumlahBarangDidonasikan)
                ->description($peningkatanBarangDidonasikan . '% ' . ($peningkatanBarangDidonasikan >= 0 ? 'peningkatan' : 'penurunan'))
                ->descriptionIcon($peningkatanBarangDidonasikan >= 0 ? 'heroicon-s-arrow-trending-up' : 'heroicon-s-arrow-trending-down')
                ->chart($trendBarangDidonasikan)
                ->color($peningkatanBarangDidonasikan >= 0 ? 'success' : 'danger'),
        ];
    }

    /**
     * Mendapatkan data tren untuk chart
     * 
     * @param string $model
     * @return array
     */
    private function getTrendData(string $model): array
    {
        $days = 7; // Tren 7 hari
        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDate = Carbon::now()->subDays($i)->endOfDay();

            switch ($model) {
                case 'Barang':
                    $count = Barang::whereBetween('TGL_MASUK', [$date, $nextDate])->count();
                    break;
                case 'BarangDidonasikan':
                    $count = Barang::where('STATUS_BARANG', 'Didonasikan')
                        ->whereBetween('TGL_MASUK', [$date, $nextDate])
                        ->count();
                    break;
                default:
                    $count = 0;
                    break;
            }

            $trend[] = $count;
        }

        // Pastikan tren tidak kosong agar chart tetap terlihat
        if (array_sum($trend) === 0) {
            return [0, 0, 0, 0, 0, 0, 1]; // Default tren jika tidak ada data
        }

        return $trend;
    }
}
