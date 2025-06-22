<?php

namespace App\Filament\Resources\DetailTransaksiPenitipBarangResource\Pages;

use App\Filament\Resources\DetailTransaksiPenitipBarangResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailTransaksiPenitipBarang extends CreateRecord
{
    protected static string $resource = DetailTransaksiPenitipBarangResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
