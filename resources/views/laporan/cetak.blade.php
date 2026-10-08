<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Inventaris & Aset Kantor</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            background: #fff;
        }
        .kop-surat {
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            border: 2px solid #0f172a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #475569;
        }
        .report-title {
            text-align: center;
            margin: 15px 0 20px;
        }
        .report-title h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .report-title p {
            margin: 4px 0 0;
            font-size: 10px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            color: #334155;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .ttd-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-box {
            text-align: center;
            width: 200px;
        }
        .ttd-space {
            height: 60px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold; font-size: 13px;">Pratinjau Lembar Cetak Laporan Inventaris Resmi</span>
        <div>
            <button onclick="window.print()" style="padding: 8px 16px; background: #e11d48; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Cetak Dokumen (Ctrl+P)</button>
            <button onclick="window.history.back()" style="padding: 8px 16px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-left: 8px;">Kembali</button>
        </div>
    </div>

    <!-- KOP SURAT RESMI -->
    <div class="kop-surat">
        <div class="kop-logo">KOP</div>
        <div class="kop-text">
            <h1>{{ $kopConfig->nama_instansi ?? 'PT. NUSANTARA SINERGI TEKNOLOGI' }}</h1>
            <p>{{ $kopConfig->alamat ?? 'Jl. Jenderal Sudirman Kav. 52-53, SCBD Lot 8, Jakarta Selatan 12190' }}</p>
            <p>Telp: {{ $kopConfig->telepon ?? '(021) 555-8921' }} | Email: {{ $kopConfig->email ?? 'inventaris@perusahaan.co.id' }} | Web: {{ $kopConfig->website ?? 'www.perusahaan.co.id' }}</p>
        </div>
    </div>

    <div class="report-title">
        <h2>BERITA ACARA & LAPORAN REKAPITULASI ASET KANTOR</h2>
        <p>Nomor Dokumen: LAP-AST/{{ date('Y/m') }}/{{ str_pad(count($items), 3, '0', STR_PAD_LEFT) }} &bull; Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <!-- TABEL DATA ASET -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Kode Barang</th>
                <th>Nama Barang / Aset</th>
                <th>Kategori</th>
                <th>Lokasi Fisik</th>
                <th class="text-center">Kuantitas</th>
                <th class="text-center">Kondisi</th>
                <th class="text-center">Status</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            @php
                $subtotal = $item->stok * $item->harga_perkiraan;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-mono" style="font-weight: bold;">{{ $item->kode_barang }}</td>
                <td style="font-weight: 600;">{{ $item->nama_barang }}</td>
                <td>{{ $item->kategori->nama_kategori ?? $item->kategori->nama ?? '-' }}</td>
                <td>{{ $item->lokasi->nama_lokasi ?? $item->lokasi->nama ?? '-' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $item->stok }} {{ $item->satuan ?? 'Unit' }}</td>
                <td class="text-center">{{ $item->kondisi }}</td>
                <td class="text-center">{{ $item->status }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_perkiraan, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px;">Tidak ada aset inventaris yang tercatat.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL NILAI TAKSIRAN ASET:</td>
                <td class="text-center">{{ $items->sum('stok') }} Unit</td>
                <td colspan="3"></td>
                <td class="text-right" style="font-size: 12px; color: #047857;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-section">
        <div class="ttd-box">
            <p>Petugas Logistik / Admin,</p>
            <div class="ttd-space"></div>
            <p style="font-weight: bold; text-decoration: underline;">( Staf Inventaris & Gudang )</p>
            <p style="color: #64748b; font-size: 10px;">Divisi General Affairs</p>
        </div>
        <div class="ttd-box">
            <p>{{ $kopConfig->kota ?? 'Jakarta' }}, {{ date('d F Y') }}<br>Mengetahui & Menyetujui,</p>
            <div class="ttd-space"></div>
            <p style="font-weight: bold; text-decoration: underline;">{{ $kopConfig->pic_penanggung_jawab ?? 'Budi Santoso, S.Kom., M.T.' }}</p>
            <p style="color: #64748b; font-size: 10px;">{{ $kopConfig->jabatan_pic ?? 'Head of General Affairs & IT' }}</p>
        </div>
    </div>
</body>
</html>
