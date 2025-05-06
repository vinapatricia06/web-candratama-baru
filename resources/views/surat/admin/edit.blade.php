@extends('layouts.admin.app')

@section('title', 'Kelola Surat Admin')
@section('content')
    <h1>Edit Surat</h1>

    @php
        // Mapping divisi untuk efisiensi
        $divisiMapping = [
            'FNC' => 'Finance',
            'PCH' => 'Purchasing',
            'DM' => 'Digital Marketing',
            'ADM' => 'Administrasi',
            'WRH' => 'Warehouse',
            'IC' => 'Interior Consultant',
        ];
    @endphp

    <form action="{{ route('surat.admin.update', $suratAdmin->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nomor_surat" class="form-label">Nomor Surat</label>
            <input type="text" id="nomor_surat" class="form-control" value="{{ $suratAdmin->formatted_nomor_surat }}" disabled>
        </div>

        <div class="mb-3">
            <label for="jenis_surat" class="form-label">Jenis Surat</label>
            <select name="jenis_surat" id="jenis_surat" class="form-control" required>
                <option value="SPOB" {{ $suratAdmin->jenis_surat == 'SPOB' ? 'selected' : '' }}>Surat Pengajuan Order Barang (SPOB)</option>
                <option value="SP" {{ $suratAdmin->jenis_surat == 'SP' ? 'selected' : '' }}>Surat Pengajuan (SP)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="divisi_pembuat" class="form-label">Dari Divisi</label>
            <select name="divisi_pembuat" id="divisi_pembuat" class="form-control" required>
                @foreach ($divisiMapping as $kode => $nama)
                    <option value="{{ $kode }}" {{ $suratAdmin->divisi_pembuat === $kode ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="divisi_tujuan" class="form-label">Ke Divisi</label>
            <select name="divisi_tujuan" id="divisi_tujuan" class="form-control" required>
                @foreach ($divisiMapping as $kode => $nama)
                    <option value="{{ $kode }}" {{ $suratAdmin->divisi_tujuan === $kode ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="file_surat" class="form-label">File Surat (opsional)</label>
            <input type="file" name="file_surat" id="file_surat" class="form-control">
            @if ($suratAdmin->file_path)
                <small>File saat ini: <a href="{{ route('surat.admin.viewPDF', $suratAdmin->id) }}">{{ basename($suratAdmin->file_path) }}</a></small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
