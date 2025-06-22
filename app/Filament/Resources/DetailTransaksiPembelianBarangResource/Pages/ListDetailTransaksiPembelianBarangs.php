<?php

namespace App\Filament\Resources\DetailTransaksiPembelianBarangResource\Pages;

use App\Filament\Resources\DetailTransaksiPembelianBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailTransaksiPembelianBarangs extends ListRecords
{
    protected static string $resource = DetailTransaksiPembelianBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
