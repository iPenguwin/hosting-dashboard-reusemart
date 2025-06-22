<?php
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKomisiBulananController extends Controller
{
    /**
     * Generate PDF “Laporan Komisi Bulanan per Produk”
     * Contoh URL: /filament/laporan/komisi-bulanan-per-produk/pdf?bulan=6&tahun=2025
     */
    public function unduhPdf(Request $request)
    {
        // 1) Baca parameter bulan & tahun (default ke sekarang)
        $bulan = (int) $request->query('bulan', Carbon::now()->month);
        $tahun = (int) $request->query('tahun', Carbon::now()->year);

        // 2) Nama‐nama bulan
        $namaBulanList = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $namaBulan = $namaBulanList[$bulan] ?? 'Unknown';

        // 3) Ambil transaksi “Lunas” di bulan/tahun terpilih,
        //    plus informasi apakah barang ini memiliki Hunter.
        //
        //    Kita join ke `barangs` (b), lalu ke `pegawais` (h) dan `jabatans` (j).
        //    Jika j.NAMA_JABATAN = 'Hunter', maka ada hunter; 
        //    jika tidak, maka ID_HUNTER = NULL.
        $transaksis = DB::table('transaksi_pembelian_barangs as t')
            ->select([
                'b.KODE_BARANG',
                'b.NAMA_BARANG',
                't.TOT_HARGA_PEMBELIAN as harga_jual',
                't.TGL_PESAN_PEMBELIAN as tanggal_masuk',
                // Kita pakai TGL_PESAN_PEMBELIAN sebagai simulasi tanggal laku,
                // karena TGL_LUNAS_PEMBELIAN sering NULL
                't.TGL_PESAN_PEMBELIAN as tanggal_laku_simulasi',
                // Ambil ID_PEGAWAI dari barangs, hanya berarti ini hunter jika jabatan = 'Hunter'
                'b.ID_PEGAWAI as id_hunter',
                'h.NAMA_PEGAWAI as nama_hunter',
                'j.NAMA_JABATAN as nama_jabatan',
                't.STATUS_PEMBAYARAN',
            ])
            ->join('barangs as b', 'b.ID_BARANG', '=', 't.ID_BARANG')
            ->leftJoin('pegawais as h', 'h.ID_PEGAWAI', '=', 'b.ID_PEGAWAI')
            ->leftJoin('jabatans as j', 'j.ID_JABATAN', '=', 'h.ID_JABATAN')
            ->where('t.STATUS_PEMBAYARAN', 'Lunas')
            ->whereRaw('MONTH(t.TGL_PESAN_PEMBELIAN) = ?', [$bulan])
            ->whereRaw('YEAR(t.TGL_PESAN_PEMBELIAN) = ?', [$tahun])
            ->orderBy('b.KODE_BARANG')
            ->get();

        // 4) Proses setiap transaksi untuk menghitung komisi,
        //    tetapi hanya beri komisi hunter jika benar‐benar ada j.NAMA_JABATAN = 'Hunter'
        $rows = collect();
        foreach ($transaksis as $t) {
            $hargaJual        = (float) $t->harga_jual;
            $tglMasuk         = Carbon::parse($t->tanggal_masuk);
            $tglLaku          = Carbon::parse($t->tanggal_laku_simulasi);
            $hariTerjual      = $tglMasuk->diffInDays($tglLaku);

            // Periksa apakah ini barang yang punya hunter valid:
            $adaHunter = (
                $t->id_hunter !== null
                && strtolower($t->nama_jabatan) === 'hunter'
            );

            // Inisialisasi semua nilai komisi
            $komisiHunter          = 0.0;
            $komisiBaseReUseMart   = 0.0;
            $bonusPenitip          = 0.0;
            $komisiReUseMartBersih = 0.0;

            if ($hariTerjual <= 7) {
                // Jual ≤ 7 hari → ada komisi base 20% untuk ReUseMart
                $komisiBaseReUseMart = 0.20 * $hargaJual; // 20%
                $bonusPenitip        = 0.10 * $komisiBaseReUseMart; // 10% dari 20%

                if ($adaHunter) {
                    // Jika memang ada hunter, beri 5% untuk hunter
                    $komisiHunter = 0.05 * $hargaJual; // 5%
                    // Sisa untuk ReUseMart bersih = base – hunter – bonus
                    $komisiReUseMartBersih =
                        $komisiBaseReUseMart
                        - $komisiHunter
                        - $bonusPenitip;
                } else {
                    // Jika tidak ada hunter, komisiHunter = 0,
                    // dan ReUseMart mengambil seluruh base minus bonus.
                    $komisiHunter        = 0.0;
                    $komisiReUseMartBersih =
                        $komisiBaseReUseMart
                        - $bonusPenitip;
                }
            }
            elseif ($hariTerjual > 30) {
                // Jual > 30 hari → perpanjangan penitipan
                // Jika ada hunter, tetap komisiHunter = 0
                $komisiHunter = 0.0;
                $bonusPenitip = 0.0;
                // ReUseMart bersih = 27,5% dari harga_jual (full)
                $komisiReUseMartBersih = 0.275 * $hargaJual;
            }
            else {
                // 8–30 hari: semua komisi = 0
                $komisiHunter          = 0.0;
                $komisiBaseReUseMart   = 0.0;
                $bonusPenitip          = 0.0;
                $komisiReUseMartBersih = 0.0;
            }

            $rows->push([
                'kode_produk'       => $t->KODE_BARANG,
                'nama_produk'       => $t->NAMA_BARANG,
                'harga_jual'        => $hargaJual,
                'tanggal_masuk'     => $tglMasuk->format('j/n/Y'),
                'tanggal_laku'      => $tglLaku->format('j/n/Y'),
                'komisi_hunter'     => $komisiHunter,
                'komisi_reusemart'  => $komisiReUseMartBersih,
                'bonus_penitip'     => $bonusPenitip,
            ]);
        }

        // 5) Hitung Grand Total
        $grandHunter        = $rows->sum('komisi_hunter');
        $grandReUseMart     = $rows->sum('komisi_reusemart');
        $grandBonusPenitip  = $rows->sum('bonus_penitip');

        // 6) Siapkan data untuk view PDF
        $data = [
            'namaBulan'         => $namaBulan,
            'tahun'             => $tahun,
            'tanggalCetak'      => Carbon::now()->locale('id')->translatedFormat('j F Y'),
            'dataRows'          => $rows,
            'grandHunter'       => $grandHunter,
            'grandReUseMart'    => $grandReUseMart,
            'grandBonusPenitip' => $grandBonusPenitip,
        ];

        // 7) Render view PDF dan download
        $pdf = Pdf::loadView(
            'laporan-komisi-bulanan-per-produk-pdf',
            $data
        )->setPaper('a4', 'portrait');

        $fileName = "laporan-komisi-{$bulan}_{$tahun}.pdf";
        return $pdf->download($fileName);
    }
}
