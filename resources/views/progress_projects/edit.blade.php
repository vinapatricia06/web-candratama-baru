@extends('layouts.admin.app')

@section('title', 'Edit Progress Project')

@section('content')
    <div class="container">
        <h2>Edit Progress Project</h2>

        @if ($errors->has('dokumentasi'))
        <script>
            alert('Ukuran gambar yang diunggah melebihi batas maksimum 3MB. Silakan pilih gambar dengan ukuran yang lebih kecil.');
        </script>
    @endif
    
        <form action="{{ route('progress_projects.update', $progress_project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
            <div class="mb-3">
                <label>Klien</label>
                <input type="text" name="klien" class="form-control" value="{{ $progress_project->klien }}" required>
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ $progress_project->alamat }}</textarea>
            </div>
            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" value="{{ $progress_project->project }}" required>
            </div>
            <div class="mb-3">
                <label>Tanggal Setting</label>
                <input type="date" name="tanggal_setting" class="form-control" value="{{ $progress_project->tanggal_setting }}" required>
            </div>
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
            <div class="mb-3">
                <label>Status</label>
                <input type="text" name="status" class="form-control" value="{{ $progress_project->status }}" required>
            </div>
            <div class="form-group">
                <label for="serah_terima">Serah Terima</label>
                <select name="serah_terima" id="serah_terima" class="form-control">
                    <option value="selesai" {{ old('serah_terima', $project->serah_terima ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="belum" {{ old('serah_terima', $project->serah_terima ?? '') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
            
            <a href="{{ route('progress_projects.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
