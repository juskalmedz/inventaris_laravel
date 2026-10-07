@extends('layouts.app')

@section('title', 'Pemeliharaan & Servis Aset - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pemeliharaan & Servis Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jadwal perbaikan rutin, tiket perbaikan vendor, dan monitoring anggaran reparasi aset kantor.</p>
        </div>
        <button onclick="document.getElementById('modalTambahMaintenance').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-medium shadow-md shadow-amber-500/20 text-sm transition-all">
            <i data-lucide="wrench" class="w-4 h-4"></i>
            Buat Tiket Servis
        </button>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase">Total Servis</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-amber-500 uppercase">Dalam Pengerjaan</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['dalam_perbaikan'] ?? 0 }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-emerald-500 uppercase">Selesai Diperbaiki</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['selesai'] ?? 0 }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="text-xs font-semibold text-slate-400 uppercase">Realisasi Biaya</div>
            <div class="text-lg font-bold text-slate-900 dark:text-white mt-1 truncate">Rp {{ number_format($stats['total_biaya'] ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabel Maintenance -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No. Tiket</th>
                        <th class="px-6 py-4">Aset / Barang</th>
                        <th class="px-6 py-4">Masalah & Vendor</th>
                        <th class="px-6 py-4">Tgl Mulai / Selesai</th>
                        <th class="px-6 py-4">Biaya (Estimasi / Realisasi)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($maintenances ?? [] as $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-amber-600 dark:text-amber-400">{{ $item->nomor_tiket }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <div class="text-xs font-mono text-slate-400">{{ $item->inventaris->kode_barang ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $item->vendor }}</div>
                            <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $item->deskripsi_masalah }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div>Mulai: {{ $item->tanggal_mulai }}</div>
                            <div class="text-slate-400">Selesai: {{ $item->tanggal_selesai ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div>Est: Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}</div>
                            <div class="font-bold text-emerald-600 dark:text-emerald-400">Akt: Rp {{ number_format($item->biaya_aktual ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status === 'Dalam Perbaikan')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400">Dalam Perbaikan</span>
                            @elseif($item->status === 'Selesai')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Selesai</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400">Rusak Berat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($item->status === 'Dalam Perbaikan')
                            <form action="{{ route('maintenance.updateStatus', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="Selesai">
                                <input type="hidden" name="tanggal_selesai" value="{{ date('Y-m-d') }}">
                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition" onclick="return confirm('Tandai servis selesai dan pulihkan status aset?')">
                                    Selesai
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada data pemeliharaan aset.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
