<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Departemen;
use App\Models\KopConfig;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Peminjaman;
use App\Models\Mutasi;
use App\Models\Maintenance;
use App\Models\Karyawan;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $kategoriId = $request->query('kategori_id');
        $lokasiId = $request->query('lokasi_id');
        $kondisi = $request->query('kondisi');
        $status = $request->query('status');
        $preset = $request->query('preset', 'rekap');

        $query = Inventaris::with(['kategori', 'lokasi']);

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }
        if ($lokasiId) {
            $query->where('lokasi_id', $lokasiId);
        }
        if ($kondisi) {
            $query->where('kondisi', $kondisi);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->orderBy('nama_barang', 'asc')->get();
        $kategoriList = Kategori::all();
        $lokasiList = Lokasi::all();
        $karyawanList = Karyawan::all();

        $barangMasuk = BarangMasuk::with('inventaris')->latest()->take(100)->get();
        $barangKeluar = BarangKeluar::with(['inventaris', 'karyawan'])->latest()->take(100)->get();
        $peminjaman = Peminjaman::with(['inventaris', 'karyawan'])->latest()->take(100)->get();
        $maintenance = Maintenance::with('inventaris')->latest()->take(100)->get();
        $mutasi = Mutasi::with(['inventaris', 'lokasiAwal', 'lokasiBaru', 'pemohon'])->latest()->take(100)->get();

        $kopConfig = KopConfig::first() ?? new KopConfig([
            'org_name' => 'KEMENTERIAN KOMUNIKASI DAN INFORMATIKA RI',
            'div_name' => 'DIREKTORAT JENDERAL SUMBER DAYA & PERANGKAT POS INFORMATIKA',
            'nomor_surat' => 'BA-INV/2026/X/001',
            'approver_title' => 'Kepala Bagian Umum & Perlengkapan',
            'approver_name' => 'Drs. Bambang Hariyanto, M.M.',
            'approver_nip' => 'NIP. 19750812 199903 1 002',
            'maker_title' => 'Petugas Pengelola Inventaris & Logistik',
            'maker_name' => 'Bambang Pratama, S.Kom.',
            'maker_nip' => 'NIP. 19880422 201101 1 005',
        ]);

        $totalAset = $items->count();
        $totalFisik = $items->sum('stok');
        $totalNilai = $items->sum(function($item) {
            return $item->stok * $item->harga_perkiraan;
        });
        $kritisCount = $items->where('stok', '<=', 1)->count();

        return view('laporan.index', compact(
            'items', 'kategoriList', 'lokasiList', 'karyawanList',
            'barangMasuk', 'barangKeluar', 'peminjaman', 'maintenance', 'mutasi',
            'kopConfig', 'totalAset', 'totalFisik', 'totalNilai', 'kritisCount', 'preset'
        ));
    }

    public function cetak(Request $request)
    {
        $items = Inventaris::with(['kategori', 'lokasi'])->get();
        $kopConfig = KopConfig::first();
        $totalNilai = $items->sum(function($item) {
            return $item->stok * $item->harga_perkiraan;
        });

        return view('laporan.cetak', compact('items', 'kopConfig', 'totalNilai'));
    }

    public function exportExcel(Request $request)
    {
        $items = Inventaris::with(['kategori', 'lokasi'])->get();
        
        $filename = "rekap_inventaris_kantor_" . date('Y-m-d_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Kode Barang', 'Nama Barang', 'Kategori', 'Lokasi', 'Stok', 'Satuan', 'Kondisi', 'Status', 'Harga Perkiraan', 'Total Nilai'];

        $callback = function() use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->kode_barang,
                    $item->nama_barang,
                    $item->kategori->nama_kategori ?? $item->kategori->nama ?? '-',
                    $item->lokasi->nama_lokasi ?? $item->lokasi->nama ?? '-',
                    $item->stok,
                    $item->satuan,
                    $item->kondisi,
                    $item->status,
                    $item->harga_perkiraan,
                    $item->stok * $item->harga_perkiraan,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
