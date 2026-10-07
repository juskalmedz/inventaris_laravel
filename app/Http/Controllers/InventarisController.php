<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\AuditLog;
use App\Models\KopConfig;
use Illuminate\Support\Facades\Auth;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventaris::with([
            'kategori',
            'lokasi',
            'mutasis' => function($q) {
                $q->orderBy('created_at', 'desc')->with(['lokasiAwal', 'lokasiBaru', 'pemohon', 'creator', 'departemenBaru']);
            }
        ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_barang', 'like', "%{$s}%")
                  ->orWhere('kode_barang', 'like', "%{$s}%")
                  ->orWhere('deskripsi', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->paginate(15)->withQueryString();
        $kategoris = Kategori::all();
        $lokasis = Lokasi::all();
        $kopConfig = KopConfig::first();

        return view('inventaris.index', compact('items', 'kategoris', 'lokasis', 'kopConfig'));
    }

    public function show($id)
    {
        $item = Inventaris::with([
            'kategori',
            'lokasi',
            'mutasis' => function($q) {
                $q->orderBy('created_at', 'desc')->with(['lokasiAwal', 'lokasiBaru', 'pemohon', 'creator', 'departemenBaru']);
            }
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jenis_aset' => 'required|in:Habis Pakai,Tidak Habis Pakai',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Dipinjam,Dalam Maintenance,Tidak Tersedia',
            'harga_perkiraan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $item = Inventaris::create($validated);

        AuditLog::create([
            'kode_log' => 'LOG-'.time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Inventaris',
            'description' => "Menambahkan aset baru: {$item->nama_barang} ({$item->kode_barang})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('inventaris.index')->with('success', 'Aset baru berhasil ditambahkan ke katalog.');
    }

    public function update(Request $request, $id)
    {
        $item = Inventaris::findOrFail($id);

        $validated = $request->validate([
            'kode_barang' => 'required|unique:inventaris,kode_barang,'.$id,
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jenis_aset' => 'required|in:Habis Pakai,Tidak Habis Pakai',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Dipinjam,Dalam Maintenance,Tidak Tersedia',
            'harga_perkiraan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $item->update($validated);

        AuditLog::create([
            'kode_log' => 'LOG-'.time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'Inventaris',
            'description' => "Memperbarui aset: {$item->nama_barang} ({$item->kode_barang})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('inventaris.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $item = Inventaris::findOrFail($id);
        $name = $item->nama_barang;
        $code = $item->kode_barang;

        $item->delete();

        AuditLog::create([
            'kode_log' => 'LOG-'.time(),
            'user_name' => Auth::user()->name ?? 'Administrator',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'DELETE',
            'module' => 'Inventaris',
            'description' => "Menghapus aset: {$name} ({$code})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('inventaris.index')->with('success', 'Aset berhasil dihapus dari sistem.');
    }
}
