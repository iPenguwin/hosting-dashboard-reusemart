<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - {{ $no_nota }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .nota-title {
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 10px;
            padding-left: 5px;
        }

        .header {
            text-align: left;
            margin-bottom: 15px;
            padding-bottom: 0px;
            padding-left: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0 0;
            font-size: 11px;
        }

        .info-section {
            padding-left: 5px;
            margin-bottom: 10px;
        }

        .info-section table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .info-section table td {
            padding: 1px 0px;
            vertical-align: top;
        }

        .info-section table td:first-child {
            width: 90px;
        }

        .info-section table td:nth-child(2) {
            width: 10px;
        }

        .items-table-container {
            padding-left: 5px;
            padding-right: 5px;
            margin-bottom: 0px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
            border-top: 1px solid #000;
        }

        table.items th,
        table.items td {
            padding: 4px 2px;
            text-align: left;
        }

        table.items th {
            border-bottom: 1px solid #000;
        }

        table.items td:last-child,
        table.items th:last-child {
            text-align: right;
        }

        table.items tr.item-row td {
            border-bottom: none;
        }

        table.items tbody tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .totals-container {
            padding-left: 5px;
            padding-right: 5px;
            margin-top: 5px;
        }

        table.totals {
            width: 100%;
            border-collapse: collapse;
        }

        table.totals td {
            padding: 2px;
        }

        table.totals td:first-child {
            text-align: left;
        }

        table.totals td:last-child {
            text-align: right;
        }

        .footer-info {
            padding-left: 5px;
            padding-right: 5px;
            margin-top: 10px;
        }

        .footer-info p {
            margin: 2px 0;
        }

        .signature {
            margin-top: 20px;
            padding-left: 5px;
        }

        .signature p {
            margin: 2px 0;
        }

        .signature .placeholder {
            margin-top: 20px;
            margin-bottom: 2px;
        }

        .signature .date-placeholder {
            margin-top: 2px;
        }
    </style>
</head>

<body>
    @php
    // Calculations for totals based on image structure (same logic as kurir)
    $subtotal_items_numeric = 0;
    foreach($items as $item_data) {
    $subtotal_items_numeric += (int)preg_replace('/[^0-9]/', '', $item_data['harga']);
    }

    $ongkos_kirim_numeric = (int)preg_replace('/[^0-9]/', '', $ongkos_kirim);
    $total_sebelum_potongan_numeric = $subtotal_items_numeric + $ongkos_kirim_numeric;

    $potongan_poin_value_numeric = 0;
    $potongan_label_display = 'Potongan';
    $potongan_value_display_for_table = '0';

    if (strpos($potongan_poin, ' - ') !== false) {
    $parts = explode(' - ', $potongan_poin);
    $potongan_label_display = 'Potongan ' . $parts[0];
    $potongan_poin_value_numeric = (int)preg_replace('/[^0-9]/', '', $parts[1]);
    $potongan_value_display_for_table = '- ' . $parts[1];
    } elseif ($potongan_poin !== '-' && $potongan_poin !== '0' && !empty(trim($potongan_poin))) {
    if (stripos($potongan_poin, 'poin') !== false) {
    $potongan_label_display = 'Potongan ' . $potongan_poin;
    } else {
    $potongan_poin_value_numeric = (int)preg_replace('/[^0-9]/', '', $potongan_poin);
    $potongan_value_display_for_table = '- ' . number_format($potongan_poin_value_numeric, 0, ',', '.');
    }
    } else {
    $potongan_value_display_for_table = '0';
    }

    $total_akhir_numeric = $total_sebelum_potongan_numeric - $potongan_poin_value_numeric;

    $subtotal_items_display = number_format($subtotal_items_numeric, 0, ',', '.');
    $total_sebelum_potongan_display = number_format($total_sebelum_potongan_numeric, 0, ',', '.');
    $total_akhir_display = number_format($total_akhir_numeric, 0, ',', '.');
    @endphp

    <div class="nota-title">
        Nota Penjualan (diambil oleh pembeli)
    </div>

    <div class="header">
        <h1>ReUse Mart</h1>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td>No Nota</td>
                <td>:</td>
                <td>{{ $no_nota }}</td>
            </tr>
            <tr>
                <td>Tanggal pesan</td>
                <td>:</td>
                <td>{{ $tanggal_pesan }}</td>
            </tr>
            <tr>
                <td>Lunas pada</td>
                <td>:</td>
                <td>{{ $tanggal_lunas }}</td>
            </tr>
            <tr>
                <td>Tanggal ambil</td>
                <td>:</td>
                <td>{{ $tanggal_ambil }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section" style="margin-top: 5px;">
        <table>
            <tr>
                <td style="vertical-align: baseline;">Pembeli</td>
                <td style="vertical-align: baseline;">:</td>
                <td>
                    {{ $pembeli['email'] }} / {{ $pembeli['nama'] }}<br>
                    {{ $pembeli['alamat'] }}
                </td>
            </tr>
            <tr>
                <td>Delivery</td>
                <td>:</td>
                <td>- ({{ $delivery_method }})</td>
            </tr>
        </table>
    </div>

    <div class="items-table-container">
        <table class="items">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr class="item-row">
                    <td>{{ $item['nama'] }}</td>
                    <td>{{ $item['harga'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals-container">
        <table class="totals">
            <tr>
                <td>Total</td>
                <td>{{ $subtotal_items_display }}</td>
            </tr>
            <tr>
                <td>Ongkos Kirim</td>
                <td>{{ $ongkos_kirim == '0' ? '0' : $ongkos_kirim }}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{ $total_sebelum_potongan_display }}</td>
            </tr>
            <tr>
                <td>{{ $potongan_label_display }}</td>
                <td>{{ $potongan_value_display_for_table }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>{{ $total_akhir_display }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer-info">
        <p>Poin dari pesanan ini: {{ $poin_didapat }}</p>
        <p>Total poin customer: {{ $total_poin }}</p>
        <br>
        <!-- <p>QC oleh: {{ $qc_oleh }}</p> -->
    </div>
    <div class="qc-section">
        <p><strong>QC/Pegawai Gudang:</strong></p>
        <p>{{ $qc_oleh ?? 'Belum ditentukan' }}</p>
    </div>
    <!-- <div class="signature">
        <p>Diambil oleh:</p>
        <p class="placeholder">(..............................)</p>
        <p class="date-placeholder">Tanggal: ..........................</p>
    </div> -->
</body>

</html>