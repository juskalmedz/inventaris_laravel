@extends('layouts.app')

@section('title', 'Master Kategori - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Master Kategori Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Klasifikasi kelompok barang, kode prefix aset, dan jenis barang (Aset Tetap / Habis Pakai).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($kategoris ?? [] as $kat)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white">{{ $kat->nama }}</h4>
                    <span class="text-xs font-mono text-slate-400">{{ $kat->kode_kategori }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
                <span class="text-slate-500">Jenis Aset:</span>
                <span class="font-semibold px-2 py-0.5 rounded-full {{ $kat->jenis === 'Aset Tetap' ? 'bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400' }}">{{ $kat->jenis }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center text-slate-400">Tidak ada kategori.</div>
        @endforelse
    </div>
</div>
@endsection
