@extends('layouts.app')

@section('title', 'Dashboard Eksekutif & Statistik Aset - Inventaris Kantor')

@section('content')
<div class="space-y-6">

    <!-- 1. HERO BANNER: ENTERPRISE OVERVIEW & QUICK ACTIONS TOOLBAR -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-rose-950 p-6 sm:p-8 text-white shadow-xl border border-slate-700/60 print:hidden">
        <!-- Ambient glow decoration -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-rose-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-stretch justify-between gap-6 lg:gap-8">
            <!-- Left: Identity, Context & Status Overview -->
            <div class="flex-1 flex flex-col justify-between space-y-4 max-w-3xl">
                <!-- Top metadata indicator -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>Sistem Operasional Aktif</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-800/80 text-slate-300 border border-slate-700/80 backdrop-blur-md">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono text-rose-200 bg-rose-950/60 border border-rose-800/50 backdrop-blur-md">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-rose-400"></i>
                        <span>Role: {{ Auth::user()->role ?? 'Administrator' }}</span>
                    </div>
                </div>

                <!-- Main Greeting & Headline -->
                <div class="space-y-1.5">
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex flex-wrap items-center gap-2">
                        <span>Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-red-300 to-amber-300">{{ Auth::user()->name ?? 'Administrator' }}</span></span>
                        <span class="inline-block text-2xl animate-pulse">👋</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed max-w-2xl">
                        Selamat datang di portal kendali operasional aset fisik kantor. Pantau ketersediaan katalog, sirkulasi distribusi, persetujuan mutasi ruangan, serta pemeliharaan preventif secara terintegrasi dan akuntabel.
                    </p>
                </div>

                <!-- Live Quick Insights Bar -->
                <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700/80 text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span>Valuasi Buku: <strong class="text-white font-mono">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</strong></span>
                    </div>
                    
                    <button type="button" onclick="openApprovalModal()" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700/80 text-slate-300 hover:text-white transition cursor-pointer">
                        <span class="w-2 h-2 rounded-full {{ ($peminjamanPending + $mutasiPending) > 0 ? 'bg-amber-400 animate-ping' : 'bg-slate-400' }}"></span>
                        <span>Antrean Approval: <strong class="text-amber-300">{{ $peminjamanPending + $mutasiPending }} Berkas</strong></span>
                    </button>

                    @if($stokKritisCount > 0 || $stokHabisCount > 0)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-900/50 border border-rose-500/40 text-rose-200">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-rose-400"></i>
                            <span>{{ $stokKritisCount + $stokHabisCount }} Aset Perlu Restock</span>
                        </div>
                    @endif

                    @if(count($overdueLoans) > 0)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-950/70 border border-amber-500/50 text-amber-200 animate-pulse">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>{{ count($overdueLoans) }} Pinjaman Jatuh Tempo</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Interactive Actions Panel -->
            <div class="xl:w-88 shrink-0 flex flex-col justify-between p-4 sm:p-5 rounded-2xl bg-slate-900/85 border border-slate-700/70 backdrop-blur-xl space-y-3.5">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800 text-xs">
                    <span class="font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
                        Pusat Aksi & Pintasan
                    </span>
                    <span class="text-[10px] font-mono text-emerald-400 bg-emerald-950/80 px-2 py-0.5 rounded border border-emerald-800/60">Siap Operasi</span>
                </div>

                <!-- Primary Action Buttons Grid -->
                <div class="grid grid-cols-2 gap-2">
                    <!-- Quick Barcode/QR Scanner Button -->
                    <button type="button" onclick="openScannerModal()" class="group p-2.5 rounded-xl text-left border border-slate-700/80 bg-slate-800/80 hover:bg-rose-950/40 hover:border-rose-500/60 transition duration-150 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-rose-500/20 text-rose-400 group-hover:scale-110 transition">
                                <i data-lucide="scan-barcode" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-100 group-hover:text-rose-300">Scan Barcode</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Cari Cepat Aset / USB</p>
                    </button>

                    <!-- Quick Approval Hub Button -->
                    <button type="button" onclick="openApprovalModal()" class="group p-2.5 rounded-xl text-left border border-slate-700/80 bg-slate-800/80 hover:bg-amber-950/40 hover:border-amber-500/60 transition duration-150 cursor-pointer relative">
                        @if(($peminjamanPending + $mutasiPending) > 0)
                            <span class="absolute -top-1.5 -right-1.5 px-1.5 py-0.2 bg-amber-500 text-slate-950 font-black text-[9px] rounded-full shadow-sm animate-bounce">
                                {{ $peminjamanPending + $mutasiPending }}
                            </span>
                        @endif
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-amber-500/20 text-amber-400 group-hover:scale-110 transition">
                                <i data-lucide="check-square" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-100 group-hover:text-amber-300">Persetujuan</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Review Pinjam & Mutasi</p>
                    </button>

                    <!-- Quick Stock In Button -->
                    <button type="button" onclick="openQuickStockInModal()" class="group p-2.5 rounded-xl text-left border border-slate-700/80 bg-slate-800/80 hover:bg-emerald-950/40 hover:border-emerald-500/60 transition duration-150 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-emerald-500/20 text-emerald-400 group-hover:scale-110 transition">
                                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-100 group-hover:text-emerald-300">Barang Masuk</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Tambah Stok Cepat</p>
                    </button>

                    <!-- Print / Export Report Button -->
                    <button type="button" onclick="window.print()" class="group p-2.5 rounded-xl text-left border border-slate-700/80 bg-slate-800/80 hover:bg-blue-950/40 hover:border-blue-500/60 transition duration-150 cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-blue-500/20 text-blue-400 group-hover:scale-110 transition">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-100 group-hover:text-blue-300">Cetak Rekap</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">Laporan Eksekutif</p>
                    </button>
                </div>

                <!-- Register Master Asset CTA -->
                <a href="{{ route('inventaris.create') }}" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all duration-200 bg-gradient-to-r from-rose-600 via-red-600 to-rose-600 hover:from-rose-500 hover:to-red-500 text-white shadow-md shadow-rose-600/30 border border-rose-400/30 active:scale-[0.98]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Daftarkan Master Aset Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. OVERDUE BORROWERS NOTIFICATION BANNER (JATUH TEMPO) -->
    @if(count($overdueLoans) > 0)
        <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-rose-500/10 dark:from-amber-950/40 dark:to-rose-950/30 border border-amber-300 dark:border-amber-800/80 rounded-2xl p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Perhatian: Ada {{ count($overdueLoans) }} Peminjaman Melewati Batas Waktu</span>
                            <span class="px-2 py-0.5 text-[10px] font-mono font-bold bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200 rounded">Jatuh Tempo</span>
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">Segera kirimkan pengingat kepada penanggung jawab peminjam untuk proses verifikasi fisik dan pengembalian unit.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start md:self-center shrink-0">
                    <a href="{{ route('peminjaman.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 transition shadow-2xs">
                        Lihat Semua Pinjaman
                    </a>
                </div>
            </div>

            <!-- Overdue list items preview with WhatsApp action trigger -->
            <div class="mt-3 pt-3 border-t border-amber-200/60 dark:border-amber-900/60 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                @foreach($overdueLoans as $ol)
                    <div class="p-2.5 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-amber-200/80 dark:border-amber-900/50 flex items-center justify-between gap-2 text-xs">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ $ol->inventaris->nama_barang ?? 'Aset' }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                <strong>{{ $ol->karyawan->nama ?? '-' }}</strong> &bull; Jatuh tempo: {{ \Carbon\Carbon::parse($ol->rencana_kembali ?? $ol->tanggal_kembali)->format('d M Y') }}
                            </p>
                        </div>
                        <button
                            type="button"
                            onclick="openWaModal('{{ $ol->id }}', '{{ $ol->karyawan->nama ?? 'Peminjam' }}', '{{ $ol->karyawan->no_telepon ?? '' }}', '{{ $ol->inventaris->nama_barang ?? 'Aset' }}', '{{ $ol->nomor_pinjam }}', '{{ \Carbon\Carbon::parse($ol->rencana_kembali ?? $ol->tanggal_kembali)->format('d M Y') }}')"
                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] flex items-center gap-1 shrink-0 transition shadow-2xs cursor-pointer"
                            title="Kirim Pesan WhatsApp Pengingat"
                        >
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                            <span>WA</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 3. 6 EXECUTIVE KPI SCORECARD METRICS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
        <!-- 1. Katalog Master -->
        <a href="{{ route('inventaris.index') }}" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs hover:border-rose-500/50 hover:shadow-md transition group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Katalog Model</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 transition">
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
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="box" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalUnitFisik }}</div>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">Populasi barang nyata</p>
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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Valuasi Buku</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-base font-black text-slate-900 dark:text-white tracking-tight font-mono">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</div>
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
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="handshake" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $peminjamanAktif }}</div>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">{{ $peminjamanPending }} butuh approval</p>
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
                    <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $mutasiPending }}</div>
                <p class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold mt-0.5">Menunggu approval</p>
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
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 transition">
                        <i data-lucide="wrench" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $maintenanceAktif }}</div>
                <p class="text-[11px] text-rose-600 dark:text-rose-400 font-semibold mt-0.5">{{ $stokKritisCount }} stok kritis</p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                <span>Tiket Servis</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>
    </div>

    <!-- 4. VISUAL METRICS: KONDISI FISIK ASET & SEBARAN KATEGORI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Visualizer Kondisi Fisik Aset (2 Kolom) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Kondisi Fisik & Kesiapan Aset</h3>
                        <p class="text-[11px] text-slate-400">Distribusi kelayakan operasional seluruh aset kantor</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Baik ({{ $kondisiBaik }})
                    </span>
                    <span class="flex items-center gap-1.5 font-medium text-amber-600 dark:text-amber-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Rusak Ringan ({{ $kondisiRusakRingan }})
                    </span>
                    <span class="flex items-center gap-1.5 font-medium text-rose-600 dark:text-rose-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Rusak Berat ({{ $kondisiRusakBerat }})
                    </span>
                </div>
            </div>

            @php
                $totalKondisi = max(1, $kondisiBaik + $kondisiRusakRingan + $kondisiRusakBerat);
                $pctBaik = round(($kondisiBaik / $totalKondisi) * 100);
                $pctRingan = round(($kondisiRusakRingan / $totalKondisi) * 100);
                $pctBerat = max(0, 100 - $pctBaik - $pctRingan);
            @endphp

            <!-- Composite Condition Bar -->
            <div class="space-y-2">
                <div class="w-full h-4 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden flex shadow-inner">
                    <div style="width: {{ $pctBaik }}%" class="bg-gradient-to-r from-emerald-500 to-emerald-600 transition-all duration-500" title="Baik: {{ $pctBaik }}%"></div>
                    <div style="width: {{ $pctRingan }}%" class="bg-gradient-to-r from-amber-400 to-amber-500 transition-all duration-500" title="Rusak Ringan: {{ $pctRingan }}%"></div>
                    <div style="width: {{ $pctBerat }}%" class="bg-gradient-to-r from-rose-500 to-red-600 transition-all duration-500" title="Rusak Berat: {{ $pctBerat }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                    <span>{{ $pctBaik }}% Siap Pakai</span>
                    <span>{{ $pctRingan }}% Perlu Perhatian</span>
                    <span>{{ $pctBerat }}% Butuh Penanganan / Servis</span>
                </div>
            </div>

            <!-- Breakdown Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/20 border border-emerald-200/70 dark:border-emerald-900/50 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Kondisi Prima (Baik)</span>
                    <div class="text-xl font-black text-emerald-900 dark:text-emerald-100">{{ $kondisiBaik }} <span class="text-xs font-normal text-slate-500">model</span></div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Siap dialokasikan untuk peminjaman & operasional harian</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-900/50 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Rusak Ringan</span>
                    <div class="text-xl font-black text-amber-900 dark:text-amber-100">{{ $kondisiRusakRingan }} <span class="text-xs font-normal text-slate-500">model</span></div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Memerlukan pembersihan, kalibrasi, atau perawatan minor</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200/70 dark:border-rose-900/50 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">Rusak Berat / Kritis</span>
                    <div class="text-xl font-black text-rose-900 dark:text-rose-100">{{ $kondisiRusakBerat }} <span class="text-xs font-normal text-slate-500">model</span></div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Wajib diajukan tiket servis atau penghapusan aset inventaris</p>
                </div>
            </div>
        </div>

        <!-- Sebaran Kategori Terbanyak (1 Kolom) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Top Kategori Aset</h3>
                </div>
                <a href="{{ route('kategori.index') }}" class="text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($kategoriStats as $kat)
                    @php
                        $katPct = $totalAset > 0 ? round(($kat->inventaris_count / $totalAset) * 100) : 0;
                    @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $kat->nama_kategori }}</span>
                            <span class="font-mono text-slate-500 dark:text-slate-400">{{ $kat->inventaris_count }} model ({{ $katPct }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div style="width: {{ $katPct }}%" class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada kategori terdaftar.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 5. INTERACTIVE CIRCULATION FEED WITH TABS (SEMUA, PINJAM, MASUK/KELUAR, MUTASI, AUDIT) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 shadow-xs space-y-5">
        <!-- Feed Header & Segmented Tab Controls -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-rose-600"></i>
                    Pusat Monitoring Sirkulasi & Log Transaksi
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pantau arus peminjaman, logistik barang, permohonan mutasi, serta riwayat audit secara real-time</p>
            </div>

            <!-- Tab Segmented Controls -->
            <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-x-auto text-xs font-bold shrink-0">
                <button type="button" onclick="switchCirculationTab('all')" id="tabCirc_all" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs transition cursor-pointer">
                    Semua
                </button>
                <button type="button" onclick="switchCirculationTab('peminjaman')" id="tabCirc_peminjaman" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                    Peminjaman
                </button>
                <button type="button" onclick="switchCirculationTab('logistik')" id="tabCirc_logistik" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                    Masuk & Keluar
                </button>
                <button type="button" onclick="switchCirculationTab('mutasi')" id="tabCirc_mutasi" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                    Mutasi Ruang
                </button>
                <button type="button" onclick="switchCirculationTab('audit')" id="tabCirc_audit" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                    Audit Log
                </button>
            </div>
        </div>

        <!-- TAB CONTENT 1: SEMUA & PINJAMAN -->
        <div id="circContent_peminjaman" class="space-y-3">
            <div class="flex items-center justify-between text-xs text-slate-500 pb-1">
                <span class="font-bold text-slate-800 dark:text-slate-200">6 Sirkulasi Peminjaman Terkini</span>
                <a href="{{ route('peminjaman.index') }}" class="text-rose-600 dark:text-rose-400 font-bold hover:underline flex items-center gap-1">
                    <span>Buka Modul Pinjam</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                @forelse($recentLoans as $loan)
                    <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs font-bold text-slate-900 dark:text-slate-100">{{ $loan->nomor_pinjam ?? $loan->nomor_peminjaman }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $loan->status === 'Dipinjam' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : ($loan->status === 'Dikembalikan' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300') }}">
                                    {{ $loan->status }}
                                </span>
                                @if($loan->isOverdue())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 animate-pulse">
                                        Terlambat
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ $loan->inventaris->nama_barang ?? 'Aset' }} ({{ $loan->jumlah }} unit)</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Peminjam: <strong class="text-slate-700 dark:text-slate-300">{{ $loan->karyawan->nama ?? '-' }}</strong> &bull; Rencana Kembali: {{ \Carbon\Carbon::parse($loan->rencana_kembali ?? $loan->tanggal_kembali)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-center">
                            @if($loan->status === 'Menunggu Approval')
                                <form method="POST" action="{{ route('peminjaman.approve', $loan->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                                        Setujui
                                    </button>
                                </form>
                            @endif
                            <button
                                type="button"
                                onclick="openWaModal('{{ $loan->id }}', '{{ $loan->karyawan->nama ?? 'Peminjam' }}', '{{ $loan->karyawan->no_telepon ?? '' }}', '{{ $loan->inventaris->nama_barang ?? 'Aset' }}', '{{ $loan->nomor_pinjam }}', '{{ \Carbon\Carbon::parse($loan->rencana_kembali ?? $loan->tanggal_kembali)->format('d M Y') }}')"
                                class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-emerald-600 rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                            >
                                <i data-lucide="message-square" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Kirim WA</span>
                            </button>
                            <a href="{{ route('peminjaman.index') }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
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

        <!-- TAB CONTENT 2: LOGISTIK MASUK & KELUAR -->
        <div id="circContent_logistik" class="space-y-4 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Barang Masuk -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                        <h4 class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            Penerimaan Stok Barang Masuk
                        </h4>
                        <button type="button" onclick="openQuickStockInModal()" class="text-[11px] font-bold text-emerald-600 hover:underline cursor-pointer">+ Tambah</button>
                    </div>
                    @forelse($barangMasukRecent as $bm)
                        <div class="text-xs py-2 flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800 last:border-0">
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $bm->inventaris->nama_barang ?? 'Aset' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $bm->supplier }} &bull; {{ \Carbon\Carbon::parse($bm->tanggal ?? $bm->tanggal_masuk)->format('d M Y') }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg font-mono text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                +{{ $bm->jumlah }} Unit
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada transaksi barang masuk.</p>
                    @endforelse
                </div>

                <!-- Barang Keluar -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                        <h4 class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            Distribusi Barang Keluar
                        </h4>
                        <a href="{{ route('barang-keluar.index') }}" class="text-[11px] font-bold text-rose-600 hover:underline">Kelola &rarr;</a>
                    </div>
                    @forelse($barangKeluarRecent as $bk)
                        <div class="text-xs py-2 flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800 last:border-0">
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $bk->inventaris->nama_barang ?? 'Aset' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $bk->karyawan->nama ?? $bk->penerima }} &bull; {{ \Carbon\Carbon::parse($bk->tanggal ?? $bk->tanggal_keluar)->format('d M Y') }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg font-mono text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                -{{ $bk->jumlah }} Unit
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada transaksi barang keluar.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB CONTENT 3: MUTASI RUANGAN -->
        <div id="circContent_mutasi" class="space-y-3 hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 pb-1">
                <span class="font-bold text-slate-800 dark:text-slate-200">Permohonan Mutasi & Pemindahan Lokasi Fisik</span>
                <a href="{{ route('mutasi.index') }}" class="text-purple-600 dark:text-purple-400 font-bold hover:underline flex items-center gap-1">
                    <span>Buka Modul Mutasi</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                @forelse($recentMutasi as $m)
                    <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-900 dark:text-white">{{ $m->nomor_mutasi }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->status === 'Disetujui' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                    {{ $m->status }}
                                </span>
                            </div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ $m->inventaris->nama_barang ?? 'Aset' }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 flex-wrap">
                                <span>Dari: <strong class="text-slate-700 dark:text-slate-300">{{ $m->lokasiAwal->nama_lokasi ?? '-' }}</strong></span>
                                <span>&rarr;</span>
                                <span>Ke: <strong class="text-purple-600 dark:text-purple-400">{{ $m->lokasiBaru->nama_lokasi ?? '-' }}</strong></span>
                                <span>&bull;</span>
                                <span>Pemohon: {{ $m->pemohon->nama ?? '-' }}</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($m->status === 'Menunggu Approval')
                                <form method="POST" action="{{ route('mutasi.approve', $m->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                                        Setujui Mutasi
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('mutasi.index') }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat mutasi aset.</div>
                @endforelse
            </div>
        </div>

        <!-- TAB CONTENT 4: AUDIT TRAIL LOG -->
        <div id="circContent_audit" class="space-y-3 hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 pb-1">
                <span class="font-bold text-slate-800 dark:text-slate-200">Log Audit Kepatuhan & Jejak Transaksi Sistem</span>
                <a href="{{ route('audit.index') }}" class="text-rose-600 dark:text-rose-400 font-bold hover:underline flex items-center gap-1">
                    <span>Semua Log Audit</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentLogs as $log)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-rose-600 dark:text-rose-400">{{ $log->kode_log }}</span>
                                <span class="px-1.5 py-0.2 bg-slate-200 dark:bg-slate-700 rounded font-semibold text-[10px] text-slate-700 dark:text-slate-300">{{ $log->module }}</span>
                                <span class="text-slate-400 text-[11px]">&bull; {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $log->description }}</p>
                        </div>
                        <span class="text-[11px] text-slate-400 shrink-0">Oleh: <strong class="text-slate-600 dark:text-slate-300">{{ $log->user_name }}</strong> ({{ $log->user_role }})</span>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">Belum ada catatan audit.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL 1: QUICK BARCODE & QR SCANNER (USB EMULATION & REAL-TIME SEARCH) -->
