@extends('layouts.app')

@section('title', 'Studio Label Stiker QR Code - Inventaris Kantor')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Print All -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>Studio Cetak Label Stiker QR Code</span>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                    Generator Vektor 1:1
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Desain label stiker aset siap cetak untuk kertas A4 dan printer roll thermal dengan kustomisasi cakupan informasi fisik.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md shadow-indigo-500/20 text-xs transition cursor-pointer">
                <span>🖨️ Cetak Semua (A4 / Roll)</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Kustomisasi Studio Label -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4 no-print">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
            <!-- Pilihan Template -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Template:</span>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" onclick="setStudioTemplate('standard')" id="stdTmpl_standard" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer">Standard</button>
                    <button type="button" onclick="setStudioTemplate('compact')" id="stdTmpl_compact" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer">Compact</button>
                    <button type="button" onclick="setStudioTemplate('security')" id="stdTmpl_security" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer">Security</button>
                    <button type="button" onclick="setStudioTemplate('warehouse')" id="stdTmpl_warehouse" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer">Warehouse</button>
                </div>
            </div>

            <!-- Pilihan Warna Aksen -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Warna:</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="setStudioTheme('#dc2626')" class="w-6 h-6 rounded-full bg-red-600 cursor-pointer" title="Merah"></button>
                    <button type="button" onclick="setStudioTheme('#2563eb')" class="w-6 h-6 rounded-full bg-blue-600 cursor-pointer" title="Biru"></button>
                    <button type="button" onclick="setStudioTheme('#059669')" class="w-6 h-6 rounded-full bg-emerald-600 cursor-pointer" title="Hijau"></button>
                    <button type="button" onclick="setStudioTheme('#7c3aed')" class="w-6 h-6 rounded-full bg-purple-600 cursor-pointer" title="Ungu"></button>
                    <button type="button" onclick="setStudioTheme('#d97706')" class="w-6 h-6 rounded-full bg-amber-600 cursor-pointer" title="Kuning"></button>
                </div>
            </div>

            <span class="text-xs font-bold text-slate-500">Total: <strong>{{ count($items) }}</strong> Aset Siap Cetak</span>
        </div>

        <!-- Cakupan Informasi Stiker (Menyesuaikan Ukuran Stiker Fisik) -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase">Cakupan Informasi:</span>
                <!-- 4 Quick Buttons -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">
                    <button type="button" onclick="setStudioScope('all')" id="stdScope_all" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer flex items-center gap-1">
                        <span>Lengkap</span>
                        <span class="text-[9px] opacity-80 font-normal">(Kode+Nama)</span>
                    </button>
                    <button type="button" onclick="setStudioScope('code_only')" id="stdScope_code_only" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1">
                        <span>Kode Saja</span>
                        <span class="text-[9px] opacity-80 font-normal">(QR+Kode)</span>
                    </button>
                    <button type="button" onclick="setStudioScope('name_only')" id="stdScope_name_only" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1">
                        <span>Nama Saja</span>
                        <span class="text-[9px] opacity-80 font-normal">(QR+Nama)</span>
                    </button>
                    <button type="button" onclick="setStudioScope('qr_only')" id="stdScope_qr_only" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1">
                        <span>Hanya QR</span>
                        <span class="text-[9px] opacity-80 font-normal">(Mini Thermal)</span>
                    </button>
                </div>
            </div>

            <!-- Toggles Mandiri -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="chkStdCode" checked onchange="handleStudioToggle('code')" class="rounded text-indigo-600">
                    <span>Kode Aset</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="chkStdName" checked onchange="handleStudioToggle('name')" class="rounded text-indigo-600">
                    <span>Nama Barang</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="chkStdHeader" checked onchange="applyStudioClasses()" class="rounded text-indigo-600">
                    <span>Kop Instansi</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="chkStdLoc" checked onchange="applyStudioClasses()" class="rounded text-indigo-600">
                    <span>Ruang Lokasi</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Grid Preview Label Stiker (Setiap item memiliki class .qr-sticker-item) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 print:grid-cols-3 print:gap-2" id="studioStickerGrid">
        @forelse($items as $item)
        <div 
            class="qr-sticker-item bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-3.5 flex flex-col justify-between shadow-xs relative overflow-hidden transition group hover:border-indigo-500"
            data-kode="{{ $item->kode_barang }}"
            data-nama="{{ $item->nama_barang }}"
            data-lokasi="{{ $item->lokasi->nama_lokasi ?? '-' }}"
            data-kondisi="{{ $item->kondisi }}"
            data-stok="{{ $item->stok }}"
            data-kategori="{{ $item->kategori->nama_kategori ?? '-' }}"
        >
            <!-- Top Accent Bar -->
            <div class="sticker-top-bar absolute top-0 left-0 right-0 h-1 z-20 bg-rose-600"></div>

            <!-- Header Kop -->
            <div class="sticker-header-part border-b border-slate-100 dark:border-slate-800 pb-1.5 mb-2 flex items-center justify-between">
                <span class="text-[9px] font-black uppercase text-slate-500 truncate">{{ $kopConfig->org_name ?? 'INVENTARIS KANTOR' }}</span>
                <span class="text-[8px] font-mono px-1 py-0.5 rounded bg-slate-100 text-slate-600">RESMI</span>
            </div>
            
            <!-- Konten Utama: QR Box & Data Aset -->
            <div class="sticker-main-body flex items-center gap-3 my-auto py-1">
                <!-- QR Box -->
                <div class="sticker-qr-box w-20 h-20 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 flex items-center justify-center shrink-0 shadow-2xs">
                    <img 
                        src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($item->kode_barang) }}" 
                        alt="QR {{ $item->kode_barang }}" 
                        class="w-full h-full object-contain"
                        crossorigin="anonymous"
                    />
                </div>

                <!-- Text Block -->
                <div class="sticker-text-block overflow-hidden flex-1 space-y-0.5">
                    <div class="sticker-code-elem font-mono font-black text-xs text-slate-900 dark:text-white truncate">
                        {{ $item->kode_barang }}
                    </div>
                    <div class="sticker-name-elem font-bold text-[11px] text-slate-800 dark:text-slate-200 line-clamp-2 leading-tight">
                        {{ $item->nama_barang }}
                    </div>
                    <div class="sticker-loc-elem text-[9px] text-slate-500 truncate">
                        📍 {{ $item->lokasi->nama_lokasi ?? '-' }}
                    </div>
                </div>
            </div>

            <!-- Footer Stiker -->
            <div class="sticker-footer-part mt-2 pt-1.5 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-[9px] text-slate-400 font-mono">
                <span>Kondisi: {{ $item->kondisi }}</span>
                <span>Scan detail</span>
            </div>

            <!-- ACTION BUTTON: GENERATE & DOWNLOAD INDIVIDUAL STICKER -->
            <div class="no-print mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button 
                    type="button" 
                    onclick="downloadIndividualSticker(event, this, {{ json_encode($item) }})" 
                    class="w-full flex items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-[10px] tracking-wide transition shadow-2xs cursor-pointer active:scale-95"
                    title="Generate & Download stiker label {{ $item->kode_barang }}"
                >
                    <span>✨ Generate &amp; Download</span>
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center text-slate-400">Tidak ada aset untuk dicetak.</div>
        @endforelse
    </div>
