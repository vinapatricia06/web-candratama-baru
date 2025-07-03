<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Klien;
use App\Models\ProgressProject;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravel\Prompts\Progress;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Maintenance::query()->with('project.klien');

        // Filter berdasarkan bulan dan tanggal (opsional)
        $bulan = $request->get('bulan');
        $tanggal = $request->get('tanggal');

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }

        if ($tanggal) {
            $query->whereDay('created_at', $tanggal);
        }

        $maintenances = $query->get();

        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $kliens = ProgressProject::select('klien_id')
            ->with('klien')
            ->groupBy('klien_id')
            ->get();
        return view('maintenances.create', compact('kliens'));
    }

    public function get_data_project(Request $request)
    {
        $data = ProgressProject::where('klien_id', $request->klien_id)->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'progress_projects_id' => 'required|exists:progress_projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'maintenance' => 'required|string',
            'status' => 'required|string',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:1536',  // 1.5MB = 1536KB
        ]);

        // Menyimpan data Maintenance
        $data = $request->except(['dokumentasi']);

        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $filename); // Simpan file di folder "image"
            $data['dokumentasi'] = 'image/' . $filename;
        }
        if (!empty($request->biaya_tambahan)) {
            $data['status_pembayaran'] = 'Menunggu Pembayaran';
        }

        Maintenance::create($data);

        return redirect()->route('maintenances.index')
            ->with('success', 'Data Maintenance berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Temukan proyek maintenance yang akan diedit
        $maintenance = Maintenance::with('project.klien')->findOrFail($id);
        $kliens = ProgressProject::select('klien_id')
            ->with('klien')
            ->groupBy('klien_id')
            ->get();  // Ambil semua klien untuk dropdown

        return view('maintenances.edit', compact('maintenance', 'kliens'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'progress_projects_id' => 'required|exists:progress_projects,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'maintenance' => 'required|string',
            'status' => 'required|string',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:1536',  // 1.5MB = 1536KB
        ]);

        // Temukan proyek maintenance yang akan diperbarui
        $maintenance = Maintenance::findOrFail($id);
        $data = $request->except(['dokumentasi']);

        if ($request->hasFile('dokumentasi')) {
            // Hapus file lama jika ada
            if ($maintenance->dokumentasi && File::exists(public_path($maintenance->dokumentasi))) {
                File::delete(public_path($maintenance->dokumentasi));
            }

            // Simpan file baru
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/dokumentasi'), $filename);
            $data['dokumentasi'] = 'storage/dokumentasi/' . $filename;
        }

        if (!empty($request->biaya_tambahan)) {
            $data['status_pembayaran'] = 'Menunggu Pembayaran';
        }

        // Update data maintenance
        $maintenance->update($data);

        return redirect()->route('maintenances.index')
            ->with('success', 'Data Maintenance berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Hapus proyek maintenance
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->dokumentasi && File::exists(public_path($maintenance->dokumentasi))) {
            File::delete(public_path($maintenance->dokumentasi));
        }

        $maintenance->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'Data Maintenance berhasil dihapus.');
    }

    public function cetakNota($id)
    {
        $data = Maintenance::with([
            'omset' => function ($query) {
                $query->where('catatan_pembayaran', 'MAINTENANCE');
            },
            'project.klien'
        ])->find($id);

        $kode_transaksi = $data['kode_transaksi'];

        $pdf = PDF::loadView('maintenances.nota', compact('data'))
            ->setPaper('A4', 'potrait');

        return $pdf->download("Nota-$kode_transaksi.pdf");
    }

    public function cetakInvoice($id)
    {
        $data = Maintenance::with('project.klien')->find($id);

        $pdf = PDF::loadView('maintenances.invoice', compact('data'))
            ->setPaper('A4', 'potrait');

        return $pdf->download("Invoice-Maintenance-$id.pdf");
    }

    public function downloadPdf()
    {
        $maintenances = Maintenance::with('project.klien')->get();

        foreach ($maintenances as $maintenance) {
            if ($maintenance->dokumentasi && file_exists(public_path($maintenance->dokumentasi))) {
                $path = public_path($maintenance->dokumentasi);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $maintenance->dokumentasi_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $maintenance->dokumentasi_base64 = null;
            }
        }

        // Load view
        $pdf = PDF::loadView('maintenances.pdf', compact('maintenances'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('maintenances.pdf');
    }

    public function hapusBulan(Request $request)
    {
        // Ambil bulan dari request
        $bulan = $request->input('bulan');

        // Hapus semua data maintenance untuk bulan yang dipilih
        Maintenance::whereMonth('tanggal_setting', $bulan)->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'Semua data bulan ini telah dihapus.');
    }
}
