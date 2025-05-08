<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Klien;

class MaintenanceController extends Controller
{
    public function index(Request $request) {
        $query = Maintenance::query();
    
        // Ambil inputan bulan dan tanggal (optional)
        $bulan = $request->get('bulan');
        $tanggal = $request->get('tanggal');
    
        if ($bulan) {
            $query->whereMonth('tanggal_setting', $bulan);
        }
    
        if ($tanggal) {
            $query->whereDay('tanggal_setting', $tanggal);
        }
    
        $maintenances = $query->get();
    
        return view('maintenances.index', compact('maintenances'));
    }
    

    public function create() {
        $kliens = Klien::all();
        return view('maintenances.create', compact('kliens'));
    }

    public function store(Request $request) {
        // Validate input
        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'no_induk' => 'required|string|unique:maintenances,no_induk',
            'alamat' => 'required|string',
            'project' => 'required|string|max:255',
            'tanggal_setting' => 'required|date',
            'tanggal_serah_terima' => 'nullable|date', // Make it nullable
            'maintenance' => 'required|string',
            'status' => 'required|in:Waiting List,Selesai',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Store the data
        $data = $request->except(['dokumentasi']);

        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $filename); // Sama seperti yang berhasil
            $data['dokumentasi'] = 'image/' . $filename;
        }
        

        Maintenance::create($data);

        return redirect()->route('maintenances.index')
                         ->with('success', 'Data Maintenance berhasil ditambahkan.');
    }

    public function edit($id) {
        // Display the form to edit a maintenance project
        $maintenance = Maintenance::findOrFail($id);
        return view('maintenances.edit', compact('maintenance'));
    }

    public function update(Request $request, $id) {
        // Validate input
        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'alamat' => 'required|string',
            'project' => 'required|string|max:255',
            'tanggal_setting' => 'required|date',
            'tanggal_serah_terima' => 'nullable|date',
            'maintenance' => 'required|string',
            'status' => 'required|in:Waiting List,Selesai',
            'dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Find the maintenance project to update
        $maintenance = Maintenance::findOrFail($id);
        $data = $request->except(['dokumentasi']);

        if ($request->hasFile('dokumentasi')) {
            // Delete old file if exists
            if ($maintenance->dokumentasi && File::exists(public_path($maintenance->dokumentasi))) {
                File::delete(public_path($maintenance->dokumentasi));
            }

            // Save new file
            $file = $request->file('dokumentasi');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/dokumentasi'), $filename);
            $data['dokumentasi'] = 'storage/dokumentasi/' . $filename;
        }

        // Update the maintenance data
        $maintenance->update($data);

        return redirect()->route('maintenances.index')
                         ->with('success', 'Data Maintenance berhasil diperbarui.');
    }

    public function destroy($id) {
        // Delete maintenance project
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->dokumentasi && File::exists(public_path($maintenance->dokumentasi))) {
            File::delete(public_path($maintenance->dokumentasi));
        }

        $maintenance->delete();

        return redirect()->route('maintenances.index')
                         ->with('success', 'Data Maintenance berhasil dihapus.');
    }

    public function downloadPdf()
{
    $maintenances = Maintenance::all();

    foreach ($maintenances as $maintenance) {
        if ($maintenance->dokumentasi && file_exists(public_path($maintenance->dokumentasi))) {
            $path = public_path( $maintenance->dokumentasi);
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
        // Get the month from the request
        $bulan = $request->input('bulan');

        // Delete all maintenance records for the selected month
        Maintenance::whereMonth('tanggal_setting', $bulan)->delete();

        return redirect()->route('maintenances.index')
                         ->with('success', 'Semua data bulan ini telah dihapus.');
    }
}
