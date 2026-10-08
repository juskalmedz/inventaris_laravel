@extends('layouts.app')

@section('title', 'Peminjaman & Pengembalian Aset - Inventaris Kantor')
@section('page_title', 'Peminjaman & Sirkulasi Aset')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                    Sirkulasi & Logistik
                </span>
                <span class="text-xs text-slate-400 font-mono">1:1 Sinkronisasi Peminjaman & WhatsApp</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Peminjaman & Pengembalian Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pinjam-pakai aset operasional kantor (laptop, proyektor, kamera, kendaraan) dengan monitoring jatuh tempo dan pengembalian stok.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahPinjam()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="handshake" class="w-4 h-4"></i>
                Buat Permohonan Pinjam Aset
            </button>
        </div>
    </div>

    <!-- KPI / Ringkasan Peminjaman -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tiket Pinjam</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $loans->total() ?? count($loans) }}</h4>
                <span class="text-xs text-slate-500">Semua Catatan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sedang Dipinjam</p>
                <h4 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5">
                    {{ $loans->where('status', 'Dipinjam')->count() }} Unit
                </h4>
                <span class="text-xs text-blue-600 font-semibold">Aktif di Lapangan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sudah Kembali</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $loans->where('status', 'Dikembalikan')->count() }} Unit
                </h4>
                <span class="text-xs text-emerald-600 font-semibold">Stok Dipulihkan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Menunggu Persetujuan</p>
                <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">
                    {{ $loans->where('status', 'Menunggu Approval')->count() }}
                </h4>
                <span class="text-xs text-amber-600 font-semibold">Perlu Approval</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Data Peminjaman -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">No. Pinjam & Tgl</th>
                        <th class="px-5 py-3.5">Aset / Barang</th>
                        <th class="px-5 py-3.5">Peminjam / PIC</th>
                        <th class="px-5 py-3.5">Periode & Jatuh Tempo</th>
                        <th class="px-5 py-3.5">Status Pinjaman</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Sirkulasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($loans as $item)
                    @php
                        $isOverdue = false;
                        if ($item->status === 'Dipinjam' && $item->tanggal_kembali) {
                            $isOverdue = \Carbon\Carbon::parse($item->tanggal_kembali)->isPast();
                        }
                        $cleanPhone = preg_replace('/[^0-9]/', '', $item->karyawan->no_hp ?? '');
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-900/60">
                                {{ $item->nomor_pinjam }}
                            </span>
                            <div class="text-[11px] text-slate-400 mt-1 font-semibold">
                                Tgl: {{ $item->tanggal_pinjam }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-slate-400 font-mono text-[11px]">{{ $item->inventaris->kode_barang ?? '-' }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 font-mono">({{ $item->jumlah }} Unit)</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $item->karyawan->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $item->karyawan->departemen ?? '-' }}</div>
                            @if($cleanPhone)
                                <a
                                    href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($item->karyawan->nama ?? '') }}%2C%20mengingatkan%20peminjaman%20aset%20{{ urlencode($item->inventaris->nama_barang ?? '') }}%20(No%3A%20{{ $item->nomor_pinjam }})%20jatuh%20tempo%20pada%20{{ $item->tanggal_kembali }}."
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 hover:underline mt-1"
                                    title="Kirim pengingat WhatsApp"
                                >
                                    <i data-lucide="message-square" class="w-3 h-3"></i>
                                    <span>Ingatkan WA</span>
                                </a>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-[11px]">
                            <div class="text-slate-500 dark:text-slate-400">Pinjam: {{ $item->tanggal_pinjam }}</div>
                            <div class="mt-0.5 font-bold {{ $isOverdue ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-200' }}">
                                Tempo: {{ $item->tanggal_kembali }}
                                @if($isOverdue)
                                    <span class="inline-block px-1.5 py-0.2 rounded bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[9px] font-black uppercase ml-1 animate-pulse">Overdue</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($item->status === 'Dipinjam')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    Dipinjam
                                </span>
                            @elseif($item->status === 'Dikembalikan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Dikembalikan
                                </span>
                                @if($item->kondisi_sesudah)
                                    <div class="text-[10px] text-slate-400 mt-0.5">Kondisi: {{ $item->kondisi_sesudah }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Approval
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($item->status === 'Menunggu Approval')
                                    <form method="POST" action="{{ route('peminjaman.approve', $item->id) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            onclick="return confirm('Setujui peminjaman ini? Stok aset di katalog akan otomatis dikurangi.')"
                                            class="px-2.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] shadow-xs transition cursor-pointer flex items-center gap-1"
                                            title="Setujui Peminjaman"
                                        >
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <span>Setujui</span>
                                        </button>
                                    </form>
                                @elseif($item->status === 'Dipinjam')
                                    <button
                                        type="button"
                                        onclick="openModalReturnPinjam({{ json_encode($item) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition cursor-pointer flex items-center gap-1"
                                        title="Proses Pengembalian Aset"
                                    >
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                        <span>Kembalikan</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400">
                            <i data-lucide="handshake" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada riwayat peminjaman aset</p>
                            <p class="text-[11px] text-slate-400">Buat permohonan pinjam aset untuk mencatat penggunaan operasional staf.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($loans, 'links'))
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Peminjaman -->
<div id="modalTambahPinjam" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="handshake" class="w-5 h-5 text-blue-500"></i>
                Buat Permohonan Pinjam Aset
            </h3>
            <button type="button" onclick="closeModalTambahPinjam()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('peminjaman.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pilih Barang / Aset Tersedia *</label>
                <select name="inventaris_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Pilih Barang Dari Katalog --</option>
                    @foreach($inventaris as $ast)
                        <option value="{{ $ast->id }}">
                            {{ $ast->kode_barang }} - {{ $ast->nama_barang }} (Sisa Stok: {{ $ast->stok }} Unit)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Pegawai Peminjam *</label>
                    <select name="karyawan_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($karyawans as $kry)
                            <option value="{{ $kry->id }}">{{ $kry->nama }} ({{ $kry->departemen }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jumlah Unit Dipinjam *</label>
                    <input type="number" name="jumlah" required min="1" value="1" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Mulai Pinjam *</label>
                    <input type="date" name="tanggal_pinjam" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tanggal Rencana Kembali (Tempo) *</label>
                    <input type="date" name="tanggal_kembali" required value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keperluan / Tujuan Pinjam *</label>
                <textarea name="keperluan" required rows="2" placeholder="Contoh: Kegiatan presentasi rapat koordinasi dinas luar kota..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Catatan Tambahan / Aksesoris Kelengkapan</label>
                <input type="text" name="catatan" placeholder="Contoh: Termasuk tas laptop, charger original, dan dongle HDMI" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahPinjam()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold shadow-md shadow-blue-600/20 cursor-pointer">Simpan Permohonan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pengembalian Aset -->
<div id="modalReturnPinjam" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="w-5 h-5 text-emerald-500"></i>
                Pengembalian Aset Fisik
            </h3>
            <button type="button" onclick="closeModalReturnPinjam()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formReturnPinjam" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kondisi Fisik Saat Dikembalikan *</label>
                <select name="kondisi_sesudah" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="Baik">✅ Baik (Utuh, Berfungsi Normal, Lengkap)</option>
                    <option value="Rusak Ringan">⚠️ Rusak Ringan (Perlu Pembersihan / Servis Kecil)</option>
                    <option value="Rusak Berat">❌ Rusak Berat (Mati Total / Ada Komponen Rusak)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Catatan Penerimaan Kembali</label>
                <textarea name="catatan" rows="3" placeholder="Contoh: Diterima kembali lengkap dan telah dicek fungsionalitasnya..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalReturnPinjam()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer">Konfirmasi Pengembalian</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahPinjam() {
        document.getElementById('modalTambahPinjam').classList.remove('hidden');
    }
    function closeModalTambahPinjam() {
        document.getElementById('modalTambahPinjam').classList.add('hidden');
    }

    function openModalReturnPinjam(data) {
        const form = document.getElementById('formReturnPinjam');
        form.action = '/peminjaman/' + data.id + '/return';
        document.getElementById('modalReturnPinjam').classList.remove('hidden');
    }
    function closeModalReturnPinjam() {
        document.getElementById('modalReturnPinjam').classList.add('hidden');
    }
</script>
@endsection
