<?php
namespace App\Http\Controllers;

use App\Models\SuratInteriorConsultan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratInteriorConsultanController extends Controller
{
    public function index()
    {
        $surats = SuratInteriorConsultan::all();
        return view('surat.interior_consultan.index', compact('surats'));
    }

    public function create()
    {
        $divisi = Auth::user()->role;
        $nama = Auth::user()->nama;
        return view('surat.interior_consultan.create', compact('nama', 'divisi'));
    }

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
            SuratInteriorConsultan::create([
                'nama' => Auth::user()->nama,
                'divisi' => Auth::user()->role,
                'keperluan' => $request->keperluan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending', // Status awal
            ]);

            // Redirect dengan pesan sukses
            return redirect()->route('surat.interior_consultan.index')->with('success', 'Surat cleaning berhasil dibuat');
        } catch (\Exception $e) {
            // Redirect kembali jika ada kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $surat = SuratInteriorConsultan::findOrFail($id);
        return view('surat.interior_consultan.edit', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keperluan' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,png,jpeg,gif|max:2048',
        ]);

        $surat = SuratInteriorConsultan::findOrFail($id);
        $filePath = $surat->file_path;

        if ($request->hasFile('file_surat')) {
            if ($filePath && Storage::exists('public/' . $filePath)) {
                Storage::delete('public/' . $filePath);
            }
            $filePath = $request->file('file_surat')->store('surat_konsultasi_interior_files', 'public');
        }

        $surat->update([
            'keperluan' => $request->keperluan,
            'file_path' => $filePath,
        ]);

        return redirect()->route('surat.interior_consultan.index')->with('success', 'Surat konsultasi interior berhasil diperbarui');
    }

    public function destroy($id)
    {
        $surat = SuratInteriorConsultan::findOrFail($id);
        if ($surat->file_path && Storage::exists('public/' . $surat->file_path)) {
            Storage::delete('public/' . $surat->file_path);
        }
        $surat->delete();
        if (auth()->user()->role === 'interior_consultan') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }

        return redirect()->route('surat.interior_consultan.index')->with('success', 'Surat konsultasi interior berhasil dihapus');
    }

    public function updateStatusPengajuan(Request $request, $id)
    {
        if (auth()->user()->role === 'interior_consultan') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }
        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        $surat = SuratInteriorConsultan::findOrFail($id);
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        // Menambahkan pemberitahuan untuk status yang diupdate
        $statusMessage = $this->getStatusMessage($request->status_pengajuan);

        // Menyimpan pesan notifikasi hanya untuk satu kali request
        session()->put('status_message', $statusMessage);

        return redirect()->route('surat.interior_consultan.index');
    }

    private function getStatusMessage($status)
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
        $surat = SuratInteriorConsultan::findOrFail($id);
        $filePath = public_path('storage/' . $surat->file_path);

        // Cek apakah file ada di server
        if (file_exists($filePath)) {
            // Mengambil ekstensi file
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            // Menentukan nama file yang akan diunduh sesuai dengan ekstensi
            $fileName = "Suratinterior_consultan_{$surat->id}." . $extension;

            // Mengirimkan file untuk diunduh
            return response()->download($filePath, $fileName);
        } else {
            // Jika file tidak ditemukan
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }

    public function viewPDF($id)
    {
        $suratKonsultasiInterior = SuratInteriorConsultan::find($id);

        if (!$suratKonsultasiInterior || !Storage::disk('public')->exists($suratKonsultasiInterior->file_path)) {
            return redirect()->route('surat.interior_consultan.index')->withErrors('File tidak ditemukan.');
        }

        return view('surat.interior_consultan.pdf', compact('suratKonsultasiInterior'));
    }

    public function destroyAll()
    {
        SuratInteriorConsultan::truncate(); // atau ->delete() jika ingin soft delete
        return redirect()->route('surat.interior_consultan.index')->with('status_message', 'Semua surat berhasil dihapus.');
    }

}
