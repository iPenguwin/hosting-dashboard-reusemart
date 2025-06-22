<?php

namespace App\Filament\Resources\TransaksiPenitipanBarangResource\Pages;

use App\Filament\Resources\TransaksiPenitipanBarangResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPenitipanBarang extends EditRecord
{
    protected static string $resource = TransaksiPenitipanBarangResource::class;

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
            ->modalDescription('Apakah Anda yakin ingin menyimpan perubahan transaksi ini?')
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
