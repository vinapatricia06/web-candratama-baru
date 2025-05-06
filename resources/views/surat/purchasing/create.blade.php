@extends('layouts.admin.app')

@section('title', 'Kelola Surat Purchasing')
@section('content')

<h1>Surat Pengajuan Purchasing</h1>

<!-- Form Pengajuan -->
<form action="{{ route('surat.purchasing.generate') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Jenis Surat</label>
        <select name="jenis_surat" class="form-control" required>
            <option value="SP">Surat Pengajuan</option>
            <option value="SPOB">Surat Pengajuan Order Barang</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Dari Divisi</label>
        <select name="divisi_pembuat" class="form-control" required>
            <option value="PCH">Purchasing</option> <!-- Hanya Purchasing -->
        </select>
    </div>

    <div class="mb-3">
        <label>Ke Divisi</label>
        <select name="divisi_tujuan" class="form-control" required>
            <option value="FNC">Finance</option>
            <option value="DM">Digital Marketing</option>
            <option value="ADM">Administrasi</option>
            <option value="WRH">Warehouse</option>
            <option value="IC">Interior Consultant</option>
        </select>
    </div>

    <!-- Upload File -->
    <div class="mb-3">
        <label>Upload File PDF:</label>
        <input 
            type="file" 
            name="file_surat" 
            class="form-control" 
            accept=".pdf" 
            required>
    </div>

    <button type="submit" class="btn btn-primary">Generate Surat</button>
</form>

@endsection
