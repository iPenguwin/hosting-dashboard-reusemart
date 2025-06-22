<?php

namespace App\Filament\Resources\KategoribarangResource\Pages;

use App\Filament\Resources\KategoribarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoribarangs extends ListRecords
{
    protected static string $resource = KategoribarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
