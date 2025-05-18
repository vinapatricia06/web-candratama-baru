@extends('layouts.admin.app')

@section('title', 'Tambah Maintenance Project')

@section('content')
    <div class="container">
        <h2>Tambah Maintenance Project</h2>

        @if ($errors->has('dokumentasi'))
            <script>
                  alert('Ukuran gambar yang diunggah melebihi batas maksimum 1.5MB. Silakan kompres gambar terlebih dahulu.');
            </script>
        @endif

        <form action="{{ route('maintenances.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Nama Klien</label>
                <select name="nama_klien" id="nama_klien" class="form-control" required>
                    <option value="">-- Pilih Klien --</option>
                    @foreach($kliens as $klien)
                        <option value="{{ $klien->nama_klien }}"
                            data-no_induk="{{ $klien->no_induk }}"
                            data-alamat="{{ $klien->alamat }}">
                            {{ $klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" name="no_induk" id="no_induk" class="form-control" required readonly>
                @error('no_induk')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" required readonly></textarea>
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
                <label>Dokumentasi (Opsional: Anda dapat mengunggah gambar dengan ukuran maksimal 1.5 MB.) </label>
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

    <script>
        // Isi otomatis No Induk dan Alamat berdasarkan pilihan Nama Klien
        document.getElementById('nama_klien').addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const noInduk = selected.getAttribute('data-no_induk');
            const alamat = selected.getAttribute('data-alamat');

            document.getElementById('no_induk').value = noInduk || '';
            document.getElementById('alamat').value = alamat || '';
        });
    </script>
@endsection
