<?php

namespace App\Filament\Resources\DetailTransaksiPenitipBarangResource\Pages;

use App\Filament\Resources\DetailTransaksiPenitipBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailTransaksiPenitipBarangs extends ListRecords
{
    protected static string $resource = DetailTransaksiPenitipBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
