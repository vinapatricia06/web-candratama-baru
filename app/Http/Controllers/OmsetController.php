<?php

namespace App\Http\Controllers;

use App\Models\Omset;
use App\Models\Klien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OmsetExport;
use App\Models\ProgressProject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Progress;

class OmsetController extends Controller
{
    // Menampilkan daftar omset
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun'); // Ambil nilai tahun dari parameter request

        $query = Omset::query()->with('project.klien');

        // Filter berdasarkan search (nama klien)
        if ($search) {
            $query->where('nama_klien', 'like', '%' . $search . '%');
        }


        if ($request->has('no_induk') && $request->get('no_induk') != '') {
            $query->where('no_induk', 'like', '%' . $request->get('no_induk') . '%');
        }

        // Filter berdasarkan bulan
        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        // Filter berdasarkan tahun
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        // Mengambil data omset
        $omsets = $query->orderBy('id_omset', 'asc')->get();

        return view('omsets.index', compact('omsets'));
    }

    // Menampilkan form untuk menambah omset
    public function create()
    {
        // Mengambil data klien untuk ditampilkan di dropdown
        $kliens = ProgressProject::select('klien_id')
            ->with('klien')
            ->groupBy('klien_id')
            ->get();
        return view('omsets.create', compact('kliens'));
    }

    // Menyimpan data omset baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'progress_projects_id' => 'required|exists:progress_projects,id',
            'sumber_lead' => 'required|string|max:255',
            'nominal' => 'required|numeric',  // Validasi nominal
        ]);

        // Menyimpan data omset jika tidak ada duplikasi
        Omset::create([
            'tanggal' => $request->tanggal,
            'progress_projects_id' => $request->progress_projects_id,  // Menyimpan ID project
            'sumber_lead' => $request->sumber_lead,
            'nominal' => $request->nominal,
        ]);

        $kode_transaksi = 'ORDER-' . rand();
        ProgressProject::where('id', $request->progress_projects_id)
            ->update(['kode_transaksi' => $kode_transaksi, 'status_pembayaran' => 'Sudah Dibayar']);

        return redirect()->route('omsets.index')->with('success', 'Data omset berhasil ditambahkan!');
    }

    // Menampilkan form untuk mengedit omset
    public function edit($id)
    {
        $omset = Omset::with('project.klien')->findOrFail($id);
        $kliens = ProgressProject::select('klien_id')
            ->with('klien')
            ->groupBy('klien_id')
            ->get(); // Ambil semua klien untuk dropdown
        return view('omsets.edit', compact('omset', 'kliens'));
    }

    // Memperbarui data omset
    public function update(Request $request, Omset $omset)
    {
        // Validasi input dengan mengubah klien_id menjadi opsional
        $request->validate([
            'tanggal' => 'required|date',
            'progress_projects_id' => 'required|exists:progress_projects,id',
            'sumber_lead' => 'required|string|max:255',
            'nominal' => 'required|numeric',  // Validasi nominal
        ]);

        // Ambil data nama klien dan no_induk berdasarkan klien_id jika dipilih

        // Memperbarui data omset lainnya
        $omset->update([
            'tanggal' => $request->tanggal,
            'progress_projects_id' => $request->progress_projects_id,
            'sumber_lead' => $request->sumber_lead,
            'nominal' => $request->nominal,
        ]);

        return redirect()->route('omsets.index')->with('success', 'Data omset berhasil diperbarui!');
    }

    // Menghapus data omset
    public function destroy(Omset $omset)
    {
        $omset->delete();
        ProgressProject::where('id', $omset->progress_projects_id)
            ->update(['kode_transaksi' => null, 'status_pembayaran' => 'Menunggu Pembayaran']);
        return redirect()->route('omsets.index')->with('success', 'Data omset berhasil dihapus!');
    }

    // Menampilkan rekap bulanan omset
    public function rekapBulanan()
    {
        $rekap = Omset::selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, SUM(nominal) as total_omset')
            ->groupBy('tahun', 'bulan')
            ->orderByDesc('tahun')
            ->orderBy('bulan')
            ->get();

        $data = [];
        $totals = [];
        $labels = [];
        $totalPerTahun = [];

        foreach ($rekap as $item) {
            $tahun = $item->tahun;
            $bulan = $item->bulan;

            if (!isset($data[$tahun])) {
                $data[$tahun] = array_fill(1, 12, 0);
                $totals[$tahun] = 0;
            }

            $data[$tahun][$bulan] = $item->total_omset;
            $totals[$tahun] += $item->total_omset;
        }

        foreach ($totals as $tahun => $total) {
            $labels[] = $tahun;
            $totalPerTahun[] = $total;
        }

        session([
            'data' => $data,
            'labels' => $labels,
            'totalPerTahun' => $totalPerTahun,
        ]);

        return view('omsets.rekap', compact('data', 'totals', 'labels', 'totalPerTahun'));
    }

    // Mengekspor data omset ke file Excel
    public function exportToExcel(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $query = Omset::query()->with('project.klien');

        if ($search) {
            $query->where('nama_klien', 'like', '%' . $search . '%');
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $omsets = $query->get();

        return Excel::download(new OmsetExport($omsets), 'omsets.xlsx');
    }

    // Menyusun dan mengunduh PDF rekap omset
    public function downloadPDF()
    {
        $data = session('data', []);
        $labels = session('labels', []);
        $totalPerTahun = session('totalPerTahun', []);

        if (empty($data) || empty($labels) || empty($totalPerTahun)) {
            $rekap = Omset::selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, SUM(nominal) as total_omset')
                ->groupBy('tahun', 'bulan')
                ->orderByDesc('tahun')
                ->orderBy('bulan')
                ->get();

            $data = [];
            $totals = [];
            $labels = [];
            $totalPerTahun = [];

            foreach ($rekap as $item) {
                $tahun = $item->tahun;
                $bulan = $item->bulan;

                if (!isset($data[$tahun])) {
                    $data[$tahun] = array_fill(1, 12, 0);
                    $totals[$tahun] = 0;
                }

                $data[$tahun][$bulan] = $item->total_omset;
                $totals[$tahun] += $item->total_omset;
            }

            foreach ($totals as $tahun => $total) {
                $labels[] = $tahun;
                $totalPerTahun[] = $total;
            }
        }

        if (empty($data)) {
            return redirect()->back()->with('error', 'Data tidak tersedia untuk diunduh.');
        }

        $chartPath = storage_path('app/public/chart-omset.png');

        if (!file_exists($chartPath)) {
            return redirect()->back()->with('error', 'Gambar grafik tidak ditemukan.');
        }

        $pdf = Pdf::loadView('pdf.grafik-omset', compact('data', 'labels', 'totalPerTahun', 'chartPath'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('grafik_omset.pdf');
    }

    // Mengunggah gambar grafik omset
    public function uploadChart(Request $request)
    {
        $imageData = $request->chart;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $image = base64_decode($imageData);
        $fileName = 'chart-omset.png';

        Storage::put('public/' . $fileName, $image);

        return response()->json(['message' => 'Chart uploaded successfully']);
    }
}
