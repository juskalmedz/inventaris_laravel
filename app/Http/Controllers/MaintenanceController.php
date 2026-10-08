<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance;
use App\Models\Inventaris;
use App\Models\AuditLog;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Maintenance::with('inventaris');

        if ($status && $status !== 'all') {
            $query->where(function ($q) use ($status) {
                $q->where('status', $status);
                if ($status === 'Tidak Dapat Diperbaiki') {
                    $q->orWhere('status', 'Tidak Bisa Diperbaiki');
                }
            });
        }

        if ($search) {
            $hasNomorTiket = \Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'nomor_tiket');
            $hasVendor = \Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'vendor');
            $hasDeskripsiMasalah = \Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'deskripsi_masalah');

            $query->where(function ($q) use ($search, $hasNomorTiket, $hasVendor, $hasDeskripsiMasalah) {
                if ($hasNomorTiket) {
                    $q->where('nomor_tiket', 'like', "%{$search}%");
                }
                $q->orWhere('tiket', 'like', "%{$search}%");

                if ($hasVendor) {
                    $q->orWhere('vendor', 'like', "%{$search}%");
                }
                $q->orWhere('teknisi', 'like', "%{$search}%");

                if ($hasDeskripsiMasalah) {
                    $q->orWhere('deskripsi_masalah', 'like', "%{$search}%");
                }
                $q->orWhere('deskripsi', 'like', "%{$search}%");

                $q->orWhereHas('inventaris', function ($sub) use ($search) {
                    $sub->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                });
            });
        }

        $maintenances = $query->orderBy('created_at', 'desc')->paginate(15);
        $inventarisList = Inventaris::where('status', '!=', 'Dihapuskan')->get();

        $totalBiaya = 0;
        if (\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'biaya_aktual')) {
            $totalBiaya = Maintenance::where('status', 'Selesai')->sum('biaya_aktual');
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'biaya')) {
            $totalBiaya = Maintenance::where('status', 'Selesai')->sum('biaya');
        }

        $stats = [
            'total' => Maintenance::count(),
            'dalam_perbaikan' => Maintenance::where('status', 'Dalam Perbaikan')->count(),
            'selesai' => Maintenance::where('status', 'Selesai')->count(),
            'tidak_dapat_diperbaiki' => Maintenance::where('status', 'Tidak Dapat Diperbaiki')->orWhere('status', 'Tidak Bisa Diperbaiki')->count(),
            'total_biaya' => $totalBiaya ?? 0,
        ];

        return view('maintenance.index', compact('maintenances', 'inventarisList', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'jenis_maintenance' => 'required|in:Rutin,Perbaikan,Insidental',
            'deskripsi_masalah' => 'required|string',
            'estimasi_biaya' => 'required|numeric|min:0',
            'vendor' => 'required|string',
            'tanggal_mulai' => 'required|date',
        ]);

        $item = Inventaris::findOrFail($validated['inventaris_id']);
        $nomorTiket = 'MTN-' . date('Ym') . '-' . sprintf('%03d', Maintenance::count() + 1);

        $maintenance = Maintenance::create([
            'nomor_tiket' => $nomorTiket,
            'tiket' => $nomorTiket,
            'inventaris_id' => $validated['inventaris_id'],
            'jenis_maintenance' => $validated['jenis_maintenance'],
            'deskripsi_masalah' => $validated['deskripsi_masalah'],
            'deskripsi' => $validated['deskripsi_masalah'],
            'estimasi_biaya' => $validated['estimasi_biaya'],
            'biaya' => $validated['estimasi_biaya'],
            'biaya_aktual' => 0,
            'vendor' => $validated['vendor'],
            'teknisi' => $validated['vendor'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_lapor' => $validated['tanggal_mulai'],
            'status' => 'Dalam Perbaikan',
        ]);

        // Update status inventaris menjadi 'Dalam Perbaikan'
        $item->update(['status' => 'Dalam Perbaikan']);

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'CREATE',
            'module' => 'Maintenance',
            'description' => "Membuat tiket servis {$nomorTiket} untuk aset {$item->nama_barang} ({$item->kode_barang}).",
        ]);

        return redirect()->route('maintenance.index')->with('success', "Tiket servis {$nomorTiket} berhasil dibuat.");
    }

    public function updateStatus(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:Selesai,Tidak Dapat Diperbaiki',
            'biaya_aktual' => 'nullable|numeric|min:0',
            'tindakan_perbaikan' => 'nullable|string',
            'tanggal_selesai' => 'required|date',
        ]);

        $biayaFinal = $validated['biaya_aktual'] ?? $maintenance->estimasi_biaya ?? $maintenance->biaya ?? 0;

        $updateData = [
            'status' => $validated['status'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'biaya_aktual')) {
            $updateData['biaya_aktual'] = $biayaFinal;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'biaya')) {
            $updateData['biaya'] = $biayaFinal;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'tindakan_perbaikan')) {
            $updateData['tindakan_perbaikan'] = $validated['tindakan_perbaikan'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('maintenances', 'tindakan')) {
            $updateData['tindakan'] = $validated['tindakan_perbaikan'];
        }

        $maintenance->update($updateData);

        $item = Inventaris::findOrFail($maintenance->inventaris_id);
        if ($validated['status'] === 'Selesai') {
            $item->update([
                'status' => 'Tersedia',
                'kondisi' => 'Baik',
            ]);
        } else {
            $item->update([
                'status' => 'Dihapuskan',
                'kondisi' => 'Rusak Berat',
            ]);
        }

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'STATUS_CHANGE',
            'module' => 'Maintenance',
            'description' => "Menyelesaikan perbaikan tiket {$maintenance->nomor_tiket} dengan status: {$validated['status']}.",
        ]);

        return back()->with('success', "Status tiket {$maintenance->nomor_tiket} berhasil diperbarui ke {$validated['status']}.");
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $nomor = $maintenance->nomor_tiket;
        $maintenance->delete();

        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'DELETE',
            'module' => 'Maintenance',
            'description' => "Menghapus riwayat servis {$nomor}.",
        ]);

        return redirect()->route('maintenance.index')->with('success', "Riwayat servis {$nomor} berhasil dihapus.");
    }
}
