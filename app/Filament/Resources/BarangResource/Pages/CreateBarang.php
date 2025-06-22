<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use App\Models\Kategoribarang;
use App\Models\TransaksiPenitipanBarang;
use App\Models\DetailTransaksiPenitipBarang;
use App\Models\Pegawai;
use App\Models\PegawaiTransaksiPenitipan;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added Log facade import
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $kategori = Kategoribarang::find($data['ID_KATEGORI']);
        if ($kategori) {
            $kategori->increment('JML_BARANG');
        }

        if (empty($data['STATUS_BARANG'])) {
            $data['STATUS_BARANG'] = 'Tersedia';
        }

        if (!isset($data['FOTO_BARANG'])) {
            $data['FOTO_BARANG'] = null;
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Barang
            $barang = static::getModel()::create($data);

            // 2. Create Transaksi jika ada TGL_MASUK
            if ($barang->TGL_MASUK) {
                $transaksi = TransaksiPenitipanBarang::create([
                    'ID_PENITIP' => $barang->ID_PENITIP,
                    'TGL_MASUK_TITIPAN' => $barang->TGL_MASUK,
                    'TGL_KELUAR_TITIPAN' => $barang->TGL_KELUAR,
                ]);

                DetailTransaksiPenitipBarang::create([
                    'ID_TRANSAKSI_PENITIPAN' => $transaksi->ID_TRANSAKSI_PENITIPAN,
                    'ID_BARANG' => $barang->ID_BARANG,
                    'NAMA_BARANG' => $barang->NAMA_BARANG,
                ]);

                // Ambil Pegawai yang sedang login (langsung dari Auth)
                $loggedInPegawai = Auth::user(); // Sudah instance dari Pegawai

                if ($loggedInPegawai && $loggedInPegawai instanceof \App\Models\Pegawai) {
                    PegawaiTransaksiPenitipan::create([
                        'ID_TRANSAKSI_PENITIPAN' => $transaksi->ID_TRANSAKSI_PENITIPAN,
                        'ID_PEGAWAI' => $loggedInPegawai->ID_PEGAWAI,
                    ]);
                } else {
                    // Fallback ke pegawai gudang
                    $qcPegawai = Pegawai::whereHas('jabatans', function ($query) {
                        $query->where('NAMA_JABATAN', 'Pegawai Gudang');
                    })->first();

                    if ($qcPegawai) {
                        PegawaiTransaksiPenitipan::create([
                            'ID_TRANSAKSI_PENITIPAN' => $transaksi->ID_TRANSAKSI_PENITIPAN,
                            'ID_PEGAWAI' => $qcPegawai->ID_PEGAWAI,
                        ]);
                    }
                }
            }

            return $barang;
        });
    }


    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                try {
                    $this->create();
                    $this->closeActionModal();
                } catch (\Exception $e) {
                    throw $e;
                }
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
