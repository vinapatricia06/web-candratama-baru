@extends('layouts.admin.app')

@section('title', 'Kelola Project')

@section('content')
    <div id="loading-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.7); z-index: 9999; display: flex; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Memproses Data...</span>
        </div>
    </div>

    <div class="container-fluid">
        <h2>Daftar Project</h2>

        <!-- Flexbox layout untuk penempatan tombol "Tambah Project" -->
        @if (
            (auth()->check() && auth()->user()->hasRole('superadmin')) ||
                auth()->user()->hasRole('marketing') ||
                auth()->user()->hasRole('admin'))
            <div class="d-flex justify-content-between mb-3" style="max-width: 650px;">
                <a href="{{ route('progress_projects.create') }}" class="btn btn-primary">Tambah Project</a>
            </div>
        @endif

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
                            <td>
                                <div class="btn-group" role="group">
                                    @if (
                                        (auth()->check() && auth()->user()->hasRole('superadmin')) ||
                                            auth()->user()->hasRole('finance') ||
                                            auth()->user()->hasRole('admin'))
                                        @if ($project->status_pembayaran == 'Menunggu Pembayaran')
                                            <button class="btn btn-dark btn-sm" onclick="pembayaran({{ $project->id }})">
                                                <i class="fas fa-money-bill-wave"></i> Pembayaran
                                            </button>
                                        @elseif($project->status_pembayaran == 'Sudah Dibayar')
                                            <a href="{{ route('progress_projects.nota', $project->id) }}" target="_blank"
                                                class="btn btn-dark btn-sm">
                                                <i class="fas fa-print"></i> Cetak Nota
                                            </a>
                                        @endif
                                    @endif
                                    @if (
                                        (auth()->check() && auth()->user()->hasRole('superadmin')) ||
                                            auth()->user()->hasRole('marketing') ||
                                            auth()->user()->hasRole('admin'))
                                        <a href="{{ route('progress_projects.edit', $project->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('progress_projects.destroy', $project->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
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
@endsection

@section('script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-3exfNPbeb-yYb9s7"></script>

    <script>
        $(document).ready(function() {
            $('#loading-overlay').fadeOut();
        });

        function pembayaran(project_id) {
            Swal.fire({
                title: 'Masukkan Informasi Pembayaran',
                html: '<label for="sumberLead">Sumber Lead</label>' +
                    '<input id="sumberLead" class="swal2-input" placeholder="Contoh: Instagram, Website, Referral, dll">' +
                    '<label for="metodePembayaran">Metode Pembayaran</label>' +
                    '<select id="metodePembayaran" class="swal2-select">' +
                    '<option value="">-- Pilih Metode Pembayaran --</option>' +
                    '<option value="midtrans">Midtrans</option>' +
                    '<option value="tunai">Tunai</option>' +
                    '</select>',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan Pembayaran',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const sumberLead = document.getElementById('sumberLead').value;
                    const metode = document.getElementById('metodePembayaran').value;

                    if (!sumberLead || !metode) {
                        Swal.showValidationMessage('Sumber Lead dan Metode Pembayaran wajib diisi!');
                        return false;
                    }

                    return {
                        sumber_lead: sumberLead,
                        metode_pembayaran: metode
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        sumber_lead,
                        metode_pembayaran
                    } = result.value;

                    var formData = new FormData();
                    formData.append('project_id', project_id);
                    formData.append('sumber_lead', sumber_lead);
                    formData.append('metode_pembayaran', metode_pembayaran);

                    $.ajax({
                        url: "{{ route('createTransaction') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: () => {
                            $('#loading-overlay').fadeIn();
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: (response) => {
                            $('#loading-overlay').fadeOut();
                            if (response.status === 'success') {
                                if (response.metode == 'midtrans') {
                                    payWithMidtrans(response.snap_token, response.project_id, response
                                        .sumber_lead, metode_pembayaran);
                                } else {
                                    Notiflix.Notify.success('Pembayaran Berhasil');
                                    const finishRedirectUrl = '/sukses';
                                    window.location.href =
                                        `${finishRedirectUrl}/${response.project_id}/${response.sumber_lead}/${metode_pembayaran}`;
                                }
                            } else {
                                Notiflix.Notify.failure(response.message || 'Transaksi gagal.');
                            }
                        },
                        error: (xhr) => {
                            $('#loading-overlay').fadeOut();
                            let message = 'Terjadi kesalahan saat membuat transaksi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Notiflix.Notify.failure(message);
                        }
                    });
                }
            });
        }

        function payWithMidtrans(snapToken, order_id, sumber_lead, metode_pembayaran) {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    Notiflix.Notify.success('Pembayaran Berhasil');
                    const finishRedirectUrl = '/sukses';
                    const orderId = result.order_id;
                    console.log('Order ID:', orderId);
                    window.location.href =
                        `${finishRedirectUrl}/${orderId}/${sumber_lead}/${metode_pembayaran}`;
                },
                onPending: function(result) {
                    Notiflix.Notify.warning('Pembayaran Pending!');
                    const finishRedirectUrl = '/gagal';
                    const orderId = result.order_id;
                    window.location.href = `${finishRedirectUrl}/${orderId}/${sumber_lead}`;
                },
                onError: function(result) {
                    Notiflix.Notify.failure('Terjadi kesalahan pada pembayaran!');
                    const finishRedirectUrl = '/gagal';
                    const orderId = result.order_id;
                    window.location.href = `${finishRedirectUrl}/${orderId}/${sumber_lead}`;
                },
                onClose: function() {
                    Notiflix.Notify.failure('Pembayaran Ditunda!');
                    const finishRedirectUrl = '/gagal';
                    const orderId = order_id;
                    window.location.href = `${finishRedirectUrl}/${orderId}/${sumber_lead}`;
                }
            });
        }
    </script>
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
