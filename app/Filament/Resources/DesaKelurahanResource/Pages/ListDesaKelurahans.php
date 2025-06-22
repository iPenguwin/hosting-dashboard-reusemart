<?php

namespace App\Filament\Resources\DesaKelurahanResource\Pages;

use App\Filament\Resources\DesaKelurahanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesaKelurahans extends ListRecords
{
    protected static string $resource = DesaKelurahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
