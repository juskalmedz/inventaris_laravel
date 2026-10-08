@extends('layouts.app')

@section('title', 'Master Kategori Aset - Inventaris Kantor')
@section('page_title', 'Master Kategori Inventaris')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                    Master Data
                </span>
                <span class="text-xs text-slate-400 font-mono">Klasifikasi Kelompok Aset</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Master Kategori Inventaris</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pengelompokan jenis barang fisik, kode prefix penomoran aset, dan asosiasi katalog inventaris.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahKategori()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Kategori Baru
            </button>
        </div>
    </div>

    <!-- KPI / Ringkasan Kategori -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kategori</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $kategoris->total() ?? count($kategoris) }}</h4>
                <span class="text-xs text-amber-600 font-semibold">Grup Terdaftar</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Aset Terafiliasi</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ $kategoris->sum('inventaris_count') }}
                </h4>
                <span class="text-xs text-blue-600 font-semibold">Unit Barang</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="boxes" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Validitas</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">100%</h4>
                <span class="text-xs text-emerald-600 font-semibold">Terkatalogisasi</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('kategori.index') }}" class="flex-1 w-full flex items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari kode kategori, nama kategori, keterangan..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none dark:text-white"
                />
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('kategori.index') }}" class="px-3 py-2.5 text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Grid Kartu Kategori Modern -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($kategoris as $kat)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md hover:border-amber-500/40 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="flex items-start justify-between gap-3 mb-3">
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/60">
                        {{ $kat->kode_kategori }}
                    </span>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                    </div>
                </div>

                <h3 class="font-black text-base text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">
                    {{ $kat->nama_kategori }}
                </h3>
                <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                    {{ $kat->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                </p>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="text-xs">
                    <span class="text-slate-400">Total Aset:</span>
                    <strong class="text-slate-900 dark:text-white ml-1 font-mono">{{ $kat->inventaris_count ?? 0 }} item</strong>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        onclick="openModalEditKategori({{ json_encode($kat) }})"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition cursor-pointer"
                        title="Edit Kategori"
                    >
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </button>

                    <form method="POST" action="{{ route('kategori.destroy', $kat->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                            title="Hapus Kategori"
                        >
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
            <i data-lucide="tag" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak ada kategori inventaris yang ditemukan</p>
            <p class="text-xs text-slate-400 mt-0.5">Tambahkan kategori baru untuk mulai mengklasifikasikan aset kantor.</p>
        </div>
        @endforelse
    </div>

    @if(method_exists($kategoris, 'links'))
        <div class="pt-4">
            {{ $kategoris->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Kategori -->
<div id="modalTambahKategori" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="tag" class="w-5 h-5 text-amber-500"></i>
                Tambah Kategori Aset
            </h3>
            <button type="button" onclick="closeModalTambahKategori()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('kategori.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Kategori *</label>
                <input type="text" name="kode_kategori" required placeholder="Contoh: KAT-TI, KAT-MEB, KAT-KND" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none uppercase" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Kategori *</label>
                <input type="text" name="nama_kategori" required placeholder="Contoh: Perangkat IT & Komputer" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Deskripsi</label>
                <textarea name="keterangan" rows="3" placeholder="Deskripsi peruntukan atau jenis aset dalam kelompok ini..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahKategori()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold shadow-md shadow-amber-600/20 cursor-pointer">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modalEditKategori" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-amber-500"></i>
                Edit Data Kategori
            </h3>
            <button type="button" onclick="closeModalEditKategori()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditKategori" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Kategori *</label>
                <input type="text" id="edit_kode_kategori" name="kode_kategori" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none uppercase" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Kategori *</label>
                <input type="text" id="edit_nama_kategori" name="nama_kategori" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Deskripsi</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalEditKategori()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold shadow-md shadow-amber-600/20 cursor-pointer">Perbarui Kategori</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahKategori() {
        document.getElementById('modalTambahKategori').classList.remove('hidden');
    }
    function closeModalTambahKategori() {
        document.getElementById('modalTambahKategori').classList.add('hidden');
    }

    function openModalEditKategori(data) {
        const form = document.getElementById('formEditKategori');
        form.action = '/kategori/' + data.id;
        document.getElementById('edit_kode_kategori').value = data.kode_kategori || '';
        document.getElementById('edit_nama_kategori').value = data.nama_kategori || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';
        document.getElementById('modalEditKategori').classList.remove('hidden');
    }
    function closeModalEditKategori() {
        document.getElementById('modalEditKategori').classList.add('hidden');
    }
</script>
@endsection
