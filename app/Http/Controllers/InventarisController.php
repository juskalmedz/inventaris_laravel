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

        if ($request->filled('bulk_search')) {
            $raw = $request->bulk_search;
            $codes = is_array($raw) ? $raw : array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)$raw)));
            if (!empty($codes)) {
                $query->where(function($q) use ($codes) {
                    $q->whereIn('kode_barang', $codes);
                    if (\Illuminate\Support\Facades\Schema::hasColumn('inventaris', 'barcode')) {
                        $q->orWhereIn('barcode', $codes);
                    }
                    $q->orWhereIn('id', array_filter($codes, 'is_numeric'));
                });
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_barang', 'like', "%{$s}%")
                  ->orWhere('kode_barang', 'like', "%{$s}%")
                  ->orWhere('deskripsi', 'like', "%{$s}%");
                if (\Illuminate\Support\Facades\Schema::hasColumn('inventaris', 'barcode')) {
                    $q->orWhere('barcode', 'like', "%{$s}%");
                }
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

        if ($request->filled('jenis_aset')) {
            $query->where('jenis_aset', $request->jenis_aset);
        }

        if ($request->filled('stok_level')) {
            if ($request->stok_level === 'tersedia') {
                $query->where('stok', '>', 3);
            } elseif ($request->stok_level === 'kritis') {
                $query->whereBetween('stok', [1, 3]);
            } elseif ($request->stok_level === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'kode');
        switch ($sort) {
            case 'nama_asc':
                $query->orderBy('nama_barang', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_barang', 'desc');
                break;
            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;
            case 'stok_asc':
                $query->orderBy('stok', 'asc');
                break;
            case 'nilai_desc':
                $query->orderByRaw('(stok * harga_perkiraan) desc');
                break;
            case 'kode':
            default:
                $query->orderBy('kode_barang', 'asc');
                break;
        }

        $items = $query->paginate(24)->withQueryString();
        $kategoris = Kategori::all();
        $lokasis = Lokasi::all();
        $kopConfig = KopConfig::first();

        // 1:1 Stats Summary matching React App.tsx
        $totalAset = Inventaris::count();
        $totalStok = Inventaris::sum('stok');
        $totalNilai = Inventaris::selectRaw('SUM(stok * harga_perkiraan) as total')->value('total') ?? 0;
        $stokTersedia = Inventaris::where('status', 'Tersedia')->sum('stok');
        $stokKritisCount = Inventaris::whereBetween('stok', [1, 3])->count();
        $stokHabisCount = Inventaris::where('stok', '<=', 0)->count();
        $kondisiBaikCount = Inventaris::where('kondisi', 'Baik')->count();
        $dalamMaintenanceCount = Inventaris::where('status', 'Dalam Maintenance')->orWhere('status', 'Dalam Perbaikan')->count();
        $asetTetapCount = Inventaris::where('jenis_aset', 'Tidak Habis Pakai')->count();
        $habisPakaiCount = Inventaris::where('jenis_aset', 'Habis Pakai')->count();

        return view('inventaris.index', compact(
            'items', 'kategoris', 'lokasis', 'kopConfig',
            'totalAset', 'totalStok', 'totalNilai', 'stokTersedia',
            'stokKritisCount', 'stokHabisCount', 'kondisiBaikCount', 'dalamMaintenanceCount',
            'asetTetapCount', 'habisPakaiCount'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = Inventaris::with(['kategori', 'lokasi']);

        if ($request->filled('bulk_search')) {
            $raw = $request->bulk_search;
            $codes = is_array($raw) ? $raw : array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)$raw)));
            if (!empty($codes)) {
                $query->where(function($q) use ($codes) {
                    $q->whereIn('kode_barang', $codes);
                    if (\Illuminate\Support\Facades\Schema::hasColumn('inventaris', 'barcode')) {
                        $q->orWhereIn('barcode', $codes);
                    }
                    $q->orWhereIn('id', array_filter($codes, 'is_numeric'));
                });
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_barang', 'like', "%{$s}%")
                  ->orWhere('kode_barang', 'like', "%{$s}%")
                  ->orWhere('deskripsi', 'like', "%{$s}%");
                if (\Illuminate\Support\Facades\Schema::hasColumn('inventaris', 'barcode')) {
                    $q->orWhere('barcode', 'like', "%{$s}%");
                }
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

        if ($request->filled('jenis_aset')) {
            $query->where('jenis_aset', $request->jenis_aset);
        }

        $items = $query->orderBy('kode_barang', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data_inventaris_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($items) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM
            fputs($output, "\xEF\xBB\xBF");
            // CSV Header
            fputcsv($output, [
                'ID',
                'Kode Barang',
                'Nomor Barcode',
                'Nama Barang',
                'Kategori',
                'Ruangan / Lokasi',
                'Tipe Aset',
                'Stok Fisik',
                'Satuan',
                'Kondisi',
                'Status',
                'Harga Satuan (Rp)',
                'Total Nilai (Rp)',
                'Spesifikasi / Deskripsi'
            ]);

            foreach ($items as $item) {
                fputcsv($output, [
                    $item->id,
                    $item->kode_barang,
                    $item->barcode ?? '',
                    $item->nama_barang,
                    $item->kategori->nama_kategori ?? '-',
                    $item->lokasi->nama_lokasi ?? '-',
                    $item->jenis_aset ?? 'Tidak Habis Pakai',
                    $item->stok,
                    $item->satuan ?? 'Unit',
                    $item->kondisi,
                    $item->status,
                    $item->harga_perkiraan,
                    ($item->stok * $item->harga_perkiraan),
                    $item->deskripsi ?? ''
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
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
            'barcode' => 'nullable|string|max:100',
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
            'barcode' => 'nullable|string|max:100',
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
