<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Bulanan - {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2,
        .header h3 {
            margin: 0; padding: 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table th,
        table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        table td {
            text-align: right;
            font-size: 11px;
        }
        table td.bulan {
            text-align: left;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .chart-container {
            text-align: center;
            margin-top: 10px;
        }
        .chart-container img {
            max-width: 100%;
            height: auto;
        }
        .chart-fallback {
            color: #555;
            font-size: 11px;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <h3><u>LAPORAN PENJUALAN BULANAN</u></h3>
        <p>Tahun : {{ $tahun }}</p>
    </div>

    {{-- Informasi tanggal cetak --}}
    <div class="info">
        Dicetak: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}
    </div>

    {{-- Tabel 12 Bulan --}}
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Jumlah Barang Terjual</th>
                <th>Jumlah Penjualan Kotor (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataBulanan as $d)
                <tr>
                    <td class="bulan">
                        @switch($d['bulan'])
                            @case('Jan')   Januari   @break
                            @case('Feb')   Februari  @break
                            @case('Mar')   Maret     @break
                            @case('Apr')   April     @break
                            @case('Mei')   Mei       @break
                            @case('Jun')   Juni      @break
                            @case('Jul')   Juli      @break
                            @case('Agu')   Agustus   @break
                            @case('Sep')   September @break
                            @case('Okt')   Oktober   @break
                            @case('Nov')   November  @break
                            @case('Des')   Desember  @break
                        @endswitch
                    </td>
                    <td>{{ number_format($d['jumlah_barang'], 0, ',', '.') }}</td>
                    <td>{{ number_format($d['jumlah_penjualan'], 0, ',', '.') }}</td>
                </tr>
            @endforeach

            {{-- Baris Total --}}
            <tr class="total-row">
                <td class="bulan">Total</td>
                <td>{{ number_format($totalBarang, 0, ',', '.') }}</td>
                <td>{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Grafik Batang --}}
    <div class="chart-container">
        @if ($chartDataUri)
            {{-- Jika data URI tersedia, tampilkan gambarnya --}}
            <img src="{{ $chartDataUri }}" alt="Grafik Penjualan Bulanan">
        @else
            {{-- Jika gagal fetch, tampilkan teks fallback --}}
            <div class="chart-fallback">
                <p>— Gagal memuat grafik batang. —</p>
            </div>
        @endif
    </div>
</body>
</html>
