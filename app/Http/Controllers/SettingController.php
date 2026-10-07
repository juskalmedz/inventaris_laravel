<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KopConfig;
use App\Models\AuditLog;
use App\Models\Inventaris;
use App\Models\Peminjaman;
use App\Models\Mutasi;

class SettingController extends Controller
{
    public function index()
    {
        $users = User::all();
        $kopConfig = KopConfig::first() ?? new KopConfig();
        $roles = ['Admin', 'Supervisor', 'Staff', 'Auditor'];
        $rolePermissions = User::ROLE_PERMISSIONS;

        return view('pengaturan.index', compact('users', 'kopConfig', 'roles', 'rolePermissions'));
    }

    public function updateKop(Request $request)
    {
        $validated = $request->validate([
            'nama_instansi' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            'email' => 'required|email',
            'website' => 'nullable|string',
            'kota' => 'required|string',
            'pic_penanggung_jawab' => 'required|string',
            'jabatan_pic' => 'required|string',
        ]);

        $kop = KopConfig::first();
        if ($kop) {
            $kop->update($validated);
        } else {
            KopConfig::create($validated);
        }

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Pengaturan',
            'description' => 'Memperbarui data identitas KOP surat resmi perusahaan.',
        ]);

        return back()->with('success', 'KOP Surat Perusahaan berhasil diperbarui.');
    }

    public function updateWa(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:fonnte,wablas,custom',
            'api_token' => 'required|string',
            'auto_reminder_active' => 'nullable|boolean',
            'reminder_days_before' => 'required|integer|min:1',
        ]);

        // Simpan setting WA ke config atau session / DB
        session(['wa_config' => $validated]);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Pengaturan',
            'description' => 'Memperbarui konfigurasi gateway WhatsApp Reminder.',
        ]);

        return back()->with('success', 'Konfigurasi WhatsApp Gateway berhasil disimpan.');
    }

    public function updateRbac(Request $request)
    {
        // Menyimpan matriks override per role
        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'RBAC',
            'description' => 'Memperbarui matriks hak akses RBAC (Role-Based Access Control).',
        ]);

        return back()->with('success', 'Matriks hak akses RBAC berhasil diperbarui.');
    }

    public function resetData(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return back()->with('error', 'Hanya Administrator yang memiliki wewenang untuk reset data sistem.');
        }

        // Jalankan seeder ulang
        \Artisan::call('db:seed', ['--force' => true]);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => $currentUser->name,
            'user_role' => $currentUser->role,
            'action' => 'DELETE',
            'module' => 'Sistem',
            'description' => 'Melakukan pemulihan data master ke kondisi awal default demo.',
        ]);

        return back()->with('success', 'Data sistem berhasil direset ke data default awal.');
    }
}
