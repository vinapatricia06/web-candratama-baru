<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgressProject;
use App\Models\User1;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgressProjectController extends Controller
{
    public function index(Request $request) {
        // Jika ada bulan yang dipilih, filter data berdasarkan bulan tersebut
        if ($request->has('bulan')) {
            $bulan = $request->get('bulan');
            // Menyaring project berdasarkan bulan
            $projects = ProgressProject::whereMonth('tanggal_setting', $bulan)->with('teknisi')->get();
        } else {
            // Ambil semua project jika bulan tidak dipilih
            $projects = ProgressProject::with('teknisi')->get();
        }

        return view('progress_projects.index', compact('projects'));
    }

    public function create() {
        $teknisiList = User1::where('role', 'teknisi')->get();
        return view('progress_projects.create', compact('teknisiList'));
    }

    public function store(Request $request) {
        $request->validate([
            'teknisi_id' => 'required|exists:users1,id_user',
            'klien' => 'required|string|max:255',
            'alamat' => 'required|string',
            'project' => 'required|string|max:255',
            'tanggal_setting' => 'required|date',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|max:255',
            'serah_terima' => 'required|in:selesai,belum', 
        ]);

        $data = $request->except(['dokumentasi']);

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

    public function edit($id) {
        $progress_project = ProgressProject::findOrFail($id);
        $teknisiList = User1::where('role', 'teknisi')->get();
        return view('progress_projects.edit', compact('progress_project', 'teknisiList'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'teknisi_id' => 'required|exists:users1,id_user',
            'klien' => 'required|string|max:255',
            'alamat' => 'required|string',
            'project' => 'required|string|max:255',
            'tanggal_setting' => 'required|date',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|max:255',
            'serah_terima' => 'required|in:selesai,belum',
        ]);
    
        $progress_project = ProgressProject::findOrFail($id);
        $data = $request->except(['dokumentasi']);

        if ($request->hasFile('dokumentasi')) {
            if ($progress_project->dokumentasi && File::exists(public_path($progress_project->dokumentasi))) {
                File::delete(public_path($progress_project->dokumentasi));
            }

            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $filename);
            $data['dokumentasi'] = 'image/' . $filename;
        }

        $progress_project->update($data);
    
        return redirect()->route('progress_projects.index')
                         ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy($id) {
        $progress_project = ProgressProject::findOrFail($id);
        if ($progress_project->dokumentasi && File::exists(public_path($progress_project->dokumentasi))) {
            File::delete(public_path($progress_project->dokumentasi));
        }

        $progress_project->delete();

        return redirect()->route('progress_projects.index')
                         ->with('success', 'Project berhasil dihapus.');
    }

    public function downloadPdf()
    {
        $projects = ProgressProject::with('teknisi')->get();
    
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
    
        // Load view PDF
        $pdf = PDF::loadView('progress_projects.pdf', compact('projects'))
                  ->setPaper('A4', 'landscape');
    
        return $pdf->download('progress_projects.pdf');
    }
    

    public function hapusBulan(Request $request)
    {
        // Ambil bulan dari request
        $bulan = $request->input('bulan');

        // Hapus semua project di bulan yang dipilih
        ProgressProject::whereMonth('tanggal_setting', $bulan)->delete();

        return redirect()->route('progress_projects.index')
                         ->with('success', 'Semua data bulan ini telah dihapus.');
    }
}
