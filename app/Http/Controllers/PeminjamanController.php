<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index()
    {
        $loans = Peminjaman::with(['inventaris', 'karyawan'])->latest()->paginate(15);
        $inventaris = Inventaris::where('stok', '>', 0)->get();
        $karyawans = Karyawan::all();

        return view('peminjaman.index', compact('loans', 'inventaris', 'karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|exists:inventaris,id',
            'karyawan_id' => 'required|exists:karyawans,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:500',
            'catatan' => 'nullable|string',
        ]);

        $item = Inventaris::findOrFail($validated['inventaris_id']);
        if ($item->stok < $validated['jumlah']) {
            return back()->with('error', "Stok fisik barang tidak mencukupi. Tersedia: {$item->stok} unit.");
        }

        $docNum = 'PJM-' . date('Ymd') . '-' . str_pad(Peminjaman::count() + 1, 3, '0', STR_PAD_LEFT);

        $loan = Peminjaman::create(array_merge($validated, [
            'nomor_pinjam' => $docNum,
            'status' => 'Menunggu Approval',
            'kondisi_sebelum' => $item->kondisi,
        ]));

        return redirect()->route('peminjaman.index')->with('success', "Permohonan pinjaman No: {$docNum} berhasil dicatat.");
    }

    public function approve(Request $request, $id)
    {
        $loan = Peminjaman::with('inventaris')->findOrFail($id);

        DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => 'Dipinjam',
                'approved_by' => Auth::user()->name ?? 'Admin',
                'approved_at' => now(),
            ]);

            // Pengurangan stok & update status aset
            $loan->inventaris->decrement('stok', $loan->jumlah);
            if ($loan->inventaris->stok <= 0) {
                $loan->inventaris->update(['status' => 'Dipinjam']);
            }
        });

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman disetujui. Stok unit fisik telah disesuaikan.');
    }

    public function processReturn(Request $request, $id)
    {
        $loan = Peminjaman::with('inventaris')->findOrFail($id);

        $validated = $request->validate([
            'kondisi_sesudah' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'catatan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $loan->update([
                'status' => 'Dikembalikan',
                'kondisi_sesudah' => $validated['kondisi_sesudah'],
                'catatan' => $validated['catatan'] ?? $loan->catatan,
            ]);

            // Kembalikan stok unit fisik
            $loan->inventaris->increment('stok', $loan->jumlah);
            $loan->inventaris->update([
                'kondisi' => $validated['kondisi_sesudah'],
                'status' => 'Tersedia'
            ]);
        });

        return redirect()->route('peminjaman.index')->with('success', 'Aset berhasil diterima kembali dan stok telah dipulihkan.');
    }
}
