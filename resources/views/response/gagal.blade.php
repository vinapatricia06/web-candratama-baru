@extends('layouts.admin.app')

@section('title', 'Pembayaran Gagal')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card elevation-3 border-0 shadow-lg">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-5x text-danger mb-4 animate__animated animate__bounceIn"></i>
                        <h3 class="text-danger font-weight-bold mb-3">Pembayaran Gagal!</h3>
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
                                    <td>{{ $data->project }}</td>
                                </tr>
                                <tr>
                                    <th>Nominal</th>
                                    <td>Rp {{ number_format($data->nominal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Status Pembayaran</th>
                                    <td><span class="badge badge-danger">{{ $data->status_pembayaran }}</span></td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('progress_projects.index') }}" class="btn btn-danger btn-lg mr-2">
                                <i class="fas fa-home mr-1"></i> Kembali ke Project
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
