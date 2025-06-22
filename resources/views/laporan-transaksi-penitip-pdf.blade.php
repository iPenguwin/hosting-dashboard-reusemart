<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <title>Laporan Transaksi Penitip - {{ $namaBulan }} {{ $tahun }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 15px;
            }
            .header {
                margin-bottom: 10px;
            }
            .header h2 {
                margin: 0;
                font-size: 16px;
            }
            .header p {
                margin: 2px 0;
                font-size: 12px;
            }
            .judul {
                text-align: center;
                font-size: 14px;
                font-weight: bold;
                margin-top: 8px;
                margin-bottom: 4px;
            }
            .info {
                text-align: center;
                font-size: 11px;
                margin-bottom: 10px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            table th,
            table td {
                border: 1px solid #333;
                padding: 6px 8px;
                text-align: center;
            }
            table th {
                background-color: #f0f0f0;
                font-weight: bold;
                font-size: 12px;
            }
            .total-row {
                background-color: #f9f9f9;
                font-weight: bold;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>ReUse Mart</h2>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>

        <div class="judul">LAPORAN TRANSAKSI PENITIP</div>

        <div class="info">
            ID Penitip: {{ $penitip->ID_PENITIP }}
            <br />
            Nama Penitip: {{ $penitip->NAMA_PENITIP }}
            <br />
            Bulan: {{ $namaBulan }} {{ $tahun }}
            <br />
            Tanggal Cetak: {{ $tanggalCetak }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Laku</th>
                    <th>Harga Jual Bersih</th>
                    <th>Bonus Terjual Cepat</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barang as $b)
                    <tr>
                        <td>{{ $b->kode_barang }}</td>
                        <td>{{ $b->nama_barang }}</td>
                        <td>{{ $b->tgl_masuk }}</td>
                        <td>{{ $b->tgl_keluar }}</td>
                        <td>{{ number_format($b->harga_jual_bersih, 0, ',', '.') }}</td>
                        <td>{{ number_format($b->bonus_terjual_cepat, 0, ',', '.') }}</td>
                        <td>{{ number_format($b->pendapatan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center">
                            Tidak ada data transaksi penitip.
                        </td>
                    </tr>
                @endforelse
                @if ($barang->count())
                    <tr class="total-row">
                        <td colspan="4">TOTAL</td>
                        <td>
                            {{ number_format($barang->sum('harga_jual_bersih'), 0, ',', '.') }}
                        </td>
                        <td>
                            {{ number_format($barang->sum('bonus_terjual_cepat'), 0, ',', '.') }}
                        </td>
                        <td>{{ number_format($barang->sum('pendapatan'), 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </body>
</html>
