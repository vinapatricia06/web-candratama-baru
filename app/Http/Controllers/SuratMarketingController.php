<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMarketing; // Model untuk surat_marketing
use Illuminate\Support\Facades\Storage;

class SuratMarketingController extends Controller
{
    

    public function index()
    {
        $nomorSurat = null;

        $suratMarketings = SuratMarketing::all();

        $years = SuratMarketing::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');

        return view('surat.digital_marketing.index', compact('nomorSurat', 'suratMarketings', 'years'));
    }




    public function generate(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'divisi_pembuat' => 'required',
            'divisi_tujuan' => 'required',
            'file_surat' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:2048',
        ]);

        try {
            $jenis_surat = strtoupper($request->jenis_surat);
            $divisi_pembuat = strtoupper($request->divisi_pembuat);
            $divisi_tujuan = strtoupper($request->divisi_tujuan);

            $bulan = date('n');
            $tahun = date('Y');
            $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $bulan_romawi = $romawi[$bulan - 1];

            $filePath = $request->hasFile('file_surat')
                ? $request->file('file_surat')->store('uploads', 'public')
                : 'uploads/default.pdf';

            $suratMarketing = SuratMarketing::create([
                'jenis_surat' => $jenis_surat,
                'divisi_pembuat' => $divisi_pembuat,
                'divisi_tujuan' => $divisi_tujuan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending',
            ]);

            $id_surat = str_pad($suratMarketing->id, 3, '0', STR_PAD_LEFT);
            $nomorSurat = "{$jenis_surat}/{$id_surat}/{$divisi_pembuat}-{$divisi_tujuan}/{$bulan_romawi}/{$tahun}";

            $suratMarketing->update(['nomor_surat' => $nomorSurat]);

            session(['nomorSurat' => $nomorSurat]);

            return redirect()->route('surat.digital_marketing.list')->with([
                'success' => 'Surat berhasil Tambahkan!',
                'id_suratMarketing' => $suratMarketing->id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function list()
    {
        $suratMarketings = SuratMarketing::orderBy('created_at', 'desc')->get();

        $years = SuratMarketing::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');

        return view('surat.digital_marketing.index', compact('suratMarketings', 'years'));
        
    }

    

    public function downloadfile($id)
    {
        $surat = SuratMarketing::findOrFail($id);

        $filePath = public_path('storage/' . $surat->file_path);

        // Mengecek apakah file ada
        if (file_exists($filePath)) {
            // Membuat nama file kustom berdasarkan ID surat
            $fileName = "SuratMarketing_{$surat->id}_{$surat->jenis_surat}.pdf"; // Sesuaikan ekstensi file jika diperlukan

            // Mengunduh file dengan nama yang sudah diatur
            return response()->download($filePath, $fileName);
        } else {
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }



    public function updateStatusPengajuan(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        // Cek apakah yang mengupdate adalah marketing
        if (auth()->user()->role === 'marketing') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }

        // Temukan surat berdasarkan ID
        $surat = SuratMarketing::findOrFail($id);
        $oldStatus = $surat->status_pengajuan;
        
        // Update status pengajuan
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        // Ambil nomor surat dari accessor
        $nomorSurat = $surat->formatted_nomor_surat;

        // Cek apakah status berubah menjadi ACC atau Tolak
        if (in_array($surat->status_pengajuan, ['ACC', 'Tolak']) && $oldStatus !== $surat->status_pengajuan) {
            // Simpan pemberitahuan bahwa status surat telah berubah
            session()->put('statusUpdated', "Surat dengan Nomor {$nomorSurat} telah di {$surat->status_pengajuan}");
        }

        // Hapus notifikasi jika surat tujuan ke DM telah diubah statusnya
        if ($surat->divisi_tujuan == 'DM' && $surat->status_pengajuan != 'Pending') {
            session()->forget('suratKeDM');
        }

        // Hapus pemberitahuan terkait status yang telah di-update
        if ($oldStatus != 'Pending' && in_array($oldStatus, ['ACC', 'Tolak'])) {
            session()->forget('statusUpdated'); // Menghapus session statusUpdated setelah status diupdate
        }

        return redirect()->route('surat.digital_marketing.list')->with('success', 'Status pengajuan berhasil diperbarui.');
    }



    public function viewPDF($id)
    {
        $suratMarketing = SuratMarketing::find($id);

        if (!$suratMarketing || !Storage::disk('public')->exists($suratMarketing->file_path)) {
            return redirect()->route('surat.digital_marketing.index')->withErrors('File tidak ditemukan.');
        }

        return view('surat.digital_marketing.pdf', compact('suratMarketing'));
    }

    public function edit($id)
    {
        $suratMarketing = SuratMarketing::findOrFail($id);
        return view('surat.digital_marketing.edit', compact('suratMarketing'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'divisi_pembuat' => 'required',
            'divisi_tujuan' => 'required',
            'file_surat' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        $suratMarketing = SuratMarketing::findOrFail($id);

        if ($request->hasFile('file_surat')) {
            if ($suratMarketing->file_path && file_exists(storage_path('app/' . $suratMarketing->file_path))) {
                unlink(storage_path('app/' . $suratMarketing->file_path));
            }

            $filePath = $request->file('file_surat')->store('surat_files');
            $suratMarketing->file_path = $filePath;
        }

        $suratMarketing->jenis_surat = $request->jenis_surat;
        $suratMarketing->divisi_pembuat = $request->divisi_pembuat;
        $suratMarketing->divisi_tujuan = $request->divisi_tujuan;
        $suratMarketing->save();

        return redirect()->route('surat.digital_marketing.list')->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $suratMarketing = SuratMarketing::findOrFail($id);
        $suratMarketing->delete();

        return redirect()->route('surat.digital_marketing.list')->with('success', 'Surat berhasil dihapus.');
    }

    public function create()
    {
        return view('surat.digital_marketing.create');
    }

    public function dashboard()
    {
        $pending = SuratMarketing::where('status_pengajuan', 'Pending')->count();
        $acc = SuratMarketing::where('status_pengajuan', 'ACC')->count();
        $tolak = SuratMarketing::where('status_pengajuan', 'Tolak')->count();

        $divisi_pembuat = SuratMarketing::distinct()->pluck('divisi_pembuat');

        // Menghitung surat yang divisi tujuannya ke DM
        $suratKeDM = SuratMarketing::where('divisi_tujuan', 'DM')->where('status_pengajuan', 'Pending')->count();

        // Menyimpan informasi surat ke DM di sesi jika ada
        if ($suratKeDM > 0) {
            session(['suratKeDM' => $suratKeDM]);
        }

        $monthlyCounts = SuratMarketing::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('count', 'month');

        return view('surat.digital_marketing.dashboard', [
            'pending' => $pending,
            'acc' => $acc,
            'tolak' => $tolak,
            'months' => $monthlyCounts->keys(),
            'monthlyCounts' => $monthlyCounts->values(),
            'suratKeDM' => $suratKeDM,
            'divisi_pembuat' => $divisi_pembuat // Pastikan variabel ini dikirimkan ke view
        ]);
    }


    public function filterByYear(Request $request)
    {
        // Ambil tahun yang dipilih dari parameter query string (URL)
        $year = $request->input('year');
        
        // Ambil data SuratMarketing berdasarkan tahun yang dipilih
        $suratMarketings = SuratMarketing::when($year, function ($query, $year) {
            return $query->whereYear('created_at', $year);
        })->get();

        // Ambil daftar tahun yang tersedia (distinct) dari data SuratMarketing
        $years = SuratMarketing::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');

        // Kirim data surat dan daftar tahun ke view
        return view('surat.digital_marketing.index', compact('suratMarketings', 'years'));
    }

    

    public function deleteByYear(Request $request)
    {
        $year = $request->input('year');

        if (!$year || !is_numeric($year)) {
            return redirect()->back()->with('error', 'Tahun tidak valid!');
        }

        $count = SuratMarketing::whereYear('created_at', $year)->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data untuk tahun ' . $year);
        }

        SuratMarketing::whereYear('created_at', $year)->delete();

        return redirect()->route('surat.digital_marketing.list')->with('success', 'Data untuk tahun ' . $year . ' telah dihapus');
    }


    

}
