<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Kantor - Masuk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 flex items-center justify-center p-4 font-sans select-none antialiased">
    
    <!-- Ambient Glow Blobs -->
    <div class="fixed -top-32 -left-32 w-96 h-96 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-32 -right-32 w-96 h-96 bg-rose-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6 relative z-10">
        
        <!-- Header & Logo -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-gradient-to-tr from-red-600 to-rose-500 rounded-2xl mx-auto flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                <i data-lucide="boxes" class="w-7 h-7"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">SISTEM INVENTARIS KANTOR</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Masuk untuk mengelola aset, sirkulasi stok, dan logistik</p>
        </div>

        <!-- Alert Error Flash -->
        @if(session('error'))
            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900 text-emerald-700 dark:text-emerald-300 text-xs flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Username</label>
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
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
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
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
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                    >
                        <i id="eyeIcon" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Kata sandi demo: <b class="font-mono text-red-500">password</b></span>
            </div>

            <button
                type="submit"
                class="w-full py-2.5 px-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold rounded-xl shadow-md shadow-red-500/20 transition cursor-pointer flex items-center justify-center gap-2"
            >
                <span>Masuk ke Sistem</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <!-- Akses Cepat Uji Peran -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 text-center">Akses Cepat Uji Peran:</p>
            <div class="grid grid-cols-2 gap-2">
                <a
                    href="{{ route('quick.login', 'Admin') }}"
                    class="p-2 rounded-xl border border-red-200 dark:border-red-900/60 bg-red-50/70 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 font-bold text-[11px] flex items-center justify-center gap-1.5 transition cursor-pointer"
                >
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Admin (Full)
                </a>
                <a
                    href="{{ route('quick.login', 'Supervisor') }}"
                    class="p-2 rounded-xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/70 dark:bg-emerald-950/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-bold text-[11px] flex items-center justify-center gap-1.5 transition cursor-pointer"
                >
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Supervisor
                </a>
                <a
                    href="{{ route('quick.login', 'Staff') }}"
                    class="p-2 rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/70 dark:bg-blue-950/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-bold text-[11px] flex items-center justify-center gap-1.5 transition cursor-pointer"
                >
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Staff Logistik
                </a>
                <a
                    href="{{ route('quick.login', 'Auditor') }}"
                    class="p-2 rounded-xl border border-amber-200 dark:border-amber-900/60 bg-amber-50/70 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-bold text-[11px] flex items-center justify-center gap-1.5 transition cursor-pointer"
                >
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Auditor (Read)
                </a>
            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();

        function togglePassword() {
            const pwdInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                pwdInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        // Auto dark mode sync with local storage or system
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>
