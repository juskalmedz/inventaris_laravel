@extends('layouts.app')

@section('page_title', 'Katalog Inventaris & Manajemen Stok Fisik')

@section('content')
<div class="space-y-5">

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
                    Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}
                </h4>
                <span class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold truncate block">
                    Valuasi Aset Terdaftar
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
                    {{ ($stokKritisCount ?? 0) > 0 ? ($stokKritisCount . ' Stok Kritis') : 'Stok Aman' }} &bull; {{ $stokHabisCount ?? 0 }} Habis
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
        <!-- ROW 1: SEARCH & ACTION BUTTONS -->
        <form method="GET" action="{{ route('inventaris.index') }}" id="filterFormInv" class="space-y-3">
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
                        <a href="{{ route('inventaris.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                        title="Cetak katalog inventaris"
                    >
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak</span>
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

            <!-- ROW 2: ADVANCED SELECTS -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
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
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Dalam Maintenance" {{ request('status') == 'Dalam Maintenance' ? 'selected' : '' }}>Dalam Maintenance</option>
                        <option value="Habis" {{ request('status') == 'Habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- ROW 3: FOOTER INFO & RESET -->
        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <span class="text-[11px] font-semibold text-slate-500">
                Menampilkan <strong class="text-slate-900 dark:text-white font-mono">{{ $items->total() ?? count($items) }}</strong> aset terdaftar
            </span>

            @if(request('search') || request('kategori_id') || request('lokasi_id') || request('kondisi') || request('status'))
                <a href="{{ route('inventaris.index') }}" class="flex items-center gap-1 text-[11px] text-rose-600 hover:text-rose-700 font-bold hover:underline">
                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                    <span>Reset Semua Filter</span>
                </a>
            @endif
        </div>
    </div>

    <!-- TABEL DATA INVENTARIS -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3.5">Kode & Nama Aset</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Lokasi Fisik</th>
                        <th class="p-3.5 text-center">Stok</th>
                        <th class="p-3.5">Kondisi</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Harga Satuan</th>
                        <th class="p-3.5 text-center">Aksi & Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="p-3.5">
                                <span class="font-mono text-[10px] font-bold text-rose-600 dark:text-rose-400 block">{{ $item->kode_barang }}</span>
                                <span class="font-bold text-slate-800 dark:text-slate-100">{{ $item->nama_barang }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-300">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td class="p-3.5 text-slate-600 dark:text-slate-300">{{ $item->lokasi->nama_lokasi ?? '-' }}</td>
                            <td class="p-3.5 text-center font-bold {{ $item->stok <= 1 ? 'text-rose-600' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ $item->stok }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->kondisi === 'Baik' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $item->kondisi }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right font-mono font-bold">
                                Rp {{ number_format($item->harga_perkiraan, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Detail & Riwayat Mutasi -->
                                    <button 
                                        type="button"
                                        onclick="showAssetDetail({{ json_encode($item) }})"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-bold text-[11px] flex items-center gap-1 cursor-pointer transition"
                                        title="Lihat Detail & Riwayat Mutasi Aset"
                                    >
                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                                        <span>Detail</span>
                                        @if($item->mutasis && count($item->mutasis) > 0)
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-mono font-bold">{{ count($item->mutasis) }}</span>
                                        @endif
                                    </button>

                                    <!-- Tombol Cetak Stiker Label QR Mandiri (1 Item) -->
                                    <button 
                                        type="button"
                                        onclick="openSingleQrModal({{ json_encode($item) }})"
                                        class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 rounded-lg font-bold text-[11px] flex items-center gap-1 cursor-pointer transition border border-indigo-200 dark:border-indigo-800"
                                        title="Cetak Stiker Label QR (1 Item Saja)"
                                    >
                                        <i data-lucide="qr-code" class="w-3.5 h-3.5 text-indigo-600"></i>
                                        <span>Label QR</span>
                                    </button>

                                    <!-- Tombol Edit Aset -->
                                    <button 
                                        type="button"
                                        onclick="openModalEdit({{ json_encode($item) }})"
                                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/50 rounded-lg transition cursor-pointer"
                                        title="Ubah Data Aset"
                                    >
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Tidak ada aset ditemukan.</td>
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
                    📦
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
                ✕
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

    function openModalTambah() {
        alert('Gunakan form tambah aset pada sistem.');
    }

    function openModalEdit(item) {
        alert(`Ubah data aset: ${item.kode_barang} (${item.nama_barang})`);
    }
</script>
@endsection
