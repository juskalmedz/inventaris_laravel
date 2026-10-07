<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\AuditLog;

class WhatsAppController extends Controller
{
    public function sendReminder(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id',
            'pesan' => 'required|string',
            'phone' => 'required|string',
        ]);

        $peminjaman = Peminjaman::with(['karyawan', 'inventaris'])->findOrFail($validated['peminjaman_id']);

        // Simulasi pengiriman via WhatsApp API Gateway (Fonnte/Wablas)
        // Log aktivitas
        AuditLog::create([
            'kode_log' => 'LOG-' . time(),
            'user_name' => Auth::user()->name ?? 'System',
            'user_role' => Auth::user()->role ?? 'Admin',
            'action' => 'UPDATE',
            'module' => 'WhatsApp Gateway',
            'description' => "Mengirimkan pesan pengingat jatuh tempo ke {$peminjaman->karyawan->nama} ({$validated['phone']}) untuk aset {$peminjaman->inventaris->nama_barang}.",
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pesan pengingat berhasil dikirim ke nomor {$validated['phone']}.",
                'preview_url' => "https://wa.me/" . preg_replace('/[^0-9]/', '', $validated['phone']) . "?text=" . urlencode($validated['pesan'])
            ]);
        }

        return back()->with('success', "Pengingat berhasil dikirim via WhatsApp ke {$peminjaman->karyawan->nama} ({$validated['phone']}).");
    }
}
