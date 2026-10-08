@extends('layouts.app')

@section('title', 'Pemeliharaan & Servis Aset - Inventaris Kantor')
@section('page_title', 'Tiket Servis & Pemeliharaan Aset')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                    Sirkulasi & Logistik
                </span>
                <span class="text-xs text-slate-400 font-mono">1:1 Siklus Perawatan Aset</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Pemeliharaan & Servis Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jadwal perawatan rutin, tiket reparasi vendor eksternal, pemulihan kondisi fisik, dan monitoring biaya servis.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahMaintenance()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="wrench" class="w-4 h-4"></i>
                Buat Tiket Servis Baru
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tiket Servis</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $stats['total'] ?? 0 }}</h4>
                <span class="text-xs text-slate-500">Semua Riwayat</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                <i data-lucide="wrench" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dalam Pengerjaan</p>
                <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ $stats['dalam_perbaikan'] ?? 0 }} Unit</h4>
                <span class="text-xs text-amber-600 font-semibold">Sedang Ditangani Vendor</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selesai Diperbaiki</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $stats['selesai'] ?? 0 }} Unit</h4>
                <span class="text-xs text-emerald-600 font-semibold">Kembali Siap Pakai</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Biaya Realisasi</p>
                <h4 class="text-xl font-black text-slate-900 dark:text-white mt-0.5 truncate">
                    Rp {{ number_format($stats['total_biaya'] ?? 0, 0, ',', '.') }}
                </h4>
                <span class="text-xs text-blue-600 font-semibold">Realisasi Anggaran</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('maintenance.index') }}" class="flex-1 w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor tiket, nama aset, vendor, kendala..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none dark:text-white"
                />
            </div>

            <div class="w-full sm:w-56">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="all">Semua Status Servis</option>
                    <option value="Dalam Perbaikan" {{ request('status') === 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Tidak Dapat Diperbaiki" {{ request('status') === 'Tidak Dapat Diperbaiki' ? 'selected' : '' }}>Tidak Dapat Diperbaiki</option>
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                Filter
            </button>

            @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('maintenance.index') }}" class="text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Maintenance -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Tiket & Tipe</th>
                        <th class="px-5 py-3.5">Aset / Barang</th>
                        <th class="px-5 py-3.5">Vendor & Kendala</th>
                        <th class="px-5 py-3.5">Rentang Waktu</th>
                        <th class="px-5 py-3.5">Estimasi / Realisasi Biaya</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($maintenances as $item)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-900/60">
                                {{ $item->nomor_tiket }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1 font-semibold uppercase">{{ $item->jenis_maintenance ?? 'Perbaikan' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <span class="text-slate-400 font-mono text-[11px]">{{ $item->inventaris->kode_barang ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <i data-lucide="store" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $item->vendor }}</span>
                            </div>
                            <div class="text-slate-400 text-[11px] mt-0.5 line-clamp-2 max-w-xs">{{ $item->deskripsi_masalah }}</div>
                        </td>
                        <td class="px-5 py-4 text-[11px]">
                            <div class="flex items-center gap-1">
                                <span class="text-slate-400">Mulai:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $item->tanggal_mulai }}</span>
                            </div>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="text-slate-400">Selesai:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $item->tanggal_selesai ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-[11px] text-slate-400">Est: Rp {{ number_format($item->estimasi_biaya ?? $item->biaya ?? 0, 0, ',', '.') }}</div>
                            <div class="font-extrabold font-mono text-emerald-600 dark:text-emerald-400 text-xs mt-0.5">
                                Real: Rp {{ number_format($item->biaya_aktual ?? $item->biaya ?? 0, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($item->status === 'Dalam Perbaikan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Dalam Perbaikan
                                </span>
                            @elseif($item->status === 'Selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                    <i data-lucide="alert-octagon" class="w-3 h-3"></i>
                                    Tidak Dapat Diperbaiki
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if($item->status === 'Dalam Perbaikan')
                                    <button
                                        type="button"
                                        onclick="openModalSelesaikanServis({{ json_encode($item) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition cursor-pointer flex items-center gap-1"
                                    >
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Update Status</span>
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('maintenance.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan servis ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                                        title="Hapus Catatan Servis"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-slate-400">
                            <i data-lucide="wrench" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada tiket pemeliharaan aset</p>
                            <p class="text-[11px] text-slate-400">Buat tiket servis baru jika ada aset kantor yang membutuhkan perawatan atau perbaikan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($maintenances, 'links'))
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Maintenance -->
<div id="modalTambahMaintenance" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="wrench" class="w-5 h-5 text-amber-500"></i>
                Buat Tiket Pemeliharaan Aset
            </h3>
            <button type="button" onclick="closeModalTambahMaintenance()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('maintenance.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pilih Aset Yang Diperbaiki *</label>
                <select name="inventaris_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">-- Pilih Barang / Aset Fisik --</option>
                    @foreach($inventarisList as $ast)
                        <option value="{{ $ast->id }}">
                            {{ $ast->kode_barang }} - {{ $ast->nama_barang }} (Kondisi: {{ $ast->kondisi }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tipe Pemeliharaan *</label>
                    <select name="jenis_maintenance" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="Perbaikan">Perbaikan Kerusakan</option>
                        <option value="Rutin">Servis Rutin Berkala</option>
                        <option value="Insidental">Insidental / Kalibrasi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Mulai Servis *</label>
                    <input type="date" name="tanggal_mulai" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Vendor / Bengkel Rekanan *</label>
                    <input type="text" name="vendor" required placeholder="Contoh: Asus Service Center, Mandiri Teknik" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Estimasi Biaya (Rp) *</label>
                    <input type="number" name="estimasi_biaya" required min="0" placeholder="Contoh: 350000" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono" />
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Deskripsi Kerusakan / Gejala Masalah *</label>
                <textarea name="deskripsi_masalah" required rows="3" placeholder="Jelaskan detail indikasi kerusakan atau komponen yang bermasalah..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahMaintenance()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold shadow-md shadow-amber-600/20 cursor-pointer">Simpan Tiket</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Selesaikan Servis -->
<div id="modalSelesaiMaintenance" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                Penyelesaian Tiket Servis
            </h3>
            <button type="button" onclick="closeModalSelesaikanServis()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formSelesaiMaintenance" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status Hasil Servis *</label>
                <select name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="Selesai">✅ Selesai (Aset Kembali Berfungsi Baik)</option>
                    <option value="Tidak Dapat Diperbaiki">❌ Tidak Dapat Diperbaiki (Dihapuskan / Afkir)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Selesai *</label>
                    <input type="date" name="tanggal_selesai" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Biaya Riil Aktual (Rp)</label>
                    <input type="number" id="selesai_biaya_aktual" name="biaya_aktual" min="0" placeholder="Biaya nota vendor..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono" />
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tindakan Perbaikan / Catatan Teknisi</label>
                <textarea name="tindakan_perbaikan" rows="3" placeholder="Contoh: Penggantian pasta termal, instalasi ulang modul catu daya..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalSelesaikanServis()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer">Simpan Penyelesaian</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahMaintenance() {
        document.getElementById('modalTambahMaintenance').classList.remove('hidden');
    }
    function closeModalTambahMaintenance() {
        document.getElementById('modalTambahMaintenance').classList.add('hidden');
    }

    function openModalSelesaikanServis(data) {
        const form = document.getElementById('formSelesaiMaintenance');
        form.action = '/maintenance/' + data.id + '/update-status';
        document.getElementById('selesai_biaya_aktual').value = data.estimasi_biaya || '';
        document.getElementById('modalSelesaiMaintenance').classList.remove('hidden');
    }
    function closeModalSelesaikanServis() {
        document.getElementById('modalSelesaiMaintenance').classList.add('hidden');
    }
</script>
@endsection
