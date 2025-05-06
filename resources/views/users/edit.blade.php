@extends('layouts.admin.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <h2>Edit User</h2>
    <form action="{{ route('users.update', $user->id_user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
        </div>
        <div class="mb-3">
            <label>Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="CEO" {{ $user->role == 'superadmin' ? 'selected' : '' }}>CEO</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="marketing" {{ $user->role == 'marketing' ? 'selected' : '' }}>Marketing</option>
                <option value="interior_consultan" {{ $user->role == 'interior_consultan' ? 'selected' : '' }}>Interior_Consultan</option>
                <option value="warehouse" {{ $user->role == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                <option value="finance" {{ $user->role == 'finance' ? 'selected' : '' }}>Finance</option>
                <option value="project_production" {{ $user->role == 'project_production' ? 'selected' : '' }}>Project_Production</option>
                <option value="teknisi" {{ $user->role == 'ekspedisi' ? 'selected' : '' }}>Ekspedisi</option>
                <option value="teknisi" {{ $user->role == 'cleaning_services' ? 'selected' : '' }}>Cleaning Services</option>
                <option value="teknisi" {{ $user->role == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
            </select>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-danger mr-2">Kembali</a>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
