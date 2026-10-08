@extends('layouts.app')

@section('title', 'Master Departemen & Divisi - Inventaris Kantor')
@section('page_title', 'Master Unit Kerja & Departemen')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300">
                    Master Data
                </span>
                <span class="text-xs text-slate-400 font-mono">Struktur Organisasi Kantor</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Master Departemen & Divisi</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pengelolaan unit kerja fungsional, kepala divisi / PIC pemegang aset, dan alokasi lantai operasional.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahDept()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Departemen Baru
            </button>
        </div>
    </div>

    <!-- KPI / Ringkasan Departemen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Unit Departemen</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $departemens->total() ?? count($departemens) }}</h4>
                <span class="text-xs text-indigo-600 font-semibold">Divisi Operasional</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 flex items-center justify-center">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kepala Divisi / PIC</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ $departemens->whereNotNull('kepala_divisi')->count() }} Orang
                </h4>
                <span class="text-xs text-purple-600 font-semibold">PIC Tertunjuk Aktif</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pegawai Staf</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ count($allKaryawan) }} Orang</h4>
                <span class="text-xs text-emerald-600 font-semibold">Tersebar di Unit Kerja</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('departemen.index') }}" class="flex-1 w-full flex items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama departemen, kode divisi, kepala divisi..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:text-white"
                />
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('departemen.index') }}" class="px-3 py-2.5 text-xs text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Grid Kartu Departemen Modern -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($departemens as $dept)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md hover:border-indigo-500/40 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="flex items-start justify-between gap-3 mb-3">
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900/60">
                        {{ $dept->kode_departemen }}
                    </span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                        <i data-lucide="building-2" class="w-4 h-4"></i>
                    </div>
                </div>

                <h3 class="font-black text-base text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                    {{ $dept->nama_departemen }}
                </h3>

                <div class="mt-3 space-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Kepala Divisi: <strong class="text-slate-700 dark:text-slate-300">{{ $dept->kepala_divisi ?? '-' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Lokasi: <span class="text-slate-600 dark:text-slate-300">{{ $dept->lokasi_lantai ?? 'Gedung Utama' }}</span></span>
                    </div>
                    @if($dept->keterangan)
                    <div class="flex items-start gap-2 text-[11px] text-slate-400 pt-0.5">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400 mt-0.5 shrink-0"></i>
                        <span class="line-clamp-2">{{ $dept->keterangan }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="text-xs">
                    <span class="text-slate-400">Pegawai Terkait:</span>
                    <strong class="text-slate-900 dark:text-white ml-1 font-mono">
                        {{ $allKaryawan->where('departemen', $dept->nama_departemen)->count() }} orang
                    </strong>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        onclick="openModalEditDept({{ json_encode($dept) }})"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition cursor-pointer"
                        title="Edit Departemen"
                    >
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </button>

                    <form method="POST" action="{{ route('departemen.destroy', $dept->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                            title="Hapus Departemen"
                        >
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
            <i data-lucide="building-2" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak ada departemen/divisi yang ditemukan</p>
            <p class="text-xs text-slate-400 mt-0.5">Tambahkan departemen baru untuk mengelola penanggung jawab aset tiap unit kerja.</p>
        </div>
        @endforelse
    </div>

    @if(method_exists($departemens, 'links'))
        <div class="pt-4">
            {{ $departemens->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Departemen -->
<div id="modalTambahDept" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="building-2" class="w-5 h-5 text-indigo-500"></i>
                Tambah Departemen Baru
            </h3>
            <button type="button" onclick="closeModalTambahDept()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('departemen.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Departemen *</label>
                <input type="text" name="kode_departemen" required placeholder="Contoh: D-IT, D-HRD, D-OPS, D-FIN" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Departemen *</label>
                <input type="text" name="nama_departemen" required placeholder="Contoh: Teknologi Informasi & Digital" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kepala Divisi / PIC *</label>
                <input type="text" name="kepala_divisi" required placeholder="Nama pimpinan unit kerja..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Lokasi Lantai / Ruang</label>
                <input type="text" name="lokasi_lantai" placeholder="Gedung Utama, Lantai 3..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Fungsi</label>
                <textarea name="keterangan" rows="2" placeholder="Tanggung jawab utama divisi..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahDept()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-md shadow-indigo-600/20 cursor-pointer">Simpan Departemen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Departemen -->
<div id="modalEditDept" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-indigo-500"></i>
                Edit Departemen / Divisi
            </h3>
            <button type="button" onclick="closeModalEditDept()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditDept" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kode Departemen *</label>
                <input type="text" id="edit_kode_dept" name="kode_departemen" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Departemen *</label>
                <input type="text" id="edit_nama_dept" name="nama_departemen" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kepala Divisi / PIC *</label>
                <input type="text" id="edit_kepala_divisi" name="kepala_divisi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Lokasi Lantai / Ruang</label>
                <input type="text" id="edit_lokasi_lantai" name="lokasi_lantai" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Keterangan / Fungsi</label>
                <textarea id="edit_keterangan" name="keterangan" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalEditDept()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-md shadow-indigo-600/20 cursor-pointer">Perbarui Departemen</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahDept() {
        document.getElementById('modalTambahDept').classList.remove('hidden');
    }
    function closeModalTambahDept() {
        document.getElementById('modalTambahDept').classList.add('hidden');
    }

    function openModalEditDept(data) {
        const form = document.getElementById('formEditDept');
        form.action = '/departemen/' + data.id;
        document.getElementById('edit_kode_dept').value = data.kode_departemen || '';
        document.getElementById('edit_nama_dept').value = data.nama_departemen || '';
        document.getElementById('edit_kepala_divisi').value = data.kepala_divisi || '';
        document.getElementById('edit_lokasi_lantai').value = data.lokasi_lantai || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';
        document.getElementById('modalEditDept').classList.remove('hidden');
    }
    function closeModalEditDept() {
        document.getElementById('modalEditDept').classList.add('hidden');
    }
</script>
@endsection
