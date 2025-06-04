@extends('layouts.admin.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card elevation-3 border-0 shadow-lg">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-5x text-success mb-4 animate__animated animate__bounceIn"></i>
                        <h3 class="text-success font-weight-bold mb-3">Pembayaran Berhasil!</h3>
                        <p class="lead">Terima kasih atas pembayaran Anda. Transaksi Anda telah berhasil diproses.</p>

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
                                    <td>Rp {{ number_format($data->biaya_tambahan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Status Pembayaran</th>
                                    <td><span class="badge badge-success">{{ $data->status_pembayaran }}</span></td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('maintenances.index') }}" class="btn btn-success btn-lg mr-2">
                                <i class="fas fa-home mr-1"></i> Kembali ke Maintenance
                            </a>
                            <a href="{{ route('omsets.index') }}" class="btn btn-outline-success btn-lg">
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
