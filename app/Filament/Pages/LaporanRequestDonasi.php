<?php

namespace App\Filament\Pages;

use App\Models\Request as DonasiRequest;
use Filament\Pages\Page;

class LaporanRequestDonasi extends Page
{
    protected static ?string $navigationLabel = 'Laporan Request Donasi';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';

    protected static string $view = 'filament.pages.laporan-request-donasi';

    public array $dataRequests = [];

    public function mount(): void
    {
        $this->muatDataRequest();
    }

    public function muatDataRequest(): void
    {
        $requests = DonasiRequest::with('organisasi')
            ->where('STATUS_REQUEST', 'Menunggu')
            ->get();

        $data = [];
        foreach ($requests as $r) {
            $data[] = [
                'id_organisasi' => $r->organisasi->ID_ORGANISASI ?? '-',
                'nama'          => $r->organisasi->NAMA_ORGANISASI ?? '-',
                'alamat'        => $r->organisasi->ALAMAT_ORGANISASI ?? '-',
                'request'       => $r->NAMA_BARANG_REQUEST ?? '-',
                'deskripsi'     => $r->DESKRIPSI_REQUEST ?? '-',
            ];
        }

        $this->dataRequests = $data;
    }
}
