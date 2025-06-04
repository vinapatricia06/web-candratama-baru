<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Transaksi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 30px auto;
            max-width: 700px;
            color: #333;
        }

        h1,
        h2,
        h3 {
            text-align: center;
            margin: 0;
        }

        h1 {
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 16px;
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .no-border td {
            border: none;
            padding: 3px 0;
        }

        .right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            color: #555;
        }

        .header-info {
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <h1>CANDRATAMA GRANITES</h1>
    <h2>NOTA TRANSAKSI</h2>

    <table class="no-border header-info">
        <tr>
            <td><strong>Kode Transaksi</strong></td>
            <td>: {{ $data['kode_transaksi'] }}</td>
        </tr>
        <tr>
            <td><strong>Nama Klien</strong></td>
            <td>: {{ $data['klien']['nama_klien'] }}</td>
        </tr>
        <tr>
            <td><strong>Alamat</strong></td>
            <td>: {{ $data['klien']['alamat'] }}</td>
        </tr>
        <tr>
            <td><strong>No Induk</strong></td>
            <td>: {{ $data['klien']['no_induk'] }}</td>
        </tr>
    </table>

    <h3 style="margin-top: 30px;">Project</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Status</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['project'] }}</td>
                <td>{{ $data['status'] }}</td>
                <td>{{ 'Rp ' . number_format($data['nominal'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="right"><strong>Subtotal</strong></td>
                <td><strong>{{ 'Rp ' . number_format($data['nominal'], 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="right"><strong>Metode Pembayaran</strong></td>
                <td><strong>{{ $data['omset']['metode_pembayaran'] }}</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="right"><strong>Status Pembayaran</strong></td>
                <td><strong>{{ $data['status_pembayaran'] }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Terima kasih atas transaksi Anda.<br>
        Silakan hubungi kami jika ada pertanyaan.
    </div>

</body>

</html>
