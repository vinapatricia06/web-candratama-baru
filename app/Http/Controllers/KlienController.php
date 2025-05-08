<?php

namespace App\Http\Controllers;

use App\Models\Klien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KlienImport;

class KlienController extends Controller
{
    
    public function index()
    {
        $kliens = Klien::all();
        return view('klien.index', compact('kliens'));
    }
    public function create()
    {
        return view('klien.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'no_induk' => 'required|string|max:100|unique:kliens',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
        ]);

        Klien::create($request->all());

        return redirect()->route('klien.index')->with('success', 'Klien berhasil ditambahkan.');
    }
    public function edit(string $id)
    {
        $klien = Klien::findOrFail($id);
        return view('klien.edit', compact('klien'));
    }
    public function update(Request $request, string $id)
    {
        $klien = Klien::findOrFail($id);

        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'no_induk' => 'required|string|max:100|unique:kliens,no_induk,' . $klien->id,
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
        ]);

        $klien->update($request->all());

        return redirect()->route('klien.index')->with('success', 'Klien berhasil diperbarui.');
    }
    public function destroy(string $id)
    {
        $klien = Klien::findOrFail($id);
        $klien->delete();

        return redirect()->route('klien.index')->with('success', 'Klien berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        Excel::import(new KlienImport, $request->file('file'));

        return redirect()->route('klien.index')->with('success', 'Klien berhasil diimpor!');
    }
}
