
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View PDF</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        iframe {
            width: 90%; /* Membatasi lebar PDF */
            height: 80vh; /* Membatasi tinggi PDF */
            border: none;
            margin-bottom: 20px;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Menampilkan PDF -->
    <iframe src="{{ asset('storage/' . $suratCleaning->file_path) }}"></iframe>
    
    <!-- Tombol kembali -->
    <a href="{{ route('surat.cleaning.index') }}" class="btn-back">Kembali</a>
</body>
</html>
