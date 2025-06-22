<?php

namespace App\Filament\Resources\DesaKelurahanResource\Pages;

use App\Filament\Resources\DesaKelurahanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDesaKelurahan extends CreateRecord
{
    protected static string $resource = DesaKelurahanResource::class;

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
