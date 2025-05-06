<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratAdmin;
use App\Models\SuratCleaning;
use App\Models\SuratInteriorConsultan;
use App\Models\SuratEkspedisi;
use Illuminate\Support\Facades\Storage;

class SuratAdminController extends Controller
{
    public function index()
    {
        $nomorSurat = null;
        $suratAdmins = SuratAdmin::orderBy('created_at', 'desc')->get();
        $years = SuratAdmin::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');
        
        
        return view('surat.admin.index', compact('nomorSurat', 'suratAdmins', 'years'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'divisi_pembuat' => 'required',
            'divisi_tujuan' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        try {
            $jenis_surat = strtoupper($request->jenis_surat);
            $divisi_pembuat = strtoupper($request->divisi_pembuat);
            $divisi_tujuan = strtoupper($request->divisi_tujuan);

            $bulan = date('n');
            $tahun = date('Y');
            $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $bulan_romawi = $romawi[$bulan];

            $filePath = $request->hasFile('file_surat')
                ? $request->file('file_surat')->store('uploads', 'public')
                : 'uploads/default.pdf';

            $suratAdmin = SuratAdmin::create([
                'jenis_surat' => $jenis_surat,
                'divisi_pembuat' => $divisi_pembuat,
                'divisi_tujuan' => $divisi_tujuan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending',
            ]);

            $id_surat = str_pad($suratAdmin->id, 3, '0', STR_PAD_LEFT);
            $nomorSurat = "{$jenis_surat}/{$id_surat}/{$divisi_pembuat}-{$divisi_tujuan}/{$bulan_romawi}/{$tahun}";

            $suratAdmin->update(['nomor_surat' => $nomorSurat]);

            session(['nomorSurat' => $nomorSurat]);

            return redirect()->route('surat.admin.index')->with('success', 'Surat berhasil di tambahkan !');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadfile($id)
    {
        $surat = SuratAdmin::findOrFail($id);
        $filePath = public_path('storage/' . $surat->file_path);
        if (file_exists($filePath)) {
            $fileName = "SuratAdmin_{$surat->id}_{$surat->jenis_surat}.pdf";
            return response()->download($filePath, $fileName);
        } else {
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }

    public function updateStatusPengajuan(Request $request, $id)
    {
        if (auth()->user()->role === 'admin') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }

        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        $surat = SuratAdmin::findOrFail($id);
        $oldStatus = $surat->status_pengajuan;
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        $nomorSurat = $surat->formatted_nomor_surat;


        if (in_array($surat->status_pengajuan, ['ACC', 'Tolak']) && $oldStatus !== $surat->status_pengajuan) {
            session(['statusUpdatedAdmin' => "Surat dengan Nomor {$nomorSurat} telah di {$surat->status_pengajuan}"]);
        }
        
        

        // Cek ulang jumlah surat Pending di kategori terkait
        $pendingEkspedisi = SuratEkspedisi::where('status_pengajuan', 'Pending')->count();
        $pendingCleaning = SuratCleaning::where('status_pengajuan', 'Pending')->count();
        $pendingInterior = SuratInteriorConsultan::where('status_pengajuan', 'Pending')->count();

        // Hapus notifikasi hanya jika kategori tersebut tidak memiliki surat Pending lagi
        if ($pendingEkspedisi == 0) session()->forget('suratEkspedisi');
        if ($pendingCleaning == 0) session()->forget('suratCleaning');
        if ($pendingInterior == 0) session()->forget('suratInteriorConsultan');

        return redirect()->route('surat.admin.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }



    public function viewPDF($id)
    {
        $suratAdmin = SuratAdmin::find($id);

        if (!$suratAdmin || !Storage::disk('public')->exists($suratAdmin->file_path)) {
            return redirect()->route('surat.admin.index')->withErrors('File tidak ditemukan.');
        }

        return view('surat.admin.pdf', compact('suratAdmin'));
    }

    public function edit($id)
    {
        $suratAdmin = SuratAdmin::findOrFail($id);
        return view('surat.admin.edit', compact('suratAdmin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'divisi_pembuat' => 'required',
            'divisi_tujuan' => 'required',
            'file_surat' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        $suratAdmin = SuratAdmin::findOrFail($id);

        if ($request->hasFile('file_surat')) {
            if ($suratAdmin->file_path && file_exists(storage_path('app/' . $suratAdmin->file_path))) {
                unlink(storage_path('app/' . $suratAdmin->file_path));
            }

            $filePath = $request->file('file_surat')->store('surat_files');
            $suratAdmin->file_path = $filePath;
        }

        $suratAdmin->jenis_surat = $request->jenis_surat;
        $suratAdmin->divisi_pembuat = $request->divisi_pembuat;
        $suratAdmin->divisi_tujuan = $request->divisi_tujuan;
        $suratAdmin->save();

        return redirect()->route('surat.admin.index')->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $suratAdmin = SuratAdmin::findOrFail($id);
        $suratAdmin->delete();

        return redirect()->route('surat.admin.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function create()
    {
        return view('surat.admin.create');
    }

    public function dashboard()
    {
        $pending = SuratAdmin::where('status_pengajuan', 'Pending')->count();
        $acc = SuratAdmin::where('status_pengajuan', 'ACC')->count();
        $tolak = SuratAdmin::where('status_pengajuan', 'Tolak')->count();

        $divisi_pembuat = SuratAdmin::distinct()->pluck('divisi_pembuat');

        

        // Cek jumlah surat yang masih Pending, bukan hanya yang baru dibuat
        $suratEksp = SuratEkspedisi::where('status_pengajuan', 'Pending')->count();
        $suratIC = SuratCleaning::where('status_pengajuan', 'Pending')->count();
        $suratCS = SuratInteriorConsultan::where('status_pengajuan', 'Pending')->count();

        // Simpan ke session jika masih ada surat Pending
        session(['suratEkspedisi' => $suratEksp > 0 ? $suratEksp : null]);
        session(['suratCleaning' => $suratIC > 0 ? $suratIC : null]);
        session(['suratInteriorConsultan' => $suratCS > 0 ? $suratCS : null]);

        $monthlyCounts = SuratAdmin::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('count', 'month');

        return view('surat.admin.dashboard', [
            'pending' => $pending,
            'acc' => $acc,
            'tolak' => $tolak,
            'months' => $monthlyCounts->keys(),
            'monthlyCounts' => $monthlyCounts->values(),
            'divisi_pembuat' => $divisi_pembuat,
            'suratEksp' => $suratEksp,
            'suratCS' => $suratCS,
            'suratIC' => $suratIC
        ]);
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('selected_surat');

        if ($ids) {
            SuratAdmin::whereIn('id', $ids)->delete();
            return redirect()->route('surat.admin.index')->with('success', 'Surat yang dipilih berhasil dihapus.');
        } else {
            return redirect()->route('surat.admin.index')->with('error', 'Tidak ada surat yang dipilih.');
        }
    }

    public function filterByYear(Request $request)
    {
        // Ambil tahun yang dipilih dari parameter query string (URL)
        $year = $request->input('year');
        
        // Ambil data SuratMarketing berdasarkan tahun yang dipilih
        $suratAdmins = SuratAdmin::when($year, function ($query, $year) {
            return $query->whereYear('created_at', $year);
        })->get();

        // Ambil daftar tahun yang tersedia (distinct) dari data SuratMarketing
        $years = SuratAdmin::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');

        // Kirim data surat dan daftar tahun ke view
        return view('surat.admin.index', compact('suratAdmins', 'years'));
    }

    

    public function deleteByYear(Request $request)
    {
        $year = $request->input('year');

        if (!$year || !is_numeric($year)) {
            return redirect()->back()->with('error', 'Tahun tidak valid!');
        }

        $count = SuratAdmin::whereYear('created_at', $year)->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data untuk tahun ' . $year);
        }

        SuratAdmin::whereYear('created_at', $year)->delete();

        return redirect()->route('surat.admin.index')->with('success', 'Data untuk tahun ' . $year . ' telah dihapus');
    }




}
