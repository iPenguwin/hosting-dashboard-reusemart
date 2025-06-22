<?php

namespace App\Filament\Resources\TransaksiPenitipanBarangResource\Pages;

use App\Filament\Resources\TransaksiPenitipanBarangResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiPenitipanBarang extends CreateRecord
{
    protected static string $resource = TransaksiPenitipanBarangResource::class;

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
