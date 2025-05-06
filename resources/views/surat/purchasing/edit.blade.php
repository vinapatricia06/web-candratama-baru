@extends('layouts.admin.app')

@section('title', 'Kelola Surat Purchasing')
@section('content')
    <h1>Edit Surat Purchasing</h1>

    @php
        // Mapping divisi untuk efisiensi
        $divisiMapping = [
            'FNC' => 'Finance',
            'DM' => 'Digital Marketing',
            'ADM' => 'Administrasi',
            'WRH' => 'Warehouse',
            'IC' => 'Interior Consultant',
        ];
    @endphp

    <form action="{{ route('surat.purchasing.update', $suratPurchasing->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nomor_surat" class="form-label">Nomor Surat</label>
            <input type="text" id="nomor_surat" class="form-control" value="{{ $suratPurchasing->formatted_nomor_surat }}" disabled>
        </div>

        <div class="mb-3">
            <label for="jenis_surat" class="form-label">Jenis Surat</label>
            <select name="jenis_surat" id="jenis_surat" class="form-control" required>
                <option value="SPOB" {{ $suratPurchasing->jenis_surat == 'SPOB' ? 'selected' : '' }}>Surat Pengajuan Order Barang (SPOB)</option>
                <option value="SP" {{ $suratPurchasing->jenis_surat == 'SP' ? 'selected' : '' }}>Surat Pengajuan (SP)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="divisi_pembuat" class="form-label">Dari Divisi</label>
            <select name="divisi_pembuat" id="divisi_pembuat" class="form-control" required>
                <option value="PCH" selected>Purchasing</option> <!-- Hanya Purchasing -->
            </select>
        </div>

        <div class="mb-3">
            <label for="divisi_tujuan" class="form-label">Ke Divisi</label>
            <select name="divisi_tujuan" id="divisi_tujuan" class="form-control" required>
                @foreach ($divisiMapping as $kode => $nama)
                    <option value="{{ $kode }}" {{ $suratPurchasing->divisi_tujuan === $kode ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="file_surat" class="form-label">File Surat (opsional)</label>
            <input type="file" name="file_surat" id="file_surat" class="form-control">
            @if ($suratPurchasing->file_path)
                <small>File saat ini: <a href="{{ route('surat.purchasing.view', $suratPurchasing->id) }}">{{ basename($suratPurchasing->file_path) }}</a></small>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
