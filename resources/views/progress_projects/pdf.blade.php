<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Progress Project</title>
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
            padding: 10px;
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

        <h1>Progress Project</h1>
        <br>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Teknisi</th>
                <th>Klien</th>
                <th>Alamat</th>
                <th>Project</th>
                <th>Tanggal Setting</th>
                <th>Dokumentasi</th>
                <th>Nominal</th>
                <th>Status Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $key => $project)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $project->teknisi->nama ?? 'Tidak Ada' }}</td>
                    <td>{{ $project->klien->nama_klien ?? 'Tidak Ada' }}</td>
                    <td>{{ $project->klien->alamat ?? 'Tidak Ada' }}</td>
                    <td>{{ $project->project }}</td>
                    <td>{{ $project->tanggal_setting }}</td>
                    <td>
                        @if ($project->dokumentasi_base64)
                            <img src="{{ $project->dokumentasi_base64 }}" alt="Dokumentasi" width="120">
                        @else
                            Tidak ada gambar
                        @endif
                    </td>
                    <td>Rp {{ number_format($project->nominal, 0, ',', '.') }}</td>
                    <td>{{ $project->status_pembayaran }}</td>
                    <td>{{ $project->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
