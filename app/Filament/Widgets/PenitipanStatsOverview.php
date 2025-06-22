<?php

namespace App\Filament\Widgets;

use App\Models\Penitip;
use App\Models\Barang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class PenitipanStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected ?string $heading = 'Statistik Penitipan Barang';

    protected function getCards(): array
    {
        $today = Carbon::today();
        $lastWeek = Carbon::today()->subDays(7);
        $lastMonth = Carbon::today()->subDays(30);

        // Penitip Statistics
        $totalPenitip = Penitip::count();
        $newPenitipThisWeek = Penitip::where('created_at', '>=', $lastWeek)->count();
        $activePenitip = Penitip::has('barangs')->count();

        // Barang Statistics
        $totalBarang = Barang::count();
        $barangMasukHariIni = Barang::whereDate('TGL_MASUK', $today)->count();
        $barangKeluarHariIni = Barang::whereDate('TGL_KELUAR', $today)->count();
        $barangDiambilHariIni = Barang::whereDate('TGL_AMBIL', $today)->count();

        // Status Statistics
        $barangTersedia = Barang::where('STATUS_BARANG', 'Tersedia')->count();
        $barangDidonasikan = Barang::where('STATUS_BARANG', 'Didonasikan')->count();
        $barangTerjual = Barang::where('STATUS_BARANG', 'Terjual')->count();
        $barangExpired = Barang::where('STATUS_BARANG', 'Kadaluarsa')->count();

        // Time-based statistics
        $barangMasukMingguIni = Barang::where('TGL_MASUK', '>=', $lastWeek)->count();
        $barangMasukBulanIni = Barang::where('TGL_MASUK', '>=', $lastMonth)->count();

        // Get trend data for comparison
        $penitipTrend = $this->getPenitipTrendData();
        $barangMasukTrend = $this->getBarangMovementTrend('masuk');
        $barangKeluarTrend = $this->getBarangMovementTrend('keluar');
        $statusTrends = [
            'Tersedia' => $this->getStatusTrendData('Tersedia'),
            'Didonasikan' => $this->getStatusTrendData('Didonasikan'),
            'Terjual' => $this->getStatusTrendData('Terjual'),
        ];

        // Helper function to calculate percentage safely
        $calculatePercentage = function ($part, $total) {
            return $total > 0 ? round(($part / $total) * 100, 1) : 0;
        };

        // Helper function to determine trend direction
        $getTrendDirection = function (array $trendData) {
            if (count($trendData) < 2) return 'heroicon-s-minus';

            $current = end($trendData);
            $previous = $trendData[count($trendData) - 2];

            return $current > $previous ? 'heroicon-s-arrow-up' : ($current < $previous ? 'heroicon-s-arrow-down' : 'heroicon-s-minus');
        };

        return [
            // Penitip Cards
            Card::make('Total Penitip', $totalPenitip)
                ->description($newPenitipThisWeek . ' baru minggu ini')
                ->descriptionIcon($newPenitipThisWeek > 0 ? 'heroicon-s-arrow-up' : 'heroicon-s-minus')
                ->chart($penitipTrend)
                ->color('primary'),

            Card::make('Penitip Aktif', $activePenitip)
                ->description($calculatePercentage($activePenitip, $totalPenitip) . '% dari total')
                ->descriptionIcon($getTrendDirection($penitipTrend))
                ->color('success'),

            // Barang Movement Cards
            Card::make('Barang Masuk Hari Ini', $barangMasukHariIni)
                ->description($barangMasukMingguIni . ' masuk minggu ini')
                ->descriptionIcon($getTrendDirection($barangMasukTrend))
                ->chart($barangMasukTrend)
                ->color('info'),

            Card::make('Barang Keluar Hari Ini', $barangKeluarHariIni)
                ->description($barangDiambilHariIni . ' diambil hari ini')
                ->descriptionIcon($getTrendDirection($barangKeluarTrend))
                ->chart($barangKeluarTrend)
                ->color($barangKeluarHariIni > 0 ? 'success' : 'gray'),

            // Status Cards
            Card::make('Barang Tersedia', $barangTersedia)
                ->description($calculatePercentage($barangTersedia, $totalBarang) . '% dari total')
                ->descriptionIcon($getTrendDirection($statusTrends['Tersedia']))
                ->chart($statusTrends['Tersedia'])
                ->color('success'),

            Card::make('Barang Didonasikan', $barangDidonasikan)
                ->description($calculatePercentage($barangDidonasikan, $totalBarang) . '% dari total')
                ->descriptionIcon($getTrendDirection($statusTrends['Didonasikan']))
                ->chart($statusTrends['Didonasikan'])
                ->color('warning'),

            Card::make('Barang Terjual', $barangTerjual)
                ->description($calculatePercentage($barangTerjual, $totalBarang) . '% dari total')
                ->descriptionIcon($getTrendDirection($statusTrends['Terjual']))
                ->chart($statusTrends['Terjual'])
                ->color('danger'),
        ];
    }

    /**
     * Get penitip trend data for the last 7 days
     */
    private function getPenitipTrendData(): array
    {
        $days = 7;
        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDate = Carbon::now()->subDays($i)->endOfDay();

            $count = Penitip::whereBetween('created_at', [$date, $nextDate])->count();
            $trend[] = $count;
        }

        return $trend;
    }

    /**
     * Get barang movement trend data
     */
    private function getBarangMovementTrend(string $type): array
    {
        $days = 7;
        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDate = Carbon::now()->subDays($i)->endOfDay();

            $count = match ($type) {
                'masuk' => Barang::whereBetween('TGL_MASUK', [$date, $nextDate])->count(),
                'keluar' => Barang::whereBetween('TGL_KELUAR', [$date, $nextDate])->count(),
                default => 0,
            };

            $trend[] = $count;
        }

        return $trend;
    }

    /**
     * Get status trend data
     */
    private function getStatusTrendData(string $status): array
    {
        $days = 7;
        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->endOfDay();

            $count = Barang::where('STATUS_BARANG', $status)
                ->where('TGL_MASUK', '<=', $date)
                ->count();

            $trend[] = $count;
        }

        return $trend;
    }
}
