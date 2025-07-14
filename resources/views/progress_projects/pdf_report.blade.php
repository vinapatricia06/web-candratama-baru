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
        @php
            $judulStatusProject = $status_project == 0 ? 'Semua Status Project' : $status_project;
            $judulStatusPembayaran =
                $status_pembayaran == 'semua' ? 'Semua Status Pembayaran' : $status_pembayaran . ' dibayar';
        @endphp
        <img src="D:\web-candratama\public\images\kops.png" alt="Candratama Granites" width="600">
        <div class="line-top"></div>
        <div class="line-bottom"></div>
        <h1>Progress Project</h1>
        <h3 style="margin-bottom: 0px;">Status Project: {{ $judulStatusProject }}</h3>
        <h3 style="margin-bottom: 0px;">Status Pembayaran: {{ $judulStatusPembayaran }}</h3>
        <br>
    </div>

    @php
        $grand_total_pembayaran = 0;
        $grand_total_uang_masuk = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Project</th>
                <th>Teknisi</th>
                <th>Klien</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Status Project</th>
                <th>Status Pembayaran</th>
                <th>Total Pembayaran</th>
                <th>Uang Masuk</th>
                <th>Sisa Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $key => $item)
                @php
                    $total_pembayaran = $item->nominal ?? 0;
                    $uang_masuk =
                        optional($item->omsets)->where('catatan_pembayaran', '!=', 'MAINTENANCE')->sum('nominal') ?? 0;
                    $sisa_pembayaran = $total_pembayaran - $uang_masuk;

                    $grand_total_pembayaran += $total_pembayaran;
                    $grand_total_uang_masuk += $uang_masuk;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->project }}</td>
                    <td>{{ $item->teknisi->nama }}</td>
                    <td>{{ $item->klien->nama_klien }}</td>
                    <td>{{ $item->tanggal_mulai }}</td>
                    <td>{{ $item->tanggal_selesai }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->status_pembayaran }}</td>
                    <td>Rp {{ number_format($total_pembayaran, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($uang_masuk, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center;">Tidak ada data yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
        @php
            $grand_total_sisa = $grand_total_pembayaran - $grand_total_uang_masuk;
        @endphp
        <tfoot>
            <tr style="font-weight: bold; background-color: #f8f9fa;">
                <td colspan="8" style="text-align: right;">Grand Total</td>
                <td>Rp {{ number_format($grand_total_pembayaran, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($grand_total_uang_masuk, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($grand_total_sisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
