<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Inventaris;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangMasuk::with('inventaris')->latest('tanggal');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('supplier', 'like', "%{$s}%")
                  ->orWhereHas('inventaris', function($sub) use ($s) {
                      $sub->where('nama_barang', 'like', "%{$s}%")
                          ->orWhere('kode_barang', 'like', "%{$s}%");
                  });
            });
        }

        $barangMasukList = $query->paginate(15)->withQueryString();
        $inventarisList = Inventaris::all();

        return view('barang-masuk.index', compact('barangMasukList', 'inventarisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'supplier' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function() use ($validated, $request) {
            $docNum = 'BM-' . date('Ymd') . '-' . str_pad(BarangMasuk::count() + 1, 3, '0', STR_PAD_LEFT);

            $bm = BarangMasuk::create(array_merge($validated, [
                'nomor_transaksi' => $docNum,
            ]));

            // SINKRONISASI STOK 1:1: Tambah stok fisik aset
            $item = Inventaris::findOrFail($validated['inventaris_id']);
            $item->increment('stok', $validated['jumlah']);
            if ($item->status === 'Tidak Tersedia' || $item->status === 'Habis') {
                $item->update(['status' => 'Tersedia']);
            }

            AuditLog::create([
                'kode_log' => 'LOG-' . time(),
                'user_name' => Auth::user()->name ?? 'Administrator',
                'user_role' => Auth::user()->role ?? 'Admin',
                'action' => 'CREATE',
                'module' => 'Barang Masuk',
                'description' => "Penerimaan pengadaan {$item->nama_barang} (+{$validated['jumlah']} unit) dari {$validated['supplier']} ({$docNum})",
                'ip' => $request->ip(),
            ]);
        });

        return redirect()->route('barang-masuk.index')->with('success', 'Transaksi barang masuk berhasil dicatat. Stok fisik bertambah.');
    }
}
