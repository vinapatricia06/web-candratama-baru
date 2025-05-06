@extends('layouts.admin.app')

@section('title', 'Edit Surat Cleaning Services')
@section('content')

    <h1 class="mb-4">Edit Surat Cleaning Services</h1>

    <form action="{{ route('surat.cleaning.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
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

        <!-- File Surat Ekspedisi -->
        <div class="mb-3">
            <label for="file_surat" class="form-label">File Surat Ekspedisi (PDF,Image)</label>
            <input type="file" name="file_surat" class="form-control" accept="application/pdf, image/*">
            @if($surat->file_path)
                <small class="form-text text-muted">Current File: <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank">View File</a></small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>

@endsection
