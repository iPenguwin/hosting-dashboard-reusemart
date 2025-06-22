<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembelianBarang;
use Barryvdh\DomPDF\Facade\Pdf;    // Pastikan sudah install barryvdh/laravel-dompdf
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanPenjualanBulananController extends Controller
{
    /**
     * Unduh PDF laporan penjualan bulanan (tabel + grafik yang di‐embed sebagai base64).
     * 
     * URL: /filament/laporan/penjualan-bulanan/pdf?tahun=2024
     */
    public function unduhPdf(Request $request)
    {
        // 1) Autentikasi & autorisasi: hanya Pegawai (admin/owner)
        $user = $request->user();
        if (
            ! $user instanceof \App\Models\Pegawai ||
            ! in_array(strtolower($user->jabatan), ['admin','owner'])
        ) {
            abort(403);
        }

        // 2) Ambil tahun dari query parameter (default: tahun ini)
        $tahun = (int) $request->query('tahun', Carbon::now()->year);

        // 3) Query: Group per bulan, hitung jumlah transaksi & total penjualan
        $hasil = TransaksiPembelianBarang::query()
            ->selectRaw('
                MONTH(created_at) AS bulan_ke,
                COUNT(*) AS jumlah_barang,
                SUM(tot_harga_pembelian) AS jumlah_penjualan
            ')
            ->whereYear('created_at', $tahun)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        // 4) Label bulan (3 huruf, Indo)
        $labelBulan = [
            'Jan','Feb','Mar','Apr','Mei','Jun',
            'Jul','Agu','Sep','Okt','Nov','Des',
        ];

        // 5) Inisialisasi array 12 bulan dengan 0
        $dataBulanan = [];
        for ($i = 0; $i < 12; $i++) {
            $dataBulanan[$i] = [
                'bulan'            => $labelBulan[$i],
                'jumlah_barang'    => 0,
                'jumlah_penjualan' => 0,
            ];
        }

        // 6) Isi data dari hasil query
        foreach ($hasil as $row) {
            $idx = intval($row->bulan_ke) - 1; // 0‐based index
            $dataBulanan[$idx] = [
                'bulan'            => $labelBulan[$idx],
                'jumlah_barang'    => (int) $row->jumlah_barang,
                'jumlah_penjualan' => (float) $row->jumlah_penjualan,
            ];
        }

        // 7) Hitung total tahunan
        $totalBarang    = array_sum(array_column($dataBulanan, 'jumlah_barang'));
        $totalPenjualan = array_sum(array_column($dataBulanan, 'jumlah_penjualan'));

        // 8) Konfigurasi Chart.js untuk grafik batang
        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $labelBulan,
                'datasets' => [[
                    'label'           => 'Jumlah Penjualan (Rp)',
                    'data'            => array_column($dataBulanan, 'jumlah_penjualan'),
                    'backgroundColor' => 'rgba(79,70,229,0.7)',
                    'borderColor'     => 'rgba(79,70,229,1)',
                    'borderWidth'     => 1,
                ]],
            ],
            'options' => [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'callback' => 
                                "function(value) {
                                    return value.toLocaleString('id-ID');
                                }"
                        ],
                    ],
                ],
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text'    => "Penjualan Kotor Per Bulan - $tahun",
                        'font' => ['size' => 14],
                    ],
                ],
            ],
        ];

        // 9) Encode konfigurasi js menjadi URL QuickChart
        $encodedConfig = rawurlencode(json_encode($chartConfig));
        $quickChartUrl = "https://quickchart.io/chart?c={$encodedConfig}&width=800&height=400";

        // 10) Fetch PNG dari QuickChart, lalu encode ke Base64
        //     Gunakan file_get_contents (pastikan allow_url_fopen = On), 
        //     atau gunakan Guzzle jika beban produksi.
        try {
            $imageBinary = @file_get_contents($quickChartUrl);
            if ($imageBinary === false) {
                // Jika gagal fetch, Anda bisa fallback ke URL saja (tapi DomPDF mungkin gagal)
                $chartDataUri = null;
            } else {
                $base64 = base64_encode($imageBinary);
                $chartDataUri = 'data:image/png;base64,' . $base64;
            }
        } catch (\Exception $e) {
            $chartDataUri = null;
        }

        // 11) Render view PDF, kirim data
        $pdf = Pdf::loadView('laporan-penjualan-bulanan-pdf', [
            'tahun'           => $tahun,
            'dataBulanan'     => $dataBulanan,
            'totalBarang'     => $totalBarang,
            'totalPenjualan'  => $totalPenjualan,
            'chartDataUri'    => $chartDataUri,
        ])
        ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-penjualan-bulanan-{$tahun}.pdf");
    }
}
