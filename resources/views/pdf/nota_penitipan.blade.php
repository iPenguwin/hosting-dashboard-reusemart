<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nota Penitipan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 5px 0;
        }

        .barang {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .barang th,
        .barang td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .barang th {
            background-color: #f2f2f2;
        }

        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }

        .ttd {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>ReUse Mart</h1>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <div class="info">
        <p><strong>No Nota</strong>: {{ $transaksi->NO_NOTA_TRANSAKSI_TITIPAN }}</p>
        <p><strong>Tanggal penitipan</strong>: {{ \Carbon\Carbon::parse($transaksi->TGL_MASUK_TITIPAN)->format('d/m/Y H:i:s') }}</p>
        <p><strong>Masa penitipan sampai</strong>: {{ \Carbon\Carbon::parse($transaksi->TGL_KELUAR_TITIPAN)->format('d/m/Y') }}</p>
    </div>

    <div class="penitip">
        <p><strong>Penitip</strong>: {{ $transaksi->penitip->KODE_PENITIP }} / {{ $transaksi->penitip->NAMA_PENITIP }}</p>
        <p>{{ $transaksi->penitip->ALAMAT_PENITIP }}</p>
        @php
        $hunter = $transaksi->detailTransaksiPenitipans->first()->barang->pegawai ?? null;
        @endphp
        <p>Delivery: {{ $hunter ? 'Kurir ReUseMart ('.$hunter->NAMA_PEGAWAI.')' : 'Belum ditentukan' }}</p>
    </div>

    <table class="barang">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Detail</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <!-- Change this part in the table body -->
            @foreach($transaksi->detailTransaksiPenitipans as $detail)
            <tr>
                <td>{{ $detail->barang->NAMA_BARANG }}</td>
                <td>
                    @if($detail->barang->GARANSI)
                    Garansi sampai {{ \Carbon\Carbon::parse($detail->barang->GARANSI)->format('M Y') }}<br>
                    @endif
                    @if($detail->barang->BERAT)
                    Berat barang: {{ $detail->barang->BERAT }} kg
                    @endif
                </td>
                <td>Rp {{ number_format($detail->barang->HARGA_BARANG, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <!-- And change the total calculation to use HARGA_BARANG -->
            <div class="total">
                <p>Total: Rp {{ number_format($transaksi->detailTransaksiPenitipans->sum(function($detail) { return $detail->barang->HARGA_BARANG; }), 0, ',', '.') }}</p>
            </div>
        </tbody>
    </table>
    <div class="qc-section">
        <p><strong>QC/Pegawai Gudang:</strong></p>
        @if($transaksi->pegawaiTransaksiPenitipans->isNotEmpty())
        @foreach($transaksi->pegawaiTransaksiPenitipans as $pegawaiTransaksi)
        <p>{{ $pegawaiTransaksi->pegawai->NAMA_PEGAWAI }} ({{ $pegawaiTransaksi->pegawai->KODE_PEGAWAI }})</p>
        @endforeach
        @else
        <p>Belum ditentukan</p>
        @endif
    </div>
</body>

</html>