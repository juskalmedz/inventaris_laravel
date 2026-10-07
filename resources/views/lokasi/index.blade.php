@extends('layouts.app')

@section('title', 'Master Lokasi / Ruangan - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Master Lokasi & Ruangan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pemetaan gedung, lantai, ruangan kerja, dan penanggung jawab lokasi fisik aset.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($lokasis ?? [] as $lok)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $lok->kode_lokasi }}</span>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-2">{{ $lok->nama }}</h3>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2 text-sm text-slate-500">
                <div class="flex justify-between">
                    <span>Gedung / Lantai:</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ $lok->gedung ?? 'Gedung Utama' }} (Lantai {{ $lok->lantai ?? 1 }})</span>
                </div>
                <div class="flex justify-between">
                    <span>Penanggung Jawab:</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ $lok->penanggung_jawab ?? '-' }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center text-slate-400">Tidak ada data lokasi.</div>
        @endforelse
    </div>
</div>
@endsection
