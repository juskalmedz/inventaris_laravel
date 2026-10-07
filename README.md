# Sistem Manajemen Inventaris & Aset Kantor (Laravel 13 - Sinkronisasi 1:1)

Aplikasi ini merupakan implementasi penuh dan resmi berbasis **Laravel 13 & PHP 8.3+** dari Sistem Manajemen Inventaris & Aset Kantor yang disinkronkan 1:1 dengan versi React SPA.

---

## 🚀 Fitur Unggulan 1:1
1. **14 Tabel Basis Data Relasional**:
   - `users` (RBAC: Admin, Supervisor, Staff, Auditor)
   - `inventaris` (Katalog, barcode, stok fisik, kondisi, status)
   - `karyawans`, `departemens`, `kategoris`, `lokasis`
   - `barang_masuks`, `barang_keluars`, `peminjamans`
   - `mutasis` (Alur Approval & pemindahan lokasi otomatis)
   - `maintenances` (Tiket perbaikan & histori servis)
   - `audit_logs` (Audit trail operasional)
   - `kop_configs` (Kop surat resmi & penandatangan)
2. **Business Logic & Validasi Otomatis**:
   - Mutasi aset yang disetujui (Approve) otomatis mengubah `lokasi_id` pada katalog inventaris.
   - Peminjaman barang memotong stok dan mengubah ketersediaan secara real-time.
   - Peringatan stok kritis (<= 1) dan kerusakan berat.
   - Template Berita Acara & Laporan ber-Kop Surat siap cetak (print stylesheet).
   - RESTful API v1 lengkap (`/api/v1/...`) untuk integrasi frontend ganda.
3. **Pembaruan Sinkronisasi Terbaru (Identik 1:1 dengan React SPA)**:
   - **Riwayat Kronologis Mutasi pada Modal Detail Aset**:
     - Rekam jejak seluruh perpindahan ruangan fisik aset (`Lokasi Asal` ➜ `Lokasi Baru` + divisi).
     - Badge status resmi (`Disetujui`, `Menunggu Approval`, `Ditolak`), tanggal, pemohon PIC, pejabat penyetuju, serta catatan approval.
     - Opsi pengurutan kronologis (Terbaru ke Lama vs Awal ke Terbaru) dan empty state jika belum pernah dimutasi.
   - **Modal Popup Mandiri Cetak Label QR (1 Item)**:
     - Aksi cetak pada tabel inventaris membuka dialog mandiri pratinjau stiker fisik realistis.
     - Pilihan template (Corporate Standard, Minimalist Compact, Security Seal, Warehouse Logistik).
     - Tombol cetak langsung (`Print 1 Item`) dan unduh PNG stiker.
   - **Kustomisasi Cakupan Informasi Stiker Sesuai Ukuran Fisik**:
     - 4 Preset cepat: **Lengkap** (Kode + Nama), **Kode Saja** (QR + Kode), **Nama Saja** (QR + Nama), **Hanya QR** (Mini Thermal 20x20 / 25x25 mm).
     - Checkbox toggle mandiri untuk **Kode Aset** dan **Nama Barang**.
   - **Tombol "Generate & Download" per Stiker (`.qr-sticker-item`)**:
     - Setiap kartu pratinjau stiker di Studio Cetak QR dilengkapi tombol unduh PNG individu berskala 3x beresolusi tinggi.
   - **Kompatibilitas Warna CSS Modern (`oklch`)**:
     - Pustaka `html2canvas-pro` terpasang untuk parsing warna Tailwind CSS v4 tanpa error.

---

## 🛠️ Cara Instalasi & Menjalankan

### Cara 1: Menggunakan Docker & Docker Compose (Sangat Direkomendasikan) 🐳

Sistem telah dilengkapi dengan `Dockerfile` dan `docker-compose.yml` siap pakai (termasuk PHP 8.3-FPM, Nginx, MySQL 8.0, dan phpMyAdmin):

```bash
# 1. Jalankan seluruh container (App, Nginx, MySQL, phpMyAdmin)
docker compose up -d --build

# 2. Cek status container
docker compose ps

# 3. Akses aplikasi:
# - Web Aplikasi Inventaris : http://localhost:8000
# - phpMyAdmin (Database)  : http://localhost:8080 (User: inventaris_user, Password: inventaris_secret)

# 4. (Opsional) Build dan Push image ke Docker Hub / Container Registry:
docker build -t username/inventaris-kantor:latest .
docker push username/inventaris-kantor:latest
```

---

### Cara 2: Instalasi Manual dengan Composer & PHP Lokal

#### 1. Kebutuhan Sistem
- PHP >= 8.2 / 8.3 / 8.4
- Composer >= 2.6
- MySQL >= 8.0 atau SQLite 3

#### 2. Langkah Setup Proyek
```bash
# 1. Masuk ke direktori laravel
cd laravel

# 2. Install dependensi composer (sudah diperbaiki untuk PHP 8.2-8.4 & Laravel 11/12/13)
composer install

# 3. Salin file environment & generate app key (jika belum ada)
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi koneksi database di file .env
# DB_CONNECTION=mysql
# DB_DATABASE=inventaris_kantor
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan migrasi dan seeder data awal 1:1
php artisan migrate:fresh --seed

# 6. Jalankan web server lokal
php artisan serve
```

Aplikasi dapat langsung diakses di browser: **`http://localhost:8000`**

---

## 🔑 Akun Demo Bawaan (Seeder)
- **Admin**: `admin` / `admin123`
- **Supervisor**: `supervisor` / `spv123`
- **Staff**: `staff` / `staff123`
- **Auditor**: `auditor` / `audit123`
