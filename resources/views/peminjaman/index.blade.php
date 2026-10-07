@extends('layouts.app')

@section('title', 'Peminjaman & Pengembalian Aset - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Peminjaman & Pengembalian Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Peminjaman aset tetap (laptop, proyektor, kamera, kendaraan operasional) beserta pengingat WhatsApp jatuh tempo.</p>
        </div>
        <button onclick="document.getElementById('modalTambahPinjam').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-md shadow-blue-500/20 text-sm transition-all">
            <i data-lucide="handshake" class="w-4 h-4"></i>
            Pinjamkan Aset
        </button>
    </div>

    <!-- Filter Tab Status -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('peminjaman.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap {{ !request('status') ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">Semua Peminjaman</a>
        <a href="{{ route('peminjaman.index', ['status' => 'Dipinjam']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap {{ request('status') === 'Dipinjam' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">Sedang Dipinjam</a>
        <a href="{{ route('peminjaman.index', ['status' => 'Dikembalikan']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap {{ request('status') === 'Dikembalikan' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">Sudah Dikembalikan</a>
    </div>

    <!-- Tabel Data Peminjaman -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No. Peminjaman</th>
                        <th class="px-6 py-4">Aset / Barang</th>
                        <th class="px-6 py-4">Peminjam (Pegawai)</th>
                        <th class="px-6 py-4">Tgl Pinjam / Jatuh Tempo</th>
                        <th class="px-6 py-4">Status & Denda</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($peminjamans ?? [] as $item)
                    @php
                        $isOverdue = $item->status === 'Dipinjam' && \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->isPast();
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ $item->nomor_peminjaman }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <div class="text-xs font-mono text-slate-400">{{ $item->inventaris->kode_barang ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item->karyawan->nama ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $item->karyawan->departemen->nama ?? '-' }}</div>
                            @if(optional($item->karyawan)->no_telepon)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->karyawan->no_telepon) }}?text=Halo%20{{ urlencode($item->karyawan->nama) }},%20pengingat%20peminjaman%20aset%20{{ urlencode($item->inventaris->nama_barang ?? '') }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline mt-0.5">
                                <i data-lucide="message-square" class="w-3 h-3"></i> WhatsApp
                            </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div>Pinjam: {{ $item->tanggal_pinjam }}</div>
                            <div class="{{ $isOverdue ? 'text-red-600 font-bold dark:text-red-400' : 'text-slate-500' }}">Rencana: {{ $item->tanggal_kembali_rencana }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($isOverdue)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400 animate-pulse">Terlambat</span>
                            @elseif($item->status === 'Dipinjam')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400">Dipinjam</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Dikembalikan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($item->status === 'Dipinjam')
                            <form action="{{ route('peminjaman.return', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="kondisi_sesudah" value="Baik">
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition" onclick="return confirm('Konfirmasi pengembalian aset ini?')">
                                    Kembalikan
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Tidak ada riwayat peminjaman aset.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
