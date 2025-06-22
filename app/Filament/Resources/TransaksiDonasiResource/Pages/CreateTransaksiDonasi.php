<?php

namespace App\Filament\Resources\TransaksiDonasiResource\Pages;

use App\Filament\Resources\TransaksiDonasiResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiDonasi extends CreateRecord
{
    protected static string $resource = TransaksiDonasiResource::class;

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
