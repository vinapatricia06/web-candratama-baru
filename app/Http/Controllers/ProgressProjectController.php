<?php

namespace App\Http\Controllers;

use App\Models\DebtPaymentProject;
use Illuminate\Http\Request;
use App\Models\ProgressProject;
use App\Models\User1;
use App\Models\Klien;
use App\Models\Omset;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ProgressProjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info('Filter parameters:', $request->all());

            // Get all technicians for the dropdown
            $teknisiList = User1::where('role', 'teknisi')->get();

            // Build query
            $query = ProgressProject::query();
            $query->with('teknisi', 'klien');  // Eager load teknisi relation

            // Apply filters
            if ($request->filled('bulan')) {
                $query->whereMonth('created_at', $request->bulan);
            }

            if ($request->filled('tanggal')) {
                $query->whereDate('created_at', $request->tanggal);
            }

            if ($request->filled('teknisi_id')) {
                $query->where('teknisi_id', $request->teknisi_id);
            }

            // Execute query
            $projects = $query->get();

            return view('progress_projects.index', compact('projects', 'teknisiList'));
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        // Get all technicians and clients for the dropdowns
        $teknisiList = User1::where('role', 'teknisi')->get();
        $kliens = Klien::all();
        return view('progress_projects.create', compact('teknisiList', 'kliens'));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'teknisi_id' => 'required|exists:users1,id_user',
            'klien_id' => 'required|exists:kliens,id',
            'project' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:1536',  // 1.5MB = 1536KB
            'status' => 'required|string|max:255',
            'nominal' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except(['dokumentasi', 'tanggal_awal_angsuran', 'jumlah_angsuran']);

            if ($request->hasFile('dokumentasi')) {
                $file = $request->file('dokumentasi');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('image'), $filename);
                $data['dokumentasi'] = 'image/' . $filename;
            }

            if ($request->is_hutang == 1) {
                $data['status_pembayaran'] = 'Belum Lunas';
            } else {
                $data['uang_muka'] = 0;
            }

            $progressProject = ProgressProject::create($data);

            if ($request->is_hutang == 1) {
                $tanggal_awal = $request->tanggal_awal_angsuran;
                $jumlah_angsuran = $request->jumlah_angsuran;
                $total_nominal = $request->nominal - $request->uang_muka;

                $nominal_per_angsuran = ceil($total_nominal / $jumlah_angsuran);

                for ($i = 0; $i < $jumlah_angsuran; $i++) {
                    $tanggal_angsuran = date('Y-m-d', strtotime($tanggal_awal . " +{$i} month"));

                    if ($i == ($jumlah_angsuran - 1)) {
                        $sisa_nominal = $total_nominal - ($nominal_per_angsuran * ($jumlah_angsuran - 1));
                        $nominal_angsuran = $sisa_nominal;
                    } else {
                        $nominal_angsuran = $nominal_per_angsuran;
                    }

                    DebtPaymentProject::create([
                        'progress_projects_id' => $progressProject->id,
                        'tanggal_angsuran' => $tanggal_angsuran,
                        'nominal' => $nominal_angsuran,
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('progress_projects.index')
                ->with('success', 'Project berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan project: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        // Find the progress project to edit
        $progress_project = ProgressProject::with('debtPayments')->findOrFail($id);
        $teknisiList = User1::where('role', 'teknisi')->get();
        $kliens = Klien::all(); // Get all clients for the dropdown

        return view('progress_projects.edit', compact('progress_project', 'teknisiList', 'kliens'));
    }

    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'teknisi_id' => 'required|exists:users1,id_user',
            'klien_id' => 'required|exists:kliens,id',
            'project' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:1536',  // 1.5MB = 1536KB
            'status' => 'required|string|max:255',
            'nominal' => 'required|numeric',
        ]);

        // Find the progress project to update
        $progress_project = ProgressProject::findOrFail($id);
        $data = $request->except(['dokumentasi', 'tanggal_awal_angsuran', 'jumlah_angsuran']);

        // Handle file upload for dokumentasi
        if ($request->hasFile('dokumentasi')) {
            // Delete old file if exists
            if ($progress_project->dokumentasi && File::exists(public_path($progress_project->dokumentasi))) {
                File::delete(public_path($progress_project->dokumentasi));
            }

            // Save new file
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $filename);
            $data['dokumentasi'] = 'image/' . $filename;
        }

        if ($request->is_hutang == 1) {
            $data['status_pembayaran'] = 'Belum Lunas';
        } else {
            if ($progress_project->status_pembayaran == 'Belum Lunas') {
                $data['status_pembayaran'] = 'Menunggu Pembayaran';
            } else {
                $data['status_pembayaran'] = $progress_project->status_pembayaran;
            }
        }

        // Update ProgressProject record
        $progress_project->update($data);

        if ($progress_project->status_pembayaran == 'Belum Lunas') {
            if (DebtPaymentProject::where('progress_projects_id', $id)->exists()) {
                DebtPaymentProject::where('progress_projects_id', $id)->delete();
            }
            if ($request->is_hutang == 1) {
                $tanggal_awal = $request->tanggal_awal_angsuran;
                $jumlah_angsuran = $request->jumlah_angsuran;
                $total_nominal = $request->nominal - $request->uang_muka;

                $nominal_per_angsuran = ceil($total_nominal / $jumlah_angsuran);

                for ($i = 0; $i < $jumlah_angsuran; $i++) {
                    $tanggal_angsuran = date('Y-m-d', strtotime($tanggal_awal . " +{$i} month"));

                    if ($i == ($jumlah_angsuran - 1)) {
                        $sisa_nominal = $total_nominal - ($nominal_per_angsuran * ($jumlah_angsuran - 1));
                        $nominal_angsuran = $sisa_nominal;
                    } else {
                        $nominal_angsuran = $nominal_per_angsuran;
                    }

                    DebtPaymentProject::create([
                        'progress_projects_id' => $id,
                        'tanggal_angsuran' => $tanggal_angsuran,
                        'nominal' => $nominal_angsuran,
                    ]);
                }
            }
        }

        return redirect()->route('progress_projects.index')
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Find and delete the progress project
        $progress_project = ProgressProject::findOrFail($id);
        if ($progress_project->dokumentasi && File::exists(public_path($progress_project->dokumentasi))) {
            File::delete(public_path($progress_project->dokumentasi));
        }

        $progress_project->delete();

        return redirect()->route('progress_projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }

    public function downloadPdf(Request $request)
    {
        try {
            Log::info('PDF Download parameters:', $request->all());

            // Build query
            $query = ProgressProject::query()->with('teknisi', 'klien');

            // Apply filters
            if ($request->filled('bulan')) {
                $query->whereMonth('created_at', $request->bulan);
            }

            if ($request->filled('tanggal')) {
                $query->whereDate('created_at', $request->tanggal);
            }

            if ($request->filled('teknisi_id')) {
                $query->where('teknisi_id', $request->teknisi_id);
            }

            $projects = $query->get();
            Log::info('PDF will contain ' . $projects->count() . ' projects');

            // Prepare data for PDF
            $filterInfo = [];

            if ($request->filled('bulan')) {
                $bulanNama = \Carbon\Carbon::create()->month($request->bulan)->format('F');
                $filterInfo[] = "Bulan: {$bulanNama}";
            }

            if ($request->filled('tanggal')) {
                $filterInfo[] = "Tanggal: " . $request->tanggal;
            }

            if ($request->filled('teknisi_id')) {
                $teknisi = User1::find($request->teknisi_id);
                if ($teknisi) {
                    $filterInfo[] = "Teknisi: " . $teknisi->nama;
                }
            }

            // Process images
            foreach ($projects as $project) {
                if ($project->dokumentasi && file_exists(public_path($project->dokumentasi))) {
                    $path = public_path($project->dokumentasi);
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $project->dokumentasi_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                } else {
                    $project->dokumentasi_base64 = null;
                }
            }

            // PDF filename
            $filename = 'progress_projects';
            if ($request->filled('teknisi_id')) {
                $teknisi = User1::find($request->teknisi_id);
                if ($teknisi) {
                    $filename .= '_' . str_replace(' ', '_', strtolower($teknisi->nama));
                }
            }
            $filename .= '.pdf';
            // Generate PDF
            $pdf = PDF::loadView('progress_projects.pdf', compact('projects', 'filterInfo'))
                ->setPaper('A4', 'landscape');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error in downloadPdf method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage());
        }
    }

    public function downloadReport(Request $request)
    {
        $status_project = $request->status_project;
        $status_pembayaran = $request->status_pembayaran;

        $data = ProgressProject::with('klien', 'teknisi', 'omsets')
            ->when($status_project, function ($query, $status_project) {
                return $query->where('status', $status_project);
            })
            ->when($status_pembayaran == "belum", function ($query) {
                return $query->whereIn('status_pembayaran', ['Menunggu Pembayaran', 'Belum Lunas']);
            })
            ->when($status_pembayaran == "sebagian", function ($query) {
                return $query->where('status_pembayaran', 'Dibayar Sebagian');
            })
            ->when($status_pembayaran == "lunas", function ($query) {
                return $query->where('status_pembayaran', ['Lunas', 'Sudah Dibayar']);
            })
            ->get();
        $pdf = PDF::loadView('progress_projects.pdf_report', compact('data'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('report_projects.pdf');
    }

    public function cetakNota($id)
    {
        $data = ProgressProject::with([
            'omset' => function ($query) {
                $query->where('catatan_pembayaran', 'PROJECT');
            },
            'klien'
        ])->find($id);

        $kode_transaksi = $data['kode_transaksi'];

        $pdf = PDF::loadView('progress_projects.nota', compact('data'))
            ->setPaper('A4', 'potrait');

        return $pdf->download("Nota-$kode_transaksi.pdf");
    }

    public function hapusBulan(Request $request)
    {
        try {
            $bulan = $request->input('bulan');
            Log::info('Deleting all projects for month: ' . $bulan);

            $deleted = ProgressProject::whereMonth('created_at', $bulan)->delete();
            Log::info('Deleted ' . $deleted . ' projects');

            return redirect()->route('progress_projects.index')
                ->with('success', 'Semua data bulan ini telah dihapus.');
        } catch (\Exception $e) {
            Log::error('Error in hapusBulan method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function detailPembayaran($id)
    {
        $data = DebtPaymentProject::where('progress_projects_id', $id)->get();
        return view('progress_projects.detail', compact('data'));
    }

    public function cetakNotaHutang($id)
    {
        $data = DebtPaymentProject::with([
            'omset' => function ($query) {
                $query->where('catatan_pembayaran', 'DEBT PROJECT');
            },
            'project.klien'
        ])
            ->orderBy('tanggal_angsuran')
            ->find($id);

        $allPayments = DebtPaymentProject::where('progress_projects_id', $data->progress_projects_id)
            ->orderBy('tanggal_angsuran')
            ->get();

        $installmentNumber = $allPayments->search(function ($item) use ($data) {
            return $item->id === $data->id;
        }) + 1;

        $kode_transaksi = $data['kode_transaksi'];

        $pdf = PDF::loadView('progress_projects.nota_hutang', compact('data', 'installmentNumber'))
            ->setPaper('A4', 'potrait');

        return $pdf->download("Nota-Angsuran-$kode_transaksi.pdf");
    }
}