</div>

<script>
    let stdThemeHex = '#dc2626';
    let stdTemplate = 'standard';
    let stdScope = 'all';

    function setStudioTemplate(tmpl) {
        stdTemplate = tmpl;
        ['standard', 'compact', 'security', 'warehouse'].forEach(t => {
            const btn = document.getElementById('stdTmpl_' + t);
            if (btn) {
                if (t === tmpl) {
                    btn.className = 'px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer';
                } else {
                    btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer';
                }
            }
        });
        applyStudioClasses();
    }

    function setStudioTheme(hex) {
        stdThemeHex = hex;
        document.querySelectorAll('.sticker-top-bar').forEach(el => {
            el.style.backgroundColor = hex;
        });
    }

    function setStudioScope(scope) {
        stdScope = scope;
        const chkCode = document.getElementById('chkStdCode');
        const chkName = document.getElementById('chkStdName');
        const chkHeader = document.getElementById('chkStdHeader');
        const chkLoc = document.getElementById('chkStdLoc');

        if (scope === 'all') {
            chkCode.checked = true;
            chkName.checked = true;
            chkHeader.checked = true;
            chkLoc.checked = true;
        } else if (scope === 'code_only') {
            chkCode.checked = true;
            chkName.checked = false;
        } else if (scope === 'name_only') {
            chkCode.checked = false;
            chkName.checked = true;
        } else if (scope === 'qr_only') {
            chkCode.checked = false;
            chkName.checked = false;
            chkHeader.checked = false;
            chkLoc.checked = false;
        }

        updateStudioScopeUI();
        applyStudioClasses();
    }

    function handleStudioToggle(type) {
        const hasCode = document.getElementById('chkStdCode').checked;
        const hasName = document.getElementById('chkStdName').checked;

        if (hasCode && hasName) {
            stdScope = 'all';
        } else if (hasCode && !hasName) {
            stdScope = 'code_only';
        } else if (!hasCode && hasName) {
            stdScope = 'name_only';
        } else {
            stdScope = 'qr_only';
        }

        updateStudioScopeUI();
        applyStudioClasses();
    }

    function updateStudioScopeUI() {
        ['all', 'code_only', 'name_only', 'qr_only'].forEach(s => {
            const btn = document.getElementById('stdScope_' + s);
            if (btn) {
                if (s === stdScope) {
                    btn.className = 'px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs cursor-pointer flex items-center gap-1';
                } else {
                    btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1';
                }
            }
        });
    }

    function applyStudioClasses() {
        const hasCode = document.getElementById('chkStdCode').checked;
        const hasName = document.getElementById('chkStdName').checked;
        const hasHeader = document.getElementById('chkStdHeader').checked;
        const hasLoc = document.getElementById('chkStdLoc').checked;
        const isQrOnly = !hasCode && !hasName;

        document.querySelectorAll('.qr-sticker-item').forEach(card => {
            const headerElem = card.querySelector('.sticker-header-part');
            const codeElem = card.querySelector('.sticker-code-elem');
            const nameElem = card.querySelector('.sticker-name-elem');
            const locElem = card.querySelector('.sticker-loc-elem');
            const footerElem = card.querySelector('.sticker-footer-part');
            const bodyElem = card.querySelector('.sticker-main-body');
            const qrBox = card.querySelector('.sticker-qr-box');
            const textBlock = card.querySelector('.sticker-text-block');

            if (headerElem) headerElem.style.display = hasHeader && !isQrOnly ? 'flex' : 'none';
            if (codeElem) codeElem.style.display = hasCode ? 'block' : 'none';
            if (nameElem) nameElem.style.display = hasName ? 'block' : 'none';
            if (locElem) locElem.style.display = hasLoc && !isQrOnly ? 'block' : 'none';
            if (footerElem) footerElem.style.display = isQrOnly ? 'none' : 'flex';

            if (isQrOnly) {
                bodyElem.className = 'sticker-main-body flex flex-col items-center justify-center my-auto py-2 text-center';
                if (qrBox) qrBox.className = 'sticker-qr-box w-28 h-28 bg-white border border-slate-200 rounded-2xl p-1 flex items-center justify-center shadow-xs';
                if (textBlock) textBlock.style.display = 'none';
            } else {
                bodyElem.className = 'sticker-main-body flex items-center gap-3 my-auto py-1';
                if (qrBox) qrBox.className = 'sticker-qr-box w-20 h-20 bg-white border border-slate-200 rounded-xl p-1 flex items-center justify-center shrink-0 shadow-2xs';
                if (textBlock) textBlock.style.display = 'block';
            }
        });
    }

    async function downloadIndividualSticker(event, btn, item) {
        event.stopPropagation();
        const card = btn.closest('.qr-sticker-item');
        if (!card) return;

        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span>⏳ Memproses...</span>';

        try {
            const h2c = window.html2canvasPro || window.html2canvas;
            if (!h2c) throw new Error("html2canvas library not loaded");
            const canvas = await h2c(card, {
                scale: 3,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false,
                ignoreElements: el => el.classList.contains('no-print')
            });

            const imgUrl = canvas.toDataURL('image/png', 1.0);
            const link = document.createElement('a');
            const cleanName = item.nama_barang.replace(/[^a-zA-Z0-9]/g, '_');
            link.href = imgUrl;
            link.download = `Label_Stiker_${item.kode_barang}_${cleanName}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (e) {
            console.error('Error generating sticker PNG:', e);
            alert(`Berhasil membuat kode QR ${item.kode_barang}.`);
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    }
</script>
@endsection
