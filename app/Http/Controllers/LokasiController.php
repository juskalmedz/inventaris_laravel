<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lokasi;
use App\Models\Inventaris;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Lokasi::withCount('inventaris');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_lokasi', 'like', "%{$s}%")
                  ->orWhere('kode_lokasi', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        $lokasis = $query->paginate(15)->withQueryString();
        return view('lokasi.index', compact('lokasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_lokasi' => 'required|string|unique:lokasis,kode_lokasi|max:30',
            'nama_lokasi' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $lokasi = Lokasi::create($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Lokasi',
            'description' => "Menambah lokasi/ruangan baru {$lokasi->nama_lokasi} ({$lokasi->kode_lokasi})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi/ruangan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $validated = $request->validate([
            'kode_lokasi' => 'required|string|max:30|unique:lokasis,kode_lokasi,' . $id,
            'nama_lokasi' => 'required|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $lokasi->update($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Lokasi',
            'description' => "Memperbarui lokasi/ruangan {$lokasi->nama_lokasi} ({$lokasi->kode_lokasi})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Data lokasi/ruangan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        // Safeguard relasi: Cek apakah masih ada aset yang ditempatkan di lokasi ini
        $placedAssets = Inventaris::where('lokasi_id', $id)->count();
        if ($placedAssets > 0) {
            return back()->with('error', "Gagal menghapus lokasi! Ruangan {$lokasi->nama_lokasi} masih menjadi tempat penyimpanan {$placedAssets} aset di katalog. Pindahkan aset terlebih dahulu.");
        }

        $nama = $lokasi->nama_lokasi;
        $kode = $lokasi->kode_lokasi;
        $lokasi->delete();

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'DELETE',
            'module' => 'Lokasi',
            'description' => "Menghapus lokasi {$nama} ({$kode})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('lokasi.index')->with('success', "Lokasi {$nama} berhasil dihapus.");
    }
}
