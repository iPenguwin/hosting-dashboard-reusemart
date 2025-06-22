<?php

namespace App\Filament\Resources\DetailTransaksiPembelianBarangResource\Pages;

use App\Filament\Resources\DetailTransaksiPembelianBarangResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailTransaksiPembelianBarang extends CreateRecord
{
    protected static string $resource = DetailTransaksiPembelianBarangResource::class;

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
