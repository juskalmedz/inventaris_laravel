@extends('layouts.app')

@section('page_title', 'Ringkasan Eksekutif & Statistik Aset')

@section('content')
<div class="space-y-6">

    <!-- KPI STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Aset -->
        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center text-xl font-bold">
                📦
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Item Aset</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $totalAset }} <span class="text-xs font-normal text-slate-400">Item</span></h3>
                <span class="text-[11px] text-emerald-600 font-semibold">{{ $totalUnitFisik }} Unit Fisik</span>
            </div>
        </div>

        <!-- Nilai Total Aset -->
        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center text-xl font-bold">
                💰
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Nilai Buku</p>
                <h3 class="text-lg font-black text-slate-900 dark:text-white mt-0.5">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</h3>
                <span class="text-[11px] text-slate-400">Total valuasi perolehan</span>
            </div>
        </div>

        <!-- Peminjaman Aktif -->
        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center text-xl font-bold">
                🤝
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Peminjaman Aktif</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $peminjamanAktif }} <span class="text-xs font-normal text-slate-400">Aset</span></h3>
                <span class="text-[11px] text-amber-600 font-semibold">{{ $peminjamanPending }} Menunggu Approval</span>
            </div>
        </div>

        <!-- Mutasi & Servis -->
        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center text-xl font-bold">
                🔄
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mutasi & Servis</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $mutasiPending }} <span class="text-xs font-normal text-slate-400">Mutasi</span></h3>
                <span class="text-[11px] text-rose-600 font-semibold">{{ $maintenanceAktif }} Unit Dalam Servis</span>
            </div>
        </div>
    </div>

    <!-- TABEL SIRKULASI TERBARU & LOG AUDIT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Peminjaman Terkini -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Pinjaman Aset Terkini</h4>
                <a href="{{ route('peminjaman.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($recentLoans as $loan)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">{{ $loan->inventaris->nama_barang ?? 'Aset' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Peminjam: {{ $loan->karyawan->nama ?? '-' }} ({{ $loan->nomor_pinjam }})</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $loan->status === 'Dipinjam' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ $loan->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 py-4 text-center">Belum ada peminjaman aktif.</p>
                @endforelse
            </div>
        </div>

        <!-- Mutasi Terkini -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Permohonan Mutasi Terkini</h4>
                <a href="{{ route('mutasi.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($recentMutasi as $m)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">{{ $m->inventaris->nama_barang ?? 'Aset' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $m->lokasiAwal->nama_lokasi ?? '-' }} &rarr; {{ $m->lokasiBaru->nama_lokasi ?? '-' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $m->status === 'Disetujui' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $m->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 py-4 text-center">Belum ada permohonan mutasi.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
