@extends('layouts.app')

@section('title', 'Sirkulasi Barang Masuk (Pengadaan) - Inventaris Kantor')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                    Sirkulasi & Logistik
                </span>
                <span class="text-xs text-slate-400 font-mono">1:1 Sinkronisasi Stok Fisik</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Sirkulasi Barang Masuk</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan pengadaan, penerimaan dari vendor/supplier, dan penambahan kuantitas unit aset fisik secara otomatis.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="document.getElementById('modalTambahMasuk').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-bold text-sm shadow-md shadow-emerald-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Catat Barang Masuk Baru
            </button>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- KPI / Ringkasan Cepat -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Transaksi Masuk</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $barangMasukList->total() ?? count($barangMasukList) }}</h4>
                <span class="text-xs text-emerald-600 font-semibold">Tercatat di Sistem</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="arrow-down-left" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Unit Masuk</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                    +{{ number_format($barangMasukList->sum('jumlah'), 0, ',', '.') }}
                </h4>
                <span class="text-xs text-slate-500">Unit bertambah ke stok</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 flex items-center justify-center">
                <i data-lucide="package-plus" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Katalog Tersedia</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ count($inventarisList) }}</h4>
                <span class="text-xs text-slate-500">Pilihan Aset Siap Restock</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="boxes" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('barang-masuk.index') }}" class="flex-1 w-full flex items-center gap-2">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor transaksi, nama aset, kode barang, atau supplier..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                >
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white rounded-xl text-sm font-semibold transition cursor-pointer">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('barang-masuk.index') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Barang Masuk -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">No. Transaksi</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Aset / Barang</th>
                        <th class="px-5 py-3.5">Supplier / Vendor</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Masuk</th>
                        <th class="px-5 py-3.5">Keterangan / Memo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($barangMasukList as $item)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                {{ $item->nomor_transaksi }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs whitespace-nowrap">
                            <div class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ \Carbon\Carbon::parse($item->tanggal)->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">
                                {{ $item->inventaris->nama_barang ?? 'Aset Tidak Ditemukan' }}
                            </div>
                            <div class="text-xs font-mono text-slate-400">
                                Kode: {{ $item->inventaris->kode_barang ?? '-' }} &bull; Lokasi: {{ $item->inventaris->lokasi->nama_lokasi ?? '-' }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $item->supplier }}
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <i data-lucide="arrow-down-left" class="w-3.5 h-3.5"></i>
                                +{{ $item->jumlah }} {{ $item->inventaris->satuan ?? 'Unit' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500 max-w-xs truncate">
                            {{ $item->keterangan ?: 'Penerimaan Pengadaan Barang' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <div class="text-slate-700 dark:text-slate-300 font-bold text-sm">Belum Ada Transaksi Barang Masuk</div>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Klik tombol "Catat Barang Masuk Baru" di atas untuk menambahkan stok penerimaan inventaris kantor.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($barangMasukList, 'links'))
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $barangMasukList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Transaksi Barang Masuk (1:1 Identik Versi React) -->
<div id="modalTambahMasuk" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">Catat Barang Masuk (Pengadaan)</h3>
                    <p class="text-xs text-slate-400">Stok fisik aset otomatis bertambah setelah disimpan</p>
                </div>
            </div>
            <button
                type="button"
                onclick="document.getElementById('modalTambahMasuk').classList.add('hidden')"
                class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('barang-masuk.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Pilih Aset -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Pilih Barang / Aset <span class="text-rose-500">*</span>
                </label>
                <select
                    name="inventaris_id"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                >
                    <option value="" disabled selected>-- Pilih Aset dari Katalog --</option>
                    @foreach($inventarisList as $inv)
                        <option value="{{ $inv->id }}">
                            [{{ $inv->kode_barang }}] {{ $inv->nama_barang }} (Stok Saat Ini: {{ $inv->stok }} {{ $inv->satuan ?? 'Unit' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal & Jumlah -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Tanggal Masuk <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Jumlah Unit Masuk <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="jumlah"
                        min="1"
                        value="1"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-emerald-600 focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-emerald-400"
                    >
                </div>
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Supplier / Vendor Pengadaan <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="supplier"
                    required
                    placeholder="Contoh: PT Mitra Informatika Mandiri / Vendor ATK"
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                >
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Keterangan / No. Surat Jalan (Opsional)
                </label>
                <textarea
                    name="keterangan"
                    rows="3"
                    placeholder="Pengadaan barang tahunan, nomor PO, nomor surat jalan..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                ></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button
                    type="button"
                    onclick="document.getElementById('modalTambahMasuk').classList.add('hidden')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold text-xs transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition cursor-pointer"
                >
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan & Update Stok Fisik
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
