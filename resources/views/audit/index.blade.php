@extends('layouts.app')

@section('title', 'Audit Trail & Log Aktivitas - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Trail & Log Aktivitas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jejak audit menyeluruh atas seluruh modifikasi data inventaris, autentikasi, mutasi, dan transaksi sirkulasi.</p>
        </div>
    </div>

    <!-- Filter & Pencarian Log -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <form method="GET" action="{{ route('audit.index') }}" class="flex-1 w-full flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas, user, kode log..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none dark:text-white">
            </div>
            <select name="module" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                <option value="all">Semua Modul</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                @endforeach
            </select>
            <select name="action" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-white">
                <option value="all">Semua Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Tabel Audit Log -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Aksi</th>
                        <th class="px-6 py-4">Modul</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                        <th class="px-6 py-4">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 text-xs font-mono text-slate-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $log->user_name }}</div>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500">{{ $log->user_role }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->action === 'CREATE')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">CREATE</span>
                            @elseif($log->action === 'UPDATE')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400">UPDATE</span>
                            @elseif($log->action === 'DELETE')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400">DELETE</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-xs text-slate-700 dark:text-slate-300">{{ $log->module }}</td>
                        <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-300 max-w-md">{{ $log->description }}</td>
                        <td class="px-6 py-4 text-xs font-mono text-slate-400">{{ $log->ip ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada catatan aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
