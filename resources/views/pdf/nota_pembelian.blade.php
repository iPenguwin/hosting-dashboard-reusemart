<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nota Transaksi - {{ $no_nota }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
        }
        .info-transaksi {
            margin-bottom: 15px;
        }
        .info-pembeli {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            padding: 5px;
            text-align: left;
        }
        table.items {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        table.items th {
            border-bottom: 1px solid #000;
        }
        table.totals {
            margin-top: 10px;
        }
        table.totals td {
            padding: 3px 5px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signature {
            margin-top: 30px;
        }
        .signature p {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            margin: 40px auto 0;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ReUse Mart</h1>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <div class="info-transaksi">
        <p><strong>No Nota:</strong> {{ $no_nota }}</p>
        <p><strong>Tanggal pesan:</strong> {{ $tanggal_pesan }}</p>
        <p><strong>Lunas pada:</strong> {{ $tanggal_lunas }}</p>
        <p><strong>Tanggal ambil:</strong> {{ $tanggal_ambil }}</p>
    </div>

    <div class="info-pembeli">
        <p><strong>Pembeli:</strong> {{ $pembeli['email'] }} / {{ $pembeli['nama'] }}</p>
        <p>{{ $pembeli['alamat'] }}</p>
        <p><strong>Delivery:</strong> {{ $delivery_method }}</p>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['nama'] }}</td>
                <td class="text-right">{{ $item['harga'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <!-- <tr>
            <td><strong>Total</strong></td>
            <td class="text-right">{{ $tot_harga_pembelian }}</td>
        </tr> -->
        <tr>
            <td><strong>Ongkos Kirim</strong></td>
            <td class="text-right">{{ $ongkos_kirim }}</td>
        </tr>
        <tr>
            <td><strong>Potongan</strong></td>
            <td class="text-right">{{ $potongan_poin }}</td>
        </tr>
        <tr>
            <td><strong>Total Bayar</strong></td>
            <td class="text-right">{{ $total_bayar }}</td>
        </tr>
    </table>

    <div class="poin-info">
        <p><strong>Poin dari pesanan ini:</strong> {{ $poin_didapat }}</p>
        <p><strong>Total poin customer:</strong> {{ $total_poin }}</p>
    </div>

    <div class="qc-info">
        <p><strong>QC oleh:</strong> {{ $qc_oleh }}</p>
    </div>

    <div class="signature">
        <p>Diambil oleh:</p>
        <p>(..............)</p>
        <p>Tanggal: ..............</p>
    </div>
</body>
</html>