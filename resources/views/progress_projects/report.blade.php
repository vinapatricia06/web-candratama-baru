@extends('layouts.admin.app')

@section('title', 'Report Project')

@section('content')
    <div class="container-fluid">
        @php
            $judulStatusProject = $status_project == 0 ? 'Semua Status Project' : $status_project;
            $judulStatusPembayaran =
                $status_pembayaran == 'semua' ? 'Semua Status Pembayaran' : $status_pembayaran . ' dibayar';
        @endphp

        <h4>Report Project - {{ $judulStatusProject }} | {{ $judulStatusPembayaran }}</h4>

        <div class="d-flex justify-content-between mb-3" style="max-width: 650px;">
            <form action="{{ route('progress_projects.downloadReport') }}" method="POST">
                @csrf
                <input type="hidden" name="status_project" value="{{ $status_project }}">
                <input type="hidden" name="status_pembayaran" value="{{ $status_pembayaran }}">
                <input type="hidden" name="tipe" value="download">
                <button type="submit" class="btn btn-primary">Export Data</button>
            </form>
        </div>
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Data Report</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                    @php
                        $grand_total_pembayaran = 0;
                        $grand_total_uang_masuk = 0;
                    @endphp

                    <table class="table table-bordered" style="font-size: 18px; width: 100%; table-layout: auto;">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Project</th>
                                <th>Teknisi</th>
                                <th>Klien</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status Project</th>
                                <th>Status Pembayaran</th>
                                <th>Total Pembayaran</th>
                                <th>Uang Masuk</th>
                                <th>Sisa Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $key => $item)
                                @php
                                    $total_pembayaran = $item->nominal ?? 0;
                                    $uang_masuk =
                                        optional($item->omsets)
                                            ->where('catatan_pembayaran', '!=', 'MAINTENANCE')
                                            ->sum('nominal') ?? 0;
                                    $sisa_pembayaran = $total_pembayaran - $uang_masuk;

                                    // Akumulasi grand total
                                    $grand_total_pembayaran += $total_pembayaran;
                                    $grand_total_uang_masuk += $uang_masuk;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->project }}</td>
                                    <td>{{ $item->teknisi->nama }}</td>
                                    <td>{{ $item->klien->nama_klien }}</td>
                                    <td>{{ $item->tanggal_mulai }}</td>
                                    <td>{{ $item->tanggal_selesai }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->status_pembayaran }}</td>
                                    <td>Rp {{ number_format($total_pembayaran, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($uang_masuk, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align: center;">Tidak ada data yang ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            @php
                                $grand_total_sisa = $grand_total_pembayaran - $grand_total_uang_masuk;
                            @endphp
                            <tr>
                                <th colspan="8" style="text-align: right;">Grand Total</th>
                                <th>Rp {{ number_format($grand_total_pembayaran, 0, ',', '.') }}</th>
                                <th>Rp {{ number_format($grand_total_uang_masuk, 0, ',', '.') }}</th>
                                <th>Rp {{ number_format($grand_total_sisa, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
