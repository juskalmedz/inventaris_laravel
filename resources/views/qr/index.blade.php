@extends('layouts.app')

@section('title', 'Studio Label Stiker QR Code - Inventaris Kantor')
@section('page_title', 'Studio Pembuat Label QR Code')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300">
                    Studio Label & QR
                </span>
                <span class="text-xs text-slate-400 font-mono">1:1 Presisi Desain Stiker Fisik</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Studio Cetak Label Stiker QR Code</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Generator stiker identifikasi aset dengan QR Code densitas tinggi, logo instansi, dan tata letak siap cetak kertas A4 / Thermal.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button
                type="button"
                onclick="toggleSelectAll()"
                id="btnSelectAll"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-2xs cursor-pointer"
            >
                <i data-lucide="check-square" class="w-4 h-4 text-slate-500"></i>
                <span id="selectAllText">Pilih Semua ({{ count($items) }})</span>
            </button>
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 active:scale-95 transition cursor-pointer"
            >
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Label Terpilih</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Kustomisasi Studio Label (1:1 Mirip React QrLabelStudioView) -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4 no-print">
        
        <!-- Baris 1: Template, Grid Density & Warna -->
        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <!-- Pilihan Template Desain -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Template:</span>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" onclick="setStudioTemplate('standard')" id="btnTmpl_standard" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer">Standard</button>
                    <button type="button" onclick="setStudioTemplate('compact')" id="btnTmpl_compact" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">Compact</button>
                    <button type="button" onclick="setStudioTemplate('security')" id="btnTmpl_security" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">Security</button>
                    <button type="button" onclick="setStudioTemplate('warehouse')" id="btnTmpl_warehouse" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">Warehouse</button>
                </div>
            </div>

            <!-- Pilihan Kerapatan Grid Lembar Cetak -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Grid Lembar:</span>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" onclick="setGridDensity('3x7')" id="btnGrid_3x7" class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer">3x7 (21 A4)</button>
                    <button type="button" onclick="setGridDensity('2x5')" id="btnGrid_2x5" class="px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">2x5 (10 Besar)</button>
                    <button type="button" onclick="setGridDensity('roll')" id="btnGrid_roll" class="px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">Roll Thermal</button>
                </div>
            </div>

            <!-- Palet Warna Aksen -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Warna:</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="setThemeColor('#e11d48')" class="w-6 h-6 rounded-full bg-rose-600 ring-2 ring-offset-2 ring-rose-500 transition cursor-pointer" title="Crimson Rose"></button>
                    <button type="button" onclick="setThemeColor('#2563eb')" class="w-6 h-6 rounded-full bg-blue-600 hover:scale-110 transition cursor-pointer" title="Sapphire Blue"></button>
                    <button type="button" onclick="setThemeColor('#059669')" class="w-6 h-6 rounded-full bg-emerald-600 hover:scale-110 transition cursor-pointer" title="Emerald Green"></button>
                    <button type="button" onclick="setThemeColor('#475569')" class="w-6 h-6 rounded-full bg-slate-600 hover:scale-110 transition cursor-pointer" title="Dark Slate"></button>
                    <button type="button" onclick="setThemeColor('#7c3aed')" class="w-6 h-6 rounded-full bg-purple-600 hover:scale-110 transition cursor-pointer" title="Royal Purple"></button>
                </div>
            </div>
        </div>

        <!-- Baris 2: Cakupan Informasi Stiker -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Cakupan Informasi:</span>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" onclick="setContentScope('all')" id="btnScope_all" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer">
                        Lengkap (Kode+Nama)
                    </button>
                    <button type="button" onclick="setContentScope('code_only')" id="btnScope_code_only" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">
                        Kode Saja (QR+Kode)
                    </button>
                    <button type="button" onclick="setContentScope('name_only')" id="btnScope_name_only" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">
                        Nama Saja (QR+Nama)
                    </button>
                    <button type="button" onclick="setContentScope('qr_only')" id="btnScope_qr_only" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer">
                        Hanya QR (Micro)
                    </button>
                </div>
            </div>

            <!-- Toggles Mandiri -->
            <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="chkShowCode" checked onchange="updateStickerVisibility()" class="rounded text-indigo-600" />
                    <span>Kode Aset</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="chkShowName" checked onchange="updateStickerVisibility()" class="rounded text-indigo-600" />
                    <span>Nama Barang</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="chkShowHeader" checked onchange="updateStickerVisibility()" class="rounded text-indigo-600" />
                    <span>Kop Instansi</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="chkShowLoc" checked onchange="updateStickerVisibility()" class="rounded text-indigo-600" />
                    <span>Lokasi Ruang</span>
                </label>
            </div>
        </div>

        <!-- Filter Pencarian Aset -->
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input
                    type="text"
                    id="searchStickerInput"
                    onkeyup="filterStickers()"
                    placeholder="Cari nama aset, kode barang, atau lokasi untuk dicetak..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                />
            </div>
            <div class="text-xs text-slate-500 font-semibold shrink-0">
                Terpilih: <strong id="selectedCounter" class="text-indigo-600 dark:text-indigo-400 font-mono">{{ count($items) }}</strong> dari {{ count($items) }} aset
            </div>
        </div>

    </div>

    <!-- GRID STIKER LABEL SIAP CETAK (Presisi Desain 1:1 React) -->
    <div id="stickerCanvasContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 print:grid-cols-3 print:gap-2">
        @forelse($items as $item)
        <div
            class="sticker-card bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-3.5 flex flex-col justify-between shadow-2xs relative overflow-hidden transition group hover:border-indigo-500 cursor-pointer select-none"
            id="card_{{ $item->id }}"
            onclick="toggleItemSelection({{ $item->id }})"
            data-id="{{ $item->id }}"
            data-kode="{{ $item->kode_barang }}"
            data-nama="{{ $item->nama_barang }}"
            data-lokasi="{{ $item->lokasi->nama_lokasi ?? '-' }}"
        >
            <!-- Checkbox Pojok Kanan Atas -->
            <div class="absolute top-2 right-2 z-20 no-print">
                <input
                    type="checkbox"
                    id="chkItem_{{ $item->id }}"
                    checked
                    onclick="event.stopPropagation(); updateItemCount();"
                    class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                />
            </div>

            <!-- Top Accent Color Bar -->
            <div class="sticker-accent-bar absolute top-0 left-0 right-0 h-1 bg-rose-600 z-10"></div>

            <!-- Header Instansi Label -->
            <div class="sticker-header border-b border-slate-100 dark:border-slate-800 pb-1.5 mb-2 flex items-center justify-between pr-6">
                <span class="text-[9px] font-black uppercase text-slate-600 dark:text-slate-300 tracking-wider truncate">
                    {{ $kopConfig->org_name ?? 'INVENTARIS KANTOR' }}
                </span>
                <span class="text-[8px] font-mono font-bold px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500">
                    ASET
                </span>
            </div>

            <!-- Isi Utama: QR Code & Detail Aset -->
            <div class="sticker-body flex items-center gap-3 my-auto py-1">
                <!-- QR Canvas Container -->
                <div class="sticker-qr-box w-20 h-20 bg-white border border-slate-200 dark:border-slate-700 rounded-xl p-1 flex items-center justify-center shrink-0 shadow-2xs">
                    <canvas id="qr_canvas_{{ $item->id }}" class="w-full h-full object-contain"></canvas>
                </div>

                <!-- Teks Detail Aset -->
                <div class="sticker-text overflow-hidden flex-1 space-y-0.5">
                    <div class="sticker-code font-mono font-black text-xs text-slate-900 dark:text-white truncate">
                        {{ $item->kode_barang }}
                    </div>
                    <div class="sticker-name font-bold text-xs text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug">
                        {{ $item->nama_barang }}
                    </div>
                    <div class="sticker-loc text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1 truncate">
                        <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
                        <span class="truncate">{{ $item->lokasi->nama_lokasi ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Label: Peringatan Properti Resmi -->
            <div class="sticker-footer border-t border-slate-100 dark:border-slate-800 pt-1.5 mt-2 flex items-center justify-between text-[8px] text-slate-400 font-mono">
                <span>DILARANG MERUSAK</span>
                <span class="font-bold text-slate-500">PROPERTI RESMI</span>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center text-slate-400">
            <i data-lucide="qr-code" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
            <p class="font-bold">Tidak ada data aset inventaris</p>
        </div>
        @endforelse
    </div>

</div>

<!-- QR Generator Script & Interactive Controls -->
<script>
    // Inisialisasi QR Code Vektor menggunakan library qrcode bawaan
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($items as $item)
            QRCode.toCanvas(
                document.getElementById('qr_canvas_{{ $item->id }}'),
                '{{ $item->kode_barang }}',
                {
                    width: 160,
                    margin: 1,
                    color: {
                        dark: '#0f172a',
                        light: '#ffffff'
                    }
                },
                function (error) {
                    if (error) console.error(error);
                }
            );
        @endforeach
        lucide.createIcons();
    });

    let activeThemeColor = '#e11d48';

    function setThemeColor(color) {
        activeThemeColor = color;
        document.querySelectorAll('.sticker-accent-bar').forEach(bar => {
            bar.style.backgroundColor = color;
        });
    }

    function setStudioTemplate(template) {
        ['standard', 'compact', 'security', 'warehouse'].forEach(t => {
            const btn = document.getElementById('btnTmpl_' + t);
            if (t === template) {
                btn.className = 'px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer';
            } else {
                btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer';
            }
        });

        const cards = document.querySelectorAll('.sticker-card');
        cards.forEach(card => {
            if (template === 'compact') {
                card.classList.add('p-2');
                card.classList.remove('p-3.5');
            } else {
                card.classList.remove('p-2');
                card.classList.add('p-3.5');
            }
        });
    }

    function setGridDensity(density) {
        ['3x7', '2x5', 'roll'].forEach(d => {
            const btn = document.getElementById('btnGrid_' + d);
            if (d === density) {
                btn.className = 'px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer';
            } else {
                btn.className = 'px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer';
            }
        });

        const container = document.getElementById('stickerCanvasContainer');
        if (density === '2x5') {
            container.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-6 print:grid-cols-2 print:gap-4';
        } else if (density === 'roll') {
            container.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 print:grid-cols-1 print:gap-2';
        } else {
            container.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 print:grid-cols-3 print:gap-2';
        }
    }

    function setContentScope(scope) {
        ['all', 'code_only', 'name_only', 'qr_only'].forEach(s => {
            const btn = document.getElementById('btnScope_' + s);
            if (s === scope) {
                btn.className = 'px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer';
            } else {
                btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 cursor-pointer';
            }
        });

        const chkCode = document.getElementById('chkShowCode');
        const chkName = document.getElementById('chkShowName');
        const chkHeader = document.getElementById('chkShowHeader');
        const chkLoc = document.getElementById('chkShowLoc');

        if (scope === 'all') {
            chkCode.checked = true;
            chkName.checked = true;
            chkHeader.checked = true;
            chkLoc.checked = true;
        } else if (scope === 'code_only') {
            chkCode.checked = true;
            chkName.checked = false;
            chkLoc.checked = false;
        } else if (scope === 'name_only') {
            chkCode.checked = false;
            chkName.checked = true;
        } else if (scope === 'qr_only') {
            chkCode.checked = false;
            chkName.checked = false;
            chkHeader.checked = false;
            chkLoc.checked = false;
        }
        updateStickerVisibility();
    }

    function updateStickerVisibility() {
        const showCode = document.getElementById('chkShowCode').checked;
        const showName = document.getElementById('chkShowName').checked;
        const showHeader = document.getElementById('chkShowHeader').checked;
        const showLoc = document.getElementById('chkShowLoc').checked;

        document.querySelectorAll('.sticker-code').forEach(el => el.style.display = showCode ? 'block' : 'none');
        document.querySelectorAll('.sticker-name').forEach(el => el.style.display = showName ? 'block' : 'none');
        document.querySelectorAll('.sticker-header').forEach(el => el.style.display = showHeader ? 'flex' : 'none');
        document.querySelectorAll('.sticker-loc').forEach(el => el.style.display = showLoc ? 'flex' : 'none');
    }

    let allSelected = true;
    function toggleSelectAll() {
        allSelected = !allSelected;
        document.querySelectorAll('[id^="chkItem_"]').forEach(chk => {
            chk.checked = allSelected;
            const id = chk.id.replace('chkItem_', '');
            const card = document.getElementById('card_' + id);
            if (allSelected) {
                card.classList.remove('opacity-40');
            } else {
                card.classList.add('opacity-40');
            }
        });
        document.getElementById('selectAllText').innerText = allSelected ? 'Batalkan Semua' : 'Pilih Semua';
        updateItemCount();
    }

    function toggleItemSelection(id) {
        const chk = document.getElementById('chkItem_' + id);
        chk.checked = !chk.checked;
        const card = document.getElementById('card_' + id);
        if (chk.checked) {
            card.classList.remove('opacity-40');
        } else {
            card.classList.add('opacity-40');
        }
        updateItemCount();
    }

    function updateItemCount() {
        let count = 0;
        document.querySelectorAll('[id^="chkItem_"]').forEach(chk => {
            if (chk.checked) count++;
        });
        document.getElementById('selectedCounter').innerText = count;
    }

    function filterStickers() {
        const query = document.getElementById('searchStickerInput').value.toLowerCase();
        document.querySelectorAll('.sticker-card').forEach(card => {
            const nama = (card.getAttribute('data-nama') || '').toLowerCase();
            const kode = (card.getAttribute('data-kode') || '').toLowerCase();
            const lokasi = (card.getAttribute('data-lokasi') || '').toLowerCase();
            if (nama.includes(query) || kode.includes(query) || lokasi.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
