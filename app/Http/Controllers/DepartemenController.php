<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $query = Departemen::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_departemen', 'like', "%{$s}%")
                  ->orWhere('kode_departemen', 'like', "%{$s}%")
                  ->orWhere('kepala_divisi', 'like', "%{$s}%");
            });
        }

        $departemens = $query->paginate(15)->withQueryString();
        $allKaryawan = Karyawan::all();

        return view('departemen.index', compact('departemens', 'allKaryawan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|string|unique:departemens,kode_departemen|max:30',
            'nama_departemen' => 'required|string|max:255',
            'kepala_divisi' => 'required|string|max:255',
            'lokasi_lantai' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $dept = Departemen::create($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Departemen',
            'description' => "Menambahkan departemen baru {$dept->nama_departemen} ({$dept->kode_departemen})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('departemen.index')->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dept = Departemen::findOrFail($id);

        $validated = $request->validate([
            'kode_departemen' => 'required|string|max:30|unique:departemens,kode_departemen,' . $id,
            'nama_departemen' => 'required|string|max:255',
            'kepala_divisi' => 'required|string|max:255',
            'lokasi_lantai' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $dept->update($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Departemen',
            'description' => "Memperbarui departemen {$dept->nama_departemen} ({$dept->kode_departemen})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $dept = Departemen::findOrFail($id);

        // Safeguard relasi: Cek apakah masih ada pegawai terdaftar di departemen ini
        $assignedEmployees = Karyawan::where('departemen', $dept->nama_departemen)->count();
        if ($assignedEmployees > 0) {
            return back()->with('error', "Gagal menghapus departemen! Departemen {$dept->nama_departemen} masih menaungi {$assignedEmployees} pegawai terdaftar. Pindahkan pegawai terlebih dahulu.");
        }

        $nama = $dept->nama_departemen;
        $kode = $dept->kode_departemen;
        $dept->delete();

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'DELETE',
            'module' => 'Departemen',
            'description' => "Menghapus departemen {$nama} ({$kode})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('departemen.index')->with('success', "Departemen {$nama} berhasil dihapus.");
    }
}
