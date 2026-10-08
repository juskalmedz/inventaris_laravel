@extends('layouts.app')

@section('title', 'Master Pegawai / Karyawan - Inventaris Kantor')
@section('page_title', 'Master Pegawai & Direktori PIC')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Master Data
                </span>
                <span class="text-xs text-slate-400 font-mono">Direktori Staf & Pemegang Aset</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Master Pegawai & PIC Aset</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan data karyawan, penugasan departemen, kontak WhatsApp darurat, dan pemantauan aset yang sedang dipinjam.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="openModalTambahKaryawan()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-bold text-sm shadow-md shadow-emerald-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Tambah Pegawai Baru
            </button>
        </div>
    </div>

    <!-- KPI / Ringkasan Karyawan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pegawai</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $totalKaryawan ?? count($karyawanList) }}</h4>
                <span class="text-xs text-emerald-600 font-semibold">Terdaftar Aktif</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Divisi Tercakup</p>
                <h4 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $totalDeptCount ?? count($departemens) }}</h4>
                <span class="text-xs text-indigo-600 font-semibold">Unit Organisasi</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 flex items-center justify-center">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Memegang Aset Pinjam</p>
                <h4 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5">{{ $activeBorrowersCount ?? 0 }}</h4>
                <span class="text-xs text-blue-600 font-semibold">Perlu Pemantauan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center">
                <i data-lucide="handshake" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bebas Pinjaman</p>
                <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ $freeBorrowersCount ?? 0 }}</h4>
                <span class="text-xs text-slate-500 font-semibold">Tidak Ada Tanggungan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('karyawan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-5 relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari NIP, nama pegawai, email..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:text-white"
                />
            </div>

            <div class="sm:col-span-3">
                <select name="departemen" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->nama_departemen }}" {{ request('departemen') == $dept->nama_departemen ? 'selected' : '' }}>
                            {{ $dept->nama_departemen }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="loan_status" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Status Pinjam</option>
                    <option value="dipinjam" {{ request('loan_status') === 'dipinjam' ? 'selected' : '' }}>Ada Pinjaman</option>
                    <option value="bebas" {{ request('loan_status') === 'bebas' ? 'selected' : '' }}>Bebas Pinjaman</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                    Filter
                </button>
                @if(request('search') || request('departemen') || request('loan_status'))
                    <a href="{{ route('karyawan.index') }}" class="px-2.5 py-2.5 text-xs text-rose-600 font-bold hover:underline" title="Reset filter">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Karyawan -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai & NIP</th>
                        <th class="px-5 py-3.5">Departemen & Jabatan</th>
                        <th class="px-5 py-3.5">Kontak WhatsApp & Email</th>
                        <th class="px-5 py-3.5 text-center">Aset Dipinjam</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($karyawanList as $item)
                    @php
                        $activeLoansCount = $item->peminjamans ? $item->peminjamans->where('status', 'Dipinjam')->count() : 0;
                        $cleanPhone = preg_replace('/[^0-9]/', '', $item->no_hp ?? '');
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                    {{ strtoupper(substr($item->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white">{{ $item->nama }}</div>
                                    <span class="font-mono text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-900/60">
                                        {{ $item->nip }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1 font-bold text-slate-800 dark:text-slate-200">
                                <i data-lucide="building-2" class="w-3.5 h-3.5 text-indigo-500"></i>
                                {{ $item->departemen }}
                            </span>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->jabatan ?? 'Staf Pelaksana' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if($item->no_hp)
                                    <a
                                        href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($item->nama) }}%2C%20informasi%20dari%20Sistem%20Inventaris%20Kantor."
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 font-bold hover:bg-emerald-100 transition"
                                        title="Kirim pesan WhatsApp"
                                    >
                                        <i data-lucide="message-square" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>{{ $item->no_hp }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1 truncate max-w-[200px]">{{ $item->email }}</div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($activeLoansCount > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    {{ $activeLoansCount }} Unit Dipinjam
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    Bebas
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button
                                    type="button"
                                    onclick="openModalEditKaryawan({{ json_encode($item) }})"
                                    class="p-2 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition cursor-pointer"
                                    title="Edit Data Pegawai"
                                >
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>

                                <form method="POST" action="{{ route('karyawan.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $item->nama }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                                        title="Hapus Pegawai"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-400">
                            <i data-lucide="users" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada pegawai yang sesuai filter</p>
                            <p class="text-[11px] text-slate-400">Silakan tambahkan data staf atau ubah kriteria pencarian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($karyawanList, 'links'))
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $karyawanList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div id="modalTambahKaryawan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="user-plus" class="w-5 h-5 text-emerald-500"></i>
                Tambah Pegawai / PIC Baru
            </h3>
            <button type="button" onclick="closeModalTambahKaryawan()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('karyawan.store') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor Induk Pegawai (NIP) *</label>
                    <input type="text" name="nip" required placeholder="Contoh: NIP-2026-001" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none uppercase" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama" required placeholder="Nama lengkap pegawai..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Departemen / Divisi *</label>
                    <select name="departemen" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Pilih Departemen</option>
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->nama_departemen }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jabatan Fungsional</label>
                    <input type="text" name="jabatan" placeholder="Contoh: Staf IT, Lead Designer..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alamat Email *</label>
                    <input type="email" name="email" required placeholder="pegawai@kantor.co.id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" placeholder="Contoh: 081234567890" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalTambahKaryawan()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer">Simpan Pegawai</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div id="modalEditKaryawan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-emerald-500"></i>
                Edit Data Pegawai
            </h3>
            <button type="button" onclick="closeModalEditKaryawan()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditKaryawan" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor Induk Pegawai (NIP) *</label>
                    <input type="text" id="edit_nip" name="nip" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none uppercase" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap *</label>
                    <input type="text" id="edit_nama" name="nama" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Departemen / Divisi *</label>
                    <select id="edit_departemen" name="departemen" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($departemens as $dept)
                            <option value="{{ $dept->nama_departemen }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jabatan Fungsional</label>
                    <input type="text" id="edit_jabatan" name="jabatan" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alamat Email *</label>
                    <input type="email" id="edit_email" name="email" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" id="edit_no_hp" name="no_hp" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalEditKaryawan()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer">Perbarui Data Pegawai</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahKaryawan() {
        document.getElementById('modalTambahKaryawan').classList.remove('hidden');
    }
    function closeModalTambahKaryawan() {
        document.getElementById('modalTambahKaryawan').classList.add('hidden');
    }

    function openModalEditKaryawan(data) {
        const form = document.getElementById('formEditKaryawan');
        form.action = '/karyawan/' + data.id;
        document.getElementById('edit_nip').value = data.nip || '';
        document.getElementById('edit_nama').value = data.nama || '';
        document.getElementById('edit_departemen').value = data.departemen || '';
        document.getElementById('edit_jabatan').value = data.jabatan || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_no_hp').value = data.no_hp || '';
        document.getElementById('modalEditKaryawan').classList.remove('hidden');
    }
    function closeModalEditKaryawan() {
        document.getElementById('modalEditKaryawan').classList.add('hidden');
    }
</script>
@endsection
