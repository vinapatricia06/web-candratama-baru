<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Tagihan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            margin: 15px auto;
            max-width: 700px;
            color: #333;
            line-height: 1.3;
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
            margin-bottom: 3px;
            color: #2c3e50;
        }

        h2 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #e74c3c;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .image-container img {
            width: 120px;
            height: auto;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .invoice-info,
        .client-info {
            width: 48%;
        }

        .invoice-info h3,
        .client-info h3 {
            text-align: left;
            margin-bottom: 6px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            font-size: 13px;
        }

        .line-top {
            border-top: 3px solid #2c3e50;
            width: 60%;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
        }

        .line-bottom {
            border-top: 2px solid #2c3e50;
            width: 60%;
            margin-top: 5px;
            margin-left: auto;
            margin-right: auto;
        }

        .no-border td {
            border: none;
            padding: 3px 0;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-style: italic;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
        }

        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .amount {
            font-weight: bold;
            color: #27ae60;
        }

        .status-paid {
            color: #27ae60;
            font-weight: bold;
        }

        .status-unpaid {
            color: #e74c3c;
            font-weight: bold;
        }

        .status-partial {
            color: #f39c12;
            font-weight: bold;
        }

        .due-date {
            color: #e74c3c;
            font-weight: bold;
        }

        .info-section {
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 4px solid #2c3e50;
        }

        .two-column {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .column {
            flex: 1;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('images/kops.png') }}" alt="Candratama Granites" width="400">
        <div class="line-top"></div>
        <div class="line-bottom"></div>
        <br>
        <h2>INVOICE TAGIHAN PEMBAYARAN MAINTENANCE</h2>
    </div>

    <div class="two-column">
        <div class="column">
            <div class="info-section">
                <h3>Informasi Invoice</h3>
                <table class="no-border">
                    <tr>
                        <td><strong>No. Invoice</strong></td>
                        <td>: <span class="invoice-number">#INVOICE-{{ $data['id'] }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Invoice</strong></td>
                        <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="column">
            <div class="info-section">
                <h3>Tagihan Kepada</h3>
                <table class="no-border">
                    <tr>
                        <td><strong>Nama Klien</strong></td>
                        <td>: {{ $data['project']['klien']['nama_klien'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>: {{ $data['project']['klien']['alamat'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>No. Identitas</strong></td>
                        <td>: {{ $data['project']['klien']['no_induk'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <h3 style="margin-top: 15px; text-align: left; color: #2c3e50; font-size: 13px;">Rincian Tagihan</h3>
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th class="center">Status</th>
                <th class="center">Maintenance</th>
                <th class="right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['project']['project'] }}</td>
                <td class="center">{{ $data['status'] }}</td>
                <td class="center">{{ $data['maintenance'] }}</td>
                <td class="right">{{ 'Rp ' . number_format($data['biaya_tambahan'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="two-column" style="margin-top: 15px;">
        <div class="column">
            <div class="info-section">
                <h3>Status Pembayaran</h3>
                <table class="no-border">
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>: <span class="status-unpaid">{{ $data['status_pembayaran'] }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Metode Pembayaran</strong></td>
                        <td>: Tunai/Pembayaran Online</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Catatan:</strong> Harap lakukan pembayaran sebelum tanggal jatuh tempo • Konfirmasi pembayaran dapat
            dikirim melalui email atau WhatsApp</p>
        <p><em>Terima kasih atas kepercayaan Anda kepada Candratama Granites</em></p>
    </div>

</body>

</html>
