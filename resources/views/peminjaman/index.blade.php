@extends('layouts.app')

@section('title', 'Peminjaman & Pengembalian Aset Tetap - Inventaris Kantor')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400">
                    Sirkulasi & Logistik
                </span>
                <span class="text-xs text-slate-400 font-mono">1:1 Sinkronisasi Peminjaman & WhatsApp</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Peminjaman & Pengembalian Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pinjam-pakai aset operasional kantor (laptop, kamera, kendaraan, proyektor) dengan audit kondisi fisik dan notifikasi pengingat.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="document.getElementById('modalTambahPinjam').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="handshake" class="w-4 h-4"></i>
                Buat Permohonan Pinjam Aset
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
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

    <!-- KPI / Ringkasan Peminjaman -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tiket Pinjam</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $loans->total() ?? count($loans) }}</h4>
                <span class="text-xs text-slate-500">Semua Catatan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sedang Dipinjam</p>
                <h4 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5">
                    {{ $loans->where('status', 'Dipinjam')->count() }}
                </h4>
                <span class="text-xs text-blue-600 font-semibold">Unit di Lapangan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sudah Kembali</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $loans->where('status', 'Dikembalikan')->count() }}
                </h4>
                <span class="text-xs text-emerald-600 font-semibold">Stok Dipulihkan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Menunggu Approval</p>
                <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">
                    {{ $loans->where('status', 'Menunggu Approval')->count() }}
                </h4>
                <span class="text-xs text-amber-600 font-semibold">Perlu Tindakan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Data Peminjaman -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">No. Peminjaman</th>
                        <th class="px-5 py-3.5">Aset / Barang</th>
                        <th class="px-5 py-3.5">Peminjam (Pegawai)</th>
                        <th class="px-5 py-3.5">Jadwal Pinjam & Jatuh Tempo</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($loans as $item)
                    @php
                        $targetDate = $item->tanggal_kembali ?? $item->rencana_kembali;
                        $isOverdue = $item->status === 'Dipinjam' && $targetDate && \Carbon\Carbon::parse($targetDate)->isPast();
                    @endphp
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                                {{ $item->nomor_pinjam }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">
                                {{ $item->inventaris->nama_barang ?? 'Aset Tidak Ditemukan' }}
                            </div>
                            <div class="text-xs font-mono text-slate-400">
                                Kode: {{ $item->inventaris->kode_barang ?? '-' }} &bull; Qty: {{ $item->jumlah }} Unit
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                {{ $item->karyawan->nama ?? 'Pegawai' }}
                            </div>
                            <div class="text-[11px] text-slate-400">
                                {{ $item->karyawan->departemen->nama ?? 'Umum' }}
                            </div>
                            @if(optional($item->karyawan)->no_telepon)
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->karyawan->no_telepon) }}?text=Halo%20{{ urlencode($item->karyawan->nama) }},%20pengingat%20peminjaman%20aset%20{{ urlencode($item->inventaris->nama_barang ?? '') }}%20(No:%20{{ $item->nomor_pinjam }})"
                                target="_blank"
                                class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold hover:underline mt-0.5"
                            >
                                <i data-lucide="message-square" class="w-3 h-3"></i> WhatsApp Reminder
                            </a>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs whitespace-nowrap">
                            <div>Pinjam: <strong class="text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}</strong></div>
                            @if($targetDate)
                            <div class="{{ $isOverdue ? 'text-rose-600 font-bold dark:text-rose-400' : 'text-slate-500' }}">
                                Tempo: {{ \Carbon\Carbon::parse($targetDate)->format('d M Y') }}
                                @if($isOverdue)
                                <span class="text-[10px] uppercase font-bold text-rose-500 bg-rose-50 dark:bg-rose-950/60 px-1.5 py-0.2 rounded ml-1">Terlambat</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($item->status === 'Dipinjam')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                    Dipinjam
                                </span>
                            @elseif($item->status === 'Dikembalikan')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    Dikembalikan
                                </span>
                            @elseif($item->status === 'Menunggu Approval')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    Menunggu Approval
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $item->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($item->status === 'Menunggu Approval')
                                <form action="{{ route('peminjaman.approve', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-xs cursor-pointer">
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('peminjaman.reject', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 bg-slate-200 hover:bg-rose-100 hover:text-rose-600 text-slate-700 rounded-lg text-xs font-bold transition cursor-pointer">
                                        Tolak
                                    </button>
                                </form>
                                @elseif($item->status === 'Dipinjam')
                                <button
                                    type="button"
                                    onclick="openReturnModal('{{ $item->id }}', '{{ $item->nomor_pinjam }}', '{{ addslashes($item->inventaris->nama_barang ?? '') }}')"
                                    class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg text-xs font-bold transition shadow-xs cursor-pointer"
                                >
                                    Proses Pengembalian
                                </button>
                                @else
                                <span class="text-xs text-slate-400 font-medium">Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <div class="text-slate-700 dark:text-slate-300 font-bold text-sm">Belum Ada Transaksi Peminjaman</div>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Klik tombol "Buat Permohonan Pinjam Aset" di atas untuk mencatat peminjaman unit operasional.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($loans, 'links'))
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $loans->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Peminjaman (1:1 Identik React App) -->
<div id="modalTambahPinjam" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="handshake" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">Permohonan Pinjam Aset</h3>
                    <p class="text-xs text-slate-400">Peminjaman aset operasional kantor antar pegawai</p>
                </div>
            </div>
            <button
                type="button"
                onclick="document.getElementById('modalTambahPinjam').classList.add('hidden')"
                class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Pilih Aset -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Pilih Barang / Aset <span class="text-rose-500">*</span>
                </label>
                <select
                    name="inventaris_id"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-white"
                >
                    <option value="" disabled selected>-- Pilih Aset yang Tersedia --</option>
                    @foreach($inventaris as $inv)
                        <option value="{{ $inv->id }}">
                            [{{ $inv->kode_barang }}] {{ $inv->nama_barang }} (Tersedia: {{ $inv->stok }} {{ $inv->satuan ?? 'Unit' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Karyawan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Peminjam (Pegawai) <span class="text-rose-500">*</span>
                </label>
                <select
                    name="karyawan_id"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-white"
                >
                    <option value="" disabled selected>-- Pilih Pegawai Peminjam --</option>
                    @foreach($karyawans as $kar)
                        <option value="{{ $kar->id }}">
                            {{ $kar->nama }} (NIK: {{ $kar->nik }} - {{ $kar->departemen->nama ?? 'Umum' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Pinjam & Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Tanggal Pinjam <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal_pinjam"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-white"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Rencana Kembali <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal_kembali"
                        value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-white"
                    >
                </div>
            </div>

            <!-- Jumlah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Jumlah Unit Dipinjam <span class="text-rose-500">*</span>
                </label>
                <input
                    type="number"
                    name="jumlah"
                    min="1"
                    value="1"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-blue-400"
                >
            </div>

            <!-- Keperluan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Keperluan Pinjam <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="keperluan"
                    required
                    placeholder="Contoh: Presentasi Klien / Meeting Luar Kota / Pameran"
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-white"
                >
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button
                    type="button"
                    onclick="document.getElementById('modalTambahPinjam').classList.add('hidden')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold text-xs transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 active:scale-95 transition cursor-pointer"
                >
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Return / Pengembalian Aset -->
<div id="modalReturn" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white text-base">Konfirmasi Pengembalian Aset</h3>
            <button
                type="button"
                onclick="document.getElementById('modalReturn').classList.add('hidden')"
                class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formReturn" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <p id="returnDocNum" class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400"></p>
                <p id="returnItemName" class="text-sm font-bold text-slate-900 dark:text-white mt-0.5"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Kondisi Fisik Saat Dikembalikan <span class="text-rose-500">*</span>
                </label>
                <select
                    name="kondisi_sesudah"
                    required
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                >
                    <option value="Baik" selected>Baik (Lengkap & Berfungsi Normal)</option>
                    <option value="Rusak Ringan">Rusak Ringan (Perlu Pembersihan / Cacat Kosmetik)</option>
                    <option value="Rusak Berat">Rusak Berat (Tidak Berfungsi / Perlu Servis)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Catatan Pengembalian (Opsional)
                </label>
                <textarea
                    name="catatan"
                    rows="2"
                    placeholder="Kelengkapan adapter, tas, remote, atau catatan fisik..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                ></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button
                    type="button"
                    onclick="document.getElementById('modalReturn').classList.add('hidden')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-semibold text-xs"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition cursor-pointer"
                >
                    Selesaikan Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReturnModal(id, docNum, itemName) {
    const form = document.getElementById('formReturn');
    form.action = `/peminjaman/${id}/return`;
    document.getElementById('returnDocNum').textContent = docNum;
    document.getElementById('returnItemName').textContent = itemName;
    document.getElementById('modalReturn').classList.remove('hidden');
}
</script>
@endsection
