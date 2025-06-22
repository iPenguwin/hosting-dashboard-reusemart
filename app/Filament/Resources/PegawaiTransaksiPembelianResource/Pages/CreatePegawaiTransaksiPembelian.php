<?php

namespace App\Filament\Resources\PegawaiTransaksiPembelianResource\Pages;

use App\Filament\Resources\PegawaiTransaksiPembelianResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePegawaiTransaksiPembelian extends CreateRecord
{
    protected static string $resource = PegawaiTransaksiPembelianResource::class;

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
