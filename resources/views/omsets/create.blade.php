@extends('layouts.admin.app')

@section('title', 'Tambah Omset')

@section('content')
<div class="container">
    <h2>Tambah Omset</h2>

    @if ($errors->has('no_induk'))
        <script>
            alert('No Induk sudah terdaftar, harap gunakan yang berbeda.');
        </script>
    @endif

    <form action="{{ route('omsets.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="no_induk">No Induk</label>
            <select name="no_induk" id="no_induk" class="form-control" required>
                <option value="">-- Pilih No Induk --</option>
                @foreach($kliens as $klien)
                    <option value="{{ $klien->no_induk }}"
                        data-nama="{{ $klien->nama_klien }}"
                        data-alamat="{{ $klien->alamat }}">
                        {{ $klien->no_induk }}
                    </option>
                @endforeach
            </select>
            @error('no_induk')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Nama Klien</label>
            <input type="text" name="nama_klien" id="nama_klien" class="form-control" readonly required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" id="alamat" class="form-control" rows="3" readonly required></textarea>
        </div>

        <div class="mb-3">
            <label>Project</label>
            <input type="text" name="project" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="sumber_lead">Sumber Lead</label>
            <input type="text" name="sumber_lead" id="sumber_lead" class="form-control" value="{{ old('sumber_lead') }}">
        </div>

        <div class="form-group">
            <label for="nominal">Nominal</label>
            <input type="number" name="nominal" id="nominal" class="form-control" required>
        </div>
        <br>
        <a href="{{ route('omsets.index') }}" class="btn btn-danger mr-2">Kembali</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

{{-- Script untuk autofill --}}
<script>
    document.getElementById('no_induk').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('nama_klien').value = selectedOption.getAttribute('data-nama') || '';
        document.getElementById('alamat').value = selectedOption.getAttribute('data-alamat') || '';
    });
</script>
@endsection
