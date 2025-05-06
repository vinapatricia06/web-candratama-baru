<?php

namespace App\Http\Controllers;

use App\Models\Omset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\OmsetExport;
use Barryvdh\DomPDF\Facade\Pdf;

class OmsetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun'); // Ambil nilai tahun dari parameter request

        $query = Omset::query();

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

        $omsets = $query->orderBy('id_omset', 'asc')->get();

        return view('omsets.index', compact('omsets'));
    }

    public function create()
    {
        return view('omsets.create');
    }

    public function store(Request $request)
    {
        // Validasi input untuk memastikan nominal adalah angka yang valid dan no_induk diisi
        $request->validate([
            'tanggal' => 'required|date',
            'no_induk' => 'required|integer|unique:omsets,no_induk',
            'nama_klien' => 'required|string|max:255',
            'alamat' => 'required|string',
            'project' => 'required|string|max:255',
            'sumber_lead' => 'required|string|max:255', // Validasi sumber_lead
            'nominal' => 'required|numeric', // Validasi untuk nominal sebagai angka
        ]);

        // Menyimpan data omset termasuk nominal yang sudah divalidasi
        Omset::create($request->all());

        return redirect()->route('omsets.index')->with('success', 'Data omset berhasil ditambahkan!');
    }

    public function edit(Omset $omset)
    {
        return view('omsets.edit', compact('omset'));
    }

    public function update(Request $request, Omset $omset)
    {
        // Validasi input untuk memastikan nominal adalah angka yang valid dan no_induk diisi
        $request->validate([
            'tanggal' => 'required|date',
            'nama_klien' => 'required|string|max:255',
            'alamat' => 'required|string',
            'project' => 'required|string|max:255',
            'sumber_lead' => 'required|string|max:255', // Validasi sumber_lead
            'nominal' => 'required|numeric', // Validasi untuk nominal sebagai angka
        ]);

        // Menyimpan data omset termasuk nominal yang sudah divalidasi
        $omset->update($request->all());


        return redirect()->route('omsets.index')->with('success', 'Data omset berhasil diperbarui!');
    }

    public function destroy(Omset $omset)
    {
        $omset->delete();
        return redirect()->route('omsets.index')->with('success', 'Data omset berhasil dihapus!');
    }

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

    public function exportToExcel(Request $request)
    {
        $search = $request->get('search');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun'); // Ambil nilai tahun dari parameter request

        $query = Omset::query();

        // Filter berdasarkan search (nama klien)
        if ($search) {
            $query->where('nama_klien', 'like', '%' . $search . '%');
        }

        // Filter berdasarkan bulan
        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        // Filter berdasarkan tahun
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $omsets = $query->get();

        return Excel::download(new OmsetExport($omsets), 'omsets.xlsx');
    }

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

    private function generateChartImage($labels, $values)
    {
        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Total Omset',
                    'data' => $values,
                    'backgroundColor' => 'rgba(0, 153, 255, 0.5)',
                    'borderColor' => 'rgba(0, 153, 255, 1)',
                    'borderWidth' => 1
                ]],
            ],
            'options' => ['responsive' => true]
        ];

        return base64_encode(json_encode($chartData));
    }

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
