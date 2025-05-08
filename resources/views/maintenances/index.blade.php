@extends('layouts.admin.app')

@section('title', 'Kelola Maintenance Project')

@section('content')
    <div class="container-fluid">
        <h2>Daftar Maintenance Project</h2>

        <!-- Flexbox layout for positioning "Tambah Maintenance" button -->
        <div class="d-flex justify-content-between mb-3" style="max-width: 650px;">
            <a href="{{ route('maintenances.create') }}" class="btn btn-primary">Tambah Maintenance</a>
        </div>

        <!-- Form untuk memilih bulan dengan desain yang lebih rapi -->
        <form action="{{ route('maintenances.index') }}" method="GET" class="mb-3 d-flex flex-wrap gap-2" style="max-width: 650px;">
            <div class="input-group me-2" style="max-width: 150px;">
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Pilih Bulan</option>
                    @foreach (range(1, 12) as $bulan)
                        <option value="{{ $bulan }}" {{ request()->get('bulan') == $bulan ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="input-group me-2" style="max-width: 150px;">
                <input type="number" name="tanggal" class="form-control" min="1" max="31" placeholder="Tanggal"
                       value="{{ request()->get('tanggal') }}">
            </div>
            <!-- "Cari" Button with Icon -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <!-- "Reset" Button with Icon -->
            <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">
                <i class="fas fa-sync"></i> Reset
            </a>
        </form>

        <!-- Tombol untuk hapus semua data bulan yang dipilih -->
        @if(request()->get('bulan'))
            <form action="{{ route('maintenances.hapusBulan') }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="bulan" value="{{ request()->get('bulan') }}">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus semua data bulan ini? Semua data akan hilang.')">Hapus Semua Data Bulan Ini</button>
            </form>
        @endif

        <!-- Menampilkan pesan sukses -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Positioning the "Download PDF" button above the "Aksi" column -->
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('maintenances.downloadPdf') }}" class="btn btn-success">Download PDF</a>
        </div>

        <!-- Tabel untuk menampilkan maintenance project dengan responsif -->
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered" style="font-size: 16px; width: 100%; table-layout: auto;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Klien</th>
                        <th>No Induk</th>
                        <th>Alamat</th>
                        <th>Project</th>
                        <th>Tanggal Setting</th>
                        <th>Maintenance</th>
                        <th>Dokumentasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($maintenances as $key => $maintenance)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $maintenance->nama_klien }}</td>
                            <td>{{ $maintenance->no_induk }}</td>
                            <td>{{ $maintenance->alamat }}</td>
                            <td>{{ $maintenance->project }}</td>
                            <td>{{ $maintenance->tanggal_setting }}</td>
                            <td>{{ $maintenance->maintenance ?? 'Tidak Ada' }}</td>
                            <td>
                            @if ($maintenance->dokumentasi)
                                <!-- Menambahkan tautan untuk melihat gambar lebih besar -->
                                <a href="#" data-toggle="modal" data-target="#imageModal" onclick="showImage('{{ asset($maintenance->dokumentasi) }}')">
                                <img src="{{ asset($maintenance->dokumentasi) }}" alt="Dokumentasi" width="120">
                                </a>
                            @else
                                Tidak ada gambar
                            @endif
                            </td>
                            <td>{{ $maintenance->status }}</td>
                            <td>
                                <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('maintenances.destroy', $maintenance->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal untuk menampilkan gambar besar -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document"> <!-- Menggunakan modal-xl untuk memperbesar ukuran modal -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Dokumentasi Gambar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" src="" alt="Dokumentasi" style="width: 100%; height: auto; max-height: 95vh; object-fit: contain;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = src;
        }
    </script>
@endsection
