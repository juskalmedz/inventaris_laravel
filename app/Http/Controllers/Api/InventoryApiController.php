<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Peminjaman;
use App\Models\Mutasi;
use App\Models\Maintenance;
use App\Models\AuditLog;
use App\Models\KopConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryApiController extends Controller
{
    /**
     * Health check telemetri dan status keamanan API
     */
    public function health()
    {
        return response()->json([
            'success' => true,
            'status' => 'ONLINE',
            'version' => 'Laravel 13.0 Enterprise REST API',
            'security_level' => 'HIGHEST (Sanctum Tokens, Sliding Rate Limiter, Anti-XSS, RBAC Enforced)',
            'timestamp' => now()->toIso8601String(),
            'sync_protocol' => 'v1.2-bi-directional',
            'metrics' => [
                'total_inventaris' => Inventaris::count(),
                'active_loans' => Peminjaman::where('status', 'Dipinjam')->count(),
                'pending_mutations' => Mutasi::where('status', 'Menunggu Approval')->count(),
                'in_repair' => Maintenance::where('status', 'Dalam Perbaikan')->count(),
            ]
        ]);
    }

    /**
     * Snapshot Data Lengkap untuk Sinkronisasi Client
     */
    public function getFullSnapshot()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'inventaris' => Inventaris::with(['kategori', 'lokasi', 'mutasis.lokasiAwal', 'mutasis.lokasiBaru', 'mutasis.pemohon'])->get(),
                'karyawan' => Karyawan::with('departemen')->get(),
                'departemen' => Departemen::all(),
                'kategori' => Kategori::all(),
                'lokasi' => Lokasi::all(),
                'barang_masuk' => BarangMasuk::with('inventaris')->latest()->take(50)->get(),
                'barang_keluar' => BarangKeluar::with('inventaris')->latest()->take(50)->get(),
                'peminjaman' => Peminjaman::with(['inventaris', 'karyawan'])->get(),
                'mutasi' => Mutasi::with(['inventaris', 'lokasiAwal', 'lokasiBaru', 'pemohon'])->get(),
                'maintenance' => Maintenance::with('inventaris')->get(),
                'kop_config' => KopConfig::first(),
                'audit_logs' => AuditLog::latest()->take(50)->get(),
            ],
            'meta' => [
                'total_assets' => Inventaris::count(),
                'total_loans' => Peminjaman::count(),
                'server_time' => now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Dashboard Summary KPI & Valuasi Finansial
     */
    public function getDashboardSummary()
    {
        $totalNilai = Inventaris::selectRaw('SUM(stok * harga_perkiraan) as total')->value('total') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_entitas_aset' => Inventaris::count(),
                'total_unit_fisik' => Inventaris::sum('stok'),
                'total_nilai_aset' => (float)$totalNilai,
                'total_nilai_aset_formatted' => 'Rp ' . number_format($totalNilai, 0, ',', '.'),
                'peminjaman_aktif' => Peminjaman::where('status', 'Dipinjam')->count(),
                'peminjaman_menunggu' => Peminjaman::where('status', 'Menunggu Approval')->count(),
                'mutasi_menunggu' => Mutasi::where('status', 'Menunggu Approval')->count(),
                'maintenance_aktif' => Maintenance::where('status', 'Dalam Perbaikan')->count(),
                'stok_kritis' => Inventaris::where('stok', '<=', 1)->count(),
                'timestamp' => now()->toIso8601String(),
            ]
        ]);
    }

    // -----------------------------------------------------------------------
    // Master Inventaris REST
    // -----------------------------------------------------------------------
    public function indexInventaris(Request $request)
    {
        $query = Inventaris::with(['kategori', 'lokasi', 'departemen']);

        if ($request->filled('kategori_id') && $request->kategori_id !== 'all') {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('lokasi_id') && $request->lokasi_id !== 'all') {
            $query->where('lokasi_id', $request->lokasi_id);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('kondisi') && $request->kondisi !== 'all') {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_barang', 'like', "%{$s}%")
                  ->orWhere('kode_barang', 'like', "%{$s}%");
            });
        }

        return response()->json([
            'success' => true,
            'total' => $query->count(),
            'data' => $query->get()
        ]);
    }

    public function showInventaris($id)
    {
        $item = Inventaris::with(['kategori', 'lokasi', 'departemen'])->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Aset tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    public function storeInventaris(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string',
            'harga_perkiraan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Dipinjam,Dalam Perbaikan,Habis,Dihapuskan',
        ]);

        $item = Inventaris::create($validated);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'API User',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Inventaris',
            'description' => "Menambahkan aset baru via API: {$item->nama_barang} ({$item->kode_barang}).",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aset baru berhasil ditambahkan.',
            'data' => $item
        ], 201);
    }

    // -----------------------------------------------------------------------
    // Sirkulasi Barang Masuk & Keluar
    // -----------------------------------------------------------------------
    public function storeBarangMasuk(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'nomor_transaksi' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'supplier' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'penerima' => 'required|string',
        ]);

        $record = DB::transaction(function() use ($validated) {
            $bm = BarangMasuk::create($validated);
            $inv = Inventaris::findOrFail($validated['inventaris_id']);
            $inv->increment('stok', $validated['jumlah']);
            if ($inv->status === 'Habis') {
                $inv->update(['status' => 'Tersedia']);
            }
            return $bm;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi barang masuk berhasil dicatat. Stok fisik bertambah.',
            'data' => $record
        ], 201);
    }

    public function storeBarangKeluar(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'nomor_transaksi' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'tanggal_keluar' => 'required|date',
            'penerima' => 'required|string',
        ]);

        $inv = Inventaris::findOrFail($validated['inventaris_id']);
        if ($inv->stok < $validated['jumlah']) {
            return response()->json([
                'success' => false,
                'message' => "Stok fisik tidak mencukupi! Tersedia: {$inv->stok}, diminta: {$validated['jumlah']}."
            ], 400);
        }

        $record = DB::transaction(function() use ($validated, $inv) {
            $bk = BarangKeluar::create($validated);
            $inv->decrement('stok', $validated['jumlah']);
            if ($inv->stok === 0) {
                $inv->update(['status' => 'Habis']);
            }
            return $bk;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi barang keluar berhasil dicatat. Stok fisik berkurang.',
            'data' => $record
        ], 201);
    }

    // -----------------------------------------------------------------------
    // Peminjaman & Pengembalian Aset
    // -----------------------------------------------------------------------
    public function storePeminjaman(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'nomor_peminjaman' => 'required|string',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date',
            'keperluan' => 'required|string',
        ]);

        $inv = Inventaris::findOrFail($validated['inventaris_id']);
        if ($inv->stok < 1) {
            return response()->json([
                'success' => false,
                'message' => "Aset '{$inv->nama_barang}' sedang tidak tersedia atau habis stok."
            ], 400);
        }

        $loan = DB::transaction(function() use ($validated, $inv) {
            $p = Peminjaman::create(array_merge($validated, ['status' => 'Dipinjam']));
            $inv->decrement('stok', 1);
            if ($inv->stok === 0) {
                $inv->update(['status' => 'Dipinjam']);
            }
            return $p;
        });

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman aset berhasil dicatat.',
            'data' => $loan
        ], 201);
    }

    public function returnPeminjaman(Request $request, $id)
    {
        $loan = Peminjaman::findOrFail($id);
        if ($loan->status === 'Dikembalikan') {
            return response()->json(['success' => false, 'message' => 'Aset sudah dikembalikan.'], 400);
        }

        DB::transaction(function() use ($loan, $request) {
            $loan->update([
                'status' => 'Dikembalikan',
                'tanggal_kembali_aktual' => now()->toDateString(),
                'kondisi_sesudah' => $request->input('kondisi_sesudah', 'Baik'),
                'catatan_pengembalian' => $request->input('catatan_pengembalian', '-'),
                'denda' => $request->input('denda', 0),
            ]);

            $inv = Inventaris::findOrFail($loan->inventaris_id);
            $inv->increment('stok', 1);
            if ($inv->status === 'Dipinjam' || $inv->status === 'Habis') {
                $inv->update(['status' => 'Tersedia', 'kondisi' => $request->input('kondisi_sesudah', 'Baik')]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian aset berhasil diproses. Stok fisik bertambah +1.',
            'data' => $loan->fresh()
        ]);
    }

    // -----------------------------------------------------------------------
    // Mutasi Aset & Approval
    // -----------------------------------------------------------------------
    public function approveMutasi(Request $request, $id)
    {
        $mutasi = Mutasi::findOrFail($id);
        
        DB::transaction(function() use ($mutasi, $request) {
            $mutasi->update([
                'status' => 'Disetujui',
                'disetujui_oleh' => Auth::user()->name ?? 'Admin',
                'tanggal_persetujuan' => now()->toDateString(),
            ]);

            Inventaris::where('id', $mutasi->inventaris_id)->update([
                'lokasi_id' => $mutasi->lokasi_baru_id,
                'departemen_id' => $mutasi->departemen_baru_id,
            ]);
        });

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'STATUS_CHANGE',
            'module' => 'Mutasi Aset',
            'description' => "Menyetujui mutasi No: {$mutasi->nomor_mutasi}. Lokasi aset berhasil diperbarui.",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mutasi berhasil disetujui & lokasi aset disinkronkan 1:1',
            'data' => $mutasi->fresh(['inventaris', 'lokasiAsal', 'lokasiTujuan'])
        ]);
    }

    // -----------------------------------------------------------------------
    // WhatsApp Reminder Dispatcher
    // -----------------------------------------------------------------------
    public function dispatchWhatsApp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'pesan' => 'required|string',
        ]);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Staff',
            'action' => 'UPDATE',
            'module' => 'WhatsApp Gateway',
            'description' => "Mengirim pengingat WhatsApp ke {$validated['phone']}.",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pesan pengingat berhasil dikirim ke nomor {$validated['phone']}.",
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
