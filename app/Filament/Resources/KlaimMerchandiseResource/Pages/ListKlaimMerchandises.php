<?php

namespace App\Filament\Resources\KlaimMerchandiseResource\Pages;

use App\Filament\Resources\KlaimMerchandiseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKlaimMerchandises extends ListRecords
{
    protected static string $resource = KlaimMerchandiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
