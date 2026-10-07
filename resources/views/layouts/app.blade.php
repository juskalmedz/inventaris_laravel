<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris & Aset Kantor') - Laravel 13</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (Vite / CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <!-- Libraries: QR Code Generator & html2canvas-pro (Support OKLCH Tailwind colors) -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.min.js"></script>
    <script>
        if (typeof window.html2canvasPro !== 'undefined' && typeof window.html2canvas === 'undefined') {
            window.html2canvas = window.html2canvasPro;
        }
    </script>
    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            main { padding: 0 !important; background: white !important; }
        }

        /* Mode Cetak 1 Item Stiker QR Khusus (Thermal & Single Label) */
        body.printing-single-item {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body.printing-single-item aside,
        body.printing-single-item header,
        body.printing-single-item main > *:not(#modalSingleQr) {
            display: none !important;
        }
        body.printing-single-item #modalSingleQr {
            position: fixed !important;
            inset: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            display: flex !important;
            z-index: 99999 !important;
        }
        body.printing-single-item #modalSingleQr .no-print {
            display: none !important;
        }
        body.printing-single-item #single-item-qr-print-sticker {
            position: absolute !important;
            left: 4mm !important;
            top: 4mm !important;
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    </style>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased flex overflow-hidden">

    <!-- SIDEBAR 1:1 DENGAN REACT APP -->
    <aside id="sidebar" class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col shrink-0 transition-all duration-300">
        <!-- Brand Header -->
        <div class="h-16 px-4 flex items-center justify-between border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-red-500 text-white flex items-center justify-center font-black shadow-md shadow-rose-500/25">
                    📦
                </div>
                <div>
                    <h1 class="font-black text-sm tracking-tight text-slate-900 dark:text-white leading-tight">Inventaris Kantor</h1>
                    <span class="text-[10px] font-mono text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider">Laravel 13 (1:1)</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1 text-xs font-semibold custom-scrollbar">
            <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase text-slate-400">Navigasi Utama</div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📊</span> <span>Dashboard Eksekutif</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase text-slate-400">Master Data</div>
            <a href="{{ route('inventaris.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('inventaris.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📦</span> <span>Katalog Inventaris</span>
            </a>
            <a href="{{ route('karyawan.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('karyawan.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>👥</span> <span>Master Karyawan</span>
            </a>
            <a href="{{ route('departemen.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('departemen.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🏢</span> <span>Departemen & Divisi</span>
            </a>
            <a href="{{ route('kategori.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('kategori.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🏷️</span> <span>Kategori Aset</span>
            </a>
            <a href="{{ route('lokasi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('lokasi.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📍</span> <span>Lokasi Penempatan</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase text-slate-400">Sirkulasi & Transaksi</div>
            <a href="{{ route('barang-masuk.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('barang-masuk.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📥</span> <span>Barang Masuk</span>
            </a>
            <a href="{{ route('barang-keluar.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('barang-keluar.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📤</span> <span>Barang Keluar</span>
            </a>
            <a href="{{ route('peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('peminjaman.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🤝</span> <span>Peminjaman Aset</span>
            </a>
            <a href="{{ route('mutasi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('mutasi.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🔄</span> <span>Mutasi & Pemindahan</span>
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('maintenance.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>🔧</span> <span>Tiket Perbaikan</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase text-slate-400">Laporan & Utilitas</div>
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('laporan.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📑</span> <span>Laporan Ber-KOP</span>
            </a>
            <a href="{{ route('qr.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('qr.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📱</span> <span>Studio Label QR</span>
            </a>
            <a href="{{ route('audit.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->routeIs('audit.*') ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span>📜</span> <span>Audit Trail & Log</span>
            </a>
        </nav>

        <!-- Footer Connected Status -->
        <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between text-[11px] text-slate-500">
            <div class="flex items-center gap-1.5 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Laravel 13 Engine</span>
            </div>
            <span class="font-mono text-[10px] text-slate-400">PHP 8.3</span>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- TOP HEADER -->
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between shrink-0">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500 hidden sm:block">Sinkronisasi 1:1 Arsitektur React SPA & Laravel 13 Framework</p>
            </div>

            <!-- Right Profile & Live Clock -->
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-mono font-bold text-slate-700 dark:text-slate-200">
                    <span class="text-rose-600">⏰</span>
                    <span id="liveClock">{{ date('H:i:s') }} WIB</span>
                </div>

                <!-- Simulation Role Badge -->
                <div class="px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 text-xs font-bold font-mono">
                    Admin
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATION TOASTS -->
        @if(session('success'))
            <div class="mx-6 mt-4 p-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl text-xs font-bold flex items-center justify-between">
                <span>✅ {{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 p-3 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 rounded-xl text-xs font-bold flex items-center justify-between">
                <span>⚠️ {{ session('error') }}</span>
            </div>
        @endif

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-6 bg-slate-50/70 dark:bg-slate-950 custom-scrollbar">
            @yield('content')
        </main>
    </div>

    <script>
        // Real-time clock update
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            const el = document.getElementById('liveClock');
            if (el) el.innerText = timeStr;
        }, 1000);
    </script>
</body>
</html>
