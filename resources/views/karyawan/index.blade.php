@extends('layouts.app')

@section('title', 'Master Pegawai / Karyawan - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Master Pegawai / Karyawan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola direktori staf, penugasan departemen, dan kontak WhatsApp peminjaman.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('modalTambahKaryawan').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-medium shadow-md shadow-rose-500/20 text-sm transition-all">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Tambah Pegawai
            </button>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <form method="GET" action="{{ route('karyawan.index') }}" class="flex-1 w-full flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, nama karyawan, nomor telepon..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none dark:text-white">
            </div>
            <select name="departemen_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                <option value="">Semua Departemen</option>
                @foreach($departemenList ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>{{ $dept->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition">Filter</button>
        </form>
    </div>

    <!-- Tabel Data Karyawan -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">NIK</th>
                        <th class="px-6 py-4">Nama Pegawai</th>
                        <th class="px-6 py-4">Departemen & Jabatan</th>
                        <th class="px-6 py-4">Kontak WhatsApp</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($karyawans as $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 font-mono text-xs text-rose-600 dark:text-rose-400 font-bold">{{ $item->nik }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item->nama }}</div>
                            <div class="text-xs text-slate-400">{{ $item->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                {{ $item->departemen->nama ?? '-' }}
                            </span>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $item->jabatan }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_telepon) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                {{ $item->no_telepon }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status === 'Aktif' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <form action="{{ route('karyawan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 rounded-lg transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Tidak ada data pegawai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
