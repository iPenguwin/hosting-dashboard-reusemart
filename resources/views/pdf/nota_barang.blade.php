<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nota Barang Titipan</title>
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

        .barang-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .barang-details th,
        .barang-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .barang-details th {
            background-color: #f2f2f2;
        }

        .ttd {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .ttd-section {
            text-align: center;
            width: 45%;
        }

        .foto-barang {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .foto-barang img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>ReUse Mart</h1>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <div class="info">
        <p><strong>No. Barang</strong>: {{ $barang->KODE_BARANG }}</p>
        <p><strong>Tanggal Masuk Barang</strong>: {{ \Carbon\Carbon::parse($barang->TGL_MASUK)->format('d/m/Y') }}</p>
        <p><strong>Tanggal Berakhir Penitipan</strong>: {{ \Carbon\Carbon::parse($barang->TGL_KELUAR)->format('d/m/Y') }}</p>
        @if($barang->TGL_AMBIL)
        <p><strong>Tanggal Ambil</strong>: {{ \Carbon\Carbon::parse($barang->TGL_AMBIL)->format('d/m/Y') }}</p>
        @endif
    </div>

    <div class="penitip">
        <p><strong>Penitip</strong>: {{ $barang->penitip->KODE_PENITIP }} / {{ $barang->penitip->NAMA_PENITIP }}</p>
        <p><strong>Alamat</strong>: {{ $barang->penitip->ALAMAT_PENITIP }}
        <p><strong>Status</strong>: {{ $barang->STATUS_BARANG }}</p>
    </div>

    <table class="barang-details">
        <tr>
            <th>Nama Barang</th>
            <td colspan="3">{{ $barang->NAMA_BARANG }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $barang->kategoribarang->NAMA_KATEGORI ?? '-' }}</td>
            <th>Rating</th>
            <td>{{ $barang->RATING }} / 5</td>
        </tr>
        <tr>
            <th>Harga</th>
            <td>Rp {{ number_format($barang->HARGA_BARANG, 0, ',', '.') }}</td>
            <th>Berat</th>
            <td>{{ $barang->BERAT ?? '-' }} kg</td>
        </tr>
        <tr>
            <th>Garansi</th>
            <td>{{ $barang->GARANSI ? \Carbon\Carbon::parse($barang->GARANSI)->format('d/m/Y') : '-' }}</td>
            <th>Petugas QC</th>
            <td>{{ $barang->nama_pegawai_qc }}</td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td colspan="3">{{ $barang->DESKRIPSI }}</td>
        </tr>
    </table>

    @if($barang->FOTO_BARANG)
    <div class="foto-barang">
        @foreach($barang->getFotoBarangUrlsAttribute() as $foto)
        @if($foto)
        <img src="{{ $foto }}" alt="Foto Barang">
        @endif
        @endforeach
    </div>
    @endif

    <div class="ttd">
        <div class="ttd-section">
            <p>Penitip,</p>
            <br><br><br>
            <p>({{ $barang->penitip->NAMA_PENITIP }})</p>
        </div>
        <div class="ttd-section">
            <p>Pegawai ReUse Mart,</p>
            <br><br><br>
            <p>({{ $barang->nama_pegawai_qc }})</p>
        </div>
    </div>
</body>

</html>