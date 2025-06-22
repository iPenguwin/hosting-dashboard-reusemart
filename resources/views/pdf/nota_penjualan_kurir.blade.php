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
            /* Added to ensure no default margin */
            padding: 0;
            /* Added for consistency */
        }

        .nota-title {
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            /* As per image */
            margin-bottom: 10px;
            /* Space before header */
            padding-left: 5px;
            /* Align with other content */
        }

        .header {
            text-align: left;
            /* Changed to left as per image */
            margin-bottom: 15px;
            /* border-bottom: 1px dashed #000; */
            /* Removed dashed line from main header, seems to be after items */
            padding-bottom: 0px;
            /* Adjusted padding */
            padding-left: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            /* Adjusted size */
            font-weight: bold;
        }

        .header p {
            margin: 2px 0 0;
            /* Adjusted margin */
            font-size: 11px;
        }

        .info-section {
            /* Combined info-transaksi and info-pembeli for layout */
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
            /* Reduced padding */
            vertical-align: top;
        }

        .info-section table td:first-child {
            width: 90px;
            /* Label width */
        }

        .info-section table td:nth-child(2) {
            width: 10px;
            /* Colon width */
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
            /* Reduced margin */
            border-top: 1px solid #000;
            /* Line above items header */
        }

        table.items th,
        table.items td {
            padding: 4px 2px;
            /* Adjusted padding */
            text-align: left;
        }

        table.items th {
            border-bottom: 1px solid #000;
            /* Line below items header */
        }

        table.items td:last-child,
        table.items th:last-child {
            text-align: right;
        }

        table.items tr.item-row td {
            border-bottom: none;
            /* No line between items */
        }

        /* Line after the last item before totals start */
        table.items tbody tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .totals-container {
            padding-left: 5px;
            padding-right: 5px;
            margin-top: 5px;
            /* Space above totals */
        }

        table.totals {
            width: 100%;
            border-collapse: collapse;
        }

        table.totals td {
            padding: 2px;
            /* Reduced padding */
        }

        table.totals td:first-child {
            text-align: left;
            /* width: 60%; */
            /* Give more space to label */
        }

        table.totals td:last-child {
            text-align: right;
        }

        /* Add top border to first row of totals if it's distinct or for emphasis */
        /* table.totals tr:first-child td { border-top: 1px dashed #ccc; padding-top: 5px;} */


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
            /* Reduced margin */
            padding-left: 5px;
        }

        .signature p {
            margin: 2px 0;
        }

        .signature .placeholder {
            margin-top: 20px;
            /* Space for signature */
            margin-bottom: 2px;
        }

        .signature .date-placeholder {
            margin-top: 2px;
        }
    </style>
</head>

<body>
    @php
    // Calculations for totals based on image structure
    $subtotal_items_numeric = 0;
    foreach($items as $item_data) {
    // Assuming $item_data['harga'] is a formatted string like "2.000.000"
    $subtotal_items_numeric += (int)preg_replace('/[^0-9]/', '', $item_data['harga']);
    }

    $ongkos_kirim_numeric = (int)preg_replace('/[^0-9]/', '', $ongkos_kirim); // $ongkos_kirim is "0" or "100.000" etc.
    $total_sebelum_potongan_numeric = $subtotal_items_numeric + $ongkos_kirim_numeric;

    $potongan_poin_value_numeric = 0;
    $potongan_label_display = 'Potongan'; // Default label
    $potongan_value_display_for_table = '0'; // Default value

    if (strpos($potongan_poin, ' - ') !== false) {
    $parts = explode(' - ', $potongan_poin); // $parts[0] = "X poin", $parts[1] = "YYY"
    $potongan_label_display = 'Potongan ' . $parts[0];
    $potongan_poin_value_numeric = (int)preg_replace('/[^0-9]/', '', $parts[1]);
    $potongan_value_display_for_table = '- ' . $parts[1];
    } elseif ($potongan_poin !== '-' && $potongan_poin !== '0' && !empty(trim($potongan_poin))) {
    // Fallback if $potongan_poin is just a number (value of discount) or "X Poin"
    // This case might need refinement based on actual $potongan_poin format for "0" points
    if (stripos($potongan_poin, 'poin') !== false) {
    $potongan_label_display = 'Potongan ' . $potongan_poin;
    // Try to extract numeric value if it's like "200 poin" and implies a value,
    // otherwise assume no direct monetary value in this string part.
    // For "X poin - YYY", the YYY is the value. For just "X poin", value is 0 unless logic implies otherwise.
    } else { // Assumed to be a direct monetary value if no "poin - "
    $potongan_poin_value_numeric = (int)preg_replace('/[^0-9]/', '', $potongan_poin);
    $potongan_value_display_for_table = '- ' . number_format($potongan_poin_value_numeric, 0, ',', '.');
    }
    } else { // Handles "-" or "0" or empty for no discount
    $potongan_value_display_for_table = '0';
    }


    $total_akhir_numeric = $total_sebelum_potongan_numeric - $potongan_poin_value_numeric;

    // Formatted display strings
    $subtotal_items_display = number_format($subtotal_items_numeric, 0, ',', '.');
    $total_sebelum_potongan_display = number_format($total_sebelum_potongan_numeric, 0, ',', '.');
    $total_akhir_display = number_format($total_akhir_numeric, 0, ',', '.');

    // QC Oleh parsing (if needed, already done in controller)
    // $qc_oleh_display = $qc_oleh;
    @endphp

    <div class="nota-title">
        Nota Penjualan (dibawa oleh kurir)
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
                <td>Tanggal kirim</td>
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
                <td>{{ $delivery_method }} @if(isset($nama_kurir) && !empty($nama_kurir)) ({{ $nama_kurir }}) @elseif($delivery_method == 'Di Kirim') (Kurir ReUseMart) @endif</td>
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
    <!-- Ganti bagian qc-section dengan: -->
    <!-- <div class="qc-section">
        <p><strong>QC/Pegawai Gudang:</strong></p>
        @if(isset($qc_oleh))
        <p>{{ $qc_oleh }}</p>
        @elseif(isset($transaksi) && $transaksi->pegawaiTransaksiPembelians->isNotEmpty())
        @foreach($transaksi->pegawaiTransaksiPembelians as $pegawaiTransaksi)
        @if($pegawaiTransaksi->pegawai->jabatans->contains('NAMA_JABATAN', 'Pegawai Gudang'))
        <p>{{ $pegawaiTransaksi->pegawai->NAMA_PEGAWAI }} ({{ $pegawaiTransaksi->pegawai->KODE_PEGAWAI }})</p>
        @endif
        @endforeach
        @else
        <p>Belum ditentukan</p>
        @endif
    </div> -->

    <div class="qc-section">
        <p><strong>QC/Pegawai Gudang:</strong></p>
        <p>{{ $qc_oleh ?? 'Belum ditentukan' }}</p>
    </div>
    <!-- <div class="signature">
        <p>Diterima oleh:</p>
        <p class="placeholder">(..............................)</p>
        <p class="date-placeholder">Tanggal: ..........................</p>
    </div> -->
</body>

</html>