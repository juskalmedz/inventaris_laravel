<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\Peminjaman;
use App\Models\Maintenance;
use App\Models\Mutasi;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\AuditLog;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Karyawan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Metrik Utama
        $totalAset = Inventaris::count();
        $totalUnitFisik = Inventaris::sum('stok');
        $totalNilaiAset = Inventaris::selectRaw('SUM(stok * harga_perkiraan) as total')->value('total') ?? 0;
        
        $peminjamanAktif = Peminjaman::where('status', 'Dipinjam')->count();
        $peminjamanPending = Peminjaman::where('status', 'Menunggu Approval')->count();
        
        $mutasiPending = Mutasi::where('status', 'Menunggu Approval')->count();
        $maintenanceAktif = Maintenance::where(function($q) {
            $q->where('status', 'Dalam Perbaikan')->orWhere('status', 'Dalam Maintenance');
        })->count();
        
        $stokKritisCount = Inventaris::where('stok', '<=', 1)->where('stok', '>', 0)->count();
        $stokHabisCount = Inventaris::where('stok', '<=', 0)->count();

        // 2. Breakdown Kondisi Fisik Aset
        $kondisiBaik = Inventaris::where('kondisi', 'Baik')->count();
        $kondisiRusakRingan = Inventaris::where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat = Inventaris::where('kondisi', 'Rusak Berat')->count();

        // 3. Breakdown per Kategori
        $kategoriStats = Kategori::withCount('inventaris')
            ->orderByDesc('inventaris_count')
            ->take(5)
            ->get();

        // 4. Overdue Loans (Jatuh Tempo Pengembalian)
        $today = Carbon::today()->toDateString();
        $overdueLoans = Peminjaman::with(['inventaris', 'karyawan'])
            ->where('status', 'Dipinjam')
            ->where(function($q) use ($today) {
                $q->where(function($sub) use ($today) {
                    $sub->whereNotNull('rencana_kembali')
                        ->where('rencana_kembali', '<', $today);
                })->orWhere(function($sub) use ($today) {
                    $sub->whereNull('rencana_kembali')
                        ->whereNotNull('tanggal_kembali')
                        ->where('tanggal_kembali', '<', $today);
                });
            })
            ->latest('tanggal_pinjam')
            ->take(6)
            ->get();

        // 5. Pending Approval Hub (Peminjaman & Mutasi Menunggu Tindakan)
        $pendingLoansList = Peminjaman::with(['inventaris', 'karyawan'])
            ->where('status', 'Menunggu Approval')
            ->latest()
            ->take(6)
            ->get();

        $pendingMutasiList = Mutasi::with(['inventaris', 'lokasiAwal', 'lokasiBaru', 'pemohon'])
            ->where('status', 'Menunggu Approval')
            ->latest()
            ->take(6)
            ->get();

        // 6. Sirkulasi Terbaru
        $recentLoans = Peminjaman::with(['inventaris', 'karyawan'])->latest()->take(6)->get();
        $recentMutasi = Mutasi::with(['inventaris', 'lokasiAwal', 'lokasiBaru', 'pemohon', 'departemenBaru'])->latest()->take(6)->get();
        $recentLogs = AuditLog::latest()->take(8)->get();
        $recentMaintenance = Maintenance::with('inventaris')->latest()->take(6)->get();
        $criticalAssets = Inventaris::with(['kategori', 'lokasi'])->where('stok', '<=', 1)->orWhere('kondisi', 'Rusak Berat')->take(5)->get();
        
        $barangMasukRecent = BarangMasuk::with('inventaris')->latest()->take(6)->get();
        $barangKeluarRecent = BarangKeluar::with(['inventaris', 'karyawan'])->latest()->take(6)->get();

        // 7. Master Data Pendukung Modal Aksi Cepat
        $allInventaris = Inventaris::select('id', 'kode_barang', 'nama_barang', 'stok', 'kondisi', 'status')->orderBy('nama_barang')->get();
        $allKaryawan = Karyawan::select('id', 'nama', 'no_telepon', 'jabatan')->orderBy('nama')->get();
        $allKategori = Kategori::select('id', 'nama_kategori')->get();
        $allLokasi = Lokasi::select('id', 'nama_lokasi')->get();

        return view('dashboard', compact(
            'totalAset', 'totalUnitFisik', 'totalNilaiAset',
            'peminjamanAktif', 'peminjamanPending', 'mutasiPending',
            'maintenanceAktif', 'stokKritisCount', 'stokHabisCount',
            'kondisiBaik', 'kondisiRusakRingan', 'kondisiRusakBerat',
            'kategoriStats', 'overdueLoans',
            'pendingLoansList', 'pendingMutasiList',
            'recentLoans', 'recentMutasi', 'recentLogs', 'recentMaintenance',
            'criticalAssets', 'barangMasukRecent', 'barangKeluarRecent',
            'allInventaris', 'allKaryawan', 'allKategori', 'allLokasi'
        ));
    }

    public function searchAsset(Request $request)
    {
        $q = trim((string)$request->input('q', ''));
        if ($q === '') {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan kode aset atau scan barcode.'
            ], 422);
        }

        $asset = Inventaris::with(['kategori', 'lokasi'])
            ->where('kode_barang', $q)
            ->orWhere('barcode', $q)
            ->orWhere('nama_barang', 'like', "%{$q}%")
            ->first();

        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => "Aset dengan kode atau barcode '{$q}' tidak ditemukan di database."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'asset' => [
                'id' => $asset->id,
                'kode_barang' => $asset->kode_barang,
                'barcode' => $asset->barcode,
                'nama_barang' => $asset->nama_barang,
                'kategori' => $asset->kategori->nama_kategori ?? '-',
                'lokasi' => $asset->lokasi->nama_lokasi ?? '-',
                'stok' => $asset->stok,
                'kondisi' => $asset->kondisi,
                'status' => $asset->status,
                'harga_perkiraan' => (float)$asset->harga_perkiraan,
                'harga_formatted' => 'Rp ' . number_format($asset->harga_perkiraan, 0, ',', '.'),
                'deskripsi' => $asset->deskripsi ?? 'Tidak ada catatan tambahan',
            ]
        ]);
    }
}
