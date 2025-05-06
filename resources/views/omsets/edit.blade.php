@extends('layouts.admin.app')

@section('title', 'Edit Omset')

@section('content')
    <div class="container">
        <h2>Edit Omset</h2>
        
        <!-- Menampilkan pesan error jika ada -->
        @if ($errors->has('no_induk'))
            <script>
                alert('No Induk sudah terdaftar, harap gunakan yang berbeda.');
            </script>
        @endif

        <form action="{{ route('omsets.update', $omset->id_omset) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $omset->tanggal }}" required>
            </div>

            <div class="form-group">
                <label for="no_induk">No Induk</label>
                <input type="number" class="form-control" name="no_induk" id="no_induk" 
                    value="{{ old('no_induk', $omset->no_induk ?? '') }}" required>
                @error('no_induk')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>Nama Klien</label>
                <input type="text" name="nama_klien" class="form-control" value="{{ $omset->nama_klien }}" required>
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ $omset->alamat }}</textarea>
            </div>

            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" value="{{ $omset->project }}" required>
            </div>

            <div class="form-group">
                <label for="sumber_lead">Sumber Lead</label>
                <input type="text" name="sumber_lead" id="sumber_lead" class="form-control"
                    value="{{ old('sumber_lead', $omset->sumber_lead) }}">
            </div>

            <div class="mb-3">
                <label>Nominal</label>
                <input type="number" name="nominal" class="form-control" value="{{ $omset->nominal }}" required>
            </div>

            <a href="{{ route('omsets.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
