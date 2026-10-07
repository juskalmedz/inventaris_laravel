<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users & RBAC
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('kode_user', 30)->unique();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->enum('role', ['Admin', 'Supervisor', 'Staff', 'Auditor'])->default('Staff');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->string('color_scheme', 30)->default('rose');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Departemen & Divisi
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('kode_departemen', 30)->unique();
            $table->string('nama_departemen');
            $table->string('kepala_divisi');
            $table->string('lokasi_lantai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Kategori Aset
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori', 30)->unique();
            $table->string('nama_kategori');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 4. Ruangan & Lokasi Penempatan
        Schema::create('lokasis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lokasi', 30)->unique();
            $table->string('nama_lokasi');
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 5. Karyawan / PIC
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 50)->unique();
            $table->string('nama');
            $table->string('departemen');
            $table->string('jabatan')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Resign'])->default('Aktif');
            $table->timestamps();
        });

        // 6. Katalog Inventaris
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang');
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('restrict');
            $table->foreignId('lokasi_id')->constrained('lokasis')->onDelete('restrict');
            $table->enum('jenis_aset', ['Habis Pakai', 'Tidak Habis Pakai'])->default('Tidak Habis Pakai');
            $table->integer('stok')->default(0);
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status', ['Tersedia', 'Dipinjam', 'Dalam Maintenance', 'Tidak Tersedia'])->default('Tersedia');
            $table->decimal('harga_perkiraan', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->text('qr_code_data')->nullable();
            $table->timestamps();
        });

        // 7. Barang Masuk
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('supplier');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 8. Barang Keluar
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade');
            $table->integer('jumlah');
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->string('keperluan');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 9. Peminjaman
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pinjam', 50)->unique();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->date('rencana_kembali')->nullable();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->enum('status', ['Menunggu Approval', 'Dipinjam', 'Dikembalikan', 'Ditolak'])->default('Menunggu Approval');
            $table->string('keperluan');
            $table->enum('kondisi_sebelum', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('kondisi_sesudah', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->nullable();
            $table->text('catatan')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // 10. Mutasi & Pemindahan Aset
        Schema::create('mutasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_mutasi', 50)->unique();
            $table->date('tanggal_permohonan');
            $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade');
            $table->foreignId('lokasi_awal_id')->constrained('lokasis')->onDelete('restrict');
            $table->foreignId('lokasi_baru_id')->constrained('lokasis')->onDelete('restrict');
            $table->foreignId('departemen_baru_id')->nullable()->constrained('departemens')->nullOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disetujui_oleh')->nullable();
            $table->date('tanggal_persetujuan')->nullable();
            $table->text('alasan');
            $table->enum('status', ['Menunggu Approval', 'Disetujui', 'Ditolak'])->default('Menunggu Approval');
            $table->text('catatan_approval')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 11. Maintenance / Tiket Servis
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('tiket', 50)->unique();
            $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade');
            $table->date('tanggal_lapor');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('kondisi', ['Rusak Ringan', 'Rusak Berat']);
            $table->text('deskripsi');
            $table->string('teknisi')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->enum('status', ['Dalam Perbaikan', 'Menunggu Suku Cadang', 'Selesai', 'Tidak Bisa Diperbaiki'])->default('Dalam Perbaikan');
            $table->timestamps();
        });

        // 12. Audit Trail
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_log', 50)->unique();
            $table->string('user_name');
            $table->string('user_role');
            $table->string('action');
            $table->string('module');
            $table->text('description');
            $table->string('ip')->nullable();
            $table->timestamps();
        });

        // 13. Kop Surat & Penandatangan
        Schema::create('kop_configs', function (Blueprint $table) {
            $table->id();
            $table->string('org_name');
            $table->string('div_name');
            $table->string('nomor_surat');
            $table->string('approver_title');
            $table->string('approver_name');
            $table->string('approver_nip');
            $table->string('maker_title');
            $table->string('maker_name');
            $table->string('maker_nip');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kop_configs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('maintenances');
        Schema::dropIfExists('mutasis');
        Schema::dropIfExists('peminjamans');
        Schema::dropIfExists('barang_keluars');
        Schema::dropIfExists('barang_masuks');
        Schema::dropIfExists('inventaris');
        Schema::dropIfExists('karyawans');
        Schema::dropIfExists('lokasis');
        Schema::dropIfExists('kategoris');
        Schema::dropIfExists('departemens');
        Schema::dropIfExists('users');
    }
};
