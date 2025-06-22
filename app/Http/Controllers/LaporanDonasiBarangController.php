<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDonasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanDonasiBarangController extends Controller
{
    public function export(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);

        $dataDonasi = TransaksiDonasi::with(['organisasi', 'request.barang.penitip'])
            ->whereYear('TGL_DONASI', $tahun)
            ->get()
            ->map(function ($donasi) {
                $barang = $donasi->request?->barang;
                $penitip = $barang?->penitip;

                return [
                    'kode_barang' => $barang?->KODE_BARANG ?? '-',
                    'nama_barang' => $barang?->NAMA_BARANG ?? '-',
                    'id_penitip' => $penitip?->ID_PENITIP ?? '-',
                    'nama_penitip' => $penitip?->NAMA_PENITIP ?? '-',
                    'tgl_donasi' => $donasi->TGL_DONASI ? Carbon::parse($donasi->TGL_DONASI)->format('d/m/Y') : '-',
                    'organisasi' => $donasi->organisasi?->NAMA_ORGANISASI ?? '-',
                    'penerima' => $donasi->PENERIMA ?? '-',
                ];
            })
            ->toArray();

        $pdf = Pdf::loadView('laporan-donasi-barang-pdf', [
            'tahun' => $tahun,
            'tanggalCetak' => Carbon::now()->translatedFormat('j F Y'),
            'dataDonasi' => $dataDonasi,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("laporan-donasi-barang-{$tahun}.pdf");
    }
}
