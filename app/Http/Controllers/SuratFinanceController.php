<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMarketing;
use App\Models\SuratFinance;
use App\Models\SuratWarehouse;
use App\Models\SuratPurchasing;
use App\Models\SuratAdmin;
use Illuminate\Support\Facades\Storage;

class SuratFinanceController extends Controller
{
    public function index()
    {
        $nomorSurat = null; // Awalnya kosong
        $suratFinances = SuratFinance::orderBy('created_at', 'desc')->get();
        $years = SuratFinance::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');

        
        return view('surat.finance.index', compact('nomorSurat', 'suratFinances', 'years'));
    }

    public function pending()
    {
        // Pastikan hanya mengambil surat dengan status 'Pending'
        $suratFinances = SuratFinance::where('status_pengajuan', 'Pending')->get();

        // Pass the filtered data to the view
        return view('auth.indexDir', compact('suratFinances'));
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

            $suratFinance = SuratFinance::create([
                'jenis_surat' => $jenis_surat,
                'divisi_pembuat' => $divisi_pembuat,
                'divisi_tujuan' => $divisi_tujuan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending',
            ]);

            $id_surat = str_pad($suratFinance->id, 3, '0', STR_PAD_LEFT);
            $nomorSurat = "{$jenis_surat}/{$id_surat}/{$divisi_pembuat}-{$divisi_tujuan}/{$bulan_romawi}/{$tahun}";

            $suratFinance->update(['nomor_surat' => $nomorSurat]);

            session(['nomorSurat' => $nomorSurat]);

            return redirect()->route('surat.finance.index')->with([
                'success' => 'Nomor surat berhasil di-generate!',
                'id_suratFinance' => $suratFinance->id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function list()
    {
        $suratFinances = SuratFinance::orderBy('created_at', 'desc')->get();
        return view('surat.finance.index', compact('suratFinances'));
    }

    public function downloadfile($id)
    {
        $surat = SuratFinance::findOrFail($id);

        $filePath = public_path('storage/' . $surat->file_path);

        // Mengecek apakah file ada
        if (file_exists($filePath)) {
            // Membuat nama file kustom berdasarkan ID surat
            $fileName = "SuratFinance_{$surat->id}_{$surat->jenis_surat}.pdf"; // Sesuaikan ekstensi file jika diperlukan

            // Mengunduh file dengan nama yang sudah diatur
            return response()->download($filePath, $fileName);
        } else {
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }

    public function updateStatusPengajuan(Request $request, $id)
    {
        if (auth()->user()->role === 'finance') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }
        
        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        $surat = SuratFinance::findOrFail($id);
        $oldStatus = $surat->status_pengajuan;
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        $nomorSurat = $surat->formatted_nomor_surat; // Ambil nomor surat dari accessor

        
        // Cek apakah status berubah menjadi ACC atau Tolak
        if (in_array($surat->status_pengajuan, ['ACC', 'Tolak']) && $oldStatus !== $surat->status_pengajuan) {
            session()->put('statusUpdated', "Surat dengan Nomor {$nomorSurat} telah di {$surat->status_pengajuan}");
        }

        // Hapus notifikasi jika surat tujuan ke Finance telah diubah statusnya
        if ($surat->divisi_tujuan == 'FNC' && $surat->status_pengajuan != 'Pending') {
            session()->forget('suratKeFinance');
        }
        if ($surat->divisi_tujuan == 'FNC' && $surat->status_pengajuan != 'Pending') {
            session()->forget('suratMarketing');
        }
        if ($surat->divisi_tujuan == 'FNC' && $surat->status_pengajuan != 'Pending') {
            session()->forget('suratAdmin');
        }
        if ($surat->divisi_tujuan == 'FNC' && $surat->status_pengajuan != 'Pending') {
            session()->forget('suratWarehouse');
        }
        if ($surat->divisi_tujuan == 'FNC' && $surat->status_pengajuan != 'Pending') {
            session()->forget('suratPurchasing');
        }

        // Hapus semua session notifikasi setelah status diperbarui
        session()->forget(['suratKeFinance', 'suratMarketing', 'suratAdmin', 'suratWarehouse', 'suratPurchasing']);
        session()->flash('statusUpdated', 'Status surat berhasil diperbarui.');

        return redirect()->route('surat.finance.pending')->with('success', 'Status pengajuan berhasil diperbarui.');
    }


    public function viewPDF($id)
    {
        $suratFinance = SuratFinance::find($id);

        if (!$suratFinance || !Storage::disk('public')->exists($suratFinance->file_path)) {
            return redirect()->route('surat.finance.index')->withErrors('File tidak ditemukan.');
        }

        return view('surat.finance.pdf', compact('suratFinance'));
    }

    public function edit($id)
    {
        $suratFinance = SuratFinance::findOrFail($id);
        return view('surat.finance.edit', compact('suratFinance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'divisi_pembuat' => 'required',
            'divisi_tujuan' => 'required',
            'file_surat' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        $suratFinance = SuratFinance::findOrFail($id);

        if ($request->hasFile('file_surat')) {
            if ($suratFinance->file_path && file_exists(storage_path('app/' . $suratFinance->file_path))) {
                unlink(storage_path('app/' . $suratFinance->file_path));
            }

            $filePath = $request->file('file_surat')->store('surat_files');
            $suratFinance->file_path = $filePath;
        }

        $suratFinance->jenis_surat = $request->jenis_surat;
        $suratFinance->divisi_pembuat = $request->divisi_pembuat;
        $suratFinance->divisi_tujuan = $request->divisi_tujuan;
        $suratFinance->save();

        return redirect()->route('surat.finance.index')->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $suratFinance = SuratFinance::findOrFail($id);
        $suratFinance->delete();

        return redirect()->route('surat.finance.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function create()
    {
        return view('surat.finance.create');
    }

    public function dashboard()
    {
        $pending = SuratFinance::where('status_pengajuan', 'Pending')->count();
        $acc = SuratFinance::where('status_pengajuan', 'ACC')->count();
        $tolak = SuratFinance::where('status_pengajuan', 'Tolak')->count();

        $divisi_pembuat = SuratFinance::distinct()->pluck('divisi_pembuat');

        // Menghitung surat yang divisi tujuannya ke Finance dan masih Pending
        $suratKeFinance = SuratFinance::where('divisi_tujuan', 'FNC')->where('status_pengajuan', 'Pending')->count();
        $suratMarketing = SuratMarketing::where('divisi_tujuan', 'FNC')->where('status_pengajuan', 'Pending')->count();
        $suratAdmin = SuratAdmin::where('divisi_tujuan', 'FNC')->where('status_pengajuan', 'Pending')->count();
        $suratWarehouse = SuratWarehouse::where('divisi_tujuan', 'FNC')->where('status_pengajuan', 'Pending')->count();
        $suratPurchasing = SuratPurchasing::where('divisi_tujuan', 'FNC')->where('status_pengajuan', 'Pending')->count();

        // **Perubahan**: Hanya menyimpan notifikasi jika masih ada surat yang pending
        session()->forget(['suratKeFinance', 'suratMarketing', 'suratAdmin', 'suratWarehouse', 'suratPurchasing']);

        if ($suratKeFinance > 0) {
            session(['suratKeFinance' => $suratKeFinance]);
        }
        if ($suratMarketing > 0) {
            session(['suratMarketing' => $suratMarketing]);
        }
        if ($suratAdmin > 0) {
            session(['suratAdmin' => $suratAdmin]);
        }
        if ($suratWarehouse > 0) {
            session(['suratWarehouse' => $suratWarehouse]);
        }
        if ($suratPurchasing > 0) {
            session(['suratPurchasing' => $suratPurchasing]);
        }

        $monthlyCounts = SuratFinance::selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count")
            ->groupBy('year', 'month')
            ->orderByRaw('year ASC, month ASC')
            ->get();

        return view('surat.finance.dashboard', [
            'pending' => $pending,
            'acc' => $acc,
            'tolak' => $tolak,
            'months' => $monthlyCounts->pluck('month')->toArray(),
            'monthlyCounts' => $monthlyCounts->pluck('count')->toArray(),
            'suratKeFinance' => $suratKeFinance,
            'suratMarketing' => $suratMarketing,
            'suratAdmin' => $suratAdmin,
            'suratWarehouse' => $suratWarehouse,
            'suratPurchasing' => $suratPurchasing,
            'divisi_pembuat' => $divisi_pembuat
        ]);
    }

    public function filterByYear(Request $request)
    {
        // Ambil tahun yang dipilih dari parameter query string (URL)
        $year = $request->input('year');
        
        // Ambil data SuratMarketing berdasarkan tahun yang dipilih
        $suratFinances = SuratFinance::when($year, function ($query, $year) {
            return $query->whereYear('created_at', $year);
        })->get();

        // Ambil daftar tahun yang tersedia (distinct) dari data SuratMarketing
        $years = SuratFinance::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');

        // Kirim data surat dan daftar tahun ke view
        return view('surat.finance.index', compact('suratFinances', 'years'));
    }

    

    public function deleteByYear(Request $request)
    {
        $year = $request->input('year');

        if (!$year || !is_numeric($year)) {
            return redirect()->back()->with('error', 'Tahun tidak valid!');
        }

        $count = SuratFinance::whereYear('created_at', $year)->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data untuk tahun ' . $year);
        }

        SuratFinance::whereYear('created_at', $year)->delete();

        return redirect()->route('surat.finance.index')->with('success', 'Data untuk tahun ' . $year . ' telah dihapus');
    }


}
