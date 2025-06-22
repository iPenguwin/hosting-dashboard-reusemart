<?php

namespace App\Filament\Resources\TransaksiDonasiResource\Pages;

use App\Filament\Resources\TransaksiDonasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiDonasis extends ListRecords
{
    protected static string $resource = TransaksiDonasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
