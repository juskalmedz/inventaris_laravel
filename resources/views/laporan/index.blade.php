@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Inventaris - Inventaris Kantor')
@section('page_title', 'Pusat Laporan & Berita Acara Sensus Aset')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Laporan & Rekapitulasi
                </span>
                <span class="text-xs text-slate-400 font-mono">Format Resmi Ber-KOP Instansi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Laporan & Berita Acara Sensus</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pusat penyusunan dokumen rekapitulasi fisik, valuasi aset, kartu mutasi bulanan, dan berita acara bertanda tangan.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button
                type="button"
                onclick="openModalKopEditor()"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-2xs cursor-pointer"
            >
                <i data-lucide="settings" class="w-4 h-4 text-slate-500"></i>
                <span>Kustomisasi KOP Surat</span>
            </button>
            <a
                href="{{ route('laporan.exportExcel') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition cursor-pointer"
            >
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export CSV / Excel</span>
            </a>
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-bold shadow-md transition cursor-pointer"
            >
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Lembar Resmi (PDF)</span>
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 no-print">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Entitas Aset</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $totalAset }} Jenis</h4>
                <span class="text-xs text-slate-500">Tercatat di Database</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Unit Fisik</p>
                <h4 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5">{{ number_format($totalFisik, 0, ',', '.') }} Unit</h4>
                <span class="text-xs text-blue-600 font-semibold">Tersedia & Terdistribusi</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="boxes" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Valuasi Nilai</p>
                <h4 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 truncate">
                    Rp {{ number_format($totalNilai, 0, ',', '.') }}
                </h4>
                <span class="text-xs text-emerald-600 font-semibold">Estimasi Nilai Buku</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aset Perlu Servis</p>
                <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ count($maintenance) }} Unit</h4>
                <span class="text-xs text-amber-600 font-semibold">Perbaikan & Pemeliharaan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                <i data-lucide="wrench" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- PRESET REPORT TABS (Sama Persis React 6 Preset) -->
    <div class="no-print bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center gap-1.5 overflow-x-auto text-xs font-bold">
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['preset' => 'rekap'])) }}" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 {{ $preset === 'rekap' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>1. Rekapitulasi Fisik</span>
        </a>
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['preset' => 'valuasi'])) }}" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 {{ $preset === 'valuasi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="coins" class="w-4 h-4"></i>
            <span>2. Valuasi & Nilai Aset</span>
        </a>
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['preset' => 'kondisi'])) }}" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 {{ $preset === 'kondisi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="wrench" class="w-4 h-4"></i>
            <span>3. Kondisi & Servis</span>
        </a>
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['preset' => 'sirkulasi'])) }}" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 {{ $preset === 'sirkulasi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
            <span>4. Sirkulasi Logistik</span>
        </a>
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['preset' => 'habispakai'])) }}" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 {{ $preset === 'habispakai' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="package" class="w-4 h-4"></i>
            <span>5. Barang Habis Pakai</span>
        </a>
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['preset' => 'mutasi'])) }}" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 {{ $preset === 'mutasi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>6. Mutasi & Kartu Kendali</span>
        </a>
    </div>

    <!-- Filter Multi-Parameter -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs no-print">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center text-xs font-medium">
            <input type="hidden" name="preset" value="{{ $preset }}" />
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kategori</label>
                <select name="kategori_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lokasi Ruangan</label>
                <select name="lokasi_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasiList as $lok)
                        <option value="{{ $lok->id }}" {{ request('lokasi_id') == $lok->id ? 'selected' : '' }}>
                            {{ $lok->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kondisi</label>
                <select name="kondisi" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ request('kondisi') === 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Status Ketersediaan</label>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Dalam Perbaikan" {{ request('status') === 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
            </div>

            <div class="flex items-end gap-2 pt-4 sm:pt-0">
                <button type="submit" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold transition cursor-pointer">
                    Filter
                </button>
                @if(request('kategori_id') || request('lokasi_id') || request('kondisi') || request('status'))
                    <a href="{{ route('laporan.index', ['preset' => $preset]) }}" class="px-3 py-2 text-rose-600 font-bold hover:underline">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- LEMBAR LAPORAN BER-KOP RESMI (Desain Bersih 100% Mirip React) -->
    <div id="printableReportSheet" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 sm:p-12 shadow-sm space-y-8">
        
        <!-- KOP Surat Resmi -->
        <div class="border-b-4 border-double border-slate-900 dark:border-slate-100 pb-5 text-center relative">
            <div class="flex items-center justify-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-700 text-white flex items-center justify-center font-black text-2xl shrink-0 shadow-md">
                    <i data-lucide="boxes" class="w-9 h-9"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase tracking-wider text-slate-900 dark:text-white" id="displayOrgName">
                        {{ $kopConfig->org_name ?? 'KEMENTERIAN KOMUNIKASI DAN INFORMATIKA RI' }}
                    </h2>
                    <h3 class="text-xs sm:text-sm font-extrabold uppercase tracking-wide text-slate-700 dark:text-slate-300" id="displayDivName">
                        {{ $kopConfig->div_name ?? 'DIREKTORAT JENDERAL SUMBER DAYA & PERANGKAT POS INFORMATIKA' }}
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1" id="displayAlamat">
                        {{ $kopConfig->alamat ?? 'Jl. Medan Merdeka Barat No. 17, Gambir, Jakarta Pusat 10110' }} &bull; Telp: (021) 3835841
                    </p>
                </div>
            </div>
        </div>

        <!-- Judul Dokumen & Nomor Berita Acara -->
        <div class="text-center space-y-1">
            <h3 class="text-base sm:text-lg font-black uppercase tracking-tight text-slate-900 dark:text-white underline">
                @if($preset === 'rekap')
                    BERITA ACARA REKAPITULASI INVENTARIS FISIK
                @elseif($preset === 'valuasi')
                    LAPORAN VALUASI & NILAI BUKU ASET KANTOR
                @elseif($preset === 'kondisi')
                    LAPORAN KONDISI FISIK & PEMELIHARAAN ASET
                @elseif($preset === 'sirkulasi')
                    LAPORAN SIRKULASI LOGISTIK & PEMINJAMAN ASET
                @elseif($preset === 'habispakai')
                    REKAPITULASI STOK BARANG HABIS PAKAI (ATK)
                @else
                    KARTU KENDALI MUTASI STOK BULANAN
                @endif
            </h3>
            <p class="text-xs font-mono font-bold text-slate-600 dark:text-slate-400" id="displayNomorSurat">
                Nomor: {{ $kopConfig->nomor_surat ?? 'BA-INV/2026/X/001' }}
            </p>
            <p class="text-[11px] text-slate-400">
                Tanggal Penetapan: <span class="font-bold text-slate-700 dark:text-slate-300">{{ date('d F Y') }}</span>
            </p>
        </div>

        <!-- Tabel Konten Dokumen Sesuai Preset -->
        @if($preset === 'rekap' || $preset === 'valuasi' || $preset === 'kondisi' || $preset === 'habispakai')
        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 font-bold uppercase text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3 w-10 text-center">No</th>
                        <th class="p-3">Kode Barang</th>
                        <th class="p-3">Nama Inventaris & Spesifikasi</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Lokasi</th>
                        <th class="p-3 text-center">Kondisi</th>
                        <th class="p-3 text-center">Stok</th>
                        @if($preset === 'valuasi')
                        <th class="p-3 text-right">Harga Satuan (Rp)</th>
                        <th class="p-3 text-right">Subtotal Nilai (Rp)</th>
                        @else
                        <th class="p-3 text-center">Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($items as $index => $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        <td class="p-3 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                        <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $item->kode_barang }}</td>
                        <td class="p-3">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $item->nama_barang }}</div>
                            <div class="text-[10px] text-slate-400 line-clamp-1">{{ $item->deskripsi ?? '-' }}</div>
                        </td>
                        <td class="p-3">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                        <td class="p-3">{{ $item->lokasi->nama_lokasi ?? '-' }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->kondisi === 'Baik' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                {{ $item->kondisi }}
                            </span>
                        </td>
                        <td class="p-3 text-center font-bold">{{ $item->stok }} Unit</td>
                        @if($preset === 'valuasi')
                        <td class="p-3 text-right font-mono">{{ number_format($item->harga_perkiraan, 0, ',', '.') }}</td>
                        <td class="p-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            {{ number_format($item->stok * $item->harga_perkiraan, 0, ',', '.') }}
                        </td>
                        @else
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $item->status === 'Tersedia' ? 'text-emerald-600' : 'text-blue-600' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-slate-400">Tidak ada data inventaris yang sesuai dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($preset === 'valuasi')
                <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t border-slate-200 dark:border-slate-800 text-xs">
                    <tr>
                        <td colspan="6" class="p-3 text-right font-extrabold uppercase">Total Akumulasi Nilai Aset:</td>
                        <td class="p-3 text-center font-bold">{{ number_format($totalFisik, 0, ',', '.') }} Unit</td>
                        <td class="p-3"></td>
                        <td class="p-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                            Rp {{ number_format($totalNilai, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @elseif($preset === 'sirkulasi')
        <!-- Tabel Sirkulasi Peminjaman & Mutasi -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 font-bold uppercase text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3">No Transaksi</th>
                        <th class="p-3">Tanggal Pinjam</th>
                        <th class="p-3">Aset Inventaris</th>
                        <th class="p-3">Peminjam / PIC</th>
                        <th class="p-3 text-center">Jumlah</th>
                        <th class="p-3">Rencana Kembali</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($peminjaman as $p)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $p->nomor_pinjam }}</td>
                        <td class="p-3">{{ $p->tanggal_pinjam }}</td>
                        <td class="p-3">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $p->inventaris->nama_barang ?? '-' }}</div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $p->inventaris->kode_barang ?? '-' }}</span>
                        </td>
                        <td class="p-3 font-semibold">{{ $p->karyawan->nama ?? '-' }}</td>
                        <td class="p-3 text-center font-bold">{{ $p->jumlah }} Unit</td>
                        <td class="p-3">{{ $p->rencana_kembali ?? '-' }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->status === 'Dipinjam' ? 'bg-amber-100 text-amber-800' : ($p->status === 'Dikembalikan' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600') }}">
                                {{ $p->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada catatan sirkulasi peminjaman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @elseif($preset === 'mutasi')
        <!-- Tabel Kartu Kendali Mutasi Stok -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 font-bold uppercase text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3">No Mutasi</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Nama Aset</th>
                        <th class="p-3">Lokasi Awal</th>
                        <th class="p-3">Lokasi Baru Tujuan</th>
                        <th class="p-3">Pemohon PIC</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($mutasi as $m)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        <td class="p-3 font-mono font-bold text-purple-600 dark:text-purple-400">{{ $m->nomor_mutasi }}</td>
                        <td class="p-3">{{ $m->tanggal_permohonan }}</td>
                        <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $m->inventaris->nama_barang ?? '-' }}</td>
                        <td class="p-3 text-slate-500">{{ $m->lokasiAwal->nama_lokasi ?? '-' }}</td>
                        <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $m->lokasiBaru->nama_lokasi ?? '-' }}</td>
                        <td class="p-3">{{ $m->pemohon->nama ?? '-' }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $m->status === 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $m->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada riwayat mutasi aset.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        <!-- Catatan & Klausul Berita Acara Resmi -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400 leading-relaxed" id="displayCatatan">
            Demikian Berita Acara Rekapitulasi Fisik & Valuasi Aset Kantor ini dibuat dengan sebenar-benarnya berdasarkan hasil verifikasi faktual lapangan guna memenuhi standar akuntabilitas kepemilikan aset dan ketertiban administrasi logistik instansi.
        </div>

        <!-- Kolom Pengesahan 2 Tanda Tangan (Sama Persis React) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-4 text-center text-xs">
            <!-- Pihak 1: Petugas Penanggung Jawab -->
            <div class="space-y-16">
                <div>
                    <p class="text-slate-500 font-medium">Petugas Pembuat / Pengurus Aset,</p>
                    <p class="font-bold text-slate-900 dark:text-white mt-1" id="displayMakerTitle">
                        {{ $kopConfig->maker_title ?? 'Petugas Pengelola Inventaris & Logistik' }}
                    </p>
                </div>
                <div>
                    <div class="font-black text-sm text-slate-900 dark:text-white underline" id="displayMakerName">
                        {{ $kopConfig->maker_name ?? 'Bambang Pratama, S.Kom.' }}
                    </div>
                    <p class="text-[11px] font-mono text-slate-500 mt-0.5" id="displayMakerNip">
                        {{ $kopConfig->maker_nip ?? 'NIP. 19880422 201101 1 005' }}
                    </p>
                </div>
            </div>

            <!-- Pihak 2: Pejabat Penyetuju / Pimpinan -->
            <div class="space-y-16">
                <div>
                    <p class="text-slate-500 font-medium">Mengetahui & Menyetujui,</p>
                    <p class="font-bold text-slate-900 dark:text-white mt-1" id="displayApproverTitle">
                        {{ $kopConfig->approver_title ?? 'Kepala Bagian Umum & Perlengkapan' }}
                    </p>
                </div>
                <div>
                    <div class="font-black text-sm text-slate-900 dark:text-white underline" id="displayApproverName">
                        {{ $kopConfig->approver_name ?? 'Drs. Bambang Hariyanto, M.M.' }}
                    </div>
                    <p class="text-[11px] font-mono text-slate-500 mt-0.5" id="displayApproverNip">
                        {{ $kopConfig->approver_nip ?? 'NIP. 19750812 199903 1 002' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- MODAL KUSTOMISASI KOP SURAT (1:1 Mirip React) -->
<div id="modalKopEditor" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden no-print">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="settings" class="w-5 h-5 text-emerald-600"></i>
                    Kustomisasi KOP Surat & Pejabat Penandatangan
                </h3>
                <p class="text-xs text-slate-500">Perbarui identitas instansi, nomor surat resmi, dan pejabat legalitas.</p>
            </div>
            <button type="button" onclick="closeModalKopEditor()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('pengaturan.updateKop') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Organisasi / Kementerian *</label>
                    <input type="text" name="org_name" value="{{ $kopConfig->org_name ?? 'KEMENTERIAN KOMUNIKASI DAN INFORMATIKA RI' }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Direktorat / Divisi *</label>
                    <input type="text" name="div_name" value="{{ $kopConfig->div_name ?? 'DIREKTORAT JENDERAL SUMBER DAYA & PERANGKAT POS INFORMATIKA' }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor Surat Berita Acara *</label>
                    <input type="text" name="nomor_surat" value="{{ $kopConfig->nomor_surat ?? 'BA-INV/2026/X/001' }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alamat Kantor Lengkap</label>
                    <input type="text" name="alamat" value="{{ $kopConfig->alamat ?? 'Jl. Medan Merdeka Barat No. 17, Gambir, Jakarta Pusat 10110' }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 font-bold text-slate-900 dark:text-white">
                Pihak 1: Petugas Pembuat / Pengurus Aset
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jabatan *</label>
                    <input type="text" name="maker_title" value="{{ $kopConfig->maker_title ?? 'Petugas Pengelola Inventaris & Logistik' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap & Gelar *</label>
                    <input type="text" name="maker_name" value="{{ $kopConfig->maker_name ?? 'Bambang Pratama, S.Kom.' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP Pegawai *</label>
                    <input type="text" name="maker_nip" value="{{ $kopConfig->maker_nip ?? 'NIP. 19880422 201101 1 005' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 font-bold text-slate-900 dark:text-white">
                Pihak 2: Pejabat Mengetahui / Pimpinan
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jabatan Pimpinan *</label>
                    <input type="text" name="approver_title" value="{{ $kopConfig->approver_title ?? 'Kepala Bagian Umum & Perlengkapan' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap & Gelar *</label>
                    <input type="text" name="approver_name" value="{{ $kopConfig->approver_name ?? 'Drs. Bambang Hariyanto, M.M.' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP Pimpinan *</label>
                    <input type="text" name="approver_nip" value="{{ $kopConfig->approver_nip ?? 'NIP. 19750812 199903 1 002' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalKopEditor()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer">Simpan KOP Surat</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalKopEditor() {
        document.getElementById('modalKopEditor').classList.remove('hidden');
    }
    function closeModalKopEditor() {
        document.getElementById('modalKopEditor').classList.add('hidden');
    }
</script>
@endsection
