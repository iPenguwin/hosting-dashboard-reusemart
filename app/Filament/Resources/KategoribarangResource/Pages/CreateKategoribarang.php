<?php

namespace App\Filament\Resources\KategoribarangResource\Pages;

use App\Filament\Resources\KategoribarangResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoribarang extends CreateRecord
{
    protected static string $resource = KategoribarangResource::class;

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

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Kategori barang ' . $this->record->NAMA_KATEGORI . ' berhasil ditambahkan';
    }
}
