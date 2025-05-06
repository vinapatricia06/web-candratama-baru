<?php
namespace App\Http\Controllers;

use App\Models\SuratCleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratCleaningController extends Controller
{
    // Menampilkan data surat cleaning
    public function index()
    {
        // Retrieve all surat cleaning records
        $surats = SuratCleaning::all();
        return view('surat.cleaning.index', compact('surats'));
    }

    // Form untuk membuat surat baru
    public function create()
    {
        // Passing the authenticated user's role and name to the view
        $divisi = Auth::user()->role;
        $nama = Auth::user()->nama;
        return view('surat.cleaning.create', compact('nama', 'divisi'));
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
            SuratCleaning::create([
                'nama' => Auth::user()->nama,
                'divisi' => Auth::user()->role,
                'keperluan' => $request->keperluan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending', // Status awal
            ]);

            // Redirect dengan pesan sukses
            return redirect()->route('surat.cleaning.index')->with('success', 'Surat cleaning berhasil dibuat');
        } catch (\Exception $e) {
            // Redirect kembali jika ada kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Form untuk mengedit surat
    public function edit($id)
    {
        // Find the SuratCleaning by its ID
        $surat = SuratCleaning::findOrFail($id);
        return view('surat.cleaning.edit', compact('surat'));
    }

    // Update data surat
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'keperluan' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,png,jpeg,gif|max:2048',
        ]);

        // Find the SuratCleaning record by its ID
        $surat = SuratCleaning::findOrFail($id);
        $filePath = $surat->file_path;

        // If a new file is uploaded, delete the old one and update the file path
        if ($request->hasFile('file_surat')) {
            if ($filePath && Storage::exists('public/' . $filePath)) {
                Storage::delete('public/' . $filePath);
            }
            $filePath = $request->file('file_surat')->store('surat_cleaning_files', 'public');
        }

        // Update the SuratCleaning record
        $surat->update([
            'keperluan' => $request->keperluan,
            'file_path' => $filePath,
        ]);

        // Redirect to index page with success message
        return redirect()->route('surat.cleaning.index')->with('success', 'Surat cleaning berhasil diperbarui');
    }

    // Menghapus surat
    public function destroy($id)
    {
        // Find the SuratCleaning by its ID
        $surat = SuratCleaning::findOrFail($id);
        
        // If the surat has a file, delete it from the storage
        if ($surat->file_path && Storage::exists('public/' . $surat->file_path)) {
            Storage::delete('public/' . $surat->file_path);
        }

        // Delete the SuratCleaning record
        $surat->delete();

        // Redirect to index page with success message
        return redirect()->route('surat.cleaning.index')->with('success', 'Surat cleaning berhasil dihapus');
    }

    // Update status pengajuan
    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()->role === 'cleaning_services') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }

        // Validasi status pengajuan
        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        // Temukan SuratCleaning berdasarkan ID dan update statusnya
        $surat = SuratCleaning::findOrFail($id);
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        // Menambahkan pemberitahuan untuk status yang diupdate
        $statusMessageCS = $this->getStatusMessageCS($request->status_pengajuan); // Pastikan fungsi ini ada

        // Menyimpan pesan notifikasi ke dalam session
        session()->put('status_messageCS', $statusMessageCS);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('surat.cleaning.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }


    private function getStatusMessageCS($status)
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


    // Download file surat
    public function download($id)
    {
        // Temukan SuratCleaning berdasarkan ID
        $surat = SuratCleaning::findOrFail($id);
        $filePath = public_path('storage/' . $surat->file_path);

        // Cek apakah file ada di server
        if (file_exists($filePath)) {
            // Mengambil ekstensi file
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            // Menentukan nama file yang akan diunduh sesuai dengan ekstensi
            $fileName = "SuratCleaning_{$surat->id}." . $extension;

            // Mengirimkan file untuk diunduh
            return response()->download($filePath, $fileName);
        } else {
            // Jika file tidak ditemukan
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }


    // View file surat (PDF)
    public function viewPDF($id)
    {
        // Find the SuratCleaning by its ID
        $suratCleaning = SuratCleaning::find($id);

        // Check if the file exists in storage
        if (!$suratCleaning || !Storage::disk('public')->exists($suratCleaning->file_path)) {
            return redirect()->route('surat.cleaning.index')->withErrors('File tidak ditemukan.');
        }

        // Return the view for displaying the PDF
        return view('surat.cleaning.pdf', compact('suratCleaning'));
    }

    public function destroyAll()
    {
        SuratCleaning::truncate(); // atau ->delete() jika ingin soft delete
        return redirect()->route('surat.cleaning.index')->with('status_message', 'Semua surat berhasil dihapus.');
    }
}
