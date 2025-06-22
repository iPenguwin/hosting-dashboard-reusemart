<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Per Kategori Barang</title>
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
            font-size: 14px;
        }

        .header .subtitle {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .report-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            text-decoration: underline;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">ReUse Mart</div>
        <div class="subtitle">Jl. Green Eco Park No. 456 Yogyakarta</div>
    </div>
    <div class="report-title">LAPORAN PENJUALAN PER KATEGORI BARANG</div>
    <div class="subtitle">Tahun: {{ $year }}</div>
    <div class="subtitle">Tanggal cetak: {{ $printDate }}</div>
    <br>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Item<br>Terjual</th>
                <th>Jumlah Item<br>Gagal Terjual</th>
                <th>Jumlah Item<br>dengan Hunter</th>
                <th>Nama Hunter</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->NAMA_KATEGORI }}</td>
                <td>{{ $category->terjual }}</td>
                <td>{{ $category->gagal_terjual }}</td>
                <td>{{ $category->hunter }}</td>
                <td>
                    @if(count($category->hunter_names) > 0)
                    {{ implode(', ', $category->hunter_names) }}
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total</td>
                <td>{{ $totalTerjual }}</td>
                <td>{{ $totalGagal }}</td>
                <td>{{ $totalHunter }}</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>
</body>

</html>