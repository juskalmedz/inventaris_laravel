<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Izinkan jika user memiliki salah satu peran yang diizinkan
        $user = $request->user();

        if (!$user) {
            // Untuk sesi demo atau simulasi tanpa login penuh
            $simulatedRole = session('simulated_role', 'Admin');
            if (in_array($simulatedRole, $roles)) {
                return $next($request);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akses ditolak oleh Matriks Hak Akses RBAC.',
                'user_role' => $user->role,
                'required_roles' => $roles,
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', "Peran Anda ({$user->role}) tidak memiliki izin untuk membuka halaman tersebut.");
    }
}
