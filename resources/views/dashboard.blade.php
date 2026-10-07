@extends('layouts.app')

@section('title', 'Dashboard Eksekutif & Statistik Aset - Inventaris Kantor')

@section('content')
<div class="space-y-6">

    <!-- 1. HERO BANNER: GLASSMORPHIC GRADIENT WITH LIVE STATUS & QUICK ACTION SHORTCUTS (IDENTIK 1:1 REACT) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-rose-950 p-6 sm:p-7 text-white shadow-xl border border-slate-700/60">
        <!-- Ambient glow decoration -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-stretch justify-between gap-6 lg:gap-8">
            <!-- Left: Identity, Context & Status Overview -->
            <div class="flex-1 flex flex-col justify-between space-y-4 max-w-3xl">
                <!-- Top metadata badge bar -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/25 backdrop-blur-md shadow-xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>Sistem Operasional Aktif</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-slate-800/80 text-slate-300 border border-slate-700/80 backdrop-blur-md">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-mono text-rose-200 bg-rose-950/60 border border-rose-800/50 backdrop-blur-md">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-rose-400"></i>
                        <span>Laravel 13 Enterprise v1.2</span>
                    </div>
                </div>

                <!-- Main Greeting & Narrative Headline -->
                <div class="space-y-1.5">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex flex-wrap items-center gap-2">
                        <span>Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-red-300 to-amber-300">{{ Auth::user()->name ?? 'Administrator' }}</span></span>
                        <span class="inline-block text-2xl animate-pulse">👋</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed max-w-2xl">
                        Selamat datang di portal kendali operasional aset fisik kantor. Pantau ketersediaan master barang, mutasi ruangan, sirkulasi distribusi, serta perawatan preventif secara terintegrasi.
                    </p>
                </div>

                <!-- Quick Insight Pills -->
                <div class="pt-1 flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700/80 text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span>Total Valuasi: <strong class="text-white font-mono">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700/80 text-slate-300">
                        <span class="w-2 h-2 rounded-full {{ $peminjamanPending > 0 ? 'bg-amber-400 animate-pulse' : 'bg-slate-400' }}"></span>
                        <span>Pending Approval: <strong class="text-white">{{ $peminjamanPending + $mutasiPending }} berkas</strong></span>
                    </div>
                    @if($stokKritisCount > 0)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-900/50 border border-rose-500/40 text-rose-200">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-rose-400"></i>
                            <span>{{ $stokKritisCount }} Aset Stok Menipis (≤1)</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Quick Actions Panel (1:1 Mirip React Dashboard) -->
            <div class="xl:w-80 shrink-0 flex flex-col justify-between p-4 rounded-2xl bg-slate-900/80 border border-slate-700/70 backdrop-blur-xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800 text-xs">
                    <span class="font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-rose-400"></i>
                        Aksi Sirkulasi Cepat
                    </span>
                    <span class="text-[10px] font-mono text-slate-400">Pintasan</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('barang-masuk.index') }}" class="group p-2.5 rounded-xl text-left border border-slate-700/60 bg-slate-800/60 hover:bg-emerald-950/40 hover:border-emerald-500/50 transition duration-150">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 bg-emerald-500/20 text-emerald-400 group-hover:scale-110 transition">
                                <i data-lucide="arrow-down-left" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-100 group-hover:text-emerald-300">Barang Masuk</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Penerimaan Stok</p>
                    </a>

                    <a href="{{ route('barang-keluar.index') }}" class="group p-2.5 rounded-xl text-left border border-slate-700/60 bg-slate-800/60 hover:bg-rose-950/40 hover:border-rose-500/50 transition duration-150">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 bg-rose-500/20 text-rose-400 group-hover:scale-110 transition">
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-100 group-hover:text-rose-300">Barang Keluar</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Distribusi Unit</p>
                    </a>

                    <a href="{{ route('peminjaman.index') }}" class="group p-2.5 rounded-xl text-left border border-slate-700/60 bg-slate-800/60 hover:bg-blue-950/40 hover:border-blue-500/50 transition duration-150">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 bg-blue-500/20 text-blue-400 group-hover:scale-110 transition">
                                <i data-lucide="handshake" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-100 group-hover:text-blue-300">Pinjam Aset</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Form Sirkulasi</p>
                    </a>

                    <a href="{{ route('maintenance.index') }}" class="group p-2.5 rounded-xl text-left border border-slate-700/60 bg-slate-800/60 hover:bg-amber-950/40 hover:border-amber-500/50 transition duration-150">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 bg-amber-500/20 text-amber-400 group-hover:scale-110 transition">
                                <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-100 group-hover:text-amber-300">Lapor Servis</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Tiket Perbaikan</p>
                    </a>
                </div>

                <a href="{{ route('inventaris.index') }}" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all duration-200 bg-gradient-to-r from-rose-600 via-red-600 to-rose-600 hover:from-rose-500 hover:to-red-500 text-white shadow-md shadow-rose-600/30 border border-rose-400/30 active:scale-[0.98]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Daftarkan Master Aset Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. 6 EXECUTIVE KPI SCORECARD METRICS (1:1 DENGAN REACT DashboardView) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
        <!-- 1. Katalog Master -->
        <a href="{{ route('inventaris.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-rose-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Katalog Model</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalAset }}</div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Model terdata</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                <span>Lihat Katalog</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- 2. Total Unit Fisik -->
        <a href="{{ route('inventaris.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-blue-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Unit Fisik</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="box" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalUnitFisik }}</div>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Populasi barang nyata</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-blue-600 dark:text-blue-400">
                <span>Cek Stok Fisik</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- 3. Valuasi Total Aset -->
        <a href="{{ route('laporan.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Valuasi Aset</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Estimasi nilai buku</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                <span>Laporan Nilai</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- 4. Peminjaman Aktif -->
        <a href="{{ route('peminjaman.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-amber-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Peminjaman</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="handshake" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $peminjamanAktif }}</div>
                <p class="text-[11px] text-amber-600 font-semibold mt-0.5">{{ $peminjamanPending }} butuh approval</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                <span>Daftar Pinjaman</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- 5. Mutasi Aset -->
        <a href="{{ route('mutasi.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-purple-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mutasi Ruang</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $mutasiPending }}</div>
                <p class="text-[11px] text-purple-600 font-semibold mt-0.5">Menunggu approval</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-purple-600 dark:text-purple-400">
                <span>Riwayat Mutasi</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- 6. Tiket Servis Perbaikan -->
        <a href="{{ route('maintenance.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-rose-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dalam Servis</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="wrench" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $maintenanceAktif }}</div>
                <p class="text-[11px] text-rose-600 font-semibold mt-0.5">{{ $stokKritisCount }} unit stok kritis</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                <span>Tiket Servis</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>
    </div>

    <!-- 3. FEED SIRKULASI & LOG AKTIVITAS 1:1 DENGAN REACT SPA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kiri (2 Kolom): Peminjaman Aktif & Mutasi Ruangan -->
        <div class="lg:col-span-2 space-y-6">

            <!-- PINJAMAN ASET AKTIF & STATUS -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 flex items-center justify-center font-bold">
                            <i data-lucide="handshake" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Peminjaman Aset Berjalan</h3>
                            <p class="text-[11px] text-slate-400">Monitoring pengembalian & peminjam penanggung jawab (PIC)</p>
                        </div>
                    </div>
                    <a href="{{ route('peminjaman.index') }}" class="text-xs text-rose-600 dark:text-rose-400 font-bold hover:underline flex items-center gap-1">
                        <span>Kelola Semua</span>
                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($recentLoans as $loan)
                        <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/40 p-2 rounded-xl transition">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900 dark:text-slate-100">{{ $loan->nomor_peminjaman ?? $loan->nomor_pinjam }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $loan->status === 'Dipinjam' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' }}">
                                        {{ $loan->status }}
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ $loan->inventaris->nama_barang ?? 'Aset' }}</p>
                                <p class="text-[11px] text-slate-400">Peminjam: <strong class="text-slate-600 dark:text-slate-300">{{ $loan->karyawan->nama ?? '-' }}</strong> &bull; Rencana Kembali: {{ \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->format('d M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2 self-end sm:self-center">
                                <a href="{{ route('peminjaman.index') }}" class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold hover:bg-slate-200 transition">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada sirkulasi peminjaman aktif saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- SIRKULASI MASUK & KELUAR TERBARU -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 flex items-center justify-center font-bold">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Arus Logistik Barang (Masuk & Keluar)</h3>
                            <p class="text-[11px] text-slate-400">Rekam transaksi penerimaan pengadaan & distribusi departemen</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('barang-masuk.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">+ Masuk</a>
                        <span class="text-slate-300">&bull;</span>
                        <a href="{{ route('barang-keluar.index') }}" class="text-[11px] font-bold text-rose-600 hover:underline">- Keluar</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Barang Masuk -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2">
                        <h4 class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 pb-2 border-b border-slate-200 dark:border-slate-700">
                            <i data-lucide="arrow-down-left" class="w-3.5 h-3.5"></i>
                            Barang Masuk Terakhir
                        </h4>
                        @forelse($barangMasukRecent->take(3) as $bm)
                            <div class="text-xs py-1.5 flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800 last:border-0">
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $bm->inventaris->nama_barang ?? 'Aset' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $bm->supplier }} &bull; {{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d/m/Y') }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                                    +{{ $bm->jumlah }}
                                </span>
                            </div>
                        @empty
                            <p class="text-[11px] text-slate-400 py-3 text-center">Belum ada transaksi masuk.</p>
                        @endforelse
                    </div>

                    <!-- Barang Keluar -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2">
                        <h4 class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5 pb-2 border-b border-slate-200 dark:border-slate-700">
                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            Barang Keluar Terakhir
                        </h4>
                        @forelse($barangKeluarRecent->take(3) as $bk)
                            <div class="text-xs py-1.5 flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800 last:border-0">
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $bk->inventaris->nama_barang ?? 'Aset' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $bk->karyawan->nama ?? $bk->penerima }} &bull; {{ \Carbon\Carbon::parse($bk->tanggal_keluar)->format('d/m/Y') }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400">
                                    -{{ $bk->jumlah }}
                                </span>
                            </div>
                        @empty
                            <p class="text-[11px] text-slate-400 py-3 text-center">Belum ada transaksi keluar.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Kanan (1 Kolom): Mutasi Ruangan & Log Audit Tamper-Proof -->
        <div class="space-y-6">

            <!-- PERMOHONAN MUTASI & APPROVAL -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 flex items-center justify-center font-bold">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Mutasi Penempatan</h4>
                    </div>
                    <a href="{{ route('mutasi.index') }}" class="text-xs text-purple-600 font-bold hover:underline">Semua &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($recentMutasi as $m)
                        <div class="py-2.5 flex items-center justify-between">
                            <div class="space-y-0.5">
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $m->inventaris->nama_barang ?? 'Aset' }}</p>
                                <p class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <span>{{ $m->lokasiAwal->nama_lokasi ?? '-' }}</span>
                                    <span>&rarr;</span>
                                    <strong class="text-slate-600 dark:text-slate-300">{{ $m->lokasiBaru->nama_lokasi ?? '-' }}</strong>
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $m->status === 'Disetujui' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400' }}">
                                {{ $m->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-slate-400 py-4 text-center text-xs">Belum ada riwayat mutasi.</p>
                    @endforelse
                </div>
            </div>

            <!-- AUDIT TRAIL OPERASIONAL (1:1 DENGAN REACT AuditTrailView) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Audit Trail Terbaru</h4>
                            <p class="text-[10px] text-slate-400">Rekam jejak kepatuhan sistem</p>
                        </div>
                    </div>
                    <a href="{{ route('audit.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Log &rarr;</a>
                </div>

                <div class="space-y-3 text-xs">
                    @forelse($recentLogs as $log)
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-800/80 space-y-1">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="font-mono font-bold text-rose-600 dark:text-rose-400">{{ $log->kode_log }}</span>
                                <span class="text-slate-400">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 text-[11px] leading-tight">{{ $log->description }}</p>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                <span>Oleh: <strong class="text-slate-600 dark:text-slate-300">{{ $log->user_name }}</strong></span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.2 bg-slate-200 dark:bg-slate-700 rounded font-semibold text-slate-700 dark:text-slate-300">{{ $log->module }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 py-4 text-center text-xs">Belum ada rekaman audit log.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
