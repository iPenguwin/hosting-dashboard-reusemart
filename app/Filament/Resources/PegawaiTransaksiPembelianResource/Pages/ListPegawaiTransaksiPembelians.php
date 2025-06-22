<?php

namespace App\Filament\Resources\PegawaiTransaksiPembelianResource\Pages;

use App\Filament\Resources\PegawaiTransaksiPembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPegawaiTransaksiPembelians extends ListRecords
{
    protected static string $resource = PegawaiTransaksiPembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
