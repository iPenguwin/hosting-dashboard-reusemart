<?php

namespace App\Filament\Resources\DiskusiPegawaiResource\Pages;

use App\Filament\Resources\DiskusiPegawaiResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDiskusiPegawai extends CreateRecord
{
    protected static string $resource = DiskusiPegawaiResource::class;

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
