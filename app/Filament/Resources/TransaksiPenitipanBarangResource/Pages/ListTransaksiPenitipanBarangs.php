<?php

namespace App\Filament\Resources\TransaksiPenitipanBarangResource\Pages;

use App\Filament\Resources\TransaksiPenitipanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPenitipanBarangs extends ListRecords
{
    protected static string $resource = TransaksiPenitipanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
