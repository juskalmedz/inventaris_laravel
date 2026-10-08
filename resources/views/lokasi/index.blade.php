@extends('layouts.app')

@section('title', 'Master Ruangan & Lokasi - Inventaris Kantor')
@section('page_title', 'Master Ruangan & Lokasi Fisik')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                    Master Data
                </span>
                <span class="text-xs text-slate-400 font-mono">Penempatan Fisik Aset</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Master Lokasi & Ruangan Fisik</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pemetaan gedung, lantai, ruangan kerja operasional, dan PIC penanggung jawab fisik aset kantor.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahLokasi()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-xl font-bold text-sm shadow-md shadow-rose-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Lokasi Baru
            </button>
        </div>
    </div>

    <!-- KPI / Ringkasan Lokasi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Ruangan / Area</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $lokasis->total() ?? count($lokasis) }}</h4>
                <span class="text-xs text-rose-600 font-semibold">Titik Lokasi Terdata</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Aset Ditempatkan</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ $lokasis->sum('inventaris_count') }}
                </h4>
                <span class="text-xs text-blue-600 font-semibold">Unit di Berbagai Ruang</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Penanggung Jawab</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">
                    {{ $lokasis->whereNotNull('penanggung_jawab')->count() }} Ruangan
                </h4>
                <span class="text-xs text-emerald-600 font-semibold">Tersedia PIC Jelas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('lokasi.index') }}" class="flex-1 w-full flex items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama lokasi, kode lokasi, penanggung jawab, gedung..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:outline-none dark:text-white"
                />
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('lokasi.index') }}" class="px-3 py-2.5 text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Grid Kartu Lokasi Modern -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($lokasis as $lok)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md hover:border-rose-500/40 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="flex items-start justify-between gap-3 mb-3">
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60">
                        {{ $lok->kode_lokasi }}
                    </span>
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                </div>

                <h3 class="font-black text-base text-slate-900 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 transition">
                    {{ $lok->nama_lokasi }}
                </h3>

                <div class="mt-3 space-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>PIC: <strong class="text-slate-700 dark:text-slate-300">{{ $lok->penanggung_jawab ?? 'Belum Ditentukan' }}</strong></span>
                    </div>
                    @if($lok->keterangan)
                    <div class="flex items-start gap-2 text-[11px] text-slate-400">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400 mt-0.5 shrink-0"></i>
                        <span class="line-clamp-2">{{ $lok->keterangan }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="text-xs">
                    <span class="text-slate-400">Aset Disimpan:</span>
                    <strong class="text-slate-900 dark:text-white ml-1 font-mono">{{ $lok->inventaris_count ?? 0 }} item</strong>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        onclick="openModalEditLokasi({{ json_encode($lok) }})"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                        title="Edit Lokasi"
                    >
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </button>

                    <form method="POST" action="{{ route('lokasi.destroy', $lok->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                            title="Hapus Lokasi"
                        >
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
            <i data-lucide="map-pin" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak ada ruangan/lokasi yang ditemukan</p>
            <p class="text-xs text-slate-400 mt-0.5">Tambahkan lokasi ruangan baru untuk mencatat letak fisik inventaris.</p>
        </div>
        @endforelse
    </div>

    @if(method_exists($lokasis, 'links'))
        <div class="pt-4">
            {{ $lokasis->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Lokasi -->
<div id="modalTambahLokasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="map-pin" class="w-5 h-5 text-rose-500"></i>
                Tambah Ruangan / Lokasi Fisik
            </h3>
            <button type="button" onclick="closeModalTambahLokasi()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('lokasi.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Lokasi *</label>
                <input type="text" name="kode_lokasi" required placeholder="Contoh: R-IT-01, GDG-A, R-MEET-2" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none uppercase" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lokasi / Ruangan *</label>
                <input type="text" name="nama_lokasi" required placeholder="Contoh: Ruang Server IT & Jaringan" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Penanggung Jawab (PIC)</label>
                <input type="text" name="penanggung_jawab" placeholder="Nama staf PIC ruangan..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Gedung / Lantai</label>
                <textarea name="keterangan" rows="3" placeholder="Gedung Utama, Lantai 2, Sayap Barat..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahLokasi()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20 cursor-pointer">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Lokasi -->
<div id="modalEditLokasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-rose-500"></i>
                Edit Ruangan / Lokasi Fisik
            </h3>
            <button type="button" onclick="closeModalEditLokasi()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditLokasi" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Lokasi *</label>
                <input type="text" id="edit_kode_lokasi" name="kode_lokasi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none uppercase" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lokasi / Ruangan *</label>
                <input type="text" id="edit_nama_lokasi" name="nama_lokasi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Penanggung Jawab (PIC)</label>
                <input type="text" id="edit_penanggung_jawab" name="penanggung_jawab" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Gedung / Lantai</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalEditLokasi()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20 cursor-pointer">Perbarui Lokasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahLokasi() {
        document.getElementById('modalTambahLokasi').classList.remove('hidden');
    }
    function closeModalTambahLokasi() {
        document.getElementById('modalTambahLokasi').classList.add('hidden');
    }

    function openModalEditLokasi(data) {
        const form = document.getElementById('formEditLokasi');
        form.action = '/lokasi/' + data.id;
        document.getElementById('edit_kode_lokasi').value = data.kode_lokasi || '';
        document.getElementById('edit_nama_lokasi').value = data.nama_lokasi || '';
        document.getElementById('edit_penanggung_jawab').value = data.penanggung_jawab || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';
        document.getElementById('modalEditLokasi').classList.remove('hidden');
    }
    function closeModalEditLokasi() {
        document.getElementById('modalEditLokasi').classList.add('hidden');
    }
</script>
@endsection
