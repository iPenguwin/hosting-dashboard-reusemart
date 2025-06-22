<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <title>Laporan Request Donasi</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header h2 {
                margin: 0;
            }
            .header p {
                margin: 2px 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            table th,
            table td {
                border: 1px solid #333;
                padding: 4px 6px;
            }
            table th {
                background-color: #f0f0f0;
                text-align: center;
            }
            table td {
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>ReUse Mart</h2>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
            <h3><u>LAPORAN REQUEST DONASI</u></h3>
            <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Organisasi</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Request</th>
                    <th>Deskripsi Request</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataRequests as $r)
                    <tr>
                        <td>{{ $r['id_organisasi'] }}</td>
                        <td>{{ $r['nama'] }}</td>
                        <td>{{ $r['alamat'] }}</td>
                        <td>{{ $r['request'] }}</td>
                        <td>{{ $r['deskripsi'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; font-style: italic">
                            Tidak ada request donasi yang belum terpenuhi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
