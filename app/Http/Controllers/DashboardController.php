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
        $maintenanceAktif = Maintenance::where('status', 'Dalam Perbaikan')->count();
        
        $stokKritisCount = Inventaris::where('stok', '<=', 1)->count();

        // 2. Sirkulasi Terbaru
        $recentLoans = Peminjaman::with(['inventaris', 'karyawan'])->latest()->take(6)->get();
        $recentMutasi = Mutasi::with(['inventaris', 'lokasiAwal', 'lokasiBaru', 'pemohon', 'departemenBaru'])->latest()->take(6)->get();
        $recentLogs = AuditLog::latest()->take(8)->get();
        $recentMaintenance = Maintenance::with('inventaris')->latest()->take(6)->get();
        $criticalAssets = Inventaris::with(['kategori', 'lokasi'])->where('stok', '<=', 1)->orWhere('kondisi', 'Rusak Berat')->take(5)->get();
        
        $barangMasukRecent = BarangMasuk::with('inventaris')->latest()->take(6)->get();
        $barangKeluarRecent = BarangKeluar::with(['inventaris', 'karyawan'])->latest()->take(6)->get();

        return view('dashboard', compact(
            'totalAset', 'totalUnitFisik', 'totalNilaiAset',
            'peminjamanAktif', 'peminjamanPending', 'mutasiPending',
            'maintenanceAktif', 'stokKritisCount',
            'recentLoans', 'recentMutasi', 'recentLogs', 'recentMaintenance',
            'criticalAssets', 'barangMasukRecent', 'barangKeluarRecent'
        ));
    }
}
