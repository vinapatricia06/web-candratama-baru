@extends('layouts.admin.app')

@section('title', 'Pembayaran Gagal')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card elevation-3 border-0 shadow-lg">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-5x text-danger mb-4 animate__animated animate__bounceIn"></i>
                        <h3 class="text-danger font-weight-bold mb-3">Pembayaran Angsuran Gagal!</h3>
                        <p class="lead">Maaf, terjadi kesalahan saat memproses transaksi Anda. Silakan coba lagi atau
                            hubungi support kami.</p>

                        <div class="mt-4 text-left">
                            <h5 class="font-weight-bold mb-3">Detail Transaksi</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <td>{{ $data->kode_transaksi }}</td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td>{{ $data->project->project }}</td>
                                </tr>
                                <tr>
                                    <th>Nominal</th>
                                    <td>Rp {{ number_format($data->nominal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Angsuran</th>
                                    <td>{{ \Carbon\Carbon::parse($data->tanggal_angsuran)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pembayaran</th>
                                    <td>{{ \Carbon\Carbon::parse($data->tanggal_pembayaran)->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Pembayaran</th>
                                    <td><span class="badge badge-success">{{ $data->status_pembayaran }}</span></td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('progress_projects.detail', $data->project->id) }}"
                                class="btn btn-danger btn-lg mr-2">
                                <i class="fas fa-home mr-1"></i> Kembali ke Detail Project
                            </a>
                            <a href="{{ route('omsets.index') }}" class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-receipt mr-1"></i> Lihat Data Omset
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">Jika ada pertanyaan, silakan hubungi tim support kami.</small>
                </div>
            </div>
        </div>
    </div>
@endsection
