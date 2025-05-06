<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Maintenance Project</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            font-size: 14px;
        }

        .image-container img {
            width: 120px;
            height: auto;
        }

        .address {
            font-size: 14px;
            margin-top: 10px;
            color: #333;
        }

        .line-top {
            border-top: 3px solid black;
            width: 60%;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
        }

        .line-bottom {
            border-top: 2px solid black;
            width: 60%;
            margin-top: 5px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>

    <div class="header">

        <img src="D:\web-candratama\public\images\kops.png" alt="Candratama Granites" width="600">

        <div class="line-top"></div>
        <div class="line-bottom"></div>
        <h1>Daftar Maintenance Project</h1>
        <br>
        <p>Berikut adalah daftar maintenance project yang tercatat.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Klien</th>
                <th>Alamat</th>
                <th>Project</th>
                <th>Tanggal Setting</th>
                <th>Maintenance</th>
                <th>Dokumentasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maintenances as $key => $maintenance)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $maintenance->nama_klien }}</td>
                    <td>{{ $maintenance->alamat }}</td> <!-- No line below this column -->
                    <td>{{ $maintenance->project }}</td>
                    <td>{{ $maintenance->tanggal_setting }}</td>
                    <td>{{ $maintenance->maintenance }}</td>
                    <td>
                        @if ($maintenance->dokumentasi_base64)
                            <img src="{{ $maintenance->dokumentasi_base64 }}" alt="Dokumentasi" width="120">
                        @else
                            Tidak ada gambar
                        @endif
                    </td>
                    <td>{{ $maintenance->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
