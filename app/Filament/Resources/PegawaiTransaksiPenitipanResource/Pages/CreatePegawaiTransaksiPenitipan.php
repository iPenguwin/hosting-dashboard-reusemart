<?php

namespace App\Filament\Resources\PegawaiTransaksiPenitipanResource\Pages;

use App\Filament\Resources\PegawaiTransaksiPenitipanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePegawaiTransaksiPenitipan extends CreateRecord
{
    protected static string $resource = PegawaiTransaksiPenitipanResource::class;

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
