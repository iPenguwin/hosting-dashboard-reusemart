<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Laporan Donasi Barang - {{ $tahun }}</title>
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
        table th, table td {
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
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <div class="judul">
        LAPORAN DONASI BARANG
    </div>

    <div class="info">
        Tahun: {{ $tahun }} | Tanggal cetak: {{ $tanggalCetak }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>ID Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Donasi</th>
                <th>Organisasi</th>
                <th>Nama Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataDonasi as $d)
                <tr>
                    <td>{{ $d['kode_barang'] }}</td>
                    <td>{{ $d['nama_barang'] }}</td>
                    <td>{{ $d['id_penitip'] }}</td>
                    <td>{{ $d['nama_penitip'] }}</td>
                    <td>{{ $d['tgl_donasi'] }}</td>
                    <td>{{ $d['organisasi'] }}</td>
                    <td>{{ $d['penerima'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; font-style: italic;">
                        Tidak ada data donasi barang untuk tahun ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
