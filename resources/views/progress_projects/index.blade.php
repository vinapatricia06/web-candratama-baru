@extends('layouts.admin.app')

@section('title', 'Kelola Progress Project')

@section('content')
    <div class="container-fluid">
        <h2>Daftar Progress Project</h2>

        <!-- Flexbox layout untuk penempatan tombol "Tambah Project" -->
        <div class="d-flex justify-content-between mb-3" style="max-width: 650px;">
            <a href="{{ route('progress_projects.create') }}" class="btn btn-primary">Tambah Project</a>
        </div>

        <!-- Form filter yang disederhanakan -->
        <form action="{{ route('progress_projects.index') }}" method="GET" class="mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filter Data</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Filter Bulan -->
                        <div class="col-md-4 mb-3">
                            <label for="bulan">Bulan:</label>
                            <select name="bulan" id="bulan" class="form-control">
                                <option value="">Semua Bulan</option>
                                @foreach (range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tanggal -->
                        <div class="col-md-4 mb-3">
                            <label for="tanggal">Tanggal Setting:</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control"
                                value="{{ request('tanggal') }}">
                        </div>

                        <!-- Filter Teknisi -->
                        <div class="col-md-4 mb-3">
                            <label for="teknisi_id">Teknisi:</label>
                            <select name="teknisi_id" id="teknisi_id" class="form-control">
                                <option value="">Semua Teknisi</option>
                                @foreach ($teknisiList as $teknisi)
                                    <option value="{{ $teknisi->id_user }}"
                                        {{ request('teknisi_id') == $teknisi->id_user ? 'selected' : '' }}>
                                        {{ $teknisi->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('progress_projects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Menampilkan pesan sukses -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Tombol untuk hapus semua data bulan yang dipilih -->
        @if (request()->get('bulan'))
            <form action="{{ route('progress_projects.hapusBulan') }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="bulan" value="{{ request()->get('bulan') }}">
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Yakin ingin menghapus semua data bulan ini? Semua data akan hilang.')">Hapus
                    Semua Data Bulan Ini</button>
            </form>
        @endif

        <!-- Menempatkan tombol "Download PDF" di sebelah kanan kolom "Aksi" -->
        <div class="d-flex justify-content-end mb-3">
            <button id="downloadPdfBtn" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
        </div>

        <!-- Tabel untuk menampilkan progress project dengan responsif -->
        <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
            <table class="table table-bordered" style="font-size: 18px; width: 100%; table-layout: auto;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Teknisi</th>
                        <th>Klien</th>
                        <th>Alamat</th>
                        <th>Project</th>
                        <th>Tanggal Setting</th>
                        <th>Dokumentasi</th>
                        <th>Status</th>
                        <th>Nominal</th>
                        <th>Status Pembayaran</th>
                        <th>Serah Terima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $key => $project)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $project->teknisi->nama ?? 'Tidak Ada' }}</td>
                            <td>{{ $project->klien->nama_klien ?? 'Tidak Ada' }}</td>
                            <td>{{ $project->klien->alamat ?? 'Tidak Ada' }}</td>
                            <td>{{ $project->project }}</td>
                            <td>{{ $project->tanggal_setting }}</td>
                            <td>
                                @if ($project->dokumentasi)
                                    <a href="#" data-toggle="modal" data-target="#imageModal"
                                        onclick="showImage('{{ asset($project->dokumentasi) }}')">
                                        <img src="{{ asset($project->dokumentasi) }}" alt="Dokumentasi" width="120">
                                    </a>
                                @else
                                    Tidak ada gambar
                                @endif
                            </td>
                            <td>{{ $project->status }}</td>
                            <td>Rp {{ number_format($project->nominal, 0, ',', '.') }}</td>
                            <td>{{ $project->status_pembayaran }}</td>
                            <td>{{ $project->serah_terima }}</td>
                            <td>
                                @if ($project->status_pembayaran == 'Menunggu Pembayaran')
                                    <button class="btn btn-dark" onclick="pembayaran()">Pembayaran</button>
                                @endif
                                <a href="{{ route('progress_projects.edit', $project->id) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('progress_projects.destroy', $project->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data yang ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal untuk menampilkan gambar besar -->
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Dokumentasi Gambar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img id="modalImage" src="" alt="Dokumentasi"
                            style="width: 100%; height: auto; max-height: 95vh; object-fit: contain;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = src;
        }

        // Script untuk download PDF berdasarkan filter yang dipilih
        document.getElementById('downloadPdfBtn').addEventListener('click', function() {
            // Dapatkan nilai filter dari form
            const bulan = document.getElementById('bulan').value;
            const tanggal = document.getElementById('tanggal').value;
            const teknisiId = document.getElementById('teknisi_id').value;

            // Buat URL download dengan parameter filter
            let downloadUrl = "{{ route('progress_projects.downloadPdf') }}";
            let params = [];

            if (bulan) params.push(`bulan=${bulan}`);
            if (tanggal) params.push(`tanggal=${tanggal}`);
            if (teknisiId) params.push(`teknisi_id=${teknisiId}`);

            if (params.length > 0) {
                downloadUrl += `?${params.join('&')}`;
            }

            console.log('Download URL:', downloadUrl); // Debug log

            // Buka URL download
            window.location.href = downloadUrl;
        });
    </script>
@endsection

@section('script')
    <script>
        function pembayaran() {
            alert('Proses pembayaran sedang dalam pengembangan.');
        }
    </script>
@endsection
