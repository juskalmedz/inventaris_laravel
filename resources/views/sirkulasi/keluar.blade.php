@extends('layouts.app')

@section('title', 'Sirkulasi Barang Keluar - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sirkulasi Barang Keluar</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan pengeluaran barang habis pakai, pemakaian divisi, dan pengurangan stok aset secara real-time.</p>
        </div>
        <button onclick="document.getElementById('modalTambahKeluar').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-medium shadow-md shadow-amber-500/20 text-sm transition-all">
            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            Catat Barang Keluar
        </button>
    </div>

    <!-- Tabel Data Barang Keluar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No. Transaksi</th>
                        <th class="px-6 py-4">Tanggal Keluar</th>
                        <th class="px-6 py-4">Aset / Barang</th>
                        <th class="px-6 py-4">Keperluan / Keterangan</th>
                        <th class="px-6 py-4">Jumlah Keluar</th>
                        <th class="px-6 py-4">Penerima / Pemohon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($barangKeluars ?? [] as $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-amber-600 dark:text-amber-400">{{ $item->nomor_transaksi }}</td>
                        <td class="px-6 py-4 text-xs">{{ $item->tanggal_keluar }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <div class="text-xs font-mono text-slate-400">{{ $item->inventaris->kode_barang ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $item->keperluan }}</td>
                        <td class="px-6 py-4 font-bold text-amber-600 dark:text-amber-400">-{{ $item->jumlah }} {{ $item->inventaris->satuan ?? 'Unit' }}</td>
                        <td class="px-6 py-4 text-xs text-slate-900 dark:text-white">{{ $item->penerima }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada riwayat transaksi barang keluar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