<!-- ======================================================== -->
<div id="modalQuickScan" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in print:hidden">
    <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <i data-lucide="scan-barcode" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900 dark:text-white">Pindai Barcode / QR Aset Cepat</h3>
                    <p class="text-[11px] text-slate-400">Kompatibel dengan USB Barcode Scanner & Input Manual</p>
                </div>
            </div>
            <button type="button" onclick="closeScannerModal()" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Scanner Input Box -->
        <form onsubmit="handleScannerSubmit(event)" class="space-y-3">
            <div class="relative">
                <input
                    type="text"
                    id="barcodeScannerInput"
                    placeholder="Scan barcode dengan alat pemindai atau ketik kode barang..."
                    class="w-full pl-10 pr-24 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-inner"
                    autofocus
                    autocomplete="off"
                >
                <i data-lucide="barcode" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <button
                    type="submit"
                    id="btnDoSearch"
                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1"
                >
                    <span>Cari</span>
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <p class="text-[11px] text-slate-400">💡 <em>Tip: Ketika alat barcode scanner USB mendeteksi barcode dan menekan tombol trigger, scanner otomatis menekan Enter untuk pencarian seketika.</em></p>
        </form>

        <!-- Search Result Placeholder & Card -->
        <div id="scanResultContainer" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 min-h-[140px] flex flex-col justify-center">
            <div id="scanLoading" class="hidden text-center py-4 space-y-2">
                <i data-lucide="loader-2" class="w-6 h-6 text-rose-600 animate-spin mx-auto"></i>
                <p class="text-xs font-bold text-slate-500">Mencari spesifikasi aset di database...</p>
            </div>

            <div id="scanEmpty" class="text-center py-4 text-slate-400 text-xs">
                Arahkan scanner ke barcode atau ketik kode seperti <strong>AST-001</strong> untuk memuat data.
            </div>

            <div id="scanFound" class="hidden space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span id="resKode" class="font-mono text-xs font-bold text-rose-600 dark:text-rose-400"></span>
                        <h4 id="resNama" class="text-sm font-black text-slate-900 dark:text-white mt-0.5"></h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Kategori: <strong id="resKategori" class="text-slate-700 dark:text-slate-300"></strong> &bull; Lokasi: <strong id="resLokasi" class="text-slate-700 dark:text-slate-300"></strong>
                        </p>
                    </div>
                    <span id="resStatusBadge" class="px-2.5 py-1 rounded-full text-xs font-bold shrink-0"></span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200 dark:border-slate-700">
                    <div class="p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block">Stok Fisik Tersedia</span>
                        <strong id="resStok" class="text-base text-slate-900 dark:text-white"></strong>
                    </div>
                    <div class="p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block">Kondisi Fisik</span>
                        <strong id="resKondisi" class="text-base text-slate-900 dark:text-white"></strong>
                    </div>
                </div>

                <!-- Action Shortcut Buttons for found asset -->
                <div class="flex items-center gap-2 pt-2">
                    <a id="btnResDetail" href="#" class="flex-1 py-2 px-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold text-center hover:opacity-90 transition">
                        Buka Detail di Inventaris
                    </a>
                    <a href="{{ route('qr.index') }}" class="py-2 px-3 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-300 rounded-xl text-xs font-bold text-center hover:bg-rose-100 transition">
                        Cetak Label QR
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: PERSETUJUAN CEPAT (APPROVAL HUB) -->
<!-- ======================================================== -->
<div id="modalApprovalHub" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in print:hidden">
    <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-5 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900 dark:text-white">Pusat Persetujuan Operasional (Approval Hub)</h3>
                    <p class="text-[11px] text-slate-400">{{ count($pendingLoansList) + count($pendingMutasiList) }} berkas menunggu validasi pimpinan / supervisor</p>
                </div>
            </div>
            <button type="button" onclick="closeApprovalModal()" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="overflow-y-auto space-y-4 pr-1 custom-scrollbar flex-1">
            <!-- Peminjaman Pending Section -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                        <i data-lucide="handshake" class="w-4 h-4 text-amber-500"></i>
                        Antrean Peminjaman Aset ({{ count($pendingLoansList) }})
                    </span>
                    <a href="{{ route('peminjaman.index') }}" class="text-rose-600 text-[11px] font-bold hover:underline">Semua Pinjaman</a>
                </div>

                @forelse($pendingLoansList as $pl)
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-rose-600 dark:text-rose-400">{{ $pl->nomor_pinjam }}</span>
                                <span class="text-slate-400">&bull; {{ \Carbon\Carbon::parse($pl->tanggal_pinjam)->format('d M Y') }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900 dark:text-white mt-0.5">{{ $pl->inventaris->nama_barang ?? 'Aset' }} ({{ $pl->jumlah }} unit)</h4>
                            <p class="text-[11px] text-slate-500">Peminjam: <strong class="text-slate-700 dark:text-slate-300">{{ $pl->karyawan->nama ?? '-' }}</strong> &bull; Keperluan: {{ $pl->keperluan }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form method="POST" action="{{ route('peminjaman.approve', $pl->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-2xs cursor-pointer">
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('peminjaman.reject', $pl->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold transition cursor-pointer">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2 italic text-center">Tidak ada permohonan peminjaman yang menunggu persetujuan.</p>
                @endforelse
            </div>

            <!-- Mutasi Pending Section -->
            <div class="space-y-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                        <i data-lucide="rotate-ccw" class="w-4 h-4 text-purple-500"></i>
                        Antrean Mutasi Penempatan Lokasi ({{ count($pendingMutasiList) }})
                    </span>
                    <a href="{{ route('mutasi.index') }}" class="text-purple-600 text-[11px] font-bold hover:underline">Semua Mutasi</a>
                </div>

                @forelse($pendingMutasiList as $pm)
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $pm->nomor_mutasi }}</span>
                                <span class="text-slate-400">&bull; Pemohon: {{ $pm->pemohon->nama ?? '-' }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900 dark:text-white mt-0.5">{{ $pm->inventaris->nama_barang ?? 'Aset' }}</h4>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                <span>Dari: {{ $pm->lokasiAwal->nama_lokasi ?? '-' }}</span>
                                <span>&rarr;</span>
                                <strong class="text-purple-600 dark:text-purple-400">Ke: {{ $pm->lokasiBaru->nama_lokasi ?? '-' }}</strong>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form method="POST" action="{{ route('mutasi.approve', $pm->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition shadow-2xs cursor-pointer">
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('mutasi.reject', $pm->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold transition cursor-pointer">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2 italic text-center">Tidak ada permohonan mutasi yang menunggu persetujuan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: QUICK STOCK IN (CATAT BARANG MASUK CEPAT) -->
<!-- ======================================================== -->
<div id="modalQuickStockIn" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in print:hidden">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900 dark:text-white">Penerimaan Stok Barang Masuk</h3>
                    <p class="text-[11px] text-slate-400">Sinkronisasi langsung menambah unit fisik katalog</p>
                </div>
            </div>
            <button type="button" onclick="closeQuickStockInModal()" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('barang-masuk.store') }}" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Master Aset <span class="text-rose-500">*</span></label>
                <select name="inventaris_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($allInventaris as $item)
                        <option value="{{ $item->id }}">{{ $item->kode_barang }} - {{ $item->nama_barang }} (Sisa: {{ $item->stok }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah Unit <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah" min="1" value="1" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Terima <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Supplier / Vendor Pengadaan <span class="text-rose-500">*</span></label>
                <input type="text" name="supplier" placeholder="Contoh: PT Sumber Rezeki Mandiri" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan</label>
                <textarea name="keterangan" rows="2" placeholder="Nomor PO / Surat Jalan / Keterangan..." class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeQuickStockInModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-md shadow-emerald-600/20 cursor-pointer">
                    Simpan Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 4: KIRIM REMINDER WHATSAPP CEPAT -->
<!-- ======================================================== -->
<div id="modalWaReminder" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in print:hidden">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900 dark:text-white">Kirim Pengingat WhatsApp</h3>
                    <p class="text-[11px] text-slate-400">Pemberitahuan jatuh tempo & verifikasi pengembalian aset</p>
                </div>
            </div>
            <button type="button" onclick="closeWaModal()" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('whatsapp.sendReminder') }}" class="space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="peminjaman_id" id="waPeminjamanId">

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Penerima <span class="text-rose-500">*</span></label>
                <input type="text" name="phone" id="waPhoneInput" placeholder="081234567890" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Isi Pesan Pengingat <span class="text-rose-500">*</span></label>
                <textarea name="pesan" id="waMessageInput" rows="5" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-sans focus:ring-2 focus:ring-emerald-500 leading-relaxed"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="openWaDirect()" class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-200 transition cursor-pointer flex items-center gap-1.5">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Buka Web WA</span>
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-md shadow-emerald-600/20 cursor-pointer">
                    Kirim & Catat Log
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- PRINT TEMPLATE FOOTER (HANYA MUNCUL SAAT CETAK / WINDOW.PRINT) -->
<!-- ======================================================== -->
<div class="hidden print:block pt-8 text-xs text-slate-600 border-t border-slate-300 mt-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-base font-black text-slate-900 uppercase">Ringkasan Eksekutif Statistik Aset Kantor</h2>
            <p class="text-xs">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i') }} WIB</p>
        </div>
        <div class="text-right">
            <p class="font-bold">Valuasi Buku: Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</p>
            <p>Total Model Terdata: {{ $totalAset }} | Unit Fisik: {{ $totalUnitFisik }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-12 pt-6 text-center">
        <div>
            <p>Petugas Logistik / Pengelola Aset,</p>
            <div class="h-16"></div>
            <p class="font-bold underline">{{ Auth::user()->name ?? 'Administrator' }}</p>
            <p class="text-[10px]">{{ Auth::user()->role ?? 'Staff Logistik' }}</p>
        </div>
        <div>
            <p>Pimpinan / Kepala Operasional,</p>
            <div class="h-16"></div>
            <p class="font-bold underline">Ir. H. Gunawan Pratama, M.T.</p>
            <p class="text-[10px]">General Manager Aset & Pengadaan</p>
        </div>
    </div>
</div>

<!-- SCRIPTS: INTERACTIVE FUNCTIONS, MODAL CONTROLS & SCANNER HANDLERS -->
<script>
    // Tab switching for circulation feeds
    function switchCirculationTab(tab) {
        ['peminjaman', 'logistik', 'mutasi', 'audit'].forEach(t => {
            const el = document.getElementById('circContent_' + t);
            if (el) {
                if (tab === 'all') {
                    el.classList.remove('hidden');
                } else if (tab === t) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        });

        // Update active tab styling
        ['all', 'peminjaman', 'logistik', 'mutasi', 'audit'].forEach(t => {
            const btn = document.getElementById('tabCirc_' + t);
            if (btn) {
                if (t === tab) {
                    btn.className = 'px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs transition cursor-pointer';
                } else {
                    btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer';
                }
            }
        });
    }

    // Modal Scanner controls
    function openScannerModal() {
        const modal = document.getElementById('modalQuickScan');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const inp = document.getElementById('barcodeScannerInput');
                if (inp) inp.focus();
            }, 100);
        }
    }

    function closeScannerModal() {
        const modal = document.getElementById('modalQuickScan');
        if (modal) modal.classList.add('hidden');
    }

    // Handle barcode search via AJAX
    function handleScannerSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('barcodeScannerInput');
        const q = input ? input.value.trim() : '';
        if (!q) return;

        const loading = document.getElementById('scanLoading');
        const empty = document.getElementById('scanEmpty');
        const found = document.getElementById('scanFound');

        if (loading) loading.classList.remove('hidden');
        if (empty) empty.classList.add('hidden');
        if (found) found.classList.add('hidden');

        fetch(`/dashboard/search-asset?q=${encodeURIComponent(q)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');

            if (data.success && data.asset) {
                const a = data.asset;
                document.getElementById('resKode').innerText = a.kode_barang + (a.barcode ? ' • ' + a.barcode : '');
                document.getElementById('resNama').innerText = a.nama_barang;
                document.getElementById('resKategori').innerText = a.kategori;
                document.getElementById('resLokasi').innerText = a.lokasi;
                document.getElementById('resStok').innerText = a.stok + ' Unit';
                document.getElementById('resKondisi').innerText = a.kondisi;
                
                const badge = document.getElementById('resStatusBadge');
                if (badge) {
                    badge.innerText = a.status;
                    if (a.status === 'Tersedia') {
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold shrink-0 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                    } else if (a.status === 'Dipinjam') {
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold shrink-0 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
                    } else {
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold shrink-0 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
                    }
                }

                const detailLink = document.getElementById('btnResDetail');
                if (detailLink) {
                    detailLink.href = `/inventaris?search=${encodeURIComponent(a.kode_barang)}`;
                }

                if (found) found.classList.remove('hidden');
            } else {
                if (empty) {
                    empty.innerHTML = `<span class="text-rose-600 font-bold">⚠️ ${data.message || 'Aset tidak ditemukan'}</span>`;
                    empty.classList.remove('hidden');
                }
            }
        })
        .catch(err => {
            if (loading) loading.classList.add('hidden');
            if (empty) {
                empty.innerHTML = `<span class="text-rose-600 font-bold">Terjadi kesalahan koneksi saat memindai.</span>`;
                empty.classList.remove('hidden');
            }
        });
    }

    // Modal Approval controls
    function openApprovalModal() {
        const modal = document.getElementById('modalApprovalHub');
        if (modal) modal.classList.remove('hidden');
    }

    function closeApprovalModal() {
        const modal = document.getElementById('modalApprovalHub');
        if (modal) modal.classList.add('hidden');
    }

    // Modal Quick Stock In controls
    function openQuickStockInModal() {
        const modal = document.getElementById('modalQuickStockIn');
        if (modal) modal.classList.remove('hidden');
    }

    function closeQuickStockInModal() {
        const modal = document.getElementById('modalQuickStockIn');
        if (modal) modal.classList.add('hidden');
    }

    // Modal WhatsApp controls
    function openWaModal(peminjamanId, peminjamNama, phone, asetNama, nomorPinjam, tanggalJatuhTempo) {
        document.getElementById('waPeminjamanId').value = peminjamanId;
        document.getElementById('waPhoneInput').value = phone || '';
        
        const defaultMsg = `Yth. ${peminjamNama},\n\nKami mengingatkan terkait peminjaman aset:\n- Nomor: ${nomorPinjam}\n- Barang: ${asetNama}\n- Batas Pengembalian: ${tanggalJatuhTempo}\n\nMohon dapat mengembalikan atau melakukan konfirmasi perpanjangan ke bagian Pengelola Logistik & Aset Kantor. Terima kasih.`;
        document.getElementById('waMessageInput').value = defaultMsg;

        const modal = document.getElementById('modalWaReminder');
        if (modal) modal.classList.remove('hidden');
    }

    function closeWaModal() {
        const modal = document.getElementById('modalWaReminder');
        if (modal) modal.classList.add('hidden');
    }

    function openWaDirect() {
        const phone = document.getElementById('waPhoneInput').value.replace(/[^0-9]/g, '');
        const text = encodeURIComponent(document.getElementById('waMessageInput').value);
        if (phone) {
            window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
        } else {
            alert('Nomor telepon belum diisi!');
        }
    }

    // Global keyboard listener for hardware USB barcode scanner
    let barcodeBuffer = '';
    let lastKeyTime = Date.now();

    window.addEventListener('keydown', (e) => {
        // Abaikan jika user sedang mengetik di input / textarea
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        if (activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select') {
            return;
        }

        const currentTime = Date.now();
        // Hardware scanner biasanya mengirim karakter dengan jeda < 50ms
        if (currentTime - lastKeyTime > 150) {
            barcodeBuffer = '';
        }
        lastKeyTime = currentTime;

        if (e.key === 'Enter') {
            if (barcodeBuffer.length >= 3) {
                openScannerModal();
                const inp = document.getElementById('barcodeScannerInput');
                if (inp) {
                    inp.value = barcodeBuffer;
                    handleScannerSubmit(e);
                }
                barcodeBuffer = '';
            }
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
        }
    });

    // Close modals on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeScannerModal();
            closeApprovalModal();
            closeQuickStockInModal();
            closeWaModal();
        }
    });
</script>
@endsection
