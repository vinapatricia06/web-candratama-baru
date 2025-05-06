<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login'); // Redirect ke login jika belum login
        }

        $user = Auth::user();

        // Jika user adalah superadmin atau ceo, izinkan akses tanpa pengecekan lebih lanjut
        if (in_array($user->role, ['superadmin', 'CEO'])) {
            return $next($request);
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        abort(403, 'Akses tidak diizinkan'); // Jika role tidak sesuai, munculkan error 403
    }
}