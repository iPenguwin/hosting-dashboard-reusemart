<?php

namespace App\Filament\Resources\TransaksiPembelianBarangResource\Pages;

use App\Filament\Resources\TransaksiPembelianBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPembelianBarangs extends ListRecords
{
    protected static string $resource = TransaksiPembelianBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
