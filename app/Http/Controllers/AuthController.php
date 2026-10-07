<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = User::where('status', 'Aktif')->get();
        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $cleanUsername = strtolower(trim($validated['username']));
        $user = User::whereRaw('LOWER(username) = ?', [$cleanUsername])->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username tidak terdaftar di sistem.'
                ], 404);
            }
            return back()->withInput()->with('error', 'Username tidak terdaftar di sistem!');
        }

        if (!$user->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun dengan status Nonaktif tidak diizinkan masuk. Hubungi Administrator.'
                ], 403);
            }
            return back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator sistem.');
        }

        $inputPassword = $validated['password'];
        $isPasswordValid = Hash::check($inputPassword, $user->password) 
            || $inputPassword === 'password' 
            || $inputPassword === 'admin'
            || ($user->username === 'admin' && $inputPassword === 'admin123');

        if (!$isPasswordValid) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kata sandi tidak sesuai! (Gunakan password demo: "password")'
                ], 401);
            }
            return back()->withInput()->with('error', 'Username atau kata sandi tidak sesuai! (Gunakan password demo: "password")');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => $user->name,
            'user_role' => $user->role,
            'action' => 'STATUS_CHANGE',
            'module' => 'Autentikasi',
            'description' => "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem.",
            'ip' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            $token = method_exists($user, 'createToken') ? $user->createToken('web-token')->plainTextToken : null;
            return response()->json([
                'success' => true,
                'message' => "Selamat datang kembali, {$user->name}!",
                'token' => $token,
                'user' => $user->toUserAccountArray(),
                'permissions' => $user->getPermissions(),
            ]);
        }

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$user->name}! Login berhasil sebagai {$user->role}.");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'kode_log' => 'LOG-' . time(),
                'user_name' => $user->name,
                'user_role' => $user->role,
                'action' => 'STATUS_CHANGE',
                'module' => 'Autentikasi',
                'description' => "Pengguna {$user->name} ({$user->role}) keluar dari sistem.",
                'ip' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil keluar dari sistem.'
            ]);
        }

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem inventaris.');
    }

    public function me(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        if (!$user) {
            return response()->json([
                'authenticated' => false,
                'user' => null,
            ], 401);
        }

        return response()->json([
            'authenticated' => true,
            'user' => $user->toUserAccountArray(),
            'permissions' => $user->getPermissions(),
            'role' => $user->role,
            'status' => $user->status,
        ]);
    }

    public function switchRoleSimulation(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:Admin,Supervisor,Staff,Auditor',
        ]);

        $currentUser = Auth::user();

        if ($currentUser && !$currentUser->hasPermission('canSwitchRoleSimulation') && !$currentUser->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peran aktif Anda tidak diizinkan untuk melakukan simulasi ganti peran.'
                ], 403);
            }
            return back()->with('error', 'Akses ditolak: Hanya peran dengan izin canSwitchRoleSimulation yang dapat beralih peran.');
        }

        $targetRole = $validated['role'];
        $targetUser = User::where('role', $targetRole)->where('status', 'Aktif')->first();

        if ($targetUser) {
            Auth::login($targetUser);
            $request->session()->put('simulated_role', $targetRole);

            AuditLog::create([
                'kode_log' => 'LOG-' . time(),
                'user_name' => $targetUser->name,
                'user_role' => $targetUser->role,
                'action' => 'STATUS_CHANGE',
                'module' => 'RBAC',
                'description' => "Beralih simulasi peran ke {$targetRole} (@{$targetUser->username}).",
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Simulasi peran berhasil diubah ke {$targetRole}",
                    'user' => $targetUser->toUserAccountArray(),
                    'permissions' => $targetUser->getPermissions(),
                ]);
            }

            return back()->with('success', "Simulasi peran berhasil dialihkan ke: {$targetRole} ({$targetUser->name}).");
        }

        return back()->with('error', "Pengguna aktif dengan peran {$targetRole} tidak ditemukan.");
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'password_baru' => 'required|string|min:4',
            'konfirmasi_password' => 'required|same:password_baru',
        ], [
            'password_baru.min' => 'Kata sandi minimal 4 karakter.',
            'konfirmasi_password.same' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password_baru']),
        ]);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => $user->name,
            'user_role' => $user->role,
            'action' => 'UPDATE',
            'module' => 'Autentikasi',
            'description' => "Pengguna {$user->name} memperbarui kata sandi akun.",
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
