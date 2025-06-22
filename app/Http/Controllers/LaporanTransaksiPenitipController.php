<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penitip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanTransaksiPenitipController extends Controller
{
    public function unduhPdf(Request $request)
    {
        $penitipId = $request->query('penitip');
        $bulan = (int) $request->query('bulan', now()->month);
        $tahun = (int) $request->query('tahun', now()->year);

        $penitip = Penitip::findOrFail($penitipId);
        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        $barang = Barang::where('ID_PENITIP', $penitipId)
        ->whereMonth('TGL_KELUAR', $bulan)
        ->whereYear('TGL_KELUAR', $tahun)
        ->whereNotNull('TGL_KELUAR')
        ->whereIn('STATUS_BARANG', ['Terkirim', 'Terjual']) // ⬅️ Tambahkan ini
        ->get()
        ->map(function ($b) {
            $harga_jual_bersih = $b->HARGA_BARANG;
            $bonus_terjual_cepat = 0;
            $pendapatan = $harga_jual_bersih + $bonus_terjual_cepat;

            return (object) [
                'kode_barang' => $b->KODE_BARANG,
                'nama_barang' => $b->NAMA_BARANG,
                'tgl_masuk' => Carbon::parse($b->TGL_MASUK)->format('d/m/Y'),
                'tgl_keluar' => Carbon::parse($b->TGL_KELUAR)->format('d/m/Y'),
                'harga_jual_bersih' => $harga_jual_bersih,
                'bonus_terjual_cepat' => $bonus_terjual_cepat,
                'pendapatan' => $pendapatan,
            ];
        });

        $pdf = Pdf::loadView('laporan-transaksi-penitip-pdf', [
            'penitip' => $penitip,
            'namaBulan' => $namaBulan,
            'tahun' => $tahun,
            'tanggalCetak' => now()->translatedFormat('j F Y'),
            'barang' => $barang,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-transaksi-penitip.pdf');
    }
}
