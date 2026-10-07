<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangKeluar::with(['inventaris', 'karyawan'])->latest('tanggal');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nomor_transaksi', 'like', "%{$s}%")
                  ->orWhere('keperluan', 'like', "%{$s}%")
                  ->orWhereHas('inventaris', function($sub) use ($s) {
                      $sub->where('nama_barang', 'like', "%{$s}%");
                  });
            });
        }

        $barangKeluarList = $query->paginate(15)->withQueryString();
        $inventarisList = Inventaris::where('stok', '>', 0)->get();
        $karyawanList = Karyawan::all();

        return view('barang-keluar.index', compact('barangKeluarList', 'inventarisList', 'karyawanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'karyawan_id' => 'required|exists:karyawans,id',
            'keperluan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $item = Inventaris::findOrFail($validated['inventaris_id']);

        // Safeguard stok fisik
        if ($item->stok < $validated['jumlah']) {
            return back()->with('error', "Gagal mengeluarkan aset! Stok fisik tidak mencukupi (Tersisa: {$item->stok} unit, Diminta: {$validated['jumlah']} unit).");
        }

        DB::transaction(function() use ($validated, $item, $request) {
            $docNum = 'BK-' . date('Ymd') . '-' . str_pad(BarangKeluar::count() + 1, 3, '0', STR_PAD_LEFT);

            $bk = BarangKeluar::create(array_merge($validated, [
                'nomor_transaksi' => $docNum,
            ]));

            // SINKRONISASI STOK 1:1: Kurangi stok fisik aset
            $item->decrement('stok', $validated['jumlah']);
            if ($item->stok <= 0) {
                $item->update(['status' => 'Tidak Tersedia']);
            }

            $karyawan = Karyawan::find($validated['karyawan_id']);
            $penerima = $karyawan ? $karyawan->nama : 'Pegawai';

            AuditLog::create([
                'kode_log' => 'LOG-' . time(),
                'user_name' => Auth::user()->name ?? 'Administrator',
                'user_role' => Auth::user()->role ?? 'Admin',
                'action' => 'CREATE',
                'module' => 'Barang Keluar',
                'description' => "Pengeluaran {$item->nama_barang} (-{$validated['jumlah']} unit) untuk {$penerima} ({$docNum})",
                'ip' => $request->ip(),
            ]);
        });

        return redirect()->route('barang-keluar.index')->with('success', 'Pengeluaran aset berhasil dicatat. Stok fisik telah disesuaikan.');
    }
}
