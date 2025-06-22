<?php

namespace App\Filament\Resources\KomisiResource\Pages;

use App\Filament\Resources\KomisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKomisis extends ListRecords
{
    protected static string $resource = KomisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
