<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Departemen;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\Inventaris;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Peminjaman;
use App\Models\Mutasi;
use App\Models\Maintenance;
use App\Models\AuditLog;
use App\Models\KopConfig;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $users = [
            ['id' => 1, 'kode_user' => 'USR-001', 'name' => 'Administrator Utama', 'username' => 'admin', 'password' => Hash::make('admin123'), 'role' => 'Admin', 'status' => 'Aktif', 'color_scheme' => 'rose'],
            ['id' => 2, 'kode_user' => 'USR-002', 'name' => 'Dewi Lestari, S.T.', 'username' => 'supervisor', 'password' => Hash::make('spv123'), 'role' => 'Supervisor', 'status' => 'Aktif', 'color_scheme' => 'amber'],
            ['id' => 3, 'kode_user' => 'USR-003', 'name' => 'Budi Santoso', 'username' => 'staff', 'password' => Hash::make('staff123'), 'role' => 'Staff', 'status' => 'Aktif', 'color_scheme' => 'emerald'],
            ['id' => 4, 'kode_user' => 'USR-004', 'name' => 'Hendro Gunawan, C.I.A.', 'username' => 'auditor', 'password' => Hash::make('audit123'), 'role' => 'Auditor', 'status' => 'Aktif', 'color_scheme' => 'purple'],
        ];
        foreach ($users as $u) User::create($u);

        // 2. Kategori
        $kategoris = [
            ['id' => 1, 'kode_kategori' => 'KTG-001', 'nama_kategori' => 'Elektronik & Gadget', 'keterangan' => 'Peralatan komputer, laptop, dan perlengkapan IT (Tidak Habis Pakai)'],
            ['id' => 2, 'kode_kategori' => 'KTG-002', 'nama_kategori' => 'Mebel & Furnitur', 'keterangan' => 'Meja kerja, kursi ergonomis, lemari berkas (Tidak Habis Pakai)'],
            ['id' => 3, 'kode_kategori' => 'KTG-003', 'nama_kategori' => 'Perangkat Jaringan', 'keterangan' => 'Router, switch jaringan, access point, kabel LAN (Tidak Habis Pakai)'],
            ['id' => 4, 'kode_kategori' => 'KTG-004', 'nama_kategori' => 'Alat Tulis & Bahan Habis Pakai', 'keterangan' => 'Kertas HVS, toner printer, ATK kantor, baterai (Habis Pakai / Butuh Stok)'],
        ];
        foreach ($kategoris as $k) Kategori::create($k);

        // 3. Lokasi
        $lokasis = [
            ['id' => 1, 'kode_lokasi' => 'LKS-001', 'nama_lokasi' => 'Ruang IT & Server Lt. 1', 'keterangan' => 'Gedung Utama Lantai 1'],
            ['id' => 2, 'kode_lokasi' => 'LKS-002', 'nama_lokasi' => 'Ruang Staff HR Lt. 2', 'keterangan' => 'Gedung Utama Lantai 2'],
            ['id' => 3, 'kode_lokasi' => 'LKS-003', 'nama_lokasi' => 'Ruang Rapat Eksekutif Lt. 3', 'keterangan' => 'Gedung Utama Lantai 3'],
            ['id' => 4, 'kode_lokasi' => 'LKS-004', 'nama_lokasi' => 'Gudang Logistik Utama', 'keterangan' => 'Gedung Penunjang Lt. 1'],
        ];
        foreach ($lokasis as $l) Lokasi::create($l);

        // 4. Departemen
        $departemens = [
            ['id' => 1, 'kode_departemen' => 'DPT-001', 'nama_departemen' => 'Teknologi Informasi', 'kepala_divisi' => 'Andi Wijaya, S.Kom.', 'lokasi_lantai' => 'Gedung Utama Lantai 1', 'keterangan' => 'Pengelolaan infrastruktur TI, server, jaringan, dan perangkat komputasi'],
            ['id' => 2, 'kode_departemen' => 'DPT-002', 'nama_departemen' => 'Human Resources & GA', 'kepala_divisi' => 'Siti Nurhaliza, S.E.', 'lokasi_lantai' => 'Gedung Utama Lantai 2', 'keterangan' => 'Pengelolaan sumber daya manusia, operasional umum, dan fasilitas kantor'],
            ['id' => 3, 'kode_departemen' => 'DPT-003', 'nama_departemen' => 'Keuangan & Akuntansi', 'kepala_divisi' => 'Budi Santoso, S.Ak.', 'lokasi_lantai' => 'Gedung Utama Lantai 2', 'keterangan' => 'Penganggaran modal aset, audit kas, dan pencatatan depresiasi neraca'],
            ['id' => 4, 'kode_departemen' => 'DPT-004', 'nama_departemen' => 'Operasional & Logistik', 'kepala_divisi' => 'Rudi Hermawan, S.T.', 'lokasi_lantai' => 'Gedung Penunjang Lt. 1', 'keterangan' => 'Penerimaan pergudangan, inventarisasi aset fisik, dan distribusi logistik'],
        ];
        foreach ($departemens as $d) Departemen::create($d);

        // 5. Karyawan
        $karyawans = [
            ['id' => 1, 'nip' => '198801152010121001', 'nama' => 'Andi Wijaya, S.Kom.', 'departemen' => 'Teknologi Informasi', 'email' => 'andi.w@kantor.co.id', 'no_hp' => '081234567890'],
            ['id' => 2, 'nip' => '199205202015032002', 'nama' => 'Siti Nurhaliza, S.E.', 'departemen' => 'Human Resources & GA', 'email' => 'siti.n@kantor.co.id', 'no_hp' => '081987654321'],
            ['id' => 3, 'nip' => '199508122018011003', 'nama' => 'Budi Santoso, S.Ak.', 'departemen' => 'Keuangan & Akuntansi', 'email' => 'budi.s@kantor.co.id', 'no_hp' => '085678901234'],
        ];
        foreach ($karyawans as $kar) Karyawan::create($kar);

        // 6. Inventaris
        $inventaris = [
            ['id' => 1, 'kode_barang' => 'INV-001', 'nama_barang' => 'Laptop Lenovo ThinkPad T14 Gen 3 (Core i7)', 'kategori_id' => 1, 'lokasi_id' => 1, 'jenis_aset' => 'Tidak Habis Pakai', 'stok' => 8, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 18500000, 'deskripsi' => 'Laptop tim engineering & IT'],
            ['id' => 2, 'kode_barang' => 'INV-002', 'nama_barang' => 'Proyektor Epson EB-E500 3300 Lumens', 'kategori_id' => 1, 'lokasi_id' => 3, 'jenis_aset' => 'Tidak Habis Pakai', 'stok' => 2, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 6200000, 'deskripsi' => 'Proyektor ruang presentasi meeting room'],
            ['id' => 3, 'kode_barang' => 'INV-003', 'nama_barang' => 'Kursi Ergonomis Sihoo M57 Mesh Adjustable', 'kategori_id' => 2, 'lokasi_id' => 2, 'jenis_aset' => 'Tidak Habis Pakai', 'stok' => 15, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 2100000, 'deskripsi' => 'Kursi kerja staf lumbar support'],
            ['id' => 4, 'kode_barang' => 'INV-004', 'nama_barang' => 'Switch Cisco Catalyst 2960-X 24 Port Gigabit', 'kategori_id' => 3, 'lokasi_id' => 1, 'jenis_aset' => 'Tidak Habis Pakai', 'stok' => 3, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 12800000, 'deskripsi' => 'Switch jaringan core server lantai 1'],
            ['id' => 5, 'kode_barang' => 'INV-005', 'nama_barang' => 'Printer Epson L3210 All-in-One InkTank', 'kategori_id' => 1, 'lokasi_id' => 2, 'jenis_aset' => 'Tidak Habis Pakai', 'stok' => 1, 'kondisi' => 'Rusak Ringan', 'status' => 'Dalam Maintenance', 'harga_perkiraan' => 2450000, 'deskripsi' => 'Head print bergaris perlu servis berkala'],
            ['id' => 6, 'kode_barang' => 'INV-006', 'nama_barang' => 'Meja Kerja Minimalis Kayu Mahoni 120cm', 'kategori_id' => 2, 'lokasi_id' => 2, 'jenis_aset' => 'Tidak Habis Pakai', 'stok' => 0, 'kondisi' => 'Rusak Berat', 'status' => 'Tidak Tersedia', 'harga_perkiraan' => 1450000, 'deskripsi' => 'Kaki meja patah sedang diafkir'],
            ['id' => 7, 'kode_barang' => 'INV-007', 'nama_barang' => 'Kertas HVS PaperOne A4 80gsm (Rim)', 'kategori_id' => 4, 'lokasi_id' => 4, 'jenis_aset' => 'Habis Pakai', 'stok' => 45, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 58000, 'deskripsi' => 'Kebutuhan cetak operasional staf administrasi'],
            ['id' => 8, 'kode_barang' => 'INV-008', 'nama_barang' => 'Toner Cartridge HP LaserJet Original 85A', 'kategori_id' => 4, 'lokasi_id' => 4, 'jenis_aset' => 'Habis Pakai', 'stok' => 12, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 850000, 'deskripsi' => 'Tinta cartridge printer LaserJet divisi finance'],
            ['id' => 9, 'kode_barang' => 'INV-009', 'nama_barang' => 'Baterai ABC Alkaline AA (Pack isi 4)', 'kategori_id' => 4, 'lokasi_id' => 4, 'jenis_aset' => 'Habis Pakai', 'stok' => 20, 'kondisi' => 'Baik', 'status' => 'Tersedia', 'harga_perkiraan' => 28000, 'deskripsi' => 'Baterai perlengkapan mouse nirkabel & remote AC'],
        ];
        foreach ($inventaris as $inv) Inventaris::create($inv);

        // 7. Barang Masuk
        BarangMasuk::create(['id' => 1, 'nomor_transaksi' => 'BM-20260301-001', 'tanggal' => '2026-03-01', 'inventaris_id' => 1, 'jumlah' => 10, 'supplier' => 'PT Mega Komputindo Mandiri', 'keterangan' => 'Pengadaan kuartal 1']);
        BarangMasuk::create(['id' => 2, 'nomor_transaksi' => 'BM-20260302-002', 'tanggal' => '2026-03-02', 'inventaris_id' => 3, 'jumlah' => 5, 'supplier' => 'CV Sihoo Furniture', 'keterangan' => 'Penambahan kursi staf baru']);

        // 8. Barang Keluar
        BarangKeluar::create(['id' => 1, 'nomor_transaksi' => 'BK-20260303-001', 'tanggal' => '2026-03-03', 'inventaris_id' => 1, 'jumlah' => 2, 'karyawan_id' => 1, 'keperluan' => 'Perbaikan', 'deskripsi' => 'Servis garansi layar']);

        // 9. Peminjaman
        Peminjaman::create(['id' => 1, 'nomor_pinjam' => 'PJM-20260304-001', 'tanggal_pinjam' => '2026-03-04', 'tanggal_kembali' => '2026-03-11', 'karyawan_id' => 1, 'inventaris_id' => 1, 'jumlah' => 2, 'status' => 'Menunggu Approval', 'keperluan' => 'Workshop migrasi sistem ke Bandung', 'catatan' => 'Workshop migrasi sistem']);
        Peminjaman::create(['id' => 2, 'nomor_pinjam' => 'PJM-20260305-002', 'tanggal_pinjam' => '2026-03-05', 'tanggal_kembali' => '2026-03-12', 'karyawan_id' => 2, 'inventaris_id' => 2, 'jumlah' => 1, 'status' => 'Dipinjam', 'keperluan' => 'Audit aset lapangan di cabang', 'catatan' => 'Audit aset lapangan']);
        Peminjaman::create(['id' => 3, 'nomor_pinjam' => 'PJM-20260306-003', 'tanggal_pinjam' => '2026-03-06', 'tanggal_kembali' => '2026-03-10', 'karyawan_id' => 3, 'inventaris_id' => 3, 'jumlah' => 1, 'status' => 'Dipinjam', 'keperluan' => 'Penunjang operasional staf divisi keuangan', 'catatan' => 'Kursi kerja penunjang rekonsiliasi']);

        // 10. Mutasi
        Mutasi::create([
            'id' => 1, 'nomor_mutasi' => 'MTS-20260301-001', 'tanggal_permohonan' => '2026-03-01', 'inventaris_id' => 2,
            'lokasi_awal_id' => 1, 'lokasi_baru_id' => 3, 'departemen_baru_id' => 3, 'karyawan_id' => 2, 'created_by_user_id' => 1,
            'disetujui_oleh' => 'Administrator Utama (Admin)', 'tanggal_persetujuan' => '2026-03-02',
            'alasan' => 'Penempatan tetap proyektor di ruang presentasi meeting room', 'status' => 'Disetujui',
            'catatan_approval' => 'Disetujui. Aset telah dicek dan berfungsi normal di Ruang Rapat Eksekutif.', 'keterangan' => 'Pindah ke ruang rapat eksekutif Lt. 3'
        ]);
        Mutasi::create([
            'id' => 2, 'nomor_mutasi' => 'MTS-20260305-002', 'tanggal_permohonan' => '2026-03-05', 'inventaris_id' => 4,
            'lokasi_awal_id' => 1, 'lokasi_baru_id' => 4, 'departemen_baru_id' => 4, 'karyawan_id' => 1, 'created_by_user_id' => 2,
            'alasan' => 'Relokasi switch jaringan backup ke gudang logistik untuk cadangan darurat', 'status' => 'Menunggu Approval',
            'keterangan' => 'Permohonan pemindahan switch Cisco 24 port'
        ]);
        Mutasi::create([
            'id' => 3, 'nomor_mutasi' => 'MTS-20260306-003', 'tanggal_permohonan' => '2026-03-06', 'inventaris_id' => 3,
            'lokasi_awal_id' => 2, 'lokasi_baru_id' => 3, 'departemen_baru_id' => 2, 'karyawan_id' => 3, 'created_by_user_id' => 3,
            'alasan' => 'Pemindahan kursi kerja ergonomis ke ruang meeting finance untuk koordinasi rekonsiliasi', 'status' => 'Menunggu Approval',
            'keterangan' => 'Permohonan mutasi fasilitas staf divisi keuangan'
        ]);

        // 11. Maintenance
        Maintenance::create(['id' => 1, 'tiket' => 'MT-001', 'inventaris_id' => 5, 'tanggal_lapor' => '2026-03-01', 'kondisi' => 'Rusak Ringan', 'deskripsi' => 'Head print bergaris perlu servis berkala', 'teknisi' => 'Epson Service Center', 'biaya' => 250000, 'status' => 'Dalam Perbaikan']);
        Maintenance::create(['id' => 2, 'tiket' => 'MT-002', 'inventaris_id' => 6, 'tanggal_lapor' => '2026-03-03', 'kondisi' => 'Rusak Berat', 'deskripsi' => 'Kaki meja patah sedang dalam pengafkiran', 'teknisi' => 'Bengkel Kayu Abadi', 'biaya' => 150000, 'status' => 'Tidak Bisa Diperbaiki']);

        // 12. Kop Config
        KopConfig::create([
            'org_name' => 'KEMENTERIAN KOMUNIKASI DAN INFORMATIKA RI',
            'div_name' => 'DIREKTORAT JENDERAL SUMBER DAYA & PERANGKAT POS INFORMATIKA',
            'nomor_surat' => 'BA-INV/2026/X/001',
            'approver_title' => 'Kepala Bagian Umum & Perlengkapan',
            'approver_name' => 'Drs. Bambang Hariyanto, M.M.',
            'approver_nip' => 'NIP. 19750812 199903 1 002',
            'maker_title' => 'Petugas Pengelola Inventaris & Logistik',
            'maker_name' => 'Bambang Pratama, S.Kom.',
            'maker_nip' => 'NIP. 19880422 201101 1 005',
        ]);

        // 13. Audit Log
        AuditLog::create(['kode_log' => 'LOG-001', 'user_name' => 'Administrator Utama', 'user_role' => 'Admin', 'action' => 'STATUS_CHANGE', 'module' => 'sistem', 'description' => 'Database Laravel 13 berhasil diinisialisasi.', 'ip' => '127.0.0.1']);
    }
}
