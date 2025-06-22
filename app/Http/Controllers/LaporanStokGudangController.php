<?php
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanStokGudangController extends Controller
{
    public function unduhPdf(Request $request)
    {
        // (Opsional) Jika Anda hanya ingin Pegawai ‘owner’ yang mengunduh,
        // letakkan pemeriksaan ini di Filament Page, bukan di sini.
        // Karena di sini kita asumsikan user sudah login sebagai AdminUser Filament.

        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('j F Y');

        // Sertakan kedua status: 'tersedia' & 'diperpanjang'
        $stok = DB::table('barangs as b')
            ->leftJoin('penitips as p', 'b.ID_PENITIP', '=', 'p.ID_PENITIP')
            ->leftJoin('pegawais as h', 'b.ID_PEGAWAI', '=', 'h.ID_PEGAWAI')
            ->leftJoin('jabatans as j', 'h.ID_JABATAN', '=', 'j.ID_JABATAN')
            ->select([
                'b.KODE_BARANG',
                'b.NAMA_BARANG',
                'b.ID_PENITIP',
                'p.NAMA_PENITIP',
                'b.TGL_MASUK',
                DB::raw("
                    CASE
                        WHEN LOWER(b.STATUS_BARANG) = 'diperpanjang'
                            THEN 'Ya'
                        ELSE 'Tidak'
                    END AS PERPANJANGAN
                "),
                DB::raw("
                    CASE
                        WHEN LOWER(j.NAMA_JABATAN) = 'hunter'
                            THEN b.ID_PEGAWAI
                        ELSE NULL
                    END AS ID_HUNTER
                "),
                DB::raw("
                    CASE
                        WHEN LOWER(j.NAMA_JABATAN) = 'hunter'
                            THEN h.NAMA_PEGAWAI
                        ELSE NULL
                    END AS NAMA_HUNTER
                "),
                'b.HARGA_BARANG as HARGA',
            ])
            // Tampilkan barang yang statusnya 'tersedia' atau 'diperpanjang'
            ->whereIn('b.STATUS_BARANG', ['tersedia', 'diperpanjang'])
            ->orderBy('b.KODE_BARANG', 'asc')
            ->get();

        $data = [
            'stok'         => $stok,
            'tanggalCetak' => $tanggalCetak,
        ];

        $pdf = Pdf::loadView('laporan-stok-gudang-pdf', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'laporan-stok-gudang_' . now()->format('Ymd') . '.pdf';
        return $pdf->download($fileName);
    }
}
