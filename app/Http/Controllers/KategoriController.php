<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Inventaris;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::withCount('inventaris');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_kategori', 'like', "%{$s}%")
                  ->orWhere('kode_kategori', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        $kategoris = $query->paginate(15)->withQueryString();
        return view('kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kategori' => 'required|string|unique:kategoris,kode_kategori|max:30',
            'nama_kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $kategori = Kategori::create($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Kategori',
            'description' => "Menambah kategori baru {$kategori->nama_kategori} ({$kategori->kode_kategori})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori aset berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:30|unique:kategoris,kode_kategori,' . $id,
            'nama_kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $kategori->update($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Kategori',
            'description' => "Memperbarui kategori {$kategori->nama_kategori} ({$kategori->kode_kategori})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        // Safeguard relasi: Cek apakah kategori masih dipakai aset
        $usedAssets = Inventaris::where('kategori_id', $id)->count();
        if ($usedAssets > 0) {
            return back()->with('error', "Gagal menghapus kategori! Kategori {$kategori->nama_kategori} masih digunakan oleh {$usedAssets} aset di katalog. Pindahkan kategori aset terlebih dahulu.");
        }

        $nama = $kategori->nama_kategori;
        $kode = $kategori->kode_kategori;
        $kategori->delete();

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'DELETE',
            'module' => 'Kategori',
            'description' => "Menghapus kategori {$nama} ({$kode})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('kategori.index')->with('success', "Kategori {$nama} berhasil dihapus.");
    }
}
