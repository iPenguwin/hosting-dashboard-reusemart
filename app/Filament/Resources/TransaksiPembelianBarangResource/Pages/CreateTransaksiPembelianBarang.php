<?php

namespace App\Filament\Resources\TransaksiPembelianBarangResource\Pages;

use App\Filament\Resources\TransaksiPembelianBarangResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiPembelianBarang extends CreateRecord
{
    protected static string $resource = TransaksiPembelianBarangResource::class;

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
