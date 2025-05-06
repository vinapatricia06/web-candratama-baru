<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratEkspedisi;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;

class SuratEkspedisiController extends Controller
{
    // Menampilkan data
    public function index()
    {
        $surats = SuratEkspedisi::all();
        return view('surat.ekspedisi.index', compact('surats'));
    }

    // Form untuk membuat surat baru
    public function create()
    {
        $divisi = Auth::user()->role;  // Mengambil divisi dari role user yang login
        $nama = Auth::user()->nama;    // Mengambil nama dari user yang login
        return view('surat.ekspedisi.create', compact('nama', 'divisi'));
    }

    // Menyimpan data surat
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'keperluan' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,png,jpeg,gif|max:2048', // Validasi PDF dan gambar
        ]);

        // Variabel untuk menyimpan path file
        $filePath = null;

        // Proses upload file jika ada
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Menggunakan nama file unik
            $filePath = $file->storeAs('surat_cleaning_files', $fileName, 'public');
        }

        try {
            // Membuat record SuratCleaning baru
            SuratEkspedisi::create([
                'nama' => Auth::user()->nama,
                'divisi' => Auth::user()->role,
                'keperluan' => $request->keperluan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending', // Status awal
            ]);

            // Redirect dengan pesan sukses
            return redirect()->route('surat.ekspedisi.index')->with('success', 'Surat Ekspedisi berhasil dibuat');
        } catch (\Exception $e) {
            // Redirect kembali jika ada kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Form untuk mengedit surat
    public function edit($id)
    {
        $surat = SuratEkspedisi::findOrFail($id);
        return view('surat.ekspedisi.edit', compact('surat'));
    }

    // Update data surat
    public function update(Request $request, $id)
    {
        $request->validate([
            'keperluan' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,png,jpeg,gif|max:2048',
        ]);

        $surat = SuratEkspedisi::findOrFail($id);
        $filePath = $surat->file_path;

        if ($request->hasFile('file_surat')) {
            // Menghapus file lama jika ada
            if ($filePath && Storage::exists('public/' . $filePath)) {
                Storage::delete('public/' . $filePath);
            }
            $filePath = $request->file('file_surat')->store('surat.ekspedisi_files', 'public');
        }

        $surat->update([
            'keperluan' => $request->keperluan,
            'file_path' => $filePath,
        ]);

        return redirect()->route('surat.ekspedisi.index')->with('success', 'Surat ekspedisi berhasil diperbarui');
    }

    // Menghapus surat
    public function destroy($id)
    {
        $surat = SuratEkspedisi::findOrFail($id);
        if ($surat->file_path && Storage::exists('public/' . $surat->file_path)) {
            Storage::delete('public/' . $surat->file_path);
        }
        $surat->delete();

        return redirect()->route('surat.ekspedisi.index')->with('success', 'Surat ekspedisi berhasil dihapus');
    }

    public function updateStatusPengajuan(Request $request, $id)
    {
        // Pastikan pengguna yang memiliki role 'ekspedisi' tidak bisa mengubah status
        if (auth()->user()->role === 'ekspedisi') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }

        // Validasi status pengajuan
        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        // Temukan SuratEkspedisi berdasarkan ID dan update statusnya
        $surat = SuratEkspedisi::findOrFail($id);
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        // Menambahkan pemberitahuan untuk status yang diupdate
        $statusMessageEKP = $this->getStatusMessageEKP($request->status_pengajuan);

        // Menyimpan pesan notifikasi hanya untuk satu kali request
        session()->put('status_messageEKP', $statusMessageEKP);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('surat.ekspedisi.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }


    private function getStatusMessageEKP($status)
    {
        switch ($status) {
            case 'ACC':
                return 'Surat telah disetujui.';
            case 'Tolak':
                return 'Surat telah ditolak.';
            case 'Pending':
            default:
                return 'Status pengajuan kembali ke Pending.';
        }
    }

    public function downloadfile($id)
    {
        // Temukan SuratCleaning berdasarkan ID
        $surat = SuratEkspedisi::findOrFail($id);
        $filePath = public_path('storage/' . $surat->file_path);

        // Cek apakah file ada di server
        if (file_exists($filePath)) {
            // Mengambil ekstensi file
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            // Menentukan nama file yang akan diunduh sesuai dengan ekstensi
            $fileName = "SuratEkspedisi_{$surat->id}." . $extension;

            // Mengirimkan file untuk diunduh
            return response()->download($filePath, $fileName);
        } else {
            // Jika file tidak ditemukan
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }

    public function viewPDF($id)
    {
        $suratEkspedisi = SuratEkspedisi::find($id);

        if (!$suratEkspedisi || !Storage::disk('public')->exists($suratEkspedisi->file_path)) {
            return redirect()->route('surat.ekspedisi.index')->withErrors('File tidak ditemukan.');
        }

        return view('surat.ekspedisi.pdf', compact('suratEkspedisi'));
    }

    public function destroyAll()
    {
        SuratEkspedisi::truncate(); // atau ->delete() jika ingin soft delete
        return redirect()->route('surat.ekspedisi.index')->with('status_message', 'Semua surat berhasil dihapus.');
    }
}
