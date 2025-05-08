@extends('layouts.admin.app')

@section('title', 'Kelola Klien')
@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-start align-items-center gap-2 mb-3">
                        <a href="{{ route('klien.create') }}" class="btn btn-primary">Tambah Klien</a>
                        
                        <!-- Form Upload Excel -->
                        <form action="{{ route('klien.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                            @csrf
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <button type="submit" class="btn btn-success">Impor Excel</button>
                        </form>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-body">
                    <table id="klienTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Klien</th>
                                <th>No Induk</th>
                                <th>Alamat</th>
                                <th>No HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kliens as $index => $klien)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $klien->nama_klien }}</td>
                                    <td>{{ $klien->no_induk }}</td>
                                    <td>{{ $klien->alamat }}</td>
                                    <td>{{ $klien->no_hp }}</td>
                                    <td>
                                        <a href="{{ route('klien.edit', $klien->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('klien.destroy', $klien->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data klien.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
  $(function() {
      $("#klienTable").DataTable({
          "responsive": true,
          "lengthChange": true,
          "autoWidth": false,
      }).buttons().container().appendTo('#klienTable_wrapper .col-md-6:eq(0)');
  });
</script>
@endsection
