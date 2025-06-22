<?php

namespace App\Http\Controllers;

use App\Models\Request as DonasiRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanRequestDonasiController extends Controller
{
    public function export()
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

        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('j F Y');

        $pdf = Pdf::loadView('laporan-request-donasi-pdf', [
            'tanggalCetak' => $tanggalCetak,
            'dataRequests' => $data,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-request-donasi.pdf');
    }
}
