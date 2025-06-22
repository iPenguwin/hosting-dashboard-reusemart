<?php

namespace App\Filament\Resources\KlaimMerchandiseResource\Pages;

use App\Filament\Resources\KlaimMerchandiseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateKlaimMerchandise extends CreateRecord
{
    protected static string $resource = KlaimMerchandiseResource::class;

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
