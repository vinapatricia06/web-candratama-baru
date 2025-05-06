<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratWarehouse;
use Illuminate\Support\Facades\Storage;

class SuratWarehouseController extends Controller
{
    public function index()
    {
        $nomorSurat = null;
        $suratWarehouses = SuratWarehouse::orderBy('created_at', 'desc')->get();
        $years = SuratWarehouse::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');

        
        return view('surat.warehouse.index', compact('nomorSurat', 'suratWarehouses','years'));
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

            $suratWarehouse = SuratWarehouse::create([
                'jenis_surat' => $jenis_surat,
                'divisi_pembuat' => $divisi_pembuat,
                'divisi_tujuan' => $divisi_tujuan,
                'file_path' => $filePath,
                'status_pengajuan' => 'Pending',
            ]);

            $id_surat = str_pad($suratWarehouse->id, 3, '0', STR_PAD_LEFT);
            $nomorSurat = "{$jenis_surat}/{$id_surat}/{$divisi_pembuat}-{$divisi_tujuan}/{$bulan_romawi}/{$tahun}";

            $suratWarehouse->update(['nomor_surat' => $nomorSurat]);

            session(['nomorSurat' => $nomorSurat]);

            return redirect()->route('surat.warehouse.index')->with('success', 'Surat berhasil di tambahkan !');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadfile($id)
    {
        $surat = SuratWarehouse::findOrFail($id);
        $filePath = public_path('storage/' . $surat->file_path);
        if (file_exists($filePath)) {
            $fileName = "SuratWarehouse_{$surat->id}_{$surat->jenis_surat}.pdf";
            return response()->download($filePath, $fileName);
        } else {
            return redirect()->back()->withErrors('File tidak ditemukan.');
        }
    }

    public function updateStatusPengajuan(Request $request, $id)
    {
        if (auth()->user()->role === 'warehouse') {
            return abort(403, 'Anda tidak diizinkan untuk mengubah status pengajuan ini.');
        }
        
        $request->validate([
            'status_pengajuan' => 'required|in:Pending,ACC,Tolak',
        ]);

        $surat = SuratWarehouse::findOrFail($id);
        $oldStatus = $surat->status_pengajuan;
        $surat->status_pengajuan = $request->status_pengajuan;
        $surat->save();

        $nomorSurat = $surat->formatted_nomor_surat; // Ambil nomor surat dari accessor

        
        // Cek apakah status berubah menjadi ACC atau Tolak
        if (in_array($surat->status_pengajuan, ['ACC', 'Tolak']) && $oldStatus !== $surat->status_pengajuan) {
            session()->put('statusUpdatedwrh', "Surat dengan Nomor {$nomorSurat} telah di {$surat->status_pengajuan}");
        }

        return redirect()->route('surat.warehouse.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function viewPDF($id)
    {
        $suratWarehouse = SuratWarehouse::find($id);

        if (!$suratWarehouse || !Storage::disk('public')->exists($suratWarehouse->file_path)) {
            return redirect()->route('surat.warehouse.index')->withErrors('File tidak ditemukan.');
        }

        return view('surat.warehouse.pdf', compact('suratWarehouse'));
    }

    public function edit($id)
    {
        $suratWarehouse = SuratWarehouse::findOrFail($id);
        return view('surat.warehouse.edit', compact('suratWarehouse'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'divisi_pembuat' => 'required',
            'divisi_tujuan' => 'required',
            'file_surat' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        $suratWarehouse = SuratWarehouse::findOrFail($id);

        if ($request->hasFile('file_surat')) {
            if ($suratWarehouse->file_path && file_exists(storage_path('app/' . $suratWarehouse->file_path))) {
                unlink(storage_path('app/' . $suratWarehouse->file_path));
            }

            $filePath = $request->file('file_surat')->store('surat_files');
            $suratWarehouse->file_path = $filePath;
        }

        $suratWarehouse->jenis_surat = $request->jenis_surat;
        $suratWarehouse->divisi_pembuat = $request->divisi_pembuat;
        $suratWarehouse->divisi_tujuan = $request->divisi_tujuan;
        $suratWarehouse->save();

        return redirect()->route('surat.warehouse.index')->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $suratWarehouse = SuratWarehouse::findOrFail($id);
        $suratWarehouse->delete();

        return redirect()->route('surat.Warehouse.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function create()
    {
        return view('surat.Warehouse.create');
    }

    public function dashboard()
    {
        $pending = SuratWarehouse::where('status_pengajuan', 'Pending')->count();
        $acc = SuratWarehouse::where('status_pengajuan', 'ACC')->count();
        $tolak = SuratWarehouse::where('status_pengajuan', 'Tolak')->count();

        $divisi_pembuat = SuratWarehouse::distinct()->pluck('divisi_pembuat');

        // Menghitung surat yang divisi tujuannya ke Finance
        $suratKeWarehouse = SuratWarehouse::where('divisi_tujuan', 'WRH')->where('status_pengajuan', 'Pending')->count();

        // Menyimpan informasi surat ke Finance di sesi jika ada
        if ($suratKeWarehouse > 0) {
            session(['suratKeWarehouse' => $suratKeWarehouse]);
        }

        $monthlyCounts = SuratWarehouse::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('count', 'month');

        return view('surat.warehouse.dashboard', [
            'pending' => $pending,
            'acc' => $acc,
            'tolak' => $tolak,
            'months' => $monthlyCounts->keys(),
            'monthlyCounts' => $monthlyCounts->values(),
            'suratKeWarehouse' => $suratKeWarehouse,
            'divisi_pembuat' => $divisi_pembuat // Pastikan variabel ini dikirimkan ke view
        ]);
    }

    public function filterByYear(Request $request)
    {
        $year = $request->input('year');
        
        $suratWarehouses = SuratWarehouse::when($year, function ($query, $year) {
            return $query->whereYear('created_at', $year);
        })->get();

        $years = SuratWarehouse::selectRaw('YEAR(created_at) as year')
                                ->distinct()
                                ->orderByDesc('year')
                                ->pluck('year');

        return view('surat.warehouse.index', compact('suratWarehouses', 'years'));
    }


    

    public function deleteByYear(Request $request)
    {
        $year = $request->input('year');

        if (!$year || !is_numeric($year)) {
            return redirect()->back()->with('error', 'Tahun tidak valid!');
        }

        $count = SuratWarehouse::whereYear('created_at', $year)->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data untuk tahun ' . $year);
        }

        SuratWarehouse::whereYear('created_at', $year)->delete();

        return redirect()->route('surat.warehouse.index')->with('success', 'Data untuk tahun ' . $year . ' telah dihapus');
    }



}
