@extends('layouts.admin.app')

@section('title', 'Kelola Omset')

@section('content')
    <div class="container-fluid">
        <h2>Data Omset</h2>

        <div class="d-flex justify-content-end">
            <button><a href="{{ route('omsets.rekap') }}" class="btn btn-primary">Rekap Omset</a></button>
        </div>

        <!-- Form untuk pencarian dengan bulan dan tahun -->
        <form action="{{ route('omsets.index') }}" method="GET" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="search" class="form-control" placeholder="Klien..."
                    value="{{ request()->get('search') }}">
                <input type="text" name="no_induk" class="form-control" placeholder="No Induk"
                    value="{{ request()->get('no_induk') }}">
                <select name="bulan" class="form-control">
                    <option value="">Bulan</option>
                    @foreach (range(1, 12) as $bulan)
                        <option value="{{ $bulan }}" {{ request()->get('bulan') == $bulan ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}
                        </option>
                    @endforeach
                </select>

                <!-- Mengubah input tipe number menjadi input tipe text untuk mengetikkan tahun tanpa batasan -->
                <input type="text" name="tahun" class="form-control" placeholder="Tahun"
                    value="{{ request()->get('tahun') }}" id="tahun">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>

        <!-- Tombol untuk download Excel -->
        <a href="{{ route('omsets.export') }}" class="btn btn-success mb-3">Download Excel</a>

        <a href="{{ route('omsets.create') }}" class="btn btn-primary mb-3">Tambah Omset</a>

        <!-- Tabel untuk menampilkan data omset dengan responsif -->
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered" style="font-size: 18px; width: 100%; table-layout: auto;">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 20px;">No</th>
                        <th style="font-size: 20px;">Tanggal</th>
                        <th style="font-size: 20px;">No Induk</th> <!-- Kolom untuk No Induk -->
                        <th style="font-size: 20px;">Klien</th>
                        <th style="font-size: 20px;">Alamat</th>
                        <th style="font-size: 20px;">Project</th>
                        <th style="font-size: 20px;">Sumber Lead</th>
                        <th style="font-size: 20px;">Nominal</th>
                        <th style="font-size: 20px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($omsets as $omset)
                        <tr>
                            <td style="font-size: 18px;">{{ $loop->iteration }}</td> <!-- Menambahkan nomor urut -->
                            <td style="font-size: 18px;">{{ $omset->tanggal }}</td>
                            <td style="font-size: 18px;">{{ $omset->no_induk }}</td>
                            <td style="font-size: 18px;">{{ $omset->nama_klien }}</td>
                            <td style="font-size: 18px;">{{ $omset->alamat }}</td>
                            <td style="font-size: 18px;">{{ $omset->project }}</td>
                            <td style="font-size: 18px;">{{ $omset->sumber_lead }}</td>
                            <td style="font-size: 18px;">Rp {{ number_format($omset->nominal, 2, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('omsets.edit', $omset->id_omset) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('omsets.destroy', $omset->id_omset) }}" method="POST"
                                    class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Yakin hapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
