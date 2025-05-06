@extends('layouts.admin.app')

@section('title', 'Kelola User')

@section('content')
<div class="container-fluid">
    <h2>Daftar User</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Tambah User</a>

    <!-- Tabel untuk menampilkan daftar user dengan responsif -->
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered" style="font-size: 18px; width: 100%; table-layout: auto;">
            <thead class="table-light">
                <tr>
                    <th style="font-size: 20px;">ID</th>
                    <th style="font-size: 20px;">Nama</th>
                    <th style="font-size: 20px;">Username</th>
                    <th style="font-size: 20px;">Role</th>
                    <th style="font-size: 20px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td style="font-size: 18px;">{{ $user->id_user }}</td>
                    <td style="font-size: 18px;">{{ $user->nama }}</td>
                    <td style="font-size: 18px;">{{ $user->username }}</td>
                    <td style="font-size: 18px;">{{ $user->role }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id_user) }}" class="btn btn-warning btn-sm" style="font-size: 18px; padding: 8px 16px;">Edit</a>
                        <form action="{{ route('users.destroy', $user->id_user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="font-size: 18px; padding: 8px 16px;" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
