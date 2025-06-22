<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Barang dengan Masa Titipan Habis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            margin-bottom: 10px;
            text-align: center;
        }

        .header .title {
            font-weight: bold;
            font-size: 16px;
        }

        .header .subtitle {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .report-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">ReUse Mart</div>
        <div class="subtitle">Jl. Green Eco Park No. 456 Yogyakarta</div>
    </div>

    <div class="report-title">LAPORAN BARANG DENGAN MASA TITIPAN HABIS</div>
    <div style="text-align: center; font-size: 11px;">Tanggal cetak: {{ $printDate }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>ID Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Akhir</th>
                <th>Batas Ambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $index => $barang)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>{{ $barang->KODE_BARANG }}</td>
                <td>{{ $barang->NAMA_BARANG }}</td>
                <td>T{{ $barang->ID_PENITIP }}</td>
                <td>{{ $barang->penitip->NAMA_PENITIP ?? '-' }}</td>
                <td>{{ $barang->TGL_MASUK->format('d/m/Y') }}</td>
                <td>{{ $barang->TGL_KELUAR->format('d/m/Y') }}</td>
                <td>{{ $barang->TGL_KELUAR->addDays(2)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center">Tidak ada data barang dengan masa titipan habis</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>