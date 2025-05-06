@extends('layouts.admin.app')

@section('title', 'Tambah Maintenance Project')

@section('content')
    <div class="container">
        <h2>Tambah Maintenance Project</h2>

        <!-- Menampilkan pop-up jika ada error untuk no_induk -->
        @if ($errors->has('no_induk'))
            <script>
                alert('No Induk sudah terdaftar, harap gunakan yang berbeda.');
            </script>
        @endif

          <!-- Menampilkan pop-up jika ada error untuk no_induk -->
          @if ($errors->has('dokumentasi'))
          <script>
              alert('Ukuran gambar yang diunggah melebihi batas maksimum 3MB. Silakan pilih gambar dengan ukuran yang lebih kecil.');
          </script>
      @endif

        <form action="{{ route('maintenances.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Nama Klien</label>
                <input type="text" name="nama_klien" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" name="no_induk" class="form-control" required>
                @error('no_induk')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Setting</label>
                <input type="date" name="tanggal_setting" class="form-control" required>
            </div>


            <div class="mb-3">
                <label>Maintenance</label>
                <input type="text" name="maintenance" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Dokumentasi (Opsional)</label>
                <input type="file" name="dokumentasi" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Waiting List">Waiting List</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <a href="{{ route('maintenances.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
@endsection
