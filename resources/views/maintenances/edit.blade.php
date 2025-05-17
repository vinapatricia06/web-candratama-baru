@extends('layouts.admin.app')

@section('title', 'Edit Maintenance Project')

@section('content')
    <div class="container">
        <h2>Edit Maintenance Project</h2>

        <!-- Error Messages -->
        @if ($errors->has('no_induk'))
            <script>
                alert('No Induk sudah terdaftar, harap gunakan yang berbeda.');
            </script>
        @endif

        @if ($errors->has('dokumentasi'))
            <script>
                alert('Ukuran gambar yang diunggah melebihi batas maksimum 1.5MB. Silakan kompres gambar terlebih dahulu.');
            </script>
        @endif

        <form action="{{ route('maintenances.update', $maintenance->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Klien Dropdown -->
            <div class="mb-3">
                <label>Nama Klien</label>
                <select name="nama_klien" id="nama_klien" class="form-control" required>
                    <option value="">-- Pilih Klien --</option>
                    @foreach($kliens as $klien)
                        <option value="{{ $klien->nama_klien }}" data-no_induk="{{ $klien->no_induk }}" data-alamat="{{ $klien->alamat }}"
                            {{ $maintenance->nama_klien == $klien->nama_klien ? 'selected' : '' }}>
                            {{ $klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- No Induk -->
            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" name="no_induk" id="no_induk" class="form-control" value="{{ old('no_induk', $maintenance->no_induk) }}" required readonly>
                @error('no_induk')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Alamat -->
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" required readonly>{{ old('alamat', $maintenance->alamat) }}</textarea>
            </div>

            <!-- Project -->
            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" value="{{ old('project', $maintenance->project) }}" required>
            </div>

            <!-- Tanggal Setting -->
            <div class="mb-3">
                <label>Tanggal Setting</label>
                <input type="date" name="tanggal_setting" class="form-control" value="{{ old('tanggal_setting', $maintenance->tanggal_setting) }}" required>
            </div>

            <!-- Maintenance -->
            <div class="mb-3">
                <label>Maintenance</label>
                <input type="text" name="maintenance" class="form-control" value="{{ old('maintenance', $maintenance->maintenance) }}" required>
            </div>

            <!-- Dokumentasi -->
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

            <!-- Status -->
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Waiting List" {{ old('status', $maintenance->status) == 'Waiting List' ? 'selected' : '' }}>Waiting List</option>
                    <option value="Selesai" {{ old('status', $maintenance->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <a href="{{ route('maintenances.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>

    <script>
        // Ensure the script runs after DOM content is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Isi otomatis No Induk dan Alamat berdasarkan pilihan Nama Klien
            document.getElementById('nama_klien').addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const noInduk = selected.getAttribute('data-no_induk');
                const alamat = selected.getAttribute('data-alamat');

                document.getElementById('no_induk').value = noInduk || '';
                document.getElementById('alamat').value = alamat || '';
            });
        });
    </script>
@endsection
