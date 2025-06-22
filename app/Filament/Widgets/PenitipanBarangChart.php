<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Penitip;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PenitipanBarangChart extends ChartWidget
{
    protected static ?string $heading = 'Chart Aktivitas Penitipan Barang';
    protected static ?string $pollingInterval = '60s';
    protected static ?string $maxHeight = '300px';

    // Remove the static redeclaration and use the parent class's property correctly
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = 30; // Last 30 days
        $dates = [];
        $barangMasukData = [];
        $barangKeluarData = [];
        $penitipData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates[] = $date->format('d M');

            // Barang masuk
            $barangMasukData[] = Barang::whereDate('TGL_MASUK', $date)->count();

            // Barang keluar (either taken or sold)
            $barangKeluarData[] = Barang::whereDate('TGL_KELUAR', $date)->count();

            // New penitip
            $penitipData[] = Penitip::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Barang Masuk',
                    'data' => $barangMasukData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Barang Keluar',
                    'data' => $barangKeluarData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Penitip Baru',
                    'data' => $penitipData,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => '#8b5cf6',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
