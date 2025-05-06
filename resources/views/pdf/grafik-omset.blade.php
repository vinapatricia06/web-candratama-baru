<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Omset Tahunan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* Reduced font size */
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
            padding: 4px; /* Reduced padding */
            text-align: center; /* Center text in both header and data cells */
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
            font-size: 18px; /* Reduced header size */
        }
    
        .header p {
            font-size: 12px;
        }
    
        .image-container img {
            width: 100px; /* Reduced image size */
            height: auto;
        }
    
        .address {
            font-size: 12px;
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
    
        .chart-img {
            text-align: center;
            margin-top: 20px;
        }
    
        .chart-img img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="D:\web-candratama\public\images\kops.png" alt="Candratama Granites" width="600"> 
        <div class="line-bottom"></div>
        <h1>Rekap Omset Tahunan</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Januari</th>
                <th>Februari</th>
                <th>Maret</th>
                <th>April</th>
                <th>Mei</th>
                <th>Juni</th>
                <th>Juli</th>
                <th>Agustus</th>
                <th>September</th>
                <th>Oktober</th>
                <th>November</th>
                <th>Desember</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $tahun => $omsetBulanan)
                <tr>
                    <td>{{ $tahun }}</td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td>{{ number_format($omsetBulanan[$i] ?? 0, 0, ',', '.') }}</td>
                    @endfor
                    <td><strong>{{ number_format($totalPerTahun[$loop->index] ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Omset per Tahun</h3>
    <ul>
        @foreach ($labels as $index => $tahun)
            <li><strong>{{ $tahun }}</strong>: Rp {{ number_format($totalPerTahun[$index] ?? 0, 0, ',', '.') }}</li>
        @endforeach
    </ul>

    <div class="chart-img">
        <img src="{{ $chartPath }}" alt="Grafik Omset" width="600"> <!-- Reduced chart size -->
    </div>
</body>

</html>
