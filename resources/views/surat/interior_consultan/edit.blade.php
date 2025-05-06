@extends('layouts.admin.app')

@section('title', 'Edit Surat Interior Consultant')
@section('content')

    <h1>Edit Surat Interior Consultant</h1>

    <form action="{{ route('surat.interior_consultan.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Nama -->
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $surat->nama }}" disabled>
        </div>

        <!-- Divisi -->
        <div class="mb-3">
            <label for="divisi" class="form-label">Divisi</label>
            <input type="text" name="divisi" class="form-control" value="{{ $surat->divisi }}" disabled>
        </div>

        <!-- Keperluan -->
        <div class="mb-3">
            <label for="keperluan" class="form-label">Keperluan</label>
            <textarea name="keperluan" class="form-control" required rows="4">{{ $surat->keperluan }}</textarea>
        </div>

        <!-- File Surat Interior Consultant -->
        <div class="mb-3">
            <label for="file_surat" class="form-label">File Surat Ekspedisi (PDF, Image)</label>
            @if($surat->file_path)
                <div>
                    <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank">Lihat File Lama</a>
                </div>
            @endif
            <input type="file" name="file_surat" class="form-control" accept="application/pdf, image/*">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah file surat.</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
    </form>

@endsection
