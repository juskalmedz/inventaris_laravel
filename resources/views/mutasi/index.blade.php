@extends('layouts.app')

@section('title', 'Mutasi & Pemindahan Aset - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mutasi & Pemindahan Aset Tetap</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pengalihan tanggung jawab fisik antar ruangan, departemen, dan alur persetujuan Berita Acara Serah Terima (BAST).</p>
        </div>
        <button onclick="document.getElementById('modalTambahMutasi').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium shadow-md shadow-indigo-500/20 text-sm transition-all">
            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
            Ajukan Mutasi Aset
        </button>
    </div>

    <!-- Tabel Mutasi -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No. Dokumen Mutasi</th>
                        <th class="px-6 py-4">Aset / Barang</th>
                        <th class="px-6 py-4">Departemen & Lokasi Asal</th>
                        <th class="px-6 py-4">Departemen & Lokasi Tujuan</th>
                        <th class="px-6 py-4">Status & Alasan</th>
                        <th class="px-6 py-4 text-right">Aksi & Approval</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                    @forelse($mutasis ?? [] as $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $item->nomor_mutasi }}
                            <div class="text-[11px] text-slate-400 font-normal">{{ $item->tanggal_permohonan ?? $item->tanggal_mutasi ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item->inventaris->nama_barang ?? '-' }}</div>
                            <div class="text-xs font-mono text-slate-400">{{ $item->inventaris->kode_barang ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div class="font-semibold text-slate-700 dark:text-slate-200">{{ $item->inventaris->lokasi->nama_lokasi ?? $item->lokasiAwal->nama_lokasi ?? '-' }}</div>
                            <div class="text-slate-400">📍 {{ $item->lokasiAwal->nama_lokasi ?? 'Lokasi Asal' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $item->departemenBaru->nama_departemen ?? 'Divisi Penerima' }}</div>
                            <div class="text-slate-400">📍 {{ $item->lokasiBaru->nama_lokasi ?? 'Lokasi Tujuan' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status === 'Disetujui')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Disetujui</span>
                            @elseif($item->status === 'Ditolak')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400">Ditolak</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400">Menunggu Approval</span>
                            @endif
                            <div class="text-xs text-slate-400 mt-1 truncate max-w-xs">{{ $item->alasan }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">👤 {{ $item->pemohon->nama ?? 'Staf Pemohon' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($item->status === 'Menunggu Approval' && (auth()->user()->isAdmin() || auth()->user()->isSupervisor()))
                            <form action="{{ route('mutasi.approve', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition cursor-pointer" onclick="return confirm('Setujui pengalihan lokasi aset ini?')">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('mutasi.reject', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition cursor-pointer" onclick="return confirm('Tolak pengajuan mutasi ini?')">
                                    Tolak
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada riwayat mutasi aset.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Mutasi -->
<div id="modalTambahMutasi" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <span>🔄 Ajukan Mutasi & Pemindahan Lokasi</span>
            </h3>
            <button onclick="document.getElementById('modalTambahMutasi').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl cursor-pointer">✕</button>
        </div>

        <form action="{{ route('mutasi.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Aset Inventaris yang Dipindahkan</label>
                <select name="inventaris_id" id="selMutasiInventaris" onchange="autoFillLokasiAwal()" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold">
                    <option value="">-- Pilih Aset --</option>
                    @foreach($inventaris ?? [] as $inv)
                        <option value="{{ $inv->id }}" data-lokasi="{{ $inv->lokasi_id }}">{{ $inv->kode_barang }} - {{ $inv->nama_barang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Lokasi Ruangan Asal</label>
                    <select name="lokasi_awal_id" id="selMutasiLokasiAwal" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold">
                        <option value="">-- Lokasi Asal --</option>
                        @foreach($lokasis ?? [] as $lok)
                            <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Lokasi Ruangan Baru / Tujuan</label>
                    <select name="lokasi_baru_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold">
                        <option value="">-- Lokasi Tujuan --</option>
                        @foreach($lokasis ?? [] as $lok)
                            <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Pegawai Pemohon / PIC</label>
                    <select name="karyawan_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($karyawans ?? [] as $kar)
                            <option value="{{ $kar->id }}">{{ $kar->nama }} ({{ $kar->nip }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Tanggal Permohonan</label>
                    <input type="date" name="tanggal_permohonan" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold">
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Alasan Permohonan Mutasi</label>
                <textarea name="alasan" rows="2" required placeholder="Contoh: Penempatan proyektor tetap di ruang rapat..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-medium"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalTambahMutasi').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-100 rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md shadow-indigo-500/20 cursor-pointer">Ajukan Mutasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function autoFillLokasiAwal() {
        const sel = document.getElementById('selMutasiInventaris');
        const opt = sel.options[sel.selectedIndex];
        const lokId = opt.getAttribute('data-lokasi');
        if (lokId) {
            document.getElementById('selMutasiLokasiAwal').value = lokId;
        }
    }
</script>
@endsection
