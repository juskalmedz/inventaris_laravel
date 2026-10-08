@extends('layouts.app')

@section('page_title', 'Data Inventaris & Manajemen Stok Fisik')

@section('content')
<div class="space-y-4">

    <!-- 1. SUMMARY STAT CARDS (1:1 IDENTIK DENGAN REACT APP) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Katalog Aset</p>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $totalAset ?? count($items) }} Jenis Barang</h4>
                <span class="text-[11px] text-rose-600 dark:text-rose-400 font-semibold">
                    {{ number_format($totalStok ?? 0, 0, ',', '.') }} Total Unit Fisik
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="box" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Taksiran Nilai</p>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                    Rp @if(($totalNilai ?? 0) >= 1000000000)
                        {{ number_format(($totalNilai ?? 0) / 1000000000, 2, ',', '.') }} M
                    @elseif(($totalNilai ?? 0) >= 1000000)
                        {{ number_format(($totalNilai ?? 0) / 1000000, 1, ',', '.') }} Jt
                    @else
                        {{ number_format($totalNilai ?? 0, 0, ',', '.') }}
                    @endif
                </h4>
                <span class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold truncate block">
                    {{ $asetTetapCount ?? 0 }} Aset Tetap • {{ $habisPakaiCount ?? 0 }} ATK/Habis
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="coins" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Ketersediaan</p>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                    {{ number_format($stokTersedia ?? 0, 0, ',', '.') }} Unit Siap Pakai
                </h4>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                    {{ ($stokKritisCount ?? 0) > 0 ? ($stokKritisCount . ' Stok Kritis') : 'Stok Aman' }} • {{ $stokHabisCount ?? 0 }} Habis
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kondisi & Pemeliharaan</p>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                    {{ $kondisiBaikCount ?? 0 }} Aset Baik
                </h4>
                <span class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold">
                    {{ $dalamMaintenanceCount ?? 0 }} Perlu / Dalam Servis
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center shrink-0">
                <i data-lucide="wrench" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- 2. ADVANCED FILTER & SEARCH PANEL (1:1 DENGAN REACT APP) -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
        <form method="GET" action="{{ route('inventaris.index') }}" id="filterFormInv" class="space-y-3">
            <input type="hidden" name="stok_level" id="inputStokLevel" value="{{ request('stok_level', 'all') }}" />
            @if(request('bulk_search'))
                <input type="hidden" name="bulk_search" value="{{ request('bulk_search') }}" />
            @endif

            <!-- ROW 1: SEARCH & ACTION BUTTONS -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="relative flex-1 min-w-[240px]">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kode aset, nama barang, model, spesifikasi, atau catatan..."
                        class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-900 dark:text-white font-medium"
                    />
                    @if(request('search'))
                        <a href="{{ route('inventaris.index', request()->except('search')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        onclick="openBulkScannerModal()"
                        class="px-3.5 py-2.5 {{ request('bulk_search') ? 'bg-rose-600 text-white border-rose-700 shadow-md shadow-rose-500/20' : 'bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-900/60' }} rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer border"
                        title="Pindai barcode lewat USB Scanner fisik atau pencarian massal multi-kode"
                    >
                        <i data-lucide="scan-barcode" class="w-4 h-4 {{ request('bulk_search') ? 'text-white' : 'text-rose-600' }}"></i>
                        <span>Scan USB Barcode</span>
                        <span id="badgeBulkScannerCount" class="hidden px-1.5 py-0.2 rounded bg-black/10 dark:bg-white/10 text-[10px] font-mono">0</span>
                    </button>

                    <a
                        href="{{ route('inventaris.exportCsv', request()->query()) }}"
                        class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                        title="Unduh data inventaris dalam format berkas CSV"
                    >
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Ekspor CSV</span>
                    </a>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                        title="Cetak katalog inventaris"
                    >
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Cetak</span>
                    </button>

                    <button
                        type="button"
                        onclick="openModalTambah()"
                        class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-500/20 transition cursor-pointer"
                    >
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Aset Baru</span>
                    </button>
                </div>
            </div>

            <!-- ROW 2: ADVANCED SELECTS (6 KOLOM 1:1 IDENTIK DENGAN REACT) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 text-xs">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Kategori Aset</label>
                    <select name="kategori_id" onchange="document.getElementById('filterFormInv').submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Ruangan / Lokasi</label>
                    <select name="lokasi_id" onchange="document.getElementById('filterFormInv').submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">Semua Ruangan</option>
                        @foreach($lokasis as $l)
                            <option value="{{ $l->id }}" {{ request('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Kondisi Fisik</label>
                    <select name="kondisi" onchange="document.getElementById('filterFormInv').submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Status Ketersediaan</label>
                    <select name="status" onchange="document.getElementById('filterFormInv').submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Tidak Tersedia" {{ request('status') == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                        <option value="Dalam Maintenance" {{ request('status') == 'Dalam Maintenance' ? 'selected' : '' }}>Dalam Maintenance</option>
                        <option value="Habis" {{ request('status') == 'Habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Tipe Aset</label>
                    <select name="jenis_aset" onchange="document.getElementById('filterFormInv').submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium font-semibold">
                        <option value="">Semua Tipe Aset</option>
                        <option value="Tidak Habis Pakai" {{ request('jenis_aset') == 'Tidak Habis Pakai' ? 'selected' : '' }}>Tidak Habis Pakai (Aset Tetap)</option>
                        <option value="Habis Pakai" {{ request('jenis_aset') == 'Habis Pakai' ? 'selected' : '' }}>Habis Pakai (ATK / Konsumsi)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Urutan Data (Sorting)</label>
                    <select name="sort" onchange="document.getElementById('filterFormInv').submit()" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-rose-600 dark:text-rose-400">
                        <option value="kode" {{ request('sort') == 'kode' ? 'selected' : '' }}>Urut: Kode Aset</option>
                        <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Urut: Nama (A-Z)</option>
                        <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Urut: Nama (Z-A)</option>
                        <option value="stok_desc" {{ request('sort') == 'stok_desc' ? 'selected' : '' }}>Urut: Stok Tertinggi</option>
                        <option value="stok_asc" {{ request('sort') == 'stok_asc' ? 'selected' : '' }}>Urut: Stok Terendah</option>
                        <option value="nilai_desc" {{ request('sort') == 'nilai_desc' ? 'selected' : '' }}>Urut: Nilai Terbesar</option>
                    </select>
                </div>
            </div>

            <!-- ROW 3: LEVEL STOK PILLS, VIEW TOGGLE (TABLE VS CARD), COUNTER & RESET -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[11px] font-bold text-slate-400 mr-1 flex items-center gap-1">
                        <i data-lucide="sliders-horizontal" class="w-3 h-3"></i>
                        Status Stok:
                    </span>
                    @php $currentLevel = request('stok_level', 'all'); @endphp
                    <button
                        type="button"
                        onclick="setFilterStokLevel('all')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer {{ $currentLevel === 'all' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Semua Stok
                    </button>
                    <button
                        type="button"
                        onclick="setFilterStokLevel('tersedia')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer {{ $currentLevel === 'tersedia' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Aman (>3)
                    </button>
                    <button
                        type="button"
                        onclick="setFilterStokLevel('kritis')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer {{ $currentLevel === 'kritis' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Kritis (1-3)
                    </button>
                    <button
                        type="button"
                        onclick="setFilterStokLevel('habis')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer {{ $currentLevel === 'habis' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Habis (0)
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tombol Toggle Kolom Barcode (Hidden Column) -->
                    <button
                        type="button"
                        onclick="toggleBarcodeColumn()"
                        id="btnToggleBarcodeCol"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold transition cursor-pointer border bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                        title="Tampilkan / sembunyikan kolom barcode di tabel"
                    >
                        <i data-lucide="scan-barcode" class="w-3.5 h-3.5 text-rose-500"></i>
                        <span>Kolom Barcode:</span>
                        <span id="lblBarcodeColStatus" class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-500">
                            Tersembunyi
                        </span>
                        <i data-lucide="eye-off" id="iconBarcodeCol" class="w-3 h-3 text-slate-400"></i>
                    </button>

                    <!-- View Mode Switcher: Tabel vs Kartu -->
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <button
                            type="button"
                            onclick="setInvViewMode('table')"
                            id="btnViewModeTable"
                            class="flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs"
                            title="Tampilan Tabel Data"
                        >
                            <i data-lucide="table" class="w-3.5 h-3.5"></i>
                            <span>Tabel</span>
                        </button>
                        <button
                            type="button"
                            onclick="setInvViewMode('cards')"
                            id="btnViewModeCards"
                            class="flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer text-slate-500 hover:text-slate-900 dark:hover:text-white"
                            title="Tampilan Kartu (Card Grid)"
                        >
                            <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                            <span>Kartu</span>
                        </button>
                    </div>

                    <span class="text-[11px] font-semibold text-slate-500">
                        Menampilkan <b class="text-slate-900 dark:text-white font-mono">{{ $items->count() }}</b> dari {{ $items->total() }} aset
                    </span>

                    @if(request('search') || request('kategori_id') || request('lokasi_id') || request('kondisi') || request('status') || request('jenis_aset') || request('sort') || request('stok_level') != '' && request('stok_level') != 'all' || request('bulk_search'))
                        <a href="{{ route('inventaris.index') }}" class="flex items-center gap-1 text-[11px] text-rose-600 hover:text-rose-700 font-bold hover:underline cursor-pointer">
                            <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                            <span>Reset Filter</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- BANNER PENCARIAN MASSAL USB BARCODE SCANNER (JIKA AKTIF) -->
    <div id="bulkSearchBanner" class="{{ request('bulk_search') ? 'flex' : 'hidden' }} bg-gradient-to-r from-rose-500/10 via-red-500/10 to-amber-500/10 border-2 border-rose-500/30 rounded-2xl p-3.5 flex-wrap items-center justify-between gap-3 shadow-xs">
        <div class="flex flex-wrap items-center gap-2 min-w-0">
            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-rose-600 text-white rounded-lg text-xs font-black shadow-xs shrink-0">
                <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-300"></i>
                <span>Pencarian Massal Barcode ({{ request('bulk_search') ? count(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)request('bulk_search'))))) : 0 }} Kode)</span>
            </div>
            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                Menampilkan <b class="font-mono text-rose-600 dark:text-rose-400">{{ $items->total() }}</b> aset yang cocok:
            </span>
            <div id="bulkChipsContainer" class="flex flex-wrap gap-1.5">
                @if(request('bulk_search'))
                    @foreach(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)request('bulk_search')))) as $bCode)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-900/60 font-mono text-xs font-bold text-rose-600 dark:text-rose-400 shadow-2xs">
                            <span>{{ $bCode }}</span>
                            <button type="button" onclick="removeBulkCode('{{ $bCode }}')" class="text-slate-400 hover:text-rose-600 cursor-pointer">✕</button>
                        </span>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button
                type="button"
                onclick="openBulkScannerModal()"
                class="px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer"
            >
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Scan / Tambah Kode</span>
            </button>
            <a
                href="{{ route('inventaris.index') }}"
                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer"
            >
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Hapus Filter Massal</span>
            </a>
        </div>
    </div>

    <!-- 3. CARD VIEW MODE (CARD GRID 1:1 DENGAN REACT APP) -->
    <div id="containerCardView" class="hidden space-y-4">
        @if($items->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center text-slate-400">
                <i data-lucide="box" class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3 opacity-60"></i>
                <h4 class="text-base font-bold text-slate-800 dark:text-slate-200">Tidak ada aset yang ditemukan</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tidak ada aset yang sesuai dengan kriteria filter atau pencarian Anda. Coba reset filter.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($items as $item)
                    @php
                        $katName = $item->kategori->nama_kategori ?? '-';
                        $lokName = $item->lokasi->nama_lokasi ?? '-';
                        $totalVal = ($item->stok ?? 0) * ($item->harga_perkiraan ?? 0);
                        $barcodeVal = $item->barcode ?? ('899' . str_pad($item->id, 9, '0', STR_PAD_LEFT));
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs hover:shadow-md hover:border-rose-200 dark:hover:border-rose-900/60 transition flex flex-col justify-between overflow-hidden group">
                        <div class="p-4 space-y-3">
                            <!-- CARD TOP: KODE & JENIS ASET -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-lg border border-rose-200 dark:border-rose-900/60">
                                    {{ $item->kode_barang }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->jenis_aset === 'Habis Pakai' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-900/60' }}">
                                    {{ $item->jenis_aset === 'Habis Pakai' ? 'Habis Pakai' : 'Aset Tetap' }}
                                </span>
                            </div>

                            <!-- NAMA BARANG & DESKRIPSI -->
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white line-clamp-1 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition" title="{{ $item->nama_barang }}">
                                    {{ $item->nama_barang }}
                                </h4>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 line-clamp-2 mt-0.5 min-h-[32px]">
                                    {{ $item->deskripsi ?: 'Tidak ada deskripsi spesifikasi tambahan.' }}
                                </p>
                            </div>

                            <!-- METADATA PILLS: KATEGORI & RUANGAN -->
                            <div class="space-y-1.5 text-[11px]">
                                <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                    <i data-lucide="tag" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                    <span class="truncate">{{ $katName }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-purple-500 shrink-0"></i>
                                    <span class="truncate">{{ $lokName }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-1 text-[10px] font-mono text-slate-500 bg-slate-50 dark:bg-slate-800/60 px-2 py-1 rounded-lg border border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-1 min-w-0">
                                        <i data-lucide="scan-barcode" class="w-3 h-3 text-rose-500 shrink-0"></i>
                                        <span class="font-bold truncate text-slate-700 dark:text-slate-300">
                                            {{ $barcodeVal }}
                                        </span>
                                    </div>
                                    <svg class="barcode-svg" data-barcode="{{ $barcodeVal }}" width="45" height="12" viewBox="0 0 45 12"></svg>
                                </div>
                            </div>

                            <!-- METRICS STRIP: STOK, KONDISI, STATUS -->
                            <div class="bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Fisik</span>
                                    <span class="font-mono font-extrabold px-2 py-0.5 rounded-lg text-xs {{ ($item->stok ?? 0) <= 0 ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900/60' : (($item->stok ?? 0) <= 3 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60' : 'bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60') }}">
                                        {{ $item->stok }} {{ $item->satuan ?? 'Unit' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-1 text-[10px]">
                                    <span class="px-2 py-0.5 rounded font-bold {{ $item->kondisi === 'Baik' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300' }}">
                                        {{ $item->kondisi }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded font-bold bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                        {{ $item->status }}
                                    </span>
                                </div>

                                <div class="pt-1.5 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Total Nilai:</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">
                                        Rp {{ number_format($totalVal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD FOOTER ACTIONS -->
                        <div class="p-3 bg-slate-50/70 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 text-xs">
                            <button
                                type="button"
                                onclick="showAssetDetail({{ json_encode($item) }})"
                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 hover:text-emerald-600 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-700 font-bold transition cursor-pointer"
                                title="Lihat Detail Lengkap Aset"
                            >
                                <i data-lucide="eye" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Detail</span>
                            </button>

                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    onclick="openSingleQrModal({{ json_encode($item) }})"
                                    class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg cursor-pointer transition font-bold"
                                    title="Cetak Stiker Label QR (1 Item)"
                                >
                                    <i data-lucide="qr-code" class="w-3.5 h-3.5 text-indigo-500"></i>
                                </button>

                                <button
                                    type="button"
                                    onclick="openModalEdit({{ json_encode($item) }})"
                                    class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg cursor-pointer transition font-bold"
                                    title="Edit Data Aset"
                                >
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </button>

                                <form method="POST" action="{{ route('inventaris.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset {{ $item->nama_barang }} ({{ $item->kode_barang }}) dari sistem?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg cursor-pointer transition font-bold"
                                        title="Hapus Aset"
                                    >
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            {{ $items->links() }}
        </div>
    </div>

    <!-- 4. TABLE VIEW MODE (1:1 DENGAN REACT APP) -->
    <div id="containerTableView" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3.5">Kode</th>
                        <th class="col-barcode hidden p-3.5 text-center font-bold text-rose-600 dark:text-rose-400">
                            <div class="flex items-center justify-center gap-1.5">
                                <i data-lucide="scan-barcode" class="w-3.5 h-3.5"></i>
                                <span>Barcode (Code-128 / UPC)</span>
                            </div>
                        </th>
                        <th class="p-3.5">Nama Barang & Model</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Ruangan Lokasi</th>
                        <th class="p-3.5 text-center">Stok Fisik</th>
                        <th class="p-3.5">Kondisi</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Taksiran Nilai</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($items as $item)
                        @php
                            $barcodeVal = $item->barcode ?? ('899' . str_pad($item->id, 9, '0', STR_PAD_LEFT));
                        @endphp
                        <tr class="group hover:bg-slate-50/95 dark:hover:bg-slate-800/65 transition-all duration-200 ease-out cursor-default">
                            <td class="p-3.5 font-mono font-bold text-rose-600 dark:text-rose-400">
                                <span class="inline-block transition-transform duration-200 ease-out group-hover:translate-x-1">
                                    {{ $item->kode_barang }}
                                </span>
                            </td>

                            <td class="col-barcode hidden p-3.5 text-center">
                                <div class="inline-flex flex-col items-center gap-1 bg-slate-50 dark:bg-slate-800/80 p-1.5 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs group-hover:border-rose-200 dark:group-hover:border-rose-900/40 transition">
                                    <svg class="barcode-svg" data-barcode="{{ $barcodeVal }}" width="84" height="18" viewBox="0 0 84 18"></svg>
                                    <div class="flex items-center gap-1">
                                        <span class="font-mono text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                            {{ $barcodeVal }}
                                        </span>
                                        <button
                                            type="button"
                                            onclick="copyBarcodeText('{{ $barcodeVal }}')"
                                            class="p-0.5 text-slate-400 hover:text-rose-600 transition cursor-pointer"
                                            title="Salin nomor barcode ini"
                                        >
                                            <i data-lucide="copy" class="w-2.5 h-2.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="p-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900 dark:text-white transition-colors duration-200 ease-out group-hover:text-rose-600 dark:group-hover:text-rose-400">
                                        {{ $item->nama_barang }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold transition-all duration-200 ease-out group-hover:shadow-2xs {{ $item->jenis_aset === 'Habis Pakai' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300' }}">
                                        {{ $item->jenis_aset === 'Habis Pakai' ? 'Habis Pakai' : 'Aset Tetap' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 line-clamp-1">{{ $item->deskripsi ?: '-' }}</div>
                            </td>

                            <td class="p-3.5">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td class="p-3.5">{{ $item->lokasi->nama_lokasi ?? '-' }}</td>

                            <td class="p-3.5 text-center">
                                <span class="font-mono font-bold px-2 py-0.5 rounded-lg text-xs transition-all duration-200 ease-out group-hover:scale-105 inline-block {{ ($item->stok ?? 0) <= 0 ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : (($item->stok ?? 0) <= 3 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300') }}">
                                    {{ $item->stok }} {{ $item->satuan ?? 'Unit' }}
                                </span>
                            </td>

                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold transition-all duration-200 {{ $item->kondisi === 'Baik' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300' }}">
                                    {{ $item->kondisi }}
                                </span>
                            </td>

                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all duration-200">
                                    {{ $item->status }}
                                </span>
                            </td>

                            <td class="p-3.5 text-right font-mono font-medium transition-colors duration-200 group-hover:text-slate-900 dark:group-hover:text-white">
                                Rp {{ number_format($item->harga_perkiraan, 0, ',', '.') }}
                            </td>

                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-80 group-hover:opacity-100 transition-opacity duration-200">
                                    <button
                                        type="button"
                                        onclick="showAssetDetail({{ json_encode($item) }})"
                                        class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg cursor-pointer transition font-bold"
                                        title="Lihat Detail Lengkap Aset"
                                    >
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <button
                                        type="button"
                                        onclick="openSingleQrModal({{ json_encode($item) }})"
                                        class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg cursor-pointer transition font-bold"
                                        title="Cetak Stiker Label QR (1 Item)"
                                    >
                                        <i data-lucide="qr-code" class="w-3.5 h-3.5 text-indigo-500"></i>
                                    </button>

                                    <button
                                        type="button"
                                        onclick="openModalEdit({{ json_encode($item) }})"
                                        class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg cursor-pointer transition font-bold"
                                        title="Edit Data Aset"
                                    >
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <form method="POST" action="{{ route('inventaris.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset {{ $item->nama_barang }} ({{ $item->kode_barang }}) dari sistem?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg cursor-pointer transition font-bold"
                                            title="Hapus Aset"
                                        >
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400">
                                <i data-lucide="box" class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-700 mb-2 opacity-60"></i>
                                <p class="font-semibold">Tidak ada data aset yang cocok dengan filter pencarian.</p>
                                <p class="text-[11px] text-slate-500 mt-1">Coba ubah kriteria filter atau klik Reset Filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100 dark:border-slate-800">
            {{ $items->links() }}
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL LENGKAP ASET DENGAN RIWAYAT KRONOLOGIS MUTASI (LOCATION TRANSFERS) -->
<!-- ========================================================================= -->
<div id="modalDetailAsset" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 flex flex-col max-h-[92vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center font-bold shrink-0">
                    <i data-lucide="box" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="detailKodeBarang" class="font-mono font-bold text-xs text-rose-600 dark:text-rose-400"></span>
                        <span id="detailJenisAset" class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800"></span>
                    </div>
                    <h3 id="detailNamaBarang" class="font-extrabold text-base text-slate-900 dark:text-white leading-tight mt-0.5 truncate"></h3>
                </div>
            </div>
            <button onclick="closeAssetDetail()" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Body Content -->
        <div class="flex-1 overflow-y-auto space-y-4 text-xs custom-scrollbar pr-1">
            <!-- Key-Value Grid Identitas -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Kategori Aset</span>
                    <span id="detailKategori" class="font-bold text-slate-800 dark:text-slate-200 text-sm"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Ruangan Lokasi Saat Ini</span>
                    <span id="detailLokasi" class="font-bold text-indigo-600 dark:text-indigo-400 text-sm block mt-0.5"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Sisa Stok Fisik</span>
                    <span id="detailStok" class="font-mono font-black text-sm"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Kondisi Fisik</span>
                    <span id="detailKondisi" class="px-2 py-0.5 rounded text-[10px] font-bold inline-block w-fit mt-0.5"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Status Operasional</span>
                    <span id="detailStatus" class="font-bold text-slate-800 dark:text-slate-200 block mt-0.5"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Taksiran Nilai Satuan</span>
                    <span id="detailHarga" class="font-mono font-bold text-slate-900 dark:text-white block mt-0.5"></span>
                </div>
            </div>

            <!-- Deskripsi -->
            <div id="detailDeskripsiBox" class="p-3.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1">
                <span class="text-[10px] text-slate-400 font-bold uppercase block">Spesifikasi & Keterangan:</span>
                <p id="detailDeskripsi" class="text-slate-700 dark:text-slate-300 leading-relaxed"></p>
            </div>

            <!-- Total Nilai Kapitalisasi Bar -->
            <div class="p-3 bg-rose-50/70 dark:bg-rose-950/30 rounded-2xl border border-rose-200 dark:border-rose-900/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase block">Total Nilai Kapitalisasi Aset</span>
                    <span id="detailSubtotalLabel" class="text-xs text-slate-500"></span>
                </div>
                <div id="detailTotalValuasi" class="font-mono font-black text-sm sm:text-base text-rose-600 dark:text-rose-400"></div>
            </div>

            <!-- SECTION: RIWAYAT KRONOLOGIS MUTASI & PEMINDAHAN LOKASI ASET -->
            <div class="p-4 bg-slate-50 dark:bg-slate-850/60 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            🔄
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Riwayat Mutasi &amp; Pemindahan Lokasi</span>
                                <span id="detailMutasiCountBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">0 Transaksi</span>
                            </h4>
                            <p class="text-[10px] text-slate-500">
                                Rekam jejak kronologis perpindahan ruangan fisik aset sejak terdaftar
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Toggle Urutan Kronologis -->
                    <button 
                        type="button" 
                        onclick="toggleDetailMutasiSort()"
                        class="self-start sm:self-auto px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-indigo-600 text-[11px] font-bold cursor-pointer transition shadow-2xs"
                    >
                        <span id="detailMutasiSortLabel">⇅ Urutan: Terbaru</span>
                    </button>
                </div>

                <!-- Kontainer Daftar Mutasi Kronologis -->
                <div id="detailMutasiListContainer" class="space-y-2.5">
                    <!-- Dynamic rendering via JS -->
                </div>
            </div>
        </div>

        <!-- Footer Modal Detail -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 shrink-0">
            <button type="button" onclick="closeAssetDetail()" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl font-bold cursor-pointer text-xs">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openSingleQrFromDetail()" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 rounded-xl font-bold cursor-pointer text-xs flex items-center gap-1.5 border border-indigo-200 dark:border-indigo-800">
                    <span>🏷️ Cetak Label QR</span>
                </button>
                <button type="button" onclick="openEditFromDetail()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-xs cursor-pointer text-xs">
                    Ubah Data Aset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL CETAK STIKER LABEL QR INDIVIDUAL (1 ITEM) SESUAI UKURAN FISIK -->
<!-- ========================================================================= -->
<div id="modalSingleQr" class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in duration-200">
        <!-- Modal Header -->
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0 font-bold">
                    🏷️
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="sqrKodeBarangBadge" class="font-mono font-bold text-xs px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200"></span>
                        <span class="text-xs font-semibold text-slate-500">Cetak Stiker Label Mandiri (1 Item)</span>
                    </div>
                    <h3 id="sqrNamaBarangHeader" class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white truncate mt-0.5"></h3>
                </div>
            </div>
            <button onclick="closeSingleQrModal()" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-xl cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Modal Body: 2 Kolom (Pengaturan & Pratinjau Stiker) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 custom-scrollbar">
            <!-- Kolom Kiri: Opsi Kustomisasi -->
            <div class="lg:col-span-5 space-y-4 text-xs">
                <!-- Template Stiker -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block">Template Stiker</label>
                    <div class="grid grid-cols-2 gap-2 font-semibold">
                        <button type="button" onclick="setSqrTemplate('standard')" id="btnTmpl_standard" class="p-2.5 rounded-xl border text-left cursor-pointer transition border-indigo-600 bg-indigo-50/70 text-indigo-900 font-bold">
                            Corporate Standard
                            <span class="text-[10px] text-slate-400 block font-normal mt-0.5">Resmi instansi & logo</span>
                        </button>
                        <button type="button" onclick="setSqrTemplate('compact')" id="btnTmpl_compact" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 text-left cursor-pointer transition">
                            Minimalist Compact
                            <span class="text-[10px] text-slate-400 block font-normal mt-0.5">Stiker thermal kecil</span>
                        </button>
                        <button type="button" onclick="setSqrTemplate('security')" id="btnTmpl_security" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 text-left cursor-pointer transition">
                            Security Seal
                            <span class="text-[10px] text-slate-400 block font-normal mt-0.5">Anti-rusak & segel</span>
                        </button>
                        <button type="button" onclick="setSqrTemplate('warehouse')" id="btnTmpl_warehouse" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 text-left cursor-pointer transition">
                            Warehouse Logistik
                            <span class="text-[10px] text-slate-400 block font-normal mt-0.5">Peralatan berat & tag</span>
                        </button>
                    </div>
                </div>

                <!-- Warna Aksen & Ukuran -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">Warna Aksen</label>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="setSqrTheme('red', '#dc2626')" class="w-6 h-6 rounded-full bg-red-600 cursor-pointer"></button>
                            <button type="button" onclick="setSqrTheme('blue', '#2563eb')" class="w-6 h-6 rounded-full bg-blue-600 cursor-pointer"></button>
                            <button type="button" onclick="setSqrTheme('emerald', '#059669')" class="w-6 h-6 rounded-full bg-emerald-600 cursor-pointer"></button>
                            <button type="button" onclick="setSqrTheme('purple', '#7c3aed')" class="w-6 h-6 rounded-full bg-purple-600 cursor-pointer"></button>
                            <button type="button" onclick="setSqrTheme('amber', '#d97706')" class="w-6 h-6 rounded-full bg-amber-600 cursor-pointer"></button>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">Ukuran Label Fisik</label>
                        <select id="sqrLabelSize" onchange="updateSqrPreview()" class="w-full text-xs font-semibold px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                            <option value="standard">Standar (70 x 40 mm)</option>
                            <option value="compact">Kecil / Thermal (50 x 30 mm)</option>
                            <option value="large">Besar / Label Tag (100 x 55 mm)</option>
                        </select>
                    </div>
                </div>

                <!-- Input Header Teks -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Header Nama Instansi / Organisasi</label>
                    <input type="text" id="sqrHeaderText" oninput="updateSqrPreview()" value="{{ $kopConfig->org_name ?? 'KEMENTERIAN / PERUSAHAAN' }}" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium">
                </div>

                <!-- Cakupan Informasi Stiker (Sesuai Ukuran Fisik) -->
                <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Cakupan Informasi Stiker</span>
                        <span id="sqrScopeBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700">Lengkap (Kode + Nama)</span>
                    </div>

                    <!-- 4 Quick Buttons -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        <button type="button" onclick="setSqrScope('all')" id="btnScope_all" class="py-2 px-1 rounded-lg font-bold text-[11px] cursor-pointer flex flex-col items-center bg-indigo-600 text-white shadow-xs">
                            <span>Lengkap</span>
                            <span class="text-[9px] opacity-80 font-normal">Kode + Nama</span>
                        </button>
                        <button type="button" onclick="setSqrScope('code_only')" id="btnScope_code_only" class="py-2 px-1 rounded-lg font-bold text-[11px] cursor-pointer flex flex-col items-center text-slate-600 hover:text-slate-900">
                            <span>Kode Saja</span>
                            <span class="text-[9px] opacity-80 font-normal">QR + Kode</span>
                        </button>
                        <button type="button" onclick="setSqrScope('name_only')" id="btnScope_name_only" class="py-2 px-1 rounded-lg font-bold text-[11px] cursor-pointer flex flex-col items-center text-slate-600 hover:text-slate-900">
                            <span>Nama Saja</span>
                            <span class="text-[9px] opacity-80 font-normal">QR + Nama</span>
                        </button>
                        <button type="button" onclick="setSqrScope('qr_only')" id="btnScope_qr_only" class="py-2 px-1 rounded-lg font-bold text-[11px] cursor-pointer flex flex-col items-center text-slate-600 hover:text-slate-900">
                            <span>Hanya QR</span>
                            <span class="text-[9px] opacity-80 font-normal">Thermal Mini</span>
                        </button>
                    </div>
                </div>

                <!-- Checkbox Atribut & Elemen -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Atribut &amp; Elemen Stiker</span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="chkSqrCode" checked onchange="handleSqrToggle('code')" class="rounded text-indigo-600">
                            <span class="font-bold">Kode Aset</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="chkSqrName" checked onchange="handleSqrToggle('name')" class="rounded text-indigo-600">
                            <span class="font-bold">Nama Barang</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="chkSqrHeader" checked onchange="updateSqrPreview()" class="rounded text-indigo-600">
                            <span>Kop / Header</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="chkSqrLoc" checked onchange="updateSqrPreview()" class="rounded text-indigo-600">
                            <span>Ruangan Lokasi</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="chkSqrCond" checked onchange="updateSqrPreview()" class="rounded text-indigo-600">
                            <span>Kondisi &amp; Stok</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="chkSqrBorders" checked onchange="updateSqrPreview()" class="rounded text-indigo-600">
                            <span>Garis Putus Gunting</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pratinjau Stiker Realistis -->
            <div class="lg:col-span-7 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2 text-xs font-bold text-slate-600 dark:text-slate-400">
                        <span>👁️ Pratinjau Stiker Fisik (Skala Realistis):</span>
                        <span id="sqrLiveSpecInfo" class="font-mono text-[10px] text-slate-400"></span>
                    </div>

                    <!-- Wrapper Pratinjau Kertas -->
                    <div class="bg-slate-100 dark:bg-slate-800/60 p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center min-h-[260px] overflow-x-auto">
                        <!-- ELEMEN STIKER NYATA YANG DICETAK & DIDOWNLOAD -->
                        <div id="single-item-qr-print-sticker" class="qr-sticker-item bg-white text-slate-900 rounded-2xl p-4 shadow-lg flex flex-col justify-between relative overflow-hidden transition-all w-[360px] min-h-[200px] border border-slate-200">
                            <!-- Garis Aksen Atas -->
                            <div id="sqrStickerTopBar" class="absolute top-0 left-0 right-0 h-1.5 z-20 bg-rose-600"></div>

                            <!-- Konten Pratinjau Dinamis -->
                            <div id="sqrStickerBody" class="relative z-10 flex flex-col h-full justify-between gap-3">
                                <!-- Dinamis dirender via JS -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi Status Download -->
                <div id="sqrFeedbackMsg" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    ✅ <span>Label berhasil digenerate & diunduh (PNG)!</span>
                </div>

                <!-- Action Bar -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 no-print">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <button type="button" onclick="printSingleSticker()" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow-md flex items-center justify-center gap-2 cursor-pointer transition active:scale-95">
                            <span>🖨️ Cetak Label Ini (Print 1 Item)</span>
                        </button>
                        <button type="button" onclick="downloadSingleStickerPng()" id="btnDownloadSqr" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-xs shadow-md flex items-center justify-center gap-2 cursor-pointer transition active:scale-95">
                            <span>✨ Generate &amp; Download (PNG)</span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" onclick="downloadRawQrImage()" class="text-xs text-indigo-600 font-bold hover:underline cursor-pointer">
                            ⬇️ Unduh File QR Saja (PNG)
                        </button>
                        <button type="button" onclick="closeSingleQrModal()" class="px-4 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 text-xs font-bold cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT LOGIKA DETAIL, MUTASI KRONOLOGIS, DAN CETAK STIKER 1 ITEM -->
<script>
    let currentDetailItem = null;
    let detailSortAsc = false;
    let currentSingleQrItem = null;
    let sqrTemplate = 'standard';
    let sqrThemeHex = '#dc2626';
    let sqrScope = 'all'; // all, code_only, name_only, qr_only
    let sqrQrDataUrl = '';

    // ==========================================
    // 1. DETAIL ASET & RIWAYAT MUTASI KRONOLOGIS
    // ==========================================
    function showAssetDetail(item) {
        currentDetailItem = item;
        document.getElementById('detailKodeBarang').innerText = item.kode_barang || '-';
        document.getElementById('detailNamaBarang').innerText = item.nama_barang || '-';
        document.getElementById('detailJenisAset').innerText = item.jenis_aset || 'Tidak Habis Pakai';
        document.getElementById('detailKategori').innerText = item.kategori ? item.kategori.nama_kategori : '-';
        document.getElementById('detailLokasi').innerText = item.lokasi ? item.lokasi.nama_lokasi : '-';
        document.getElementById('detailStok').innerText = `${item.stok} Unit`;
        document.getElementById('detailKondisi').innerText = item.kondisi || 'Baik';
        document.getElementById('detailStatus').innerText = item.status || 'Tersedia';
        document.getElementById('detailHarga').innerText = 'Rp ' + Number(item.harga_perkiraan || 0).toLocaleString('id-ID');
        
        const deskripsi = item.deskripsi || '';
        document.getElementById('detailDeskripsi').innerText = deskripsi;
        document.getElementById('detailDeskripsiBox').style.display = deskripsi ? 'block' : 'none';

        const totalVal = Number(item.stok || 0) * Number(item.harga_perkiraan || 0);
        document.getElementById('detailSubtotalLabel').innerText = `(${item.stok} unit × Rp ${Number(item.harga_perkiraan || 0).toLocaleString('id-ID')})`;
        document.getElementById('detailTotalValuasi').innerText = 'Rp ' + totalVal.toLocaleString('id-ID');

        renderDetailMutasiHistory();
        document.getElementById('modalDetailAsset').classList.remove('hidden');
    }

    function closeAssetDetail() {
        document.getElementById('modalDetailAsset').classList.add('hidden');
    }

    function toggleDetailMutasiSort() {
        detailSortAsc = !detailSortAsc;
        document.getElementById('detailMutasiSortLabel').innerText = detailSortAsc ? '⇅ Urutan: Terlama ke Baru' : '⇅ Urutan: Terbaru ke Lama';
        renderDetailMutasiHistory();
    }

    function renderDetailMutasiHistory() {
        if (!currentDetailItem) return;
        const container = document.getElementById('detailMutasiListContainer');
        const badge = document.getElementById('detailMutasiCountBadge');
        
        const mutasis = currentDetailItem.mutasis || [];
        badge.innerText = `${mutasis.length} Transaksi`;

        if (mutasis.length === 0) {
            const locName = currentDetailItem.lokasi ? currentDetailItem.lokasi.nama_lokasi : 'Lokasi Asal';
            container.innerHTML = `
                <div class="py-6 px-4 text-center rounded-xl bg-white dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-400 flex items-center justify-center text-lg">📍</div>
                    <div class="space-y-0.5 max-w-sm">
                        <span class="font-bold text-slate-700 dark:text-slate-300 text-xs block">Belum Ada Riwayat Mutasi Lokasi</span>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Aset ini belum pernah mengalami permohonan pemindahan ruangan. Lokasi penempatan saat ini tetap di <strong>${locName}</strong>.
                        </p>
                    </div>
                </div>
            `;
            return;
        }

        const sorted = [...mutasis].sort((a, b) => {
            const da = new Date(a.tanggal_permohonan || a.created_at || '').getTime() || a.id;
            const db = new Date(b.tanggal_permohonan || b.created_at || '').getTime() || b.id;
            return detailSortAsc ? da - db : db - da;
        });

        let html = '';
        sorted.forEach(m => {
            const noDoc = m.nomor_mutasi || `MTS-${m.id}`;
            const tgl = m.tanggal_permohonan ? new Date(m.tanggal_permohonan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
            const lokAwal = m.lokasi_awal ? m.lokasi_awal.nama_lokasi : `Ruangan #${m.lokasi_awal_id}`;
            const lokBaru = m.lokasi_baru ? m.lokasi_baru.nama_lokasi : `Ruangan #${m.lokasi_baru_id}`;
            const deptBaru = m.departemen_baru ? m.departemen_baru.nama_departemen : null;
            const pemohon = m.pemohon ? m.pemohon.nama : (m.creator ? m.creator.name : 'Staf Pemohon');
            
            let statusBadge = '';
            if (m.status === 'Disetujui') {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">✅ Disetujui</span>';
            } else if (m.status === 'Menunggu Approval') {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">⏳ Menunggu Approval</span>';
            } else {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">❌ Ditolak</span>';
            }

            html += `
                <div class="p-3 rounded-xl border bg-white dark:bg-slate-800/90 border-slate-200 dark:border-slate-700 shadow-2xs space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-1.5 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-[11px] px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200">${noDoc}</span>
                            <span class="text-[10px] text-slate-400">📅 ${tgl}</span>
                        </div>
                        ${statusBadge}
                    </div>

                    <!-- Rute Perpindahan -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px] line-through opacity-75">📍 ${lokAwal}</span>
                        <span class="text-indigo-500 font-bold">➜</span>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[11px] border border-indigo-200">📍 ${lokBaru}</span>
                        ${deptBaru ? `<span class="text-[10px] font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1 ml-auto">🏢 Divisi: <b>${deptBaru}</b></span>` : ''}
                    </div>

                    <!-- Alasan & PIC -->
                    <div class="pt-1 text-[11px] space-y-1">
                        ${m.alasan ? `<div class="text-slate-600 dark:text-slate-400"><strong>Alasan:</strong> ${m.alasan}</div>` : ''}
                        <div class="flex items-center justify-between text-[10px] text-slate-500">
                            <span>👤 Pemohon: <strong>${pemohon}</strong></span>
                            ${m.disetujui_oleh ? `<span class="text-emerald-700 font-medium">Disetujui: ${m.disetujui_oleh}</span>` : ''}
                        </div>
                        ${m.catatan_approval ? `<div class="mt-1 p-2 rounded-lg bg-slate-50 text-[10px] text-slate-600"><em>Catatan: ${m.catatan_approval}</em></div>` : ''}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function openSingleQrFromDetail() {
        closeAssetDetail();
        if (currentDetailItem) {
            openSingleQrModal(currentDetailItem);
        }
    }

    function openEditFromDetail() {
        closeAssetDetail();
        if (currentDetailItem) {
            openModalEdit(currentDetailItem);
        }
    }

    // ==========================================
    // 2. MODAL CETAK STIKER LABEL QR INDIVIDUAL
    // ==========================================
    function openSingleQrModal(item) {
        currentSingleQrItem = item;
        document.getElementById('sqrKodeBarangBadge').innerText = item.kode_barang || '-';
        document.getElementById('sqrNamaBarangHeader').innerText = item.nama_barang || '-';

        // Generate QR code data URL via QRCode library
        QRCode.toDataURL(item.kode_barang, { width: 500, margin: 1.5, errorCorrectionLevel: 'H' }, function (err, url) {
            if (!err) {
                sqrQrDataUrl = url;
                updateSqrPreview();
            }
        });

        document.getElementById('modalSingleQr').classList.remove('hidden');
    }

    function closeSingleQrModal() {
        document.getElementById('modalSingleQr').classList.add('hidden');
    }

    function setSqrTemplate(tmpl) {
        sqrTemplate = tmpl;
        ['standard', 'compact', 'security', 'warehouse'].forEach(t => {
            const btn = document.getElementById('btnTmpl_' + t);
            if (btn) {
                if (t === tmpl) {
                    btn.className = 'p-2.5 rounded-xl border text-left cursor-pointer transition border-indigo-600 bg-indigo-50/70 text-indigo-900 font-bold';
                } else {
                    btn.className = 'p-2.5 rounded-xl border border-slate-200 text-slate-600 text-left cursor-pointer transition';
                }
            }
        });
        updateSqrPreview();
    }

    function setSqrTheme(name, hex) {
        sqrThemeHex = hex;
        document.getElementById('sqrStickerTopBar').style.backgroundColor = hex;
        updateSqrPreview();
    }

    function setSqrScope(scope) {
        sqrScope = scope;
        const chkCode = document.getElementById('chkSqrCode');
        const chkName = document.getElementById('chkSqrName');
        const chkHeader = document.getElementById('chkSqrHeader');
        const chkLoc = document.getElementById('chkSqrLoc');
        const chkCond = document.getElementById('chkSqrCond');

        if (scope === 'all') {
            chkCode.checked = true;
            chkName.checked = true;
            chkHeader.checked = true;
        } else if (scope === 'code_only') {
            chkCode.checked = true;
            chkName.checked = false;
        } else if (scope === 'name_only') {
            chkCode.checked = false;
            chkName.checked = true;
        } else if (scope === 'qr_only') {
            chkCode.checked = false;
            chkName.checked = false;
            chkHeader.checked = false;
            chkLoc.checked = false;
            chkCond.checked = false;
        }

        updateScopeUI();
        updateSqrPreview();
    }

    function handleSqrToggle(type) {
        const hasCode = document.getElementById('chkSqrCode').checked;
        const hasName = document.getElementById('chkSqrName').checked;

        if (hasCode && hasName) {
            sqrScope = 'all';
        } else if (hasCode && !hasName) {
            sqrScope = 'code_only';
        } else if (!hasCode && hasName) {
            sqrScope = 'name_only';
        } else {
            sqrScope = 'qr_only';
        }

        updateScopeUI();
        updateSqrPreview();
    }

    function updateScopeUI() {
        ['all', 'code_only', 'name_only', 'qr_only'].forEach(s => {
            const btn = document.getElementById('btnScope_' + s);
            if (btn) {
                if (s === sqrScope) {
                    btn.className = 'py-2 px-1 rounded-lg font-bold text-[11px] cursor-pointer flex flex-col items-center bg-indigo-600 text-white shadow-xs';
                } else {
                    btn.className = 'py-2 px-1 rounded-lg font-bold text-[11px] cursor-pointer flex flex-col items-center text-slate-600 hover:text-slate-900';
                }
            }
        });

        const badge = document.getElementById('sqrScopeBadge');
        if (sqrScope === 'qr_only') {
            badge.innerText = 'Hanya QR Code';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-700';
        } else if (sqrScope === 'code_only') {
            badge.innerText = 'Kode Saja';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700';
        } else if (sqrScope === 'name_only') {
            badge.innerText = 'Nama Saja';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700';
        } else {
            badge.innerText = 'Lengkap (Kode + Nama)';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700';
        }
    }

    function updateSqrPreview() {
        if (!currentSingleQrItem) return;
        const bodyEl = document.getElementById('sqrStickerBody');
        const headerText = document.getElementById('sqrHeaderText').value || 'INVENTARIS KANTOR';
        const hasCode = document.getElementById('chkSqrCode').checked;
        const hasName = document.getElementById('chkSqrName').checked;
        const hasHeader = document.getElementById('chkSqrHeader').checked;
        const hasLoc = document.getElementById('chkSqrLoc').checked;
        const hasCond = document.getElementById('chkSqrCond').checked;
        const hasBorders = document.getElementById('chkSqrBorders').checked;
        const stickerContainer = document.getElementById('single-item-qr-print-sticker');

        stickerContainer.className = `qr-sticker-item bg-white text-slate-900 rounded-2xl p-4 shadow-lg flex flex-col justify-between relative overflow-hidden transition-all w-[360px] min-h-[200px] ${hasBorders ? 'border-2 border-dashed border-slate-300' : 'border border-slate-200'}`;

        const isQrOnly = !hasCode && !hasName;
        const locName = currentSingleQrItem.lokasi ? currentSingleQrItem.lokasi.nama_lokasi : '-';
        const katName = currentSingleQrItem.kategori ? currentSingleQrItem.kategori.nama_kategori : '-';

        document.getElementById('sqrLiveSpecInfo').innerText = `Template: ${sqrTemplate.toUpperCase()} • ${sqrScope}`;

        if (isQrOnly) {
            bodyEl.innerHTML = `
                <div class="relative z-10 flex flex-col items-center justify-center my-auto py-2 h-full text-center">
                    <div class="w-28 h-28 bg-white p-1 rounded-2xl flex items-center justify-center border border-slate-200 shadow-2xs">
                        <img src="${sqrQrDataUrl}" alt="QR" class="w-full h-full object-contain" />
                    </div>
                    <span class="text-[10px] font-mono font-bold text-slate-400 mt-2 uppercase tracking-wide">Hanya QR Code • Thermal Mini</span>
                </div>
            `;
            return;
        }

        if (sqrTemplate === 'compact') {
            bodyEl.innerHTML = `
                <div class="relative z-10 flex items-center gap-3 h-full">
                    <div class="w-20 h-20 bg-white p-1 rounded-xl flex items-center justify-center shrink-0 border border-slate-200">
                        <img src="${sqrQrDataUrl}" alt="QR" class="w-full h-full object-contain" />
                    </div>
                    <div class="min-w-0 flex-1 space-y-0.5">
                        ${hasHeader ? `<div class="text-[8px] font-black uppercase text-slate-500 truncate">${headerText}</div>` : ''}
                        ${hasCode ? `<div class="font-mono font-black text-xs text-slate-900">${currentSingleQrItem.kode_barang}</div>` : ''}
                        ${hasName ? `<div class="text-[11px] font-bold text-slate-900 truncate">${currentSingleQrItem.nama_barang}</div>` : ''}
                        ${hasLoc ? `<div class="text-[9px] text-slate-500 truncate">📍 ${locName}</div>` : ''}
                        ${hasCond ? `<div class="text-[8px] font-mono text-slate-400">${currentSingleQrItem.kondisi} • ${currentSingleQrItem.stok} UNIT</div>` : ''}
                    </div>
                </div>
            `;
        } else if (sqrTemplate === 'security') {
            bodyEl.innerHTML = `
                <div class="relative z-10 flex flex-col h-full justify-between gap-2">
                    <div class="bg-amber-400 text-slate-950 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest text-center">
                        SECURITY SEAL • DO NOT REMOVE • RESMI
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 bg-white p-1 rounded-xl flex items-center justify-center shrink-0 border-2 border-slate-800">
                            <img src="${sqrQrDataUrl}" alt="QR" class="w-full h-full object-contain" />
                        </div>
                        <div class="min-w-0 flex-1">
                            ${hasHeader ? `<span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded text-white inline-block" style="background-color: ${sqrThemeHex}">${headerText}</span>` : ''}
                            ${hasCode ? `<div class="font-mono font-black text-sm text-slate-950 mt-1">${currentSingleQrItem.kode_barang}</div>` : ''}
                            ${hasName ? `<div class="text-xs font-bold text-slate-800 truncate">${currentSingleQrItem.nama_barang}</div>` : ''}
                            ${hasLoc ? `<div class="text-[9px] text-slate-500 font-mono mt-0.5">LOK: ${locName}</div>` : ''}
                        </div>
                    </div>
                    <div class="text-[8px] text-slate-400 font-mono text-center border-t border-slate-200 pt-1">
                        PROPERTI RESMI KANTOR - DILARANG MEMINDAHKAN TANPA IZIN
                    </div>
                </div>
            `;
        } else if (sqrTemplate === 'warehouse') {
            bodyEl.innerHTML = `
                <div class="relative z-10 flex flex-col h-full justify-between gap-2.5">
                    <div class="border-b-2 pb-1 flex items-center justify-between" style="border-color: ${sqrThemeHex}">
                        <div>
                            <div class="text-[8px] font-black uppercase text-slate-500">${headerText}</div>
                            ${hasCode ? `<div class="font-mono font-black text-base text-slate-900 leading-tight">${currentSingleQrItem.kode_barang}</div>` : ''}
                        </div>
                        <span class="px-2 py-0.5 text-white rounded font-mono font-black text-xs" style="background-color: ${sqrThemeHex}">${currentSingleQrItem.stok} UNIT</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 bg-white p-1 rounded-xl flex items-center justify-center shrink-0 border-2 border-slate-300">
                            <img src="${sqrQrDataUrl}" alt="QR" class="w-full h-full object-contain" />
                        </div>
                        <div class="min-w-0 flex-1 space-y-0.5">
                            ${hasName ? `<h5 class="text-xs font-black text-slate-900 line-clamp-2">${currentSingleQrItem.nama_barang}</h5>` : ''}
                            ${hasLoc ? `<div class="text-[10px] text-slate-600 font-semibold">Ruang: <b>${locName}</b></div>` : ''}
                            <div class="text-[9px] text-slate-600">${katName} • Status: ${currentSingleQrItem.status} (${currentSingleQrItem.kondisi})</div>
                        </div>
                    </div>
                    <div class="bg-slate-100 p-1 rounded text-[8px] text-slate-600 font-mono flex items-center justify-between">
                        <span>KLASIFIKASI: ${currentSingleQrItem.jenis_aset || 'Aset Tetap'}</span>
                        <span>SCAN UNTUK DETAIL INVENTARIS</span>
                    </div>
                </div>
            `;
        } else {
            // Standard Corporate Template
            bodyEl.innerHTML = `
                <div class="relative z-10 flex flex-col h-full justify-between gap-3">
                    ${hasHeader ? `
                        <div class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider flex items-center justify-between text-white" style="background-color: ${sqrThemeHex}">
                            <span>${headerText}</span>
                            <span class="font-mono text-[9px] opacity-90">TAG RESMI</span>
                        </div>
                    ` : ''}

                    <div class="flex items-center gap-3">
                        <div class="w-20 h-20 bg-white p-1 rounded-xl flex items-center justify-center shrink-0 border border-slate-200 shadow-2xs">
                            <img src="${sqrQrDataUrl}" alt="QR" class="w-full h-full object-contain" />
                        </div>
                        <div class="min-w-0 flex-1 space-y-0.5">
                            ${hasCode ? `<div class="font-mono font-black text-sm text-slate-900 tracking-tight">${currentSingleQrItem.kode_barang}</div>` : ''}
                            ${hasName ? `<div class="text-xs font-extrabold text-slate-900 line-clamp-2 leading-tight">${currentSingleQrItem.nama_barang}</div>` : ''}
                            ${hasLoc ? `<div class="text-[10px] text-slate-600 truncate mt-0.5">Ruang: <b>${locName}</b></div>` : ''}
                            <div class="text-[9px] text-slate-500 font-medium">Kategori: ${katName} • ${currentSingleQrItem.jenis_aset || 'Aset Tetap'}</div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[9px] text-slate-500 font-mono">
                        ${hasCond ? `<span>KONDISI: <b>${currentSingleQrItem.kondisi}</b></span><span>STOK: <b>${currentSingleQrItem.stok} UNIT</b></span>` : ''}
                        <span class="text-[8px] text-slate-400">SCAN UNTUK DETAIL</span>
                    </div>
                </div>
            `;
        }
    }

    function printSingleSticker() {
        document.body.classList.add('printing-single-item');
        window.print();
        setTimeout(() => {
            document.body.classList.remove('printing-single-item');
        }, 1200);
    }

    async function downloadSingleStickerPng() {
        if (!currentSingleQrItem) return;
        const el = document.getElementById('single-item-qr-print-sticker');
        const btn = document.getElementById('btnDownloadSqr');
        btn.disabled = true;
        btn.innerHTML = '<span>⏳ Memproses...</span>';

        try {
            const h2c = window.html2canvasPro || window.html2canvas;
            if (!h2c) throw new Error("html2canvas library not loaded");
            const canvas = await h2c(el, {
                scale: 3,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false
            });
            const imgUrl = canvas.toDataURL('image/png', 1.0);
            const link = document.createElement('a');
            const cleanName = currentSingleQrItem.nama_barang.replace(/[^a-zA-Z0-9]/g, '_');
            link.href = imgUrl;
            link.download = `Label_Stiker_${currentSingleQrItem.kode_barang}_${cleanName}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            const msg = document.getElementById('sqrFeedbackMsg');
            msg.classList.remove('hidden');
            setTimeout(() => msg.classList.add('hidden'), 4000);
        } catch (e) {
            console.error('Error html2canvas-pro:', e);
            downloadRawQrImage();
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span>✨ Generate &amp; Download (PNG)</span>';
        }
    }

    function downloadRawQrImage() {
        if (!sqrQrDataUrl || !currentSingleQrItem) return;
        const link = document.createElement('a');
        const cleanName = currentSingleQrItem.nama_barang.replace(/[^a-zA-Z0-9]/g, '_');
        link.href = sqrQrDataUrl;
        link.download = `QR_Code_${currentSingleQrItem.kode_barang}_${cleanName}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

<!-- MODAL TAMBAH INVENTARIS -->
<div id="modalTambahInventaris" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-[92vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-rose-600"></i>
                Tambah Aset Inventaris Baru
            </h3>
            <button type="button" onclick="closeModalTambah()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('inventaris.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Barang / Aset *</label>
                    <input type="text" name="kode_barang" required placeholder="Contoh: AST-2026-001" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none uppercase" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Barang / Aset *</label>
                    <input type="text" name="nama_barang" required placeholder="Contoh: Laptop ThinkPad T14 Gen 4" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kategori *</label>
                    <select name="kategori_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ruangan / Lokasi *</label>
                    <select name="lokasi_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($lokasis as $l)
                            <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenis Aset *</label>
                    <select name="jenis_aset" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Tidak Habis Pakai">Tidak Habis Pakai (Aset Tetap)</option>
                        <option value="Habis Pakai">Habis Pakai</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kuantitas Stok *</label>
                    <input type="number" name="stok" required min="0" value="1" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Taksiran Harga (Rp) *</label>
                    <input type="number" name="harga_perkiraan" required min="0" placeholder="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kondisi Fisik *</label>
                    <select name="kondisi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status Ketersediaan *</label>
                    <select name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Dalam Maintenance">Dalam Maintenance</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor Barcode Fisik (UPC / Code-128)</label>
                <div class="relative">
                    <input type="text" name="barcode" placeholder="Scan barcode fisik atau biarkan kosong untuk generate otomatis..." class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                    <i data-lucide="scan-barcode" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Spesifikasi & Keterangan</label>
                <textarea name="deskripsi" rows="2" placeholder="Spesifikasi teknis, nomor seri pabrikan, atau catatan aset..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambah()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20 cursor-pointer">Simpan Aset</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT INVENTARIS -->
<div id="modalEditInventaris" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-[92vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-amber-500"></i>
                Ubah Data Aset Inventaris
            </h3>
            <button type="button" onclick="closeModalEdit()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditInventaris" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Barang / Aset *</label>
                    <input type="text" id="edit_kode_barang" name="kode_barang" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none uppercase" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Barang / Aset *</label>
                    <input type="text" id="edit_nama_barang" name="nama_barang" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kategori *</label>
                    <select id="edit_kategori_id" name="kategori_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ruangan / Lokasi *</label>
                    <select id="edit_lokasi_id" name="lokasi_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        @foreach($lokasis as $l)
                            <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jenis Aset *</label>
                    <select id="edit_jenis_aset" name="jenis_aset" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Tidak Habis Pakai">Tidak Habis Pakai (Aset Tetap)</option>
                        <option value="Habis Pakai">Habis Pakai</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kuantitas Stok *</label>
                    <input type="number" id="edit_stok" name="stok" required min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Taksiran Harga (Rp) *</label>
                    <input type="number" id="edit_harga_perkiraan" name="harga_perkiraan" required min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kondisi Fisik *</label>
                    <select id="edit_kondisi" name="kondisi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status Ketersediaan *</label>
                    <select id="edit_status" name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Dalam Maintenance">Dalam Maintenance</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor Barcode Fisik (UPC / Code-128)</label>
                <div class="relative">
                    <input type="text" id="edit_barcode" name="barcode" placeholder="Nomor barcode..." class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                    <i data-lucide="scan-barcode" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Spesifikasi & Keterangan</label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalEdit()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold shadow-md shadow-amber-600/20 cursor-pointer">Perbarui Data Aset</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PEMINDAI BARCODE USB & PENCARIAN MASSAL (1:1 IDENTIK DENGAN REACT) -->
<div id="modalBulkScanner" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col max-h-[92vh]">
        <!-- HEADER -->
        <div class="p-4 sm:p-5 bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20 shadow-inner">
                    <i data-lucide="scan-barcode" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base sm:text-lg font-black tracking-tight">Pemindai Barcode USB & Pencarian Massal</h3>
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white">
                            <i data-lucide="zap" class="w-3 h-3 text-amber-300"></i>
                            USB HID Emulation
                        </span>
                    </div>
                    <p class="text-xs text-rose-100 mt-0.5">Mendukung alat scan genggam (USB/Bluetooth) & multi-barcode filtering</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" onclick="toggleScannerMute()" id="btnMuteScanner" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition" title="Suara Beep Scanner">
                    <i data-lucide="volume-2" id="iconMuteScanner" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="closeBulkScannerModal()" class="p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/20 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
            <!-- STATUS HARDWARE -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Status: Siap Menerima Input Scanner USB</span>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Arahkan barcode scanner fisik ke label, kode akan otomatis masuk antrean.</p>
                    </div>
                </div>
                <button type="button" onclick="toggleBulkTextMode()" id="btnToggleBulkText" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline cursor-pointer">
                    Mode Tempel Teks (Batch)
                </button>
            </div>

            <!-- FORM SINGLE / SCANNER INPUT -->
            <div id="scannerSingleInputWrapper" class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Kolom Tangkap Scanner (Fokus Otomatis)</label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="scan-barcode" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input
                            type="text"
                            id="scannerCaptureInput"
                            placeholder="Scan barcode fisik atau ketik kode barang lalu tekan Enter..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-rose-300 dark:border-rose-900/60 bg-rose-50/30 dark:bg-rose-950/20 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500"
                        />
                    </div>
                    <button type="button" onclick="addManualScanCode()" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition cursor-pointer shadow-xs">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah</span>
                    </button>
                </div>
            </div>

            <!-- FORM BATCH TEXTAREA -->
            <div id="scannerBatchTextWrapper" class="space-y-2 hidden">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tempel Banyak Barcode Sekaligus (Baris baru / koma / spasi)</label>
                <textarea id="scannerBatchTextarea" rows="4" placeholder="AST-2026-001&#10;AST-2026-002&#10;899100100001" class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="toggleBulkTextMode()" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300">Batal</button>
                    <button type="button" onclick="extractBatchBarcodes()" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition">Ekstrak & Tambah</button>
                </div>
            </div>

            <!-- SIMULASI SCANNER CHIPS -->
            <div class="p-3 bg-amber-50/60 dark:bg-amber-950/30 rounded-2xl border border-amber-200/80 dark:border-amber-900/40 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-amber-800 dark:text-amber-300 flex items-center gap-1.5 text-[11px]">
                        <i data-lucide="keyboard" class="w-3.5 h-3.5"></i>
                        Uji Coba Cepat (Simulasi Perangkat Pemindai USB):
                    </span>
                    <span class="text-[10px] text-amber-700/80 dark:text-amber-400">Klik untuk simulasikan scan</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($items->take(5) as $sim)
                        <button
                            type="button"
                            onclick="simulateUsbScan('{{ $sim->barcode ?? $sim->kode_barang }}')"
                            class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-amber-100 text-slate-700 dark:text-slate-200 border border-amber-200 dark:border-amber-800/60 rounded-lg font-mono text-[11px] font-bold transition flex items-center gap-1 shadow-2xs"
                        >
                            <i data-lucide="zap" class="w-3 h-3 text-amber-500"></i>
                            <span>{{ $sim->kode_barang }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- DAFTAR BARCODE DALAM ANTREAN -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700 dark:text-slate-300">
                        Daftar Barcode Terpindai (<span id="countModalQueue">0</span>)
                    </span>
                    <button type="button" onclick="clearModalQueue()" class="text-[11px] text-rose-600 hover:text-rose-700 font-bold hover:underline cursor-pointer flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                        <span>Kosongkan Antrean</span>
                    </button>
                </div>

                <div id="modalQueueContainer" class="max-h-52 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 p-2 text-xs">
                    <p class="text-center text-slate-400 py-6">Belum ada barcode dalam antrean. Scan atau simulasikan di atas.</p>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0">
            <div class="text-xs text-slate-500">
                Total terpilih: <b id="footerModalQueueCount" class="text-slate-900 dark:text-white font-mono">0</b> barcode
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeBulkScannerModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-700 transition cursor-pointer">Tutup</button>
                <button type="button" onclick="applyBulkFilterToTable()" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 text-white shadow-md shadow-rose-500/20 transition cursor-pointer flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Terapkan ke Tabel Inventaris</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FLOATING HUD TOAST FOR USB SCANNER -->
<div id="floatingScannerHud" class="fixed bottom-6 right-6 z-[9999] bg-slate-900/95 dark:bg-slate-950/95 text-white border-2 border-rose-500 rounded-2xl p-4 shadow-2xl items-center gap-3.5 max-w-sm hidden backdrop-blur-md animate-in slide-in-from-bottom-5">
    <div class="w-10 h-10 rounded-xl bg-rose-600/30 border border-rose-500/50 flex items-center justify-center text-rose-400 shrink-0">
        <i data-lucide="zap" class="w-5 h-5 text-amber-300 animate-pulse"></i>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-1">
            <span class="text-[10px] uppercase font-bold tracking-wider text-rose-400 flex items-center gap-1">
                <i data-lucide="scan-barcode" class="w-3 h-3"></i>
                USB Scanner Terdeteksi
            </span>
        </div>
        <p id="hudScannedCode" class="font-mono text-xs font-black truncate text-white mt-0.5">-</p>
        <p id="hudScannedName" class="text-[11px] text-slate-300 truncate">Kode barcode diterima sistem</p>
    </div>
    <button type="button" onclick="document.getElementById('floatingScannerHud').classList.add('hidden')" class="p-1 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white transition">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>

<script>
    // State management for Barcode Column, View Mode, and Bulk Scanner
    let showBarcodeColumn = localStorage.getItem('inv_show_barcode_col') === 'true';
    let invViewMode = localStorage.getItem('inv_view_mode') || 'table';
    let bulkQueue = [];
    let scannerSoundEnabled = true;

    function setInvViewMode(mode) {
        invViewMode = mode;
        localStorage.setItem('inv_view_mode', mode);
        applyViewMode();
    }

    function applyViewMode() {
        const tableContainer = document.getElementById('containerTableView');
        const cardContainer = document.getElementById('containerCardView');
        const btnTable = document.getElementById('btnViewModeTable');
        const btnCards = document.getElementById('btnViewModeCards');

        if (invViewMode === 'cards') {
            if (cardContainer) cardContainer.classList.remove('hidden');
            if (tableContainer) tableContainer.classList.add('hidden');
            if (btnCards) {
                btnCards.className = 'flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs';
            }
            if (btnTable) {
                btnTable.className = 'flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer text-slate-500 hover:text-slate-900 dark:hover:text-white';
            }
        } else {
            if (tableContainer) tableContainer.classList.remove('hidden');
            if (cardContainer) cardContainer.classList.add('hidden');
            if (btnTable) {
                btnTable.className = 'flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer bg-white dark:bg-slate-700 text-rose-600 dark:text-rose-400 shadow-xs';
            }
            if (btnCards) {
                btnCards.className = 'flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold transition cursor-pointer text-slate-500 hover:text-slate-900 dark:hover:text-white';
            }
        }
        renderBarcodeSvgs();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function setFilterStokLevel(val) {
        const input = document.getElementById('inputStokLevel');
        if (input) {
            input.value = val;
            document.getElementById('filterFormInv').submit();
        }
    }

    // Initialize Barcode SVG Stripes
    function renderBarcodeSvgs() {
        document.querySelectorAll('.barcode-svg').forEach(svg => {
            const code = (svg.getAttribute('data-barcode') || '000000').trim();
            svg.innerHTML = '';
            let modules = [1, 0, 1];
            for (let i = 0; i < code.length; i++) {
                const c = code.charCodeAt(i);
                modules.push((c % 3) + 1, 0, ((c >> 1) % 2) + 1, 0);
            }
            modules.push(1, 0, 1, 1);
            const total = modules.length;
            const w = 80;
            const mWidth = w / total;
            let currentX = 0;
            modules.forEach(isBlack => {
                if (isBlack) {
                    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    rect.setAttribute('x', currentX);
                    rect.setAttribute('y', 0);
                    rect.setAttribute('width', Math.max(1, mWidth));
                    rect.setAttribute('height', 16);
                    rect.setAttribute('fill', 'currentColor');
                    rect.setAttribute('class', 'text-slate-800 dark:text-slate-200');
                    svg.appendChild(rect);
                }
                currentX += mWidth;
            });
        });
    }

    // Toggle Hidden Barcode Column in Table
    function toggleBarcodeColumn() {
        showBarcodeColumn = !showBarcodeColumn;
        localStorage.setItem('inv_show_barcode_col', showBarcodeColumn ? 'true' : 'false');
        applyBarcodeColumnVisibility();
    }

    function applyBarcodeColumnVisibility() {
        const cols = document.querySelectorAll('.col-barcode');
        const lbl = document.getElementById('lblBarcodeColStatus');
        const icon = document.getElementById('iconBarcodeCol');
        const btn = document.getElementById('btnToggleBarcodeCol');

        if (showBarcodeColumn) {
            cols.forEach(el => el.classList.remove('hidden'));
            if (lbl) {
                lbl.innerText = 'Tampil';
                lbl.className = 'px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-rose-600 text-white';
            }
            if (btn) btn.className = 'flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-bold transition cursor-pointer border bg-rose-50 dark:bg-rose-950/50 border-rose-300 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 shadow-2xs';
        } else {
            cols.forEach(el => el.classList.add('hidden'));
            if (lbl) {
                lbl.innerText = 'Tersembunyi';
                lbl.className = 'px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-500';
            }
            if (btn) btn.className = 'flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-bold transition cursor-pointer border bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Synthetic Scanner Beep
    function playScannerBeep() {
        if (!scannerSoundEnabled) return;
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1760, ctx.currentTime);
            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        } catch(e) {}
    }

    function toggleScannerMute() {
        scannerSoundEnabled = !scannerSoundEnabled;
        const btn = document.getElementById('btnMuteScanner');
        if (btn) {
            btn.innerHTML = scannerSoundEnabled ? '<i data-lucide="volume-2" class="w-4 h-4"></i>' : '<i data-lucide="volume-x" class="w-4 h-4"></i>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    // Modal Bulk Scanner Functions
    function openBulkScannerModal() {
        document.getElementById('modalBulkScanner').classList.remove('hidden');
        renderModalQueue();
        setTimeout(() => {
            document.getElementById('scannerCaptureInput')?.focus();
        }, 150);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeBulkScannerModal() {
        document.getElementById('modalBulkScanner').classList.add('hidden');
    }

    function toggleBulkTextMode() {
        const singleW = document.getElementById('scannerSingleInputWrapper');
        const batchW = document.getElementById('scannerBatchTextWrapper');
        const btn = document.getElementById('btnToggleBulkText');
        if (batchW.classList.contains('hidden')) {
            batchW.classList.remove('hidden');
            singleW.classList.add('hidden');
            btn.innerText = 'Mode Scanner Otomatis';
        } else {
            batchW.classList.add('hidden');
            singleW.classList.remove('hidden');
            btn.innerText = 'Mode Tempel Teks (Batch)';
            document.getElementById('scannerCaptureInput')?.focus();
        }
    }

    function addManualScanCode() {
        const input = document.getElementById('scannerCaptureInput');
        const val = input.value.trim();
        if (val) {
            addCodeToQueue(val);
            input.value = '';
            input.focus();
        }
    }

    function extractBatchBarcodes() {
        const ta = document.getElementById('scannerBatchTextarea');
        const raw = ta.value;
        const tokens = raw.split(/[\r\n,\t ]+/).map(t => t.trim()).filter(t => t.length > 0);
        tokens.forEach(t => addCodeToQueue(t));
        ta.value = '';
        toggleBulkTextMode();
    }

    function addCodeToQueue(code) {
        if (!bulkQueue.includes(code)) {
            bulkQueue.unshift(code);
            playScannerBeep();
            renderModalQueue();
            showFloatingHud(code);
        }
    }

    function removeQueueItem(index) {
        bulkQueue.splice(index, 1);
        renderModalQueue();
    }

    function clearModalQueue() {
        bulkQueue = [];
        renderModalQueue();
    }

    function renderModalQueue() {
        const container = document.getElementById('modalQueueContainer');
        const countLbl = document.getElementById('countModalQueue');
        const footerCount = document.getElementById('footerModalQueueCount');
        const badgeTop = document.getElementById('badgeBulkScannerCount');

        if (countLbl) countLbl.innerText = bulkQueue.length;
        if (footerCount) footerCount.innerText = bulkQueue.length;
        if (badgeTop) {
            badgeTop.innerText = bulkQueue.length;
            if (bulkQueue.length > 0) badgeTop.classList.remove('hidden');
            else badgeTop.classList.add('hidden');
        }

        if (bulkQueue.length === 0) {
            container.innerHTML = '<p class="text-center text-slate-400 py-6">Belum ada barcode dalam antrean. Scan atau simulasikan di atas.</p>';
            return;
        }

        let html = '';
        bulkQueue.forEach((c, idx) => {
            html += `
                <div class="p-2.5 flex items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 flex items-center justify-center shrink-0">${idx + 1}</span>
                        <span class="font-mono font-bold text-rose-600 dark:text-rose-400 truncate">${c}</span>
                    </div>
                    <button type="button" onclick="removeQueueItem(${idx})" class="text-slate-400 hover:text-rose-600 p-1">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            `;
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function simulateUsbScan(code) {
        addCodeToQueue(code);
    }

    function applyBulkFilterToTable() {
        if (bulkQueue.length === 0) {
            window.location.href = "{{ route('inventaris.index') }}";
            return;
        }
        const url = new URL("{{ route('inventaris.index') }}", window.location.origin);
        url.searchParams.set('bulk_search', bulkQueue.join(','));
        window.location.href = url.toString();
    }

    function removeBulkCode(code) {
        const url = new URL(window.location.href);
        const current = url.searchParams.get('bulk_search') || '';
        const list = current.split(',').map(s => s.trim()).filter(s => s !== code && s.length > 0);
        if (list.length > 0) {
            url.searchParams.set('bulk_search', list.join(','));
        } else {
            url.searchParams.delete('bulk_search');
        }
        window.location.href = url.toString();
    }

    function copyBarcodeText(text) {
        navigator.clipboard.writeText(text);
        showFloatingHud(text, 'Barcode berhasil disalin ke clipboard');
    }

    function showFloatingHud(code, subtitle) {
        const hud = document.getElementById('floatingScannerHud');
        if (!hud) return;
        document.getElementById('hudScannedCode').innerText = code;
        if (subtitle) document.getElementById('hudScannedName').innerText = subtitle;
        hud.classList.remove('hidden');
        hud.classList.add('flex');
        clearTimeout(window.__hudTimer);
        window.__hudTimer = setTimeout(() => {
            hud.classList.add('hidden');
            hud.classList.remove('flex');
        }, 3500);
    }

    // Global USB Scanner Keyboard Listener (HID Emulation)
    (function initUsbScannerKeyboardListener() {
        let buffer = '';
        let lastTime = Date.now();

        window.addEventListener('keydown', function(e) {
            const isModalOpen = !document.getElementById('modalBulkScanner')?.classList.contains('hidden');
            const target = e.target;
            const isInput = target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA');

            const now = Date.now();
            const delta = now - lastTime;
            lastTime = now;

            if (e.key === 'Enter') {
                const code = buffer.trim();
                const isFast = buffer.length >= 3 && delta < 85;

                if (code && (isFast || (!isInput && buffer.length >= 2))) {
                    addCodeToQueue(code);
                    if (isModalOpen) {
                        const cap = document.getElementById('scannerCaptureInput');
                        if (cap) cap.value = '';
                    } else {
                        showFloatingHud(code, 'Barcode diterima via USB scanner');
                    }
                    if (!isInput) e.preventDefault();
                }
                buffer = '';
                return;
            }

            if (e.key && e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
                if (delta > 160 && isInput) {
                    buffer = e.key;
                } else {
                    buffer += e.key;
                }
            }
        });
    })();

    // Listen to Enter on modal input
    document.getElementById('scannerCaptureInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addManualScanCode();
        }
    });

    // Populate active bulk_search from query string on page load
    (function syncQueryBulk() {
        const urlParams = new URLSearchParams(window.location.search);
        const b = urlParams.get('bulk_search');
        if (b) {
            bulkQueue = b.split(',').map(s => s.trim()).filter(s => s.length > 0);
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        applyViewMode();
        renderBarcodeSvgs();
        applyBarcodeColumnVisibility();
    });

    function openModalTambah() {
        document.getElementById('modalTambahInventaris').classList.remove('hidden');
    }
    function closeModalTambah() {
        document.getElementById('modalTambahInventaris').classList.add('hidden');
    }

    function openModalEdit(item) {
        const form = document.getElementById('formEditInventaris');
        form.action = '/inventaris/' + item.id;
        document.getElementById('edit_kode_barang').value = item.kode_barang || '';
        document.getElementById('edit_nama_barang').value = item.nama_barang || '';
        document.getElementById('edit_kategori_id').value = item.kategori_id || '';
        document.getElementById('edit_lokasi_id').value = item.lokasi_id || '';
        document.getElementById('edit_jenis_aset').value = item.jenis_aset || 'Tidak Habis Pakai';
        document.getElementById('edit_stok').value = item.stok !== undefined ? item.stok : 1;
        document.getElementById('edit_harga_perkiraan').value = item.harga_perkiraan || 0;
        document.getElementById('edit_kondisi').value = item.kondisi || 'Baik';
        document.getElementById('edit_status').value = item.status || 'Tersedia';
        document.getElementById('edit_deskripsi').value = item.deskripsi || '';
        document.getElementById('edit_barcode').value = item.barcode || '';
        document.getElementById('modalEditInventaris').classList.remove('hidden');
    }
    function closeModalEdit() {
        document.getElementById('modalEditInventaris').classList.add('hidden');
    }
</script>
@endsection
