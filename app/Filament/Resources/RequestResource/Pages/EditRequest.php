<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use App\Models\Barang;
use App\Models\Request;
use App\Models\TransaksiDonasi;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditRequest extends EditRecord
{
    protected static string $resource = RequestResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldStatus = $record->STATUS_REQUEST;
        $newStatus = $data['STATUS_REQUEST'];

        // Jika status berubah dari selain 'Diterima' menjadi 'Diterima'
        if ($oldStatus !== 'Diterima' && $newStatus === 'Diterima') {
            // Jika ada ID_BARANG yang dipilih
            if (isset($data['ID_BARANG']) && $data['ID_BARANG']) {
                // Gunakan method allocateBarang dari model Request
                $record->allocateBarang($data['ID_BARANG']);

                // Buat transaksi donasi jika belum ada
                if ($record->transaksiDonasis()->count() === 0) {
                    TransaksiDonasi::create([
                        'ID_ORGANISASI' => $record->ID_ORGANISASI,
                        'ID_REQUEST' => $record->ID_REQUEST,
                        'TGL_DONASI' => now(),
                        'PENERIMA' => null
                    ]);
                }
            }
        }

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Perubahan Data')
            ->modalDescription('Apakah Anda yakin ingin menyimpan perubahan request ini?')
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
