@extends('layouts.admin.app')

@section('title', 'Tambah Progress Project')

@section('content')
    <div class="container">
        <h2>Tambah Project</h2>

        @if ($errors->has('dokumentasi'))
            <script>
                alert('Ukuran gambar yang diunggah melebihi batas maksimum 1.5MB. Silakan kompres gambar terlebih dahulu.');
            </script>
        @endif

        <form action="{{ route('progress_projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Teknisi</label>
                <select name="teknisi_id" class="form-control" required>
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach ($teknisiList as $teknisi)
                        <option value="{{ $teknisi->id_user }}">{{ $teknisi->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="klien_id">Nama Klien</label>
                <select name="klien_id" id="klien_id" class="form-control" required>
                    <option value="">-- Pilih Nama Klien --</option>
                    @foreach ($kliens as $klien)
                        <option value="{{ $klien->id }}" data-alamat="{{ $klien->alamat }}">
                            {{ $klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" class="form-control" required readonly></textarea>
            </div>


            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Dokumentasi (Opsional)</label>
                <input type="file" name="dokumentasi" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Inisialisasi">Inisialisasi</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Nominal</label>
                <input type="number" name="nominal" class="form-control" required>
            </div>

            <a href="{{ route('progress_projects.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>

    <script>
        document.getElementById('klien_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const alamat = selected.getAttribute('data-alamat');
            document.getElementById('alamat').value = alamat;
        });
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
            const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');

            tanggalMulai.addEventListener('change', function() {
                tanggalSelesai.min = this.value;

                if (tanggalSelesai.value < this.value) {
                    tanggalSelesai.value = '';
                }
            });
        });
    </script>

@endsection
