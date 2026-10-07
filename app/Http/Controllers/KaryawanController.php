<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\AuditLog;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('peminjamans');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', $request->departemen);
        }

        if ($request->filled('loan_status')) {
            if ($request->loan_status === 'dipinjam') {
                $query->whereHas('peminjamans', function($q) {
                    $q->where('status', 'Dipinjam');
                });
            } elseif ($request->loan_status === 'bebas') {
                $query->whereDoesntHave('peminjamans', function($q) {
                    $q->where('status', 'Dipinjam');
                });
            }
        }

        $karyawanList = $query->paginate(15)->withQueryString();
        $departemens = Departemen::all();

        // Statistik ringkasan 1:1
        $totalKaryawan = Karyawan::count();
        $totalDeptCount = Karyawan::distinct('departemen')->count('departemen');
        $activeBorrowersCount = Karyawan::whereHas('peminjamans', function($q) {
            $q->where('status', 'Dipinjam');
        })->count();
        $freeBorrowersCount = $totalKaryawan - $activeBorrowersCount;

        return view('karyawan.index', compact(
            'karyawanList', 'departemens', 'totalKaryawan', 
            'totalDeptCount', 'activeBorrowersCount', 'freeBorrowersCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawans,nip|max:50',
            'nama' => 'required|string|max:255',
            'departemen' => 'required|string',
            'jabatan' => 'nullable|string',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:30',
        ]);

        $karyawan = Karyawan::create($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Karyawan',
            'description' => "Menambahkan pegawai/PIC baru: {$karyawan->nama} (NIP: {$karyawan->nip})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data pegawai baru berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:karyawans,nip,' . $id,
            'nama' => 'required|string|max:255',
            'departemen' => 'required|string',
            'jabatan' => 'nullable|string',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:30',
        ]);

        $karyawan->update($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Karyawan',
            'description' => "Memperbarui data pegawai: {$karyawan->nama} ({$karyawan->nip})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Safeguard relasi peminjaman aktif
        $activeLoans = Peminjaman::where('karyawan_id', $id)->where('status', 'Dipinjam')->count();
        if ($activeLoans > 0) {
            return back()->with('error', "Gagal menghapus pegawai! Pegawai {$karyawan->nama} masih memiliki {$activeLoans} aset pinjaman aktif.");
        }

        $nama = $karyawan->nama;
        $nip = $karyawan->nip;
        $karyawan->delete();

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'DELETE',
            'module' => 'Karyawan',
            'description' => "Menghapus pegawai {$nama} ({$nip})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('karyawan.index')->with('success', "Pegawai {$nama} berhasil dihapus dari sistem.");
    }
}
