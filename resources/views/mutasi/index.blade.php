@extends('layouts.app')

@section('title', 'Mutasi & Pemindahan Aset - Inventaris Kantor')
@section('page_title', 'Mutasi & Pemindahan Aset Tetap')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300">
                    Sirkulasi & Logistik
                </span>
                <span class="text-xs text-slate-400 font-mono">1:1 Berita Acara Serah Terima (BAST)</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Mutasi & Pemindahan Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pengalihan fisik aset tetap antar ruangan/gedung kerja dengan persetujuan bertingkat (approval workflow).</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahMutasi()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-purple-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                Ajukan Mutasi Aset
            </button>
        </div>
    </div>

    <!-- KPI / Ringkasan Mutasi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Permohonan</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $mutasis->total() ?? count($mutasis) }}</h4>
                <span class="text-xs text-slate-500">Berkas Dokumen</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Menunggu Approval</p>
                <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">
                    {{ $mutasis->where('status', 'Menunggu Approval')->count() }}
                </h4>
                <span class="text-xs text-amber-600 font-semibold">Perlu Tindakan Review</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Disetujui / Dialihkan</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $mutasis->where('status', 'Disetujui')->count() }}
                </h4>
                <span class="text-xs text-emerald-600 font-semibold">Lokasi Terupdate</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Permohonan Ditolak</p>
                <h4 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-0.5">
                    {{ $mutasis->where('status', 'Ditolak')->count() }}
                </h4>
                <span class="text-xs text-rose-600 font-semibold">Tidak Memenuhi Syarat</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center">
                <i data-lucide="x-circle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Data Mutasi -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">No. Mutasi & Tanggal</th>
                        <th class="px-5 py-3.5">Aset / Barang</th>
                        <th class="px-5 py-3.5">Rute Pemindahan (Asal &rarr; Tujuan)</th>
                        <th class="px-5 py-3.5">Pemohon & Alasan</th>
                        <th class="px-5 py-3.5">Status Dokumen</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Approval</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($mutasis as $item)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded border border-purple-200 dark:border-purple-900/60">
                                {{ $item->nomor_mutasi }}
                            </span>
                            <div class="text-[11px] text-slate-400 mt-1 font-semibold">
                                {{ $item->tanggal_permohonan ?? '-' }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <span class="text-slate-400 font-mono text-[11px]">{{ $item->inventaris->kode_barang ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px]">
                                    📍 {{ $item->lokasiAwal->nama_lokasi ?? '-' }}
                                </span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-purple-500 shrink-0"></i>
                                <span class="px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-semibold text-[11px] border border-emerald-200 dark:border-emerald-800">
                                    🎯 {{ $item->lokasiBaru->nama_lokasi ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $item->pemohon->nama ?? 'Staf Pemohon' }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 max-w-xs italic">
                                "{{ $item->alasan }}"
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($item->status === 'Disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Disetujui
                                </span>
                                @if($item->disetujui_oleh)
                                    <div class="text-[10px] text-slate-400 mt-0.5">Oleh: {{ $item->disetujui_oleh }}</div>
                                @endif
                            @elseif($item->status === 'Ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Approval
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($item->status === 'Menunggu Approval')
                                <div class="flex items-center justify-end gap-1.5">
                                    <form method="POST" action="{{ route('mutasi.approve', $item->id) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            onclick="return confirm('Setujui mutasi ini? Lokasi fisik aset di katalog akan otomatis dipindahkan.')"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition cursor-pointer flex items-center gap-1"
                                            title="Setujui Mutasi"
                                        >
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('mutasi.reject', $item->id) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            onclick="return confirm('Tolak permohonan mutasi aset ini?')"
                                            class="px-2.5 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-950 text-slate-700 dark:text-slate-300 hover:text-rose-600 font-bold text-[11px] transition cursor-pointer flex items-center gap-1"
                                            title="Tolak Mutasi"
                                        >
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            <span>Tolak</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate-400 text-[11px] italic">Telah Diproses</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400">
                            <i data-lucide="arrow-left-right" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada data mutasi aset</p>
                            <p class="text-[11px] text-slate-400">Ajukan mutasi aset tetap untuk memindahkan tanggung jawab penempatan ruangan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($mutasis, 'links'))
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $mutasis->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Mutasi -->
<div id="modalTambahMutasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="arrow-left-right" class="w-5 h-5 text-purple-500"></i>
                Permohonan Mutasi / Pemindahan Aset
            </h3>
            <button type="button" onclick="closeModalTambahMutasi()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('mutasi.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pilih Aset Yang Dimutasi *</label>
                <select id="mutasi_inventaris_id" name="inventaris_id" required onchange="handleSelectAssetMutasi(this)" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <option value="">-- Pilih Barang / Aset Fisik --</option>
                    @foreach($inventaris as $ast)
                        <option value="{{ $ast->id }}" data-lokasi-id="{{ $ast->lokasi_id }}">
                            {{ $ast->kode_barang }} - {{ $ast->nama_barang }} (Lokasi Saat Ini: {{ $ast->lokasi->nama_lokasi ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Lokasi Asal *</label>
                    <select id="mutasi_lokasi_awal" name="lokasi_awal_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="">-- Pilih Lokasi Asal --</option>
                        @foreach($lokasis as $lok)
                            <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Lokasi Tujuan Baru *</label>
                    <select name="lokasi_baru_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        @foreach($lokasis as $lok)
                            <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pegawai Pemohon *</label>
                    <select name="karyawan_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($karyawans as $kry)
                            <option value="{{ $kry->id }}">{{ $kry->nama }} ({{ $kry->departemen }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Permohonan *</label>
                    <input type="date" name="tanggal_permohonan" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alasan Pemindahan / Mutasi *</label>
                <textarea name="alasan" required rows="2" placeholder="Contoh: Kebutuhan operasional unit kerja baru di lantai 2..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Kondisi Fisik Saat Dimutasi</label>
                <input type="text" name="keterangan" placeholder="Contoh: Fisik baik lengkap charger dan mouse..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahMutasi()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold shadow-md shadow-purple-600/20 cursor-pointer">Ajukan Permohonan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahMutasi() {
        document.getElementById('modalTambahMutasi').classList.remove('hidden');
    }
    function closeModalTambahMutasi() {
        document.getElementById('modalTambahMutasi').classList.add('hidden');
    }

    function handleSelectAssetMutasi(select) {
        const option = select.options[select.selectedIndex];
        const lokasiId = option.getAttribute('data-lokasi-id');
        if (lokasiId) {
            document.getElementById('mutasi_lokasi_awal').value = lokasiId;
        }
    }
</script>
@endsection
