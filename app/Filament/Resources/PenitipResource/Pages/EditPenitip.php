<?php

namespace App\Filament\Resources\PenitipResource\Pages;

use App\Filament\Resources\PenitipResource;
use App\Models\Penitip;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPenitip extends EditRecord
{
    protected static string $resource = PenitipResource::class;

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
            ->modalHeading(fn(Penitip $record) => 'Edit Pegawai ' . $record->NAMA_PENITIP)
            ->modalDescription(fn(Penitip $record) => 'Apakah Anda yakin ingin mengedit pegawai ' . $record->NAMA_PENITIP . '?')
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
