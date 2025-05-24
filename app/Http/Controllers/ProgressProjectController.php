<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgressProject;
use App\Models\User1;
use App\Models\Klien;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

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
                $query->whereMonth('tanggal_setting', $request->bulan);
            }

            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal_setting', $request->tanggal);
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
            'tanggal_setting' => 'required|date',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:1536',  // 1.5MB = 1536KB
            'status' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'serah_terima' => 'required|in:selesai,belum',
        ]);

        // Store the data
        $data = $request->except(['dokumentasi']);

        // Handle file upload for dokumentasi
        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $filename);
            $data['dokumentasi'] = 'image/' . $filename;
        }

        ProgressProject::create($data);

        return redirect()->route('progress_projects.index')
            ->with('success', 'Project berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Find the progress project to edit
        $progress_project = ProgressProject::findOrFail($id);
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
            'tanggal_setting' => 'required|date',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:1536',  // 1.5MB = 1536KB
            'status' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'serah_terima' => 'required|in:selesai,belum',
        ]);

        // Find the progress project to update
        $progress_project = ProgressProject::findOrFail($id);
        $data = $request->except(['dokumentasi']);

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

        // Update ProgressProject record
        $progress_project->update($data);

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
                $query->whereMonth('tanggal_setting', $request->bulan);
            }

            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal_setting', $request->tanggal);
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

    public function hapusBulan(Request $request)
    {
        try {
            $bulan = $request->input('bulan');
            Log::info('Deleting all projects for month: ' . $bulan);

            $deleted = ProgressProject::whereMonth('tanggal_setting', $bulan)->delete();
            Log::info('Deleted ' . $deleted . ' projects');

            return redirect()->route('progress_projects.index')
                ->with('success', 'Semua data bulan ini telah dihapus.');
        } catch (\Exception $e) {
            Log::error('Error in hapusBulan method: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
