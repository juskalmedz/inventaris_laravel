<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mutasi;
use App\Models\Inventaris;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index()
    {
        $mutasis = Mutasi::with(['inventaris', 'lokasiAwal', 'lokasiBaru', 'pemohon'])->latest()->paginate(15);
        $inventaris = Inventaris::all();
        $lokasis = Lokasi::all();
        $karyawans = Karyawan::all();

        return view('mutasi.index', compact('mutasis', 'inventaris', 'lokasis', 'karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'lokasi_awal_id' => 'required|exists:lokasis,id',
            'lokasi_baru_id' => 'required|exists:lokasis,id|different:lokasi_awal_id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_permohonan' => 'required|date',
            'alasan' => 'required|string|max:500',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah aset sudah memiliki permohonan pending
        $hasPending = Mutasi::where('inventaris_id', $validated['inventaris_id'])
            ->where('status', 'Menunggu Approval')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Aset ini masih memiliki permohonan mutasi aktif yang menunggu approval.');
        }

        $docNum = 'MTS-' . date('Ymd') . '-' . str_pad(Mutasi::count() + 1, 3, '0', STR_PAD_LEFT);

        $mutasi = Mutasi::create(array_merge($validated, [
            'nomor_mutasi' => $docNum,
            'status' => 'Menunggu Approval',
            'created_by_user_id' => Auth::id() ?? 1,
        ]));

        AuditLog::create([
            'kode_log' => 'LOG-'.time(),
            'user_name' => Auth::user()->name ?? 'User',
            'user_role' => Auth::user()->role ?? 'Staff',
            'action' => 'CREATE',
            'module' => 'Mutasi',
            'description' => "Mengajukan mutasi aset {$docNum} ke ruangan tujuan",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('mutasi.index')->with('success', "Permohonan mutasi No: {$docNum} berhasil dibuat dan menunggu persetujuan.");
    }

    public function approve(Request $request, $id)
    {
        $mutasi = Mutasi::with('inventaris')->findOrFail($id);

        if ($mutasi->status !== 'Menunggu Approval') {
            return back()->with('error', 'Status mutasi ini tidak dalam antrean approval.');
        }

        DB::transaction(function () use ($mutasi, $request) {
            // 1. Update status mutasi
            $mutasi->update([
                'status' => 'Disetujui',
                'disetujui_oleh' => Auth::user()->name . ' (' . Auth::user()->role . ')',
                'tanggal_persetujuan' => now()->toDateString(),
                'catatan_approval' => $request->input('catatan_approval', 'Disetujui pimpinan.'),
            ]);

            // 2. SINKRONISASI 1:1: Update lokasi fisik aset di katalog inventaris
            $mutasi->inventaris->update([
                'lokasi_id' => $mutasi->lokasi_baru_id
            ]);

            AuditLog::create([
                'kode_log' => 'LOG-'.time(),
                'user_name' => Auth::user()->name ?? 'Admin',
                'user_role' => Auth::user()->role ?? 'Admin',
                'action' => 'APPROVE',
                'module' => 'Mutasi',
                'description' => "Menyetujui mutasi {$mutasi->nomor_mutasi}. Lokasi fisik aset resmi dialihkan.",
                'ip' => $request->ip(),
            ]);
        });

        return redirect()->route('mutasi.index')->with('success', 'Mutasi disetujui. Lokasi aset di katalog resmi diperbarui!');
    }

    public function reject(Request $request, $id)
    {
        $mutasi = Mutasi::findOrFail($id);

        $mutasi->update([
            'status' => 'Ditolak',
            'disetujui_oleh' => Auth::user()->name . ' (' . Auth::user()->role . ')',
            'catatan_approval' => 'Ditolak: ' . $request->input('alasan_penolakan', 'Tidak memenuhi kualifikasi pemindahan.'),
        ]);

        AuditLog::create([
            'kode_log' => 'LOG-'.time(),
            'user_name' => Auth::user()->name ?? 'Admin',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'REJECT',
            'module' => 'Mutasi',
            'description' => "Menolak permohonan mutasi {$mutasi->nomor_mutasi}",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('mutasi.index')->with('info', 'Permohonan mutasi aset telah ditolak.');
    }
}
