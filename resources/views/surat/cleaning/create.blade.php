@extends('layouts.admin.app')

@section('title', 'Form Surat Cleaning Services')
@section('content')

    <h1 class="mb-4">Form Surat Cleaning Services Baru</h1>

    <form action="{{ route('surat.cleaning.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Nama -->
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $nama }}" disabled>
        </div>

        <!-- Divisi -->
        <div class="mb-3">
            <label for="divisi" class="form-label">Divisi</label>
            <input type="text" name="divisi" class="form-control" value="{{ $divisi }}" disabled>
        </div>

        <!-- Keperluan -->
        <div class="mb-3">
            <label for="keperluan" class="form-label">Keperluan</label>
            <textarea name="keperluan" class="form-control" required rows="4"></textarea>
        </div>

        <!-- File Surat Cleaning -->
        <div class="mb-3">
            <label for="file_surat" class="form-label">File Surat Cleaning (PDF, Image)</label>
            <input type="file" name="file_surat" class="form-control" accept="application/pdf, image/*">
        </div>        

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

@endsection
