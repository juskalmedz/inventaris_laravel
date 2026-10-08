<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris & Aset Kantor') - Laravel 13</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Anti-FOUC Theme Initializer -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tailwind CSS (Vite Native with Standalone Fallback) -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                            mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                        },
                        colors: {
                            brand: {
                                50: '#fff1f2',
                                100: '#ffe4e6',
                                200: '#fecdd3',
                                300: '#fda4af',
                                400: '#fb7185',
                                500: '#f43f5e',
                                600: '#e11d48',
                                700: '#be123c',
                                800: '#9f1239',
                                900: '#881337',
                                950: '#4c0519',
                            }
                        },
                        boxShadow: {
                            '2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                            'xs': '0 1px 3px 0 rgba(0, 0, 0, 0.07)',
                        }
                    }
                }
            }
        </script>
        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
            .glass-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
            .dark .glass-panel { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
            @media print {
                aside, header, nav, .no-print { display: none !important; }
                body { background: #ffffff !important; color: #0f172a !important; margin: 0 !important; }
                main { padding: 0 !important; background: white !important; overflow: visible !important; }
            }
            body.printing-single-item { background: #ffffff !important; margin: 0 !important; padding: 0 !important; }
            body.printing-single-item aside, body.printing-single-item header, body.printing-single-item main > *:not(#modalSingleQr) { display: none !important; }
            body.printing-single-item #modalSingleQr { position: fixed !important; inset: 0 !important; background: transparent !important; padding: 0 !important; display: flex !important; z-index: 99999 !important; }
            body.printing-single-item #modalSingleQr .no-print { display: none !important; }
            body.printing-single-item #single-item-qr-print-sticker { position: absolute !important; left: 4mm !important; top: 4mm !important; box-shadow: none !important; border: 1px solid #cbd5e1 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        </style>
    @endif

    <!-- QR & Canvas Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.min.js"></script>
    <script>
        if (typeof window.html2canvasPro !== 'undefined' && typeof window.html2canvas === 'undefined') {
            window.html2canvas = window.html2canvasPro;
        }
    </script>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased flex overflow-hidden">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="closeMobileSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-30 hidden lg:hidden transition-opacity"></div>

    <!-- SIDEBAR NAVIGATION -->
    <aside id="appSidebar" class="fixed inset-y-0 left-0 z-40 lg:static w-64 sm:w-72 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800 flex flex-col shrink-0 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 will-change-transform select-none">
        
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 cursor-pointer group min-w-0" title="INVENTARIS - Dashboard">
                <div class="w-10 h-10 bg-gradient-to-tr from-rose-600 via-rose-500 to-red-500 rounded-xl flex items-center justify-center text-white shadow-md shadow-rose-500/25 group-hover:scale-105 group-hover:shadow-rose-500/40 transition-all duration-300 shrink-0">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
                <div class="overflow-hidden whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white">INVENTARIS</span>
                        <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-md border bg-rose-50 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-900/80">L13</span>
                    </div>
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                        Aset & Sirkulasi Kantor
                    </div>
                </div>
            </a>

            <!-- Close button for Mobile / Collapse for Desktop -->
            <button
                type="button"
                onclick="closeMobileSidebar()"
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                title="Tutup Menu"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Menu List -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1 text-xs font-semibold custom-scrollbar">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="w-full flex items-center px-3 py-2.5 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-rose-500' }}"></i>
                </div>
                <span class="ml-3 truncate font-bold">Dashboard</span>
            </a>

            <!-- Master Data Section -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Master Data
            </div>

            <a href="{{ route('inventaris.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('inventaris.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="box" class="w-4 h-4 {{ request()->routeIs('inventaris.*') ? 'text-white' : 'text-blue-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Data Inventaris</span>
            </a>

            <a href="{{ route('karyawan.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('karyawan.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('karyawan.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Data Karyawan / PIC</span>
            </a>

            <a href="{{ route('departemen.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('departemen.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="building-2" class="w-4 h-4 {{ request()->routeIs('departemen.*') ? 'text-white' : 'text-indigo-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Departemen & Divisi</span>
            </a>

            <a href="{{ route('kategori.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('kategori.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="tag" class="w-4 h-4 {{ request()->routeIs('kategori.*') ? 'text-white' : 'text-amber-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Kategori Inventaris</span>
            </a>

            <a href="{{ route('lokasi.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('lokasi.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="map-pin" class="w-4 h-4 {{ request()->routeIs('lokasi.*') ? 'text-white' : 'text-rose-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Ruangan & Lokasi</span>
            </a>

            <!-- Sirkulasi & Logistik Section -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Sirkulasi & Logistik
            </div>

            <a href="{{ route('barang-masuk.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('barang-masuk.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 {{ request()->routeIs('barang-masuk.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Barang Masuk (Pengadaan)</span>
            </a>

            <a href="{{ route('barang-keluar.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('barang-keluar.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 {{ request()->routeIs('barang-keluar.*') ? 'text-white' : 'text-rose-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Barang Keluar (Distribusi)</span>
            </a>

            <a href="{{ route('peminjaman.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('peminjaman.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="handshake" class="w-4 h-4 {{ request()->routeIs('peminjaman.*') ? 'text-white' : 'text-blue-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Peminjaman Aset</span>
            </a>

            <a href="{{ route('mutasi.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('mutasi.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 {{ request()->routeIs('mutasi.*') ? 'text-white' : 'text-purple-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Mutasi & Pemindahan</span>
            </a>

            <a href="{{ route('maintenance.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('maintenance.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="wrench" class="w-4 h-4 {{ request()->routeIs('maintenance.*') ? 'text-white' : 'text-amber-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Tiket Servis & Servis</span>
            </a>

            <!-- Laporan & Utilitas Section -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Laporan & Utilitas
            </div>

            <a href="{{ route('laporan.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('laporan.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 {{ request()->routeIs('laporan.*') ? 'text-white' : 'text-emerald-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Laporan Resmi Ber-KOP</span>
            </a>

            <a href="{{ route('qr.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('qr.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="qr-code" class="w-4 h-4 {{ request()->routeIs('qr.*') ? 'text-white' : 'text-indigo-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Studio Cetak Label QR</span>
            </a>

            <!-- Keamanan & Audit -->
            <div class="px-3 pt-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Keamanan & Sistem
            </div>

            <a href="{{ route('audit.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('audit.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="shield-check" class="w-4 h-4 {{ request()->routeIs('audit.*') ? 'text-white' : 'text-purple-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Audit Trail & Log</span>
            </a>

            <a href="{{ route('pengaturan.index') }}" class="w-full flex items-center px-3 py-2 rounded-xl text-left cursor-pointer transition relative group {{ request()->routeIs('pengaturan.*') ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-5 h-5 flex items-center justify-center shrink-0">
                    <i data-lucide="settings" class="w-4 h-4 {{ request()->routeIs('pengaturan.*') ? 'text-white' : 'text-slate-500' }}"></i>
                </div>
                <span class="ml-3 truncate">Pengaturan & RBAC</span>
            </a>

            <!-- Download ZIP Project Lengkap Button -->
            <div class="pt-2 px-1">
                <a href="/api/v1/laravel/download-zip" download="laravel13-inventaris-aset-kantor-lengkap.zip" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold text-xs shadow-md shadow-rose-600/25 transition transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer text-center">
                    <i data-lucide="download" class="w-4 h-4 shrink-0"></i>
                    <span class="truncate">Unduh ZIP Laravel 13</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer Status -->
        <div class="p-3 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/60 flex items-center justify-between text-[11px] text-slate-500">
            <div class="flex items-center gap-1.5 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-slate-700 dark:text-slate-300">Tailwind v4 Active</span>
            </div>
            <span class="font-mono text-[9px] font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-100/80 dark:bg-emerald-950/80 px-2 py-0.5 rounded border border-emerald-300 dark:border-emerald-800">100% READY</span>
        </div>
    </aside>

    <!-- MAIN PANE WRAPPER -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        
        <!-- TOP HEADER -->
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0 z-10 transition-colors">
            
            <!-- Left: Mobile Menu Trigger & Title -->
            <div class="flex items-center min-w-0 mr-2">
                <button
                    type="button"
                    onclick="openMobileSidebar()"
                    class="lg:hidden p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0 mr-3 shadow-2xs active:scale-95 flex items-center justify-center"
                    title="Buka Menu"
                >
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <div class="min-w-0">
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white capitalize truncate flex items-center gap-2">
                        <span>@yield('page_title', 'Ringkasan Eksekutif & Statistik Aset')</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 hidden sm:block truncate">
                        Sistem Manajemen Inventaris & Aset Kantor &bull; Tailwind CSS Framework
                    </p>
                </div>
            </div>

            <!-- Right: Real-time Date, Clock, Theme Toggle, Role Switcher & User Profile -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                
                <!-- LIVE CLOCK WITH DAY, DATE & TIME -->
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

                <!-- Dark / Light Mode Toggle -->
                <button
                    type="button"
                    onclick="toggleThemeMode()"
                    id="themeToggleBtn"
                    class="p-2 sm:p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer shrink-0 shadow-2xs active:scale-95 flex items-center justify-center"
                    title="Beralih Mode Gelap / Terang"
                >
                    <i data-lucide="moon" id="themeMoonIcon" class="w-4 h-4 block dark:hidden"></i>
                    <i data-lucide="sun" id="themeSunIcon" class="w-4 h-4 hidden dark:block text-amber-400"></i>
                </button>

                <!-- Simulation Role Switcher Dropdown Form -->
                <form method="POST" action="{{ route('switch.role') }}" class="hidden sm:inline-flex items-center">
                    @csrf
                    <div class="relative">
                        <select
                            name="role"
                            onchange="this.form.submit()"
                            title="Simulasi Ganti Role Hak Akses"
                            class="appearance-none pl-3 pr-7 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-900/80 text-rose-700 dark:text-rose-300 text-xs font-bold font-mono cursor-pointer hover:bg-rose-100 dark:hover:bg-rose-900/50 transition focus:outline-none focus:ring-2 focus:ring-rose-500"
                        >
                            @php $currentRole = Auth::user()->role ?? 'Admin'; @endphp
                            <option value="Admin" {{ $currentRole === 'Admin' ? 'selected' : '' }}>👑 Admin</option>
                            <option value="Supervisor" {{ $currentRole === 'Supervisor' ? 'selected' : '' }}>🛡️ Supervisor</option>
                            <option value="Staff" {{ $currentRole === 'Staff' ? 'selected' : '' }}>👤 Staff</option>
                            <option value="Auditor" {{ $currentRole === 'Auditor' ? 'selected' : '' }}>🔍 Auditor</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-2 top-1/2 -translate-y-1/2 text-rose-600 dark:text-rose-400 pointer-events-none"></i>
                    </div>
                </form>

                <!-- User Profile & Avatar -->
                <div class="flex items-center gap-2 pl-1 border-l border-slate-200 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-600 to-red-500 text-white flex items-center justify-center font-extrabold text-xs shadow-md shadow-rose-500/20">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="hidden lg:block text-left text-xs leading-tight">
                        <p class="font-bold text-slate-900 dark:text-white truncate max-w-[120px]">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">@<span>{{ Auth::user()->username ?? 'admin' }}</span></p>
                    </div>

                    <!-- Logout Button Form -->
                    <form method="POST" action="{{ route('logout') }}" class="inline ml-1">
                        @csrf
                        <button
                            type="submit"
                            title="Keluar dari Sistem (Logout)"
                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer flex items-center justify-center"
                        >
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

            </div>
        </header>

        <!-- FLASH NOTIFICATION TOASTS -->
        @if(session('success'))
            <div id="flashSuccessToast" class="mx-4 sm:mx-6 mt-4 p-3.5 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-2xl text-xs font-bold flex items-center justify-between gap-2 shadow-xs transition">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashSuccessToast').remove()" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 cursor-pointer p-1">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="flashErrorToast" class="mx-4 sm:mx-6 mt-4 p-3.5 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 rounded-2xl text-xs font-bold flex items-center justify-between gap-2 shadow-xs transition">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashErrorToast').remove()" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 cursor-pointer p-1">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-100/70 dark:bg-slate-950 custom-scrollbar">
            @yield('content')
        </main>
    </div>

    <!-- SCRIPT REAL-TIME & TOGGLES -->
    <script>
        // Refresh icons
        function refreshIcons() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }

        // Mobile drawer handlers
        function openMobileSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }

        // Theme Mode Toggle
        function toggleThemeMode() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            refreshIcons();
        }

        window.toggleThemeMode = toggleThemeMode;
        window.toggleTheme = toggleThemeMode;

        // Initialize Theme from localStorage
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();

        document.addEventListener('DOMContentLoaded', () => {
            refreshIcons();
        });

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
