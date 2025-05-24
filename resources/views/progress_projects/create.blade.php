@extends('layouts.admin.app')

@section('title', 'Tambah Progress Project')

@section('content')
    <div class="container">
        <h2>Tambah Progress Project</h2>

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
                <label>Tanggal Setting</label>
                <input type="date" name="tanggal_setting" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Dokumentasi (Opsional)</label>
                <input type="file" name="dokumentasi" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <input type="text" name="status" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Nominal</label>
                <input type="number" name="nominal" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Serah Terima</label>
                <select name="serah_terima" class="form-control" required>
                    <option value="belum" selected>Belum</option>
                    <option value="selesai">Selesai</option>
                </select>
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
    </script>

@endsection
