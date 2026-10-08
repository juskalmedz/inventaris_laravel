@extends('layouts.app')

@section('title', 'Pengaturan & Manajemen RBAC - Inventaris Kantor')
@section('page_title', 'Pengaturan Sistem & Hak Akses (RBAC)')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                    Keamanan & Administrasi
                </span>
                <span class="text-xs text-slate-400 font-mono">Multi-Role RBAC & Master Config</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Pengaturan Sistem & Hak Akses (RBAC)</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola akun multi-role (Admin, Supervisor, Staff, Auditor), matriks izin operasional, KOP surat resmi, dan gateway WhatsApp.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <button
                type="button"
                onclick="openModalAddUser()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
            <a
                href="/api/v1/laravel/download-zip"
                download="laravel13-inventaris-aset-kantor-lengkap.zip"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-2xs"
            >
                <i data-lucide="download" class="w-4 h-4 text-slate-500"></i>
                <span>Unduh Proyek (ZIP)</span>
            </a>
        </div>
    </div>

    <!-- TAB NAVIGASI PENGATURAN (1:1 Mirip React SettingsAndRbac) -->
    <div class="bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center gap-1.5 overflow-x-auto text-xs font-bold">
        <button type="button" onclick="switchSettingsTab('users')" id="tabBtn_users" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 bg-rose-600 text-white shadow-sm cursor-pointer">
            <i data-lucide="users" class="w-4 h-4"></i>
            <span>1. Manajemen Akun Pengguna</span>
        </button>
        <button type="button" onclick="switchSettingsTab('rbac')" id="tabBtn_rbac" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>2. Matriks Perizinan RBAC</span>
        </button>
        <button type="button" onclick="switchSettingsTab('kop')" id="tabBtn_kop" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
            <i data-lucide="building-2" class="w-4 h-4"></i>
            <span>3. Identitas KOP Surat</span>
        </button>
        <button type="button" onclick="switchSettingsTab('wa')" id="tabBtn_wa" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
            <i data-lucide="message-square" class="w-4 h-4"></i>
            <span>4. Gateway WhatsApp Reminder</span>
        </button>
        <button type="button" onclick="switchSettingsTab('reset')" id="tabBtn_reset" class="px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
            <span>5. Reset & Pemulihan Sistem</span>
        </button>
    </div>

    <!-- TAB 1: MANAJEMEN AKUN PENGGUNA -->
    <div id="settingsTab_users" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-rose-600"></i>
                        Daftar Akun Pengguna Terdaftar
                    </h3>
                    <p class="text-xs text-slate-500">Setiap pengguna memiliki peran dengan hak akses bertingkat dan audit logging terpusat.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                    Total: {{ count($users) }} Akun
                </span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 font-bold uppercase text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-3.5">Kode User</th>
                            <th class="p-3.5">Nama & Profil</th>
                            <th class="p-3.5">Username</th>
                            <th class="p-3.5">Peran (Role)</th>
                            <th class="p-3.5">Hak Akses Utama</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @foreach($users as $u)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="p-3.5 font-mono font-bold text-slate-900 dark:text-white">{{ $u->kode_user }}</td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-600 to-red-500 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $u->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 font-mono text-slate-600 dark:text-slate-400">{{ '@' . $u->username }}</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $u->role === 'Admin' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300' : ($u->role === 'Supervisor' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300' : ($u->role === 'Staff' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300')) }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="p-3.5 text-[11px] text-slate-500">
                                @if($u->role === 'Admin')
                                    <span class="text-rose-600 font-bold">Akses Penuh (Full Control & Approval)</span>
                                @elseif($u->role === 'Supervisor')
                                    <span class="text-blue-600 font-bold">Persetujuan Mutasi & Peminjaman</span>
                                @elseif($u->role === 'Staff')
                                    <span class="text-emerald-600 font-bold">Entri Transaksi & Sirkulasi Logistik</span>
                                @else
                                    <span class="text-amber-600 font-bold">Read-Only, Verifikasi & Cetak Laporan</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1 {{ $u->status === 'Aktif' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'Aktif' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                    {{ $u->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        type="button"
                                        onclick="openModalResetPassword({{ $u->id }}, '{{ $u->name }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Reset Kata Sandi"
                                    >
                                        <i data-lucide="key" class="w-4 h-4"></i>
                                    </button>
                                    @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('pengaturan.deleteUser', $u->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                                            title="Hapus Akun Pengguna"
                                        >
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: MATRIKS PERIZINAN RBAC (1:1 Mirip React Matrix) -->
    <div id="settingsTab_rbac" class="space-y-6 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
            <div>
                <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-indigo-600"></i>
                    Matriks Perizinan Peran (Role-Based Access Control)
                </h3>
                <p class="text-xs text-slate-500">Tabel pembagian hak wewenang akses 10 modul sistem untuk masing-masing peran secara granular.</p>
            </div>

            <!-- Matriks Tabel Peran -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 font-bold uppercase text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-3.5">Modul & Hak Akses</th>
                            <th class="p-3.5 text-center text-rose-600">Admin (Penuh)</th>
                            <th class="p-3.5 text-center text-blue-600">Supervisor</th>
                            <th class="p-3.5 text-center text-emerald-600">Staff Logistik</th>
                            <th class="p-3.5 text-center text-amber-600">Auditor (Read)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @php
                            $matrixPerms = [
                                ['group' => 'Dashboard', 'title' => 'Melihat Statistik & KPI Dashboard', 'a' => true, 's' => true, 'st' => true, 'au' => true],
                                ['group' => 'Inventaris', 'title' => 'Melihat Katalog & Detail Aset', 'a' => true, 's' => true, 'st' => true, 'au' => true],
                                ['group' => 'Inventaris', 'title' => 'Tambah, Edit & Hapus Aset', 'a' => true, 's' => true, 'st' => false, 'au' => false],
                                ['group' => 'Organisasi', 'title' => 'Kelola Karyawan (PIC) & Divisi', 'a' => true, 's' => true, 'st' => false, 'au' => false],
                                ['group' => 'Sirkulasi', 'title' => 'Entri Barang Masuk & Keluar', 'a' => true, 's' => true, 'st' => true, 'au' => false],
                                ['group' => 'Peminjaman', 'title' => 'Pengajuan & Verifikasi Pinjam', 'a' => true, 's' => true, 'st' => true, 'au' => false],
                                ['group' => 'Peminjaman', 'title' => 'Persetujuan (Approval) Pinjam', 'a' => true, 's' => true, 'st' => false, 'au' => false],
                                ['group' => 'Mutasi', 'title' => 'Pengajuan Pemindahan Aset', 'a' => true, 's' => true, 'st' => true, 'au' => false],
                                ['group' => 'Mutasi', 'title' => 'Approval & Eksekusi Lokasi BAST', 'a' => true, 's' => true, 'st' => false, 'au' => false],
                                ['group' => 'Maintenance', 'title' => 'Buat Tiket Servis & Selesaikan', 'a' => true, 's' => true, 'st' => false, 'au' => false],
                                ['group' => 'Laporan', 'title' => 'Akses Laporan & Export Excel', 'a' => true, 's' => true, 'st' => false, 'au' => true],
                                ['group' => 'QR Studio', 'title' => 'Desain & Cetak Stiker QR', 'a' => true, 's' => true, 'st' => true, 'au' => true],
                                ['group' => 'Keamanan', 'title' => 'Manajemen Akun & Role RBAC', 'a' => true, 's' => false, 'st' => false, 'au' => false],
                                ['group' => 'Audit Log', 'title' => 'Melihat Rekam Jejak Audit Trail', 'a' => true, 's' => true, 'st' => false, 'au' => true],
                            ];
                        @endphp
                        @foreach($matrixPerms as $mp)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="p-3.5">
                                <span class="text-[10px] uppercase font-bold text-slate-400 mr-2">[{{ $mp['group'] }}]</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $mp['title'] }}</span>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="inline-flex w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 items-center justify-center font-bold text-xs">✓</span>
                            </td>
                            <td class="p-3.5 text-center">
                                @if($mp['s'])
                                <span class="inline-flex w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 items-center justify-center font-bold text-xs">✓</span>
                                @else
                                <span class="inline-flex w-5 h-5 rounded-full bg-slate-100 text-slate-400 items-center justify-center font-bold text-xs">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                @if($mp['st'])
                                <span class="inline-flex w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 items-center justify-center font-bold text-xs">✓</span>
                                @else
                                <span class="inline-flex w-5 h-5 rounded-full bg-slate-100 text-slate-400 items-center justify-center font-bold text-xs">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                @if($mp['au'])
                                <span class="inline-flex w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 items-center justify-center font-bold text-xs">✓</span>
                                @else
                                <span class="inline-flex w-5 h-5 rounded-full bg-slate-100 text-slate-400 items-center justify-center font-bold text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: IDENTITAS KOP SURAT RESMI -->
    <div id="settingsTab_kop" class="space-y-6 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="building-2" class="w-5 h-5 text-indigo-600"></i>
                        Identitas KOP Surat Resmi & Tanda Tangan
                    </h3>
                    <p class="text-xs text-slate-500">KOP surat otomatis disematkan pada setiap cetak Berita Acara, Laporan Rekapitulasi, dan Label QR Code.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pengaturan.updateKop') }}" class="space-y-4 text-xs font-medium">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Organisasi / Kementerian *</label>
                        <input type="text" name="org_name" value="{{ $kopConfig->org_name ?? 'KEMENTERIAN KOMUNIKASI DAN INFORMATIKA RI' }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Direktorat / Divisi *</label>
                        <input type="text" name="div_name" value="{{ $kopConfig->div_name ?? 'DIREKTORAT JENDERAL SUMBER DAYA & PERANGKAT POS INFORMATIKA' }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nomor Surat Berita Acara *</label>
                        <input type="text" name="nomor_surat" value="{{ $kopConfig->nomor_surat ?? 'BA-INV/2026/X/001' }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Alamat Kantor Lengkap</label>
                        <input type="text" name="alamat" value="{{ $kopConfig->alamat ?? 'Jl. Medan Merdeka Barat No. 17, Gambir, Jakarta Pusat 10110' }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 font-bold text-slate-900 dark:text-white">
                    Pihak 1: Petugas Pembuat / Pengurus Aset
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jabatan *</label>
                        <input type="text" name="maker_title" value="{{ $kopConfig->maker_title ?? 'Petugas Pengelola Inventaris & Logistik' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap & Gelar *</label>
                        <input type="text" name="maker_name" value="{{ $kopConfig->maker_name ?? 'Bambang Pratama, S.Kom.' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP Pegawai *</label>
                        <input type="text" name="maker_nip" value="{{ $kopConfig->maker_nip ?? 'NIP. 19880422 201101 1 005' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 font-bold text-slate-900 dark:text-white">
                    Pihak 2: Pejabat Mengetahui / Pimpinan
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jabatan Pimpinan *</label>
                        <input type="text" name="approver_title" value="{{ $kopConfig->approver_title ?? 'Kepala Bagian Umum & Perlengkapan' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap & Gelar *</label>
                        <input type="text" name="approver_name" value="{{ $kopConfig->approver_name ?? 'Drs. Bambang Hariyanto, M.M.' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">NIP Pimpinan *</label>
                        <input type="text" name="approver_nip" value="{{ $kopConfig->approver_nip ?? 'NIP. 19750812 199903 1 002' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20 cursor-pointer">
                        Simpan Perubahan KOP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 4: WHATSAPP GATEWAY REMINDER -->
    <div id="settingsTab_wa" class="space-y-6 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
            <div>
                <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="message-square" class="w-5 h-5 text-emerald-600"></i>
                    Konfigurasi WhatsApp Gateway Reminder
                </h3>
                <p class="text-xs text-slate-500">Integrasi API WhatsApp untuk pengiriman otomatis notifikasi jatuh tempo pengembalian aset inventaris.</p>
            </div>

            <form method="POST" action="{{ route('pengaturan.updateWa') }}" class="space-y-4 text-xs font-medium">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Provider WhatsApp API *</label>
                        <select name="provider" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                            <option value="fonnte">Fonnte WhatsApp API Gateway</option>
                            <option value="wablas">Wablas WhatsApp Provider</option>
                            <option value="custom">Custom Webhook Dispatcher</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">API Token / Secret Key *</label>
                        <input type="password" name="api_token" value="demo_fonnte_token_8921" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Waktu Notifikasi (Hari Sebelum Jatuh Tempo)</label>
                    <input type="number" name="reminder_days_before" min="1" max="14" value="2" class="w-48 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                    <span class="text-[10px] text-slate-400 mt-1 block">Sistem akan menyusun notifikasi peringatan 2 hari sebelum masa pinjam aset berakhir.</span>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer">
                        Simpan Pengaturan WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 5: RESET & PEMULIHAN SISTEM -->
    <div id="settingsTab_reset" class="space-y-6 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-rose-200 dark:border-rose-900/60 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 flex items-center justify-center shadow-xs">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <h3 class="font-black text-lg text-rose-600 dark:text-rose-400">Pemulihan & Reset Basis Data Pabrikan</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Tindakan ini akan mengembalikan seluruh tabel database inventaris, transaksi masuk, keluar, sirkulasi pinjam, tiket servis, dan mutasi ke dataset bawaan resmi (Seeder).
                </p>
            </div>

            <form method="POST" action="{{ route('pengaturan.resetData') }}" onsubmit="return confirm('PERINGATAN KERAS: Apakah Anda yakin ingin mereset seluruh database inventaris ke kondisi awal pabrikan?');">
                @csrf
                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 cursor-pointer flex items-center gap-2"
                >
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Eksekusi Reset Data Sekarang</span>
                </button>
            </form>
        </div>
    </div>

</div>

<!-- MODAL TAMBAH PENGGUNA BARU (1:1 Mirip React UserModal) -->
<div id="modalAddUser" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="user-plus" class="w-5 h-5 text-rose-600"></i>
                Tambah Akun Pengguna Baru
            </h3>
            <button type="button" onclick="closeModalAddUser()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('pengaturan.storeUser') }}" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Nama Lengkap Pegawai *</label>
                <input type="text" name="name" required placeholder="Contoh: Sarah Anindita, S.Kom." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none" />
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Username Login *</label>
                <input type="text" name="username" required placeholder="sarah_logistik" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-rose-500 focus:outline-none" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Peran (Role) *</label>
                    <select name="role" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Staff">Staff Logistik</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Auditor">Auditor (Read)</option>
                        <option value="Admin">Administrator (Full)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Status Akun *</label>
                    <select name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kata Sandi Awal *</label>
                <input type="password" name="password" required value="password" placeholder="Kata sandi..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono" />
                <span class="text-[10px] text-slate-400 mt-1 block">Bawaan: <code class="font-mono text-rose-500">password</code></span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalAddUser()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-bold shadow-md shadow-rose-600/20 cursor-pointer">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL RESET PASSWORD PENGGUNA -->
<div id="modalResetPassword" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-sm p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="key" class="w-5 h-5 text-amber-500"></i>
                Reset Kata Sandi
            </h3>
            <button type="button" onclick="closeModalResetPassword()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formResetPassword" method="POST" action="" class="space-y-4 text-xs font-medium">
            @csrf
            <div>
                <p class="text-slate-500 mb-2">Reset kata sandi akun untuk: <strong id="resetUserName" class="text-slate-900 dark:text-white"></strong></p>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kata Sandi Baru *</label>
                <input type="password" name="password" required value="password" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500 focus:outline-none" />
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModalResetPassword()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold shadow-md shadow-amber-600/20 cursor-pointer">Reset Sandi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchSettingsTab(tab) {
        ['users', 'rbac', 'kop', 'wa', 'reset'].forEach(t => {
            const btn = document.getElementById('tabBtn_' + t);
            const panel = document.getElementById('settingsTab_' + t);
            if (t === tab) {
                btn.className = 'px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 bg-rose-600 text-white shadow-sm cursor-pointer';
                panel.classList.remove('hidden');
            } else {
                btn.className = 'px-4 py-2 rounded-xl whitespace-nowrap transition flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer';
                panel.classList.add('hidden');
            }
        });
        lucide.createIcons();
    }

    function openModalAddUser() {
        document.getElementById('modalAddUser').classList.remove('hidden');
    }
    function closeModalAddUser() {
        document.getElementById('modalAddUser').classList.add('hidden');
    }

    function openModalResetPassword(id, name) {
        document.getElementById('formResetPassword').action = '/pengaturan/users/' + id + '/reset-password';
        document.getElementById('resetUserName').innerText = name;
        document.getElementById('modalResetPassword').classList.remove('hidden');
    }
    function closeModalResetPassword() {
        document.getElementById('modalResetPassword').classList.add('hidden');
    }
</script>
@endsection
