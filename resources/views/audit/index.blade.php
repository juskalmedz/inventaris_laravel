@extends('layouts.app')

@section('title', 'Audit Trail & Log Aktivitas - Inventaris Kantor')
@section('page_title', 'Audit Trail & Rekam Jejak Sistem')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300">
                    Keamanan & Sistem
                </span>
                <span class="text-xs text-slate-400 font-mono">Immutable Audit Trail Log</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Audit Trail & Log Aktivitas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jejak audit menyeluruh atas seluruh modifikasi data aset, autentikasi sesi, sirkulasi stok, dan mutasi ruangan.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-bold font-mono">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Audit Logger Aktif</span>
            </span>
        </div>
    </div>

    <!-- Filter & Pencarian Log -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('audit.index') }}" class="flex-1 w-full flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari aktivitas, user, kode log, rincian perubahan..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:ring-2 focus:ring-purple-500 focus:outline-none dark:text-white"
                />
            </div>
            <select name="module" onchange="this.form.submit()" class="px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                <option value="all">Semua Modul</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                @endforeach
            </select>
            <select name="action" onchange="this.form.submit()" class="px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                <option value="all">Semua Tipe Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">Filter</button>
            @if(request('search') || (request('module') && request('module') !== 'all') || (request('action') && request('action') !== 'all'))
                <a href="{{ route('audit.index') }}" class="px-3 py-2.5 text-xs text-rose-600 font-bold hover:underline flex items-center">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Audit Log -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Waktu Rekam</th>
                        <th class="px-5 py-3.5">Pengguna & Peran</th>
                        <th class="px-5 py-3.5">Tindakan / Aksi</th>
                        <th class="px-5 py-3.5">Modul Sistem</th>
                        <th class="px-5 py-3.5">Deskripsi Detail Aktivitas</th>
                        <th class="px-5 py-3.5 font-mono">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900 dark:text-white">{{ $log->user_name }}</div>
                            <span class="inline-block mt-0.5 text-[10px] px-2 py-0.5 rounded-md font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {{ $log->user_role }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($log->action === 'CREATE')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">CREATE</span>
                            @elseif($log->action === 'UPDATE')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800">UPDATE</span>
                            @elseif($log->action === 'DELETE')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">DELETE</span>
                            @elseif($log->action === 'APPROVE')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-teal-100 text-teal-800 dark:bg-teal-950/80 dark:text-teal-300 border border-teal-200 dark:border-teal-800">APPROVE</span>
                            @elseif($log->action === 'REJECT')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300 border border-red-200 dark:border-red-800">REJECT</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-purple-100 text-purple-800 dark:bg-purple-950/80 dark:text-purple-300 border border-purple-200 dark:border-purple-800">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800 dark:text-slate-200">
                            {{ $log->module }}
                        </td>
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300 max-w-md leading-relaxed">
                            {{ $log->description }}
                        </td>
                        <td class="px-5 py-4 font-mono text-[11px] text-slate-400">
                            {{ $log->ip ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400">
                            <i data-lucide="shield-check" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600 dark:text-slate-300">Belum ada catatan aktivitas audit yang sesuai</p>
                            <p class="text-[11px] text-slate-400">Setiap perubahan data dan transaksi akan tercatat otomatis di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($logs, 'links'))
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
