<?php

namespace App\Filament\Widgets;

use App\Models\Pegawai;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

// class TestChartWidget extends ChartWidget
// {
//     protected static ?int $sort = 2;

//     protected static ?string $heading = 'Chart';

//     protected function getData(): array
//     {
//         $data = Trend::model(Pegawai::class)
//             ->between(
//                 start: now()->startOfMonth(),
//                 end: now()->endOfMonth(),
//             )
//             ->perMonth()
//             ->count();

//         return [
//             'datasets' => [
//                 [
//                     'label' => 'Pegawai Chart',
//                     'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
//                 ],
//             ],
//             'labels' => $data->map(fn(TrendValue $value) => $value->date),
//         ];
//     }

//     protected function getType(): string
//     {
//         return 'line';
//     }
// }
