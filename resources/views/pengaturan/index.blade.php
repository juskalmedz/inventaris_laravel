@extends('layouts.app')

@section('title', 'Pengaturan & Manajemen RBAC - Inventaris Kantor')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pengaturan & Hak Akses (RBAC)</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Konfigurasi akun pengguna, matriks hak akses 4 peran (Admin, Supervisor, Staff, Auditor), KOP surat, dan WhatsApp gateway.</p>
        </div>
    </div>

    <!-- 1. Daftar Akun Pengguna -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-rose-600"></i>
            Manajemen Akun Pengguna (RBAC Multi-Role)
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="p-3">Kode User</th>
                        <th class="p-3">Nama Lengkap</th>
                        <th class="p-3">Username</th>
                        <th class="p-3">Peran (Role)</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($users as $u)
                    <tr>
                        <td class="p-3 font-mono text-xs font-bold">{{ $u->kode_user }}</td>
                        <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ $u->name }}</td>
                        <td class="p-3 text-slate-500">{{ '@' . $u->username }}</td>
                        <td class="p-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $u->role === 'Admin' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400' : ($u->role === 'Supervisor' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400' : ($u->role === 'Staff' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400')) }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $u->status === 'Aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                {{ $u->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Form KOP Surat Perusahaan -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="building-2" class="w-5 h-5 text-indigo-600"></i>
            Identitas KOP Surat Resmi Perusahaan
        </h3>
        <form method="POST" action="{{ route('pengaturan.updateKop') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Instansi / Perusahaan</label>
                <input type="text" name="nama_instansi" value="{{ $kopConfig->nama_instansi }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Kota / Domisili</label>
                <input type="text" name="kota" value="{{ $kopConfig->kota }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Alamat Lengkap</label>
                <input type="text" name="alamat" value="{{ $kopConfig->alamat }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">No. Telepon / Fax</label>
                <input type="text" name="telepon" value="{{ $kopConfig->telepon }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Email Resmi</label>
                <input type="email" name="email" value="{{ $kopConfig->email }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">PIC Penanggung Jawab</label>
                <input type="text" name="pic_penanggung_jawab" value="{{ $kopConfig->pic_penanggung_jawab }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Jabatan PIC</label>
                <input type="text" name="jabatan_pic" value="{{ $kopConfig->jabatan_pic }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm transition">
                    Simpan Identitas KOP
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Konfigurasi WhatsApp Reminder Gateway -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="message-square" class="w-5 h-5 text-emerald-600"></i>
            Konfigurasi Gateway WhatsApp Reminder
        </h3>
        <form method="POST" action="{{ route('pengaturan.updateWa') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Provider WhatsApp Gateway</label>
                <select name="provider" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    <option value="fonnte">Fonnte API Gateway</option>
                    <option value="wablas">Wablas Official</option>
                    <option value="custom">Custom Webhook Gateway</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Pengingat Otomatis (Hari Sebelum Jatuh Tempo)</label>
                <input type="number" name="reminder_days_before" value="2" min="1" max="14" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">API Token / Secret Key</label>
                <input type="password" name="api_token" value="fonnte_secret_token_live_xyz123" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition">
                    Simpan Konfigurasi WhatsApp
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
