<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User1;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User1::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'username' => 'required|unique:users1', 
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        User1::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id_user)
    {
        $user = User1::findOrFail($id_user);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id_user)
    {
        $request->validate([
            'nama' => 'required',
            'username' => 'required|unique:users1,username,' . $id_user . ',id_user', 
            'role' => 'required'
        ]);

        $user = User1::findOrFail($id_user);
        $user->update([
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id_user)
    {
        User1::destroy($id_user);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
