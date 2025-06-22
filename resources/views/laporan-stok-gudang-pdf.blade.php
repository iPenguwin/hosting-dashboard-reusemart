<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Laporan Stok Gudang - {{ $tanggalCetak }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 15px;
        }
        .header {
            margin-bottom: 8px;
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
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        .info {
            font-size: 11px;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th,
        table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }
        table td {
            font-size: 11px;
            text-align: right;
        }
        table td.left {
            text-align: left;
        }
    </style>
</head>
<body>
    {{-- HEADER: ReUse Mart & Alamat --}}
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    {{-- JUDUL LAPORAN --}}
    <div class="judul">
        LAPORAN Stok Gudang
    </div>

    {{-- Info Tanggal Cetak --}}
    <div class="info">
        Tanggal cetak: {{ $tanggalCetak }}
    </div>

    {{-- TABEL STOK GUDANG --}}
    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>ID Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Perpanjangan</th>
                <th>ID Hunter</th>
                <th>Nama Hunter</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stok as $item)
                <tr>
                    <td class="left">{{ $item->KODE_BARANG }}</td>
                    <td class="left">{{ $item->NAMA_BARANG }}</td>
                    <td class="left">{{ $item->ID_PENITIP }}</td>
                    <td class="left">{{ $item->NAMA_PENITIP }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->TGL_MASUK)->format('j/n/Y') }}</td>
                    <td class="left">{{ $item->PERPANJANGAN }}</td>
                    <td class="left">{{ $item->ID_HUNTER }}</td>
                    <td class="left">{{ $item->NAMA_HUNTER }}</td>
                    <td>{{ number_format($item->HARGA, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @if ($stok->isEmpty())
                <tr>
                    <td colspan="9" style="text-align: center; font-style: italic;">
                        Tidak ada stok di gudang hari ini.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
