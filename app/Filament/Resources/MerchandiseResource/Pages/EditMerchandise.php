<?php

namespace App\Filament\Resources\MerchandiseResource\Pages;

use App\Filament\Resources\MerchandiseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditMerchandise extends EditRecord
{
    protected static string $resource = MerchandiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Perubahan Data')
            ->modalDescription('Apakah Anda yakin ingin menyimpan perubahan merchandise ini?')
            ->modalSubmitActionLabel('Ya, Simpan')
            ->modalCancelActionLabel('Batal')
            ->action(function () {
                $this->closeActionModal();
                $this->save();
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
