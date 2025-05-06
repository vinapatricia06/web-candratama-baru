<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User1;
use Illuminate\Support\Facades\Hash;
use App\Models\Omset;


class AuthController extends Controller
{
    // **Login**
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Ambil user berdasarkan username
        $user = User1::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            // Redirect berdasarkan role
            if ($user->isSuperADM()) {
                return redirect('/users'); //sudah
            } elseif ($user->isAdmin()) {
                return redirect('/surat/admin/dashboard'); //sudah
            } elseif ($user->isCEO()) {
                return redirect('/dashboard/CEO'); //sudah
            } elseif ($user->isDM()) {
                return redirect('/dashboard/marketing'); //sudah
            } elseif ($user->isIC()) {
                return redirect('/surat/interior_consultan');
            } elseif ($user->isWRH()) {
                return redirect('/surat/warehouse/dashboard'); 
            } elseif ($user->isFNC()) {
                return redirect('/surat/finance/dashboard'); //kurang dashboard
            }elseif ($user->isPCH()) {
                return redirect('/surat/purchasing/dashboard');
            } elseif ($user->isEks()) {
                return redirect('/surat-ekspedisi');
            } elseif ($user->isCS()) {
                return redirect('/surat/cleaning');
            } elseif ($user->isTeknisi()) {
                return redirect('/users');
            } else {
                abort(403, 'Akses tidak diizinkan');
            }
        }

        return response()->json(['message' => 'Username atau password salah'], 401);
    }

    // **Logout**
    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('message', 'Logout berhasil');
    } 

    

    public function dashboardCEO()
    {
        // Ambil user yang sedang login
        $user = Auth::user(); // Mengambil data pengguna yang sedang login
    
        // Ambil nama direktur dari data user
        $directorName = $user->nama; // Asumsi nama kolom di tabel users adalah 'nama'
    
        // Format waktu saat ini
        $dateTime = now()->format('l, d M Y H:i');  // Menampilkan tanggal dan waktu
    
        // Mengambil data omset per tahun dan bulan dari model
        $rekap = Omset::selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, SUM(nominal) as total_omset')
            ->groupBy('tahun', 'bulan')
            ->orderByDesc('tahun')
            ->orderBy('bulan')
            ->get();
    
        // Menyusun data omset per tahun
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
    
        // Mengirimkan data ke view dashboard
        return view('auth.dashboardCEO', compact('directorName', 'dateTime', 'data', 'labels', 'totalPerTahun'));
    }
}    