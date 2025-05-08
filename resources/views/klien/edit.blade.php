@extends('layouts.admin.app')

@section('title', 'Edit Klien')

@section('content')
<div class="container">
    <h2>Edit Klien</h2>

    <form action="{{ route('klien.update', $klien->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="no_induk">No Induk</label>
            <input type="text" class="form-control" name="no_induk" id="no_induk" value="{{ old('no_induk', $klien->no_induk) }}" required>
            @error('no_induk')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="nama_klien">Nama Klien</label>
            <input type="text" class="form-control" name="nama_klien" id="nama_klien" value="{{ old('nama_klien', $klien->nama_klien) }}" required>
            @error('nama_klien')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="alamat">Alamat</label>
            <textarea class="form-control" name="alamat" id="alamat" required>{{ old('alamat', $klien->alamat) }}</textarea>
            @error('alamat')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="no_hp">No HP</label>
            <input type="text" class="form-control" name="no_hp" id="no_hp" value="{{ old('no_hp', $klien->no_hp) }}" required>
            @error('no_hp')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <a href="{{ route('klien.index') }}" class="btn btn-danger">Batal</a>
        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
</div>
@endsection
