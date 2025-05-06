@extends('layouts.admin.app')

@section('title', 'Kelola Surat Warehouse')

@section('content')

    <h1>Daftar Surat Pengajuan Warehouse</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- <!-- Notifikasi Surat untuk Warehouse -->
    @if (session('suratKeWarehouse') > 0)
        <div class="alert alert-warning" id="notification-alert">
            Ada {{ session('suratKeWarehouse') }} surat yang masuk untuk divisi Warehouse dengan status Pending.
        </div>
    @endif --}}

    @if(session('statusUpdatedwrh'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <span>{{ session('statusUpdatedwrh') }}</span>
            <form action="{{ route('notif.clearwrh') }}" method="POST" style="margin-left: 10px;">
                @csrf
                <button type="submit" class="btn-close"></button>
            </form>
        </div>
    @endif

        <!-- Year Filter Form -->
        <form action="{{ route('surat.warehouse.filterByYear') }}" method="GET">
            @csrf
            <div class="mb-3">
                <label for="year" class="form-label">Pilih Tahun</label>
                <select name="year" id="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
            </div>
        </form>
          
    
        <!-- Button to delete all data for the selected year, only for superadmin -->
        @if (Auth::user()->role == 'superadmin' && request('year'))
        <form action="{{ route('surat.warehouse.deleteByYear') }}" method="POST" style="margin-bottom: 20px;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data untuk tahun {{ request('year') }}?');">
            @csrf
            <input type="hidden" name="year" value="{{ request('year') }}">
            <button type="submit" class="btn btn-danger">
                Hapus Semua Data Tahun {{ request('year') }}
            </button>
        </form>
        @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('surat.warehouse.create') }}" class="btn btn-primary">Tambah Surat</a>
    </div>

    <!-- Responsive Table -->
    <div class="table-responsive">
        <table border="1" cellpadding="10" class="table table-bordered" style="width: 100%; margin: 0 auto; border-collapse: collapse; text-align: center;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th>No</th>
                    <th>No. Surat</th>
                    <th>Dari Divisi</th>
                    <th>Ke Divisi</th>
                    <th>File Surat</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $divisiMapping = [
                        'FNC' => 'Finance',
                        'PCH' => 'Purchasing',
                        'DM' => 'Digital Marketing',
                        'ADM' => 'Administrasi',
                        'WRH' => 'Warehouse',
                        'IC' => 'Interior Consultant',
                    ];
                @endphp

                @foreach($suratWarehouses as $index => $surat)
                <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $surat->formatted_nomor_surat }}</td>
                    <td>{{ $divisiMapping[$surat->divisi_pembuat] ?? $surat->divisi_pembuat }}</td>
                    <td>{{ $divisiMapping[$surat->divisi_tujuan] ?? $surat->divisi_tujuan }}</td>
                    <td>
                        @if ($surat->file_path)
                            <a href="{{ route('surat.warehouse.download', $surat->id) }}" class="btn btn-success">Download File</a>
                        @else
                            Tidak Ada File
                        @endif
                    </td>

                    <td>
                        <!-- Form untuk update status -->
                        <form action="{{ route('surat.warehouse.updateStatus', $surat->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status_pengajuan" class="form-select">
                                <option value="Pending" {{ $surat->status_pengajuan == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="ACC" {{ $surat->status_pengajuan == 'ACC' ? 'selected' : '' }}>ACC</option>
                                <option value="Tolak" {{ $surat->status_pengajuan == 'Tolak' ? 'selected' : '' }}>Tolak</option>
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Update</button>
                        </form>
                    </td>

                    <td>
                        @if ($surat->file_path)
                            <a href="{{ route('surat.warehouse.view', $surat->id) }}" class="btn btn-primary">View File</a>
                        @endif

                        <a href="{{ route('surat.warehouse.edit', $surat->id) }}" class="btn btn-warning">Edit</a>
                        @if (Auth::user()->role == 'superadmin')
                            <form action="{{ route('surat.warehouse.destroy', $surat->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?');">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
