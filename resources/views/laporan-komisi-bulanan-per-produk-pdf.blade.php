<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Laporan Komisi Bulanan per Produk - {{ $namaBulan }} {{ $tahun }}</title>
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
        table td.produk {
            text-align: left;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    {{-- HEADER: Nama Toko & Alamat --}}
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    {{-- JUDUL LAPORAN --}}
    <div class="judul">
        Laporan Komisi Bulanan per Produk
    </div>

    {{-- Info Bulan, Tahun, Tanggal Cetak --}}
    <div class="info">
        Bulan: {{ $namaBulan }} {{ $tahun }} | Dicetak: {{ $tanggalCetak }}
    </div>

    {{-- TABEL KOMISI --}}
    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 10%;">Kode Produk</th>
                <th style="width: 25%;">Nama Produk</th>
                <th style="width: 12%;">Harga Jual</th>
                <th style="width: 10%;">Tgl Masuk</th>
                <th style="width: 10%;">Tgl Laku</th>
                <th style="width: 10%;">Komisi Hunter</th>
                <th style="width: 10%;">Komisi ReUseMart</th>
                <th style="width: 10%;">Bonus Penitip</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            @forelse ($dataRows as $row)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $row['kode_produk'] }}</td>
                    <td class="produk">{{ $row['nama_produk'] }}</td>
                    <td>{{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                    <td>{{ $row['tanggal_masuk'] }}</td>
                    <td>{{ $row['tanggal_laku'] }}</td>
                    <td>{{ number_format($row['komisi_hunter'], 0, ',', '.') }}</td>
                    <td>{{ number_format($row['komisi_reusemart'], 0, ',', '.') }}</td>
                    <td>{{ number_format($row['bonus_penitip'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; font-style: italic;">
                        Tidak ada transaksi yang terjual di bulan ini.
                    </td>
                </tr>
            @endforelse

            @if ($dataRows->count() > 0)
                <tr class="total-row">
                    {{-- Gabungkan kolom No, Kode Produk, Nama Produk --}}
                    <td colspan="3" style="text-align: center;">Total</td>
                    {{-- Total Harga Jual --}}
                    <td>
                        {{ number_format($dataRows->sum('harga_jual'), 0, ',', '.') }}
                    </td>
                    {{-- Kosongkan kolom Tgl Masuk dan Tgl Laku --}}
                    <td></td>
                    <td></td>
                    {{-- Total Komisi Hunter --}}
                    <td>
                        {{ number_format($grandHunter, 0, ',', '.') }}
                    </td>
                    {{-- Total Komisi ReUseMart --}}
                    <td>
                        {{ number_format($grandReUseMart, 0, ',', '.') }}
                    </td>
                    {{-- Total Bonus Penitip --}}
                    <td>
                        {{ number_format($grandBonusPenitip, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
