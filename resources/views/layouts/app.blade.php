<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris & Aset Kantor') - Laravel 13</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
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
    <!-- Libraries: QR Code Generator & html2canvas-pro -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.min.js"></script>
    <script>
        if (typeof window.html2canvasPro !== 'undefined' && typeof window.html2canvas === 'undefined') {
            window.html2canvas = window.html2canvasPro;
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

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
<body class="h-full bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased flex overflow-hidden">

    <!-- SIDEBAR 1:1 IDENTIK DENGAN REACT SPA -->
    <aside id="appSidebar" class="w-64 sm:w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col shrink-0 transition-[width] duration-300 ease-in-out will-change-[width] select-none relative z-20">
        
        <!-- Sidebar Top Header & Brand (1:1 React INVENTARIS SHEETS style) -->
        <div class="h-16 px-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 cursor-pointer group min-w-0" title="INVENTARIS - Dashboard">
                <div class="w-10 h-10 bg-gradient-to-tr from-rose-600 to-red-500 rounded-xl flex items-center justify-center text-white shadow-md shadow-rose-500/25 group-hover:scale-105 transition-all duration-300 shrink-0">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
                <div class="overflow-hidden whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white">INVENTARIS</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800">LARAVEL</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                        Sinkronisasi 1:1 React
                    </div>
                </div>
            </a>

            <!-- Collapse Toggle Button -->
            <button
                type="button"
                onclick="toggleSidebarCollapse()"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0 active:scale-95"
                title="Perkecil / Perbesar Sidebar"
            >
                <i data-lucide="panel-left-close" id="sidebarCollapseIcon" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Navigation Menu List -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1 text-xs font-semibold custom-scrollbar">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="w-full flex items-center px-3 py-2.5 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-rose-500' }}"></i>
                </div>
                <span class="ml-3 truncate font-bold">Dashboard</span>
            </a>

            <!-- Master Data Section -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Master Data
            </div>

            <a href="{{ route('inventaris.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('inventaris.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="box" class="w-4 h-4 {{ request()->routeIs('inventaris.*') ? 'text-white' : 'text-blue-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Katalog Inventaris</span>
            </a>

            <a href="{{ route('karyawan.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('karyawan.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('karyawan.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Data Karyawan / PIC</span>
            </a>

            <a href="{{ route('departemen.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('departemen.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="building-2" class="w-4 h-4 {{ request()->routeIs('departemen.*') ? 'text-white' : 'text-indigo-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Departemen & Divisi</span>
            </a>

            <a href="{{ route('kategori.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('kategori.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="tag" class="w-4 h-4 {{ request()->routeIs('kategori.*') ? 'text-white' : 'text-amber-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Kategori Inventaris</span>
            </a>

            <a href="{{ route('lokasi.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('lokasi.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="map-pin" class="w-4 h-4 {{ request()->routeIs('lokasi.*') ? 'text-white' : 'text-rose-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Ruangan & Lokasi</span>
            </a>

            <!-- Sirkulasi & Logistik Section -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Sirkulasi & Logistik
            </div>

            <a href="{{ route('barang-masuk.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('barang-masuk.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 {{ request()->routeIs('barang-masuk.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Barang Masuk (Pengadaan)</span>
            </a>

            <a href="{{ route('barang-keluar.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('barang-keluar.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 {{ request()->routeIs('barang-keluar.*') ? 'text-white' : 'text-rose-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Barang Keluar (Distribusi)</span>
            </a>

            <a href="{{ route('peminjaman.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('peminjaman.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="handshake" class="w-4 h-4 {{ request()->routeIs('peminjaman.*') ? 'text-white' : 'text-blue-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Peminjaman Aset</span>
            </a>

            <a href="{{ route('mutasi.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('mutasi.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 {{ request()->routeIs('mutasi.*') ? 'text-white' : 'text-purple-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Mutasi & Pemindahan</span>
            </a>

            <a href="{{ route('maintenance.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('maintenance.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="wrench" class="w-4 h-4 {{ request()->routeIs('maintenance.*') ? 'text-white' : 'text-amber-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Tiket Servis & Servis</span>
            </a>

            <!-- Laporan & Utilitas Section -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Laporan & Utilitas
            </div>

            <a href="{{ route('laporan.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('laporan.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 {{ request()->routeIs('laporan.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Laporan Resmi Ber-KOP</span>
            </a>

            <a href="{{ route('qr.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('qr.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="qr-code" class="w-4 h-4 {{ request()->routeIs('qr.*') ? 'text-white' : 'text-indigo-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Studio Cetak Label QR</span>
            </a>

            <!-- Keamanan & Audit -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Keamanan & Sistem
            </div>

            <a href="{{ route('audit.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('audit.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="shield-check" class="w-4 h-4 {{ request()->routeIs('audit.*') ? 'text-white' : 'text-purple-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Audit Trail & Log</span>
            </a>
        </nav>

        <!-- Sidebar Footer Status -->
        <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 flex items-center justify-between text-[11px] text-slate-500">
            <div class="flex items-center gap-1.5 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>REST API Connected</span>
            </div>
            <span class="font-mono text-[9px] font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/80 px-1.5 py-0.5 rounded border border-emerald-300 dark:border-emerald-800">GRADE A+</span>
        </div>
    </aside>

    <!-- MAIN PANE WRAPPER -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        
        <!-- TOP HEADER (1:1 DENGAN REACT HEADER) -->
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0 z-10">
            
            <!-- Left: Toggle & Title -->
            <div class="flex items-center min-w-0 mr-2">
                <button
                    type="button"
                    onclick="toggleSidebarCollapse()"
                    class="p-2 sm:p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0 mr-3 shadow-2xs active:scale-95 flex items-center justify-center"
                    title="Menu Sidebar"
                >
                    <i data-lucide="menu" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>
                </button>

                <div class="min-w-0">
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white capitalize truncate">
                        @yield('page_title', 'Ringkasan Eksekutif & Statistik Aset')
                    </h2>
                    <p class="text-[11px] text-slate-500 hidden sm:block truncate">
                        Sistem Manajemen Inventaris & Aset Kantor (Sinkronisasi 1:1 React & Laravel 13)
                    </p>
                </div>
            </div>

            <!-- Right: Real-time Date, Clock, User Profile & Actions -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                
                <!-- LIVE CLOCK WITH DAY, DATE & TIME (1:1 React Style) -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-slate-700 dark:text-slate-300 shadow-2xs">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0"></i>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="hidden lg:inline font-semibold">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}
                        </span>
                        <span class="hidden lg:inline text-slate-400 dark:text-slate-500">&bull;</span>
                        <span id="liveClock" class="font-mono font-bold text-slate-900 dark:text-white tabular-nums tracking-tight">
                            {{ date('H:i:s') }} WIB
                        </span>
                    </div>
                </div>

                <!-- Simulation Role Badge -->
                <div class="px-2.5 py-1 rounded-xl bg-rose-100 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs font-bold font-mono">
                    {{ Auth::user()->role ?? 'Admin' }}
                </div>

                <!-- User Profile & Avatar -->
                <div class="flex items-center gap-2 pl-1 border-l border-slate-200 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-600 to-red-500 text-white flex items-center justify-center font-extrabold text-xs shadow-md shadow-rose-500/20">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="hidden lg:block text-left text-xs leading-tight">
                        <p class="font-bold text-slate-900 dark:text-white truncate max-w-[120px]">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">@<span>{{ Auth::user()->username ?? 'admin' }}</span></p>
                    </div>
                </div>

            </div>
        </header>

        <!-- FLASH NOTIFICATION TOASTS -->
        @if(session('success'))
            <div class="mx-6 mt-4 p-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 p-3 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-100/70 dark:bg-slate-950 custom-scrollbar">
            @yield('content')
        </main>
    </div>

    <!-- SCRIPT REAL-TIME & TOGGLES -->
    <script>
        // Initialize Lucide icons
        function refreshIcons() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        document.addEventListener('DOMContentLoaded', refreshIcons);

        // Sidebar collapse toggle
        let isSidebarCollapsed = false;
        function toggleSidebarCollapse() {
            isSidebarCollapsed = !isSidebarCollapsed;
            const aside = document.getElementById('appSidebar');
            if (isSidebarCollapsed) {
                aside.classList.remove('w-64', 'sm:w-72');
                aside.classList.add('w-20');
            } else {
                aside.classList.remove('w-20');
                aside.classList.add('w-64', 'sm:w-72');
            }
        }

        // Real-time clock update (HH:MM:SS WIB)
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            const el = document.getElementById('liveClock');
            if (el) el.innerText = timeStr;
        }, 1000);
    </script>
</body>
</html>
