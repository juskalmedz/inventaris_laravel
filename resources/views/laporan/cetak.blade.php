<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR Code Inventaris</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            background: #fff;
        }
        .header-print {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-print h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-print p {
            margin: 2px 0 0;
            font-size: 11px;
            color: #64748b;
        }
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .qr-card {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            page-break-inside: avoid;
            background: #fafafa;
        }
        .qr-card img {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            background: #fff;
            padding: 4px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .qr-info {
            flex: 1;
            min-width: 0;
        }
        .qr-code-tag {
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
            color: #e11d48;
        }
        .qr-title {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .qr-meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: none;
            }
            .qr-card {
                border-style: solid;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 12px; background: #f1f5f9; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 13px; font-weight: bold;">Pratinjau Lembar Cetak Label QR ({{ count($items) }} Aset)</span>
        <div>
            <button onclick="window.print()" style="padding: 8px 16px; background: #e11d48; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Cetak Sekarang</button>
            <button onclick="window.history.back()" style="padding: 8px 16px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-left: 8px;">Kembali</button>
        </div>
    </div>

    <div style="padding: 12px;">
        <div class="header-print">
            <h2>{{ $kopConfig->nama_instansi ?? 'SISTEM INVENTARIS & ASET KANTOR' }}</h2>
            <p>Lembar Label Stiker QR Code &bull; Dicetak pada: {{ date('d F Y, H:i') }} WIB</p>
        </div>

        <div class="qr-grid">
            @forelse($items as $item)
            @php
                $qrData = $item->qr_code_data ?: "INVENTARIS:{$item->kode_barang}|{$item->nama_barang}|SN:{$item->nomor_seri}";
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
            @endphp
            <div class="qr-card">
                <img src="{{ $qrUrl }}" alt="QR {{ $item->kode_barang }}">
                <div class="qr-info">
                    <div class="qr-code-tag">{{ $item->kode_barang }}</div>
                    <div class="qr-title">{{ $item->nama_barang }}</div>
                    <div class="qr-meta">
                        <div>Kategori: {{ $item->kategori->nama_kategori ?? $item->kategori->nama ?? '-' }}</div>
                        <div>Lokasi: {{ $item->lokasi->nama_lokasi ?? $item->lokasi->nama ?? '-' }}</div>
                        @if($item->nomor_seri)
                        <div>SN: {{ $item->nomor_seri }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #94a3b8;">
                Tidak ada aset yang dipilih untuk dicetak.
            </div>
            @endforelse
        </div>
    </div>
</body>
</html>
