<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Inventaris & Aset Kantor (Laravel 13)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 text-slate-800 dark:text-slate-100 font-sans flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-gradient-to-tr from-red-600 to-rose-500 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl shadow-lg shadow-red-500/30 font-black">
                📦
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">SISTEM INVENTARIS KANTOR</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Masuk untuk mengelola aset, sirkulasi stok, dan logistik kantor</p>
        </div>

        @if(session('error'))
            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Username Akun</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">👤</span>
                    <input
                        type="text"
                        name="username"
                        id="usernameInput"
                        value="{{ old('username', 'admin') }}"
                        required
                        placeholder="admin / supervisor / staff / auditor"
                        class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        value="password"
                        required
                        placeholder="Masukkan kata sandi"
                        class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-red-500 focus:outline-none"
                    />
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer"
                    >
                        👁️
                    </button>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Kata sandi demo bawaan: <b class="font-mono text-red-500">password</b></span>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-red-600 focus:ring-red-500" checked>
                    <span>Ingat saya di perangkat ini</span>
                </label>
                <span class="text-emerald-500 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Sistem Aktif
                </span>
            </div>

            <button
                type="submit"
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 active:scale-95 text-white font-black tracking-wide shadow-md shadow-red-600/30 transition-all cursor-pointer flex items-center justify-center gap-2"
            >
                <span>Masuk ke Sistem Inventaris</span>
                <span>&rarr;</span>
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
            <div class="flex items-center justify-between text-[11px]">
                <span class="font-bold text-slate-500 uppercase tracking-wider">Akses Cepat Demo (RBAC):</span>
                <span class="text-[10px] font-mono text-slate-400">Pilih Role</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                <button
                    type="button"
                    onclick="setQuickRole('admin', 'password')"
                    class="p-2 rounded-xl border border-red-200 dark:border-red-900 bg-red-50/50 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-900/50 transition cursor-pointer text-center group"
                >
                    <div class="font-bold text-red-600 dark:text-red-400 text-xs">Admin</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">@admin</div>
                </button>

                <button
                    type="button"
                    onclick="setQuickRole('supervisor', 'password')"
                    class="p-2 rounded-xl border border-amber-200 dark:border-amber-900 bg-amber-50/50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition cursor-pointer text-center group"
                >
                    <div class="font-bold text-amber-600 dark:text-amber-400 text-xs">Supervisor</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">@supervisor</div>
                </button>

                <button
                    type="button"
                    onclick="setQuickRole('staff', 'password')"
                    class="p-2 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50/50 dark:bg-emerald-950/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition cursor-pointer text-center group"
                >
                    <div class="font-bold text-emerald-600 dark:text-emerald-400 text-xs">Staff</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">@staff</div>
                </button>

                <button
                    type="button"
                    onclick="setQuickRole('auditor', 'password')"
                    class="p-2 rounded-xl border border-purple-200 dark:border-purple-900 bg-purple-50/50 dark:bg-purple-950/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition cursor-pointer text-center group"
                >
                    <div class="font-bold text-purple-600 dark:text-purple-400 text-xs">Auditor</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">@auditor</div>
                </button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const el = document.getElementById('passwordInput');
            el.type = el.type === 'password' ? 'text' : 'password';
        }

        function setQuickRole(user, pwd) {
            document.getElementById('usernameInput').value = user;
            document.getElementById('passwordInput').value = pwd;
        }
    </script>
</body>
</html>
