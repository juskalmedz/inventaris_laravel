@extends('layouts.app')

@section('title', 'Laporan & Rekap Inventaris - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan & Rekapitulasi Inventaris</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Penyusunan laporan berkala, valuasi nilai buku aset kantor, dan ekspor dokumen resmi ber-KOP.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.exportExcel') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Export CSV / Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 rounded-xl text-sm font-semibold shadow-sm transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Ringkasan Finansial -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase">Total Entitas Aset</span>
            <div class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $totalAset }} Item</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase">Total Unit Fisik Tersedia</span>
            <div class="text-3xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $totalFisik }} Unit</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase">Total Valuasi Nilai Aset</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 truncate">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Format KOP Surat Preview -->
    <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <div class="border-b-2 border-slate-900 dark:border-slate-100 pb-4 text-center">
            <h2 class="text-xl font-black tracking-wide uppercase text-slate-900 dark:text-white">{{ $kopConfig->nama_instansi }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $kopConfig->alamat }}</p>
            <p class="text-xs text-slate-500">Telp: {{ $kopConfig->telepon }} | Email: {{ $kopConfig->email }}</p>
        </div>

        <div class="text-center">
            <h3 class="text-base font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 underline">REKAPITULASI LAPORAN SENSUS INVENTARIS ASET</h3>
            <p class="text-xs text-slate-400 mt-1">Dicetak pada tanggal: {{ date('d F Y') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800 font-semibold border-b border-slate-300 dark:border-slate-700">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Kode Aset</th>
                        <th class="p-3">Nama Barang</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Lokasi</th>
                        <th class="p-3">Stok</th>
                        <th class="p-3">Kondisi</th>
                        <th class="p-3">Harga Satuan</th>
                        <th class="p-3 text-right">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($items as $idx => $row)
                    <tr>
                        <td class="p-3">{{ $idx + 1 }}</td>
                        <td class="p-3 font-mono font-bold">{{ $row->kode_barang }}</td>
                        <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ $row->nama_barang }}</td>
                        <td class="p-3">{{ $row->kategori->nama ?? '-' }}</td>
                        <td class="p-3">{{ $row->lokasi->nama ?? '-' }}</td>
                        <td class="p-3 font-bold">{{ $row->stok }} {{ $row->satuan }}</td>
                        <td class="p-3">{{ $row->kondisi }}</td>
                        <td class="p-3">Rp {{ number_format($row->harga_perkiraan, 0, ',', '.') }}</td>
                        <td class="p-3 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($row->stok * $row->harga_perkiraan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
