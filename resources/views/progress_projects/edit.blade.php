@extends('layouts.admin.app')

@section('title', 'Edit Progress Project')

@section('content')
    <div class="container">
        <h2>Edit Progress Project</h2>

        <!-- Error Messages -->
        @if ($errors->has('dokumentasi'))
            <script>
                alert('Ukuran gambar yang diunggah melebihi batas maksimum 1.5MB. Silakan kompres gambar terlebih dahulu.');
            </script>
        @endif

        <form action="{{ route('progress_projects.update', $progress_project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Teknisi Dropdown -->
            <div class="mb-3">
                <label>Teknisi</label>
                <select name="teknisi_id" class="form-control" required>
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach ($teknisiList as $teknisi)
                        <option value="{{ $teknisi->id_user }}" {{ $progress_project->teknisi_id == $teknisi->id_user ? 'selected' : '' }}>
                            {{ $teknisi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Klien Dropdown -->
            <div class="mb-3">
                <label>Nama Klien</label>
                <select name="nama_klien" id="nama_klien" class="form-control" required>
                    <option value="">-- Pilih Klien --</option>
                    @foreach($kliens as $klien)
                        <option value="{{ $klien->nama_klien }}" 
                                data-no_induk="{{ $klien->no_induk }}" 
                                data-alamat="{{ $klien->alamat }}"
                                {{ $progress_project->nama_klien == $klien->nama_klien ? 'selected' : '' }}>
                            {{ $klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Alamat -->
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" required readonly>{{ old('alamat', $progress_project->alamat) }}</textarea>
            </div>

            <!-- Project -->
            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" value="{{ old('project', $progress_project->project) }}" required>
            </div>

            <!-- Tanggal Setting -->
            <div class="mb-3">
                <label>Tanggal Setting</label>
                <input type="date" name="tanggal_setting" class="form-control" value="{{ old('tanggal_setting', $progress_project->tanggal_setting) }}" required>
            </div>

            <!-- Dokumentasi -->
            <div class="mb-3">
                <label>Dokumentasi</label>
                <br>
                @if ($progress_project->dokumentasi)
                    <img src="{{ asset($progress_project->dokumentasi) }}" alt="Dokumentasi" width="150">
                @else
                    Tidak ada gambar
                @endif
                <br><br>
                <input type="file" name="dokumentasi" class="form-control">
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label>Status</label>
                <input type="text" name="status" class="form-control" value="{{ old('status', $progress_project->status) }}" required>
            </div>

            <!-- Serah Terima -->
            <div class="form-group">
                <label for="serah_terima">Serah Terima</label>
                <select name="serah_terima" id="serah_terima" class="form-control">
                    <option value="selesai" {{ old('serah_terima', $progress_project->serah_terima ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="belum" {{ old('serah_terima', $progress_project->serah_terima ?? '') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>

            <a href="{{ route('progress_projects.index') }}" class="btn btn-danger mr-2">Kembali</a>
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
