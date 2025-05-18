@extends('layouts.admin.app')

@section('title', 'Edit Omset')

@section('content')
    <div class="container">
        <h2>Edit Omset</h2>

        <!-- Menampilkan pesan error jika ada -->
        @if ($errors->has('klien_id'))
            <script>
                alert('Klien yang dipilih tidak valid.');
            </script>
        @endif

        <form action="{{ route('omsets.update', $omset->id_omset) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Tanggal -->
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $omset->tanggal }}" required>
            </div>

            <!-- Nama Klien (Jika tidak ingin mengubah klien, tidak perlu memilih) -->
            <div class="form-group">
                <label for="klien_id">Nama Klien</label>
                <select name="klien_id" id="klien_id" class="form-control">
                    <option value="">-- Jika tidak ingin mengubah klien, tidak perlu memilih --</option>
                    @foreach($kliens as $klien)
                        <option value="{{ $klien->id }}"
                            data-no_induk="{{ $klien->no_induk }}"
                            data-alamat="{{ $klien->alamat }}"
                            {{ $omset->klien_id == $klien->id ? 'selected' : '' }}>
                            {{ $klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
                @error('klien_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- No Induk -->
            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" name="no_induk" id="no_induk" class="form-control" value="{{ old('no_induk', $omset->no_induk) }}" readonly required>
            </div>

            <!-- Alamat -->
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3" readonly required>{{ old('alamat', $omset->alamat) }}</textarea>
            </div>

            <!-- Project -->
            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" value="{{ old('project', $omset->project) }}" required>
            </div>

            <!-- Sumber Lead -->
            <div class="form-group">
                <label for="sumber_lead">Sumber Lead</label>
                <input type="text" name="sumber_lead" id="sumber_lead" class="form-control" value="{{ old('sumber_lead', $omset->sumber_lead) }}">
            </div>

            <!-- Nominal -->
            <div class="mb-3">
                <label>Nominal</label>
                <input type="number" name="nominal" class="form-control" value="{{ old('nominal', $omset->nominal) }}" required>
            </div>

            <a href="{{ route('omsets.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>

    {{-- Script untuk autofill --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const klienSelect = document.getElementById('klien_id');
            
            // Jika halaman pertama kali dimuat, autofill berdasarkan data omset
            const selectedKlien = klienSelect.querySelector('option[selected]');
            if (selectedKlien) {
                const noInduk = selectedKlien.getAttribute('data-no_induk');
                const alamat = selectedKlien.getAttribute('data-alamat');

                document.getElementById('no_induk').value = noInduk || '';
                document.getElementById('alamat').value = alamat || '';
            }

            // Autofill saat klien dipilih
            klienSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const noInduk = selected.getAttribute('data-no_induk');
                const alamat = selected.getAttribute('data-alamat');

                document.getElementById('no_induk').value = noInduk || '';
                document.getElementById('alamat').value = alamat || '';
            });
        });
    </script>
@endsection
