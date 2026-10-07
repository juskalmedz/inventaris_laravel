@extends('layouts.app')

@section('title', 'Master Departemen - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Master Departemen</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Struktur unit kerja, penanggung jawab (PIC), dan alokasi budget tahunan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($departemens ?? [] as $dept)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-mono text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2.5 py-1 rounded-lg">{{ $dept->kode_departemen }}</span>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-2">{{ $dept->nama }}</h3>
                </div>
                <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2 text-sm">
                <div class="flex justify-between text-slate-500">
                    <span>Kepala Dept (PIC):</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ $dept->kepala_departemen ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Alokasi Budget:</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($dept->budget_tahunan ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Jumlah Karyawan:</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ $dept->karyawans_count ?? 0 }} Pegawai</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center text-slate-400">Tidak ada data departemen.</div>
        @endforelse
    </div>
</div>
@endsection
