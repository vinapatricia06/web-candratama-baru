@extends('layouts.admin.app')

@section('title', 'Edit Maintenance Project')

@section('content')
    <div class="container">
        <h2>Edit Maintenance Project</h2>

        <!-- Menampilkan pop-up jika ada error untuk no_induk -->
        @if ($errors->has('no_induk'))
            <script>
                alert('No Induk sudah terdaftar, harap gunakan yang berbeda.');
            </script>
        @endif

        <!-- Menampilkan pop-up jika ada error untuk no_induk -->
        @if ($errors->has('dokumentasi'))
            <script>
                  alert('Ukuran gambar yang diunggah melebihi batas maksimum 1.5MB. Silakan kompres gambar terlebih dahulu.');
            </script>
        @endif

        <form action="{{ route('maintenances.update', $maintenance->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Klien</label>
                <input type="text" name="nama_klien" class="form-control" value="{{ $maintenance->nama_klien }}" required>
            </div>
            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" name="no_induk" class="form-control" value="{{ $maintenance->no_induk }}" required>
                @error('no_induk')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ $maintenance->alamat }}</textarea>
            </div>

            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" value="{{ $maintenance->project }}" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Setting</label>
                <input type="date" name="tanggal_setting" class="form-control" value="{{ $maintenance->tanggal_setting }}" required>
            </div>

            <div class="mb-3">
                <label>Maintenance</label>
                <input type="text" name="maintenance" class="form-control" value="{{ $maintenance->maintenance }}" required>
            </div>

            <div class="mb-3">
                <label>Dokumentasi</label>
                <br>
                @if ($maintenance->dokumentasi)
                    <img src="{{ asset($maintenance->dokumentasi) }}" alt="Dokumentasi" width="150">
                @else
                    Tidak ada gambar
                @endif
                <br><br>
                <input type="file" name="dokumentasi" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Waiting List" {{ $maintenance->status == 'Waiting List' ? 'selected' : '' }}>Waiting List</option>
                    <option value="Selesai" {{ $maintenance->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <a href="{{ route('maintenances.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
