<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Models\TransaksiDonasi;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $request = parent::handleRecordCreation($data);

        // Jika request dibuat oleh Owner dan langsung diterima
        if (Auth::user()->JABATAN === 'Owner' && $data['STATUS_REQUEST'] === 'Diterima' && isset($data['ID_BARANG'])) {
            // Update status barang
            $barang = Barang::find($data['ID_BARANG']);
            $barang->update([
                'STATUS_BARANG' => 'Didonasikan',
                'ID_ORGANISASI' => Auth::user()->ID_ORGANISASI
            ]);

            // Buat transaksi donasi
            TransaksiDonasi::create([
                'ID_ORGANISASI' => Auth::user()->ID_ORGANISASI,
                'ID_REQUEST' => $request->ID_REQUEST,
                'ID_BARANG' => $data['ID_BARANG'],
                'TGL_DONASI' => null,
                'PENERIMA' => null,
                'STATUS_DONASI' => 'Selesai'
            ]);
        }

        return $request;
    }

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
