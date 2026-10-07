<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\QrStudioController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\WhatsAppController;

// Halaman Utama & Redirect ke Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Autentikasi & Simulasi Multi-Role (1:1 RBAC Login Flow)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-role-simulation', [AuthController::class, 'switchRoleSimulation'])->name('switch.role');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password');

// Grup Rute Terproteksi RBAC (Dashboard & Master Data)
Route::middleware(['web'])->group(function () {
    // 1. Dashboard Eksekutif & Statistik
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Master Inventaris (Katalog & Stok Fisik)
    Route::resource('inventaris', InventarisController::class);

    // 3. Master Karyawan, Departemen, Kategori, Lokasi
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('departemen', DepartemenController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('lokasi', LokasiController::class);

    // 4. Sirkulasi: Barang Masuk & Keluar
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::resource('barang-keluar', BarangKeluarController::class);

    // 5. Sirkulasi: Peminjaman & Pengembalian Aset
    Route::resource('peminjaman', PeminjamanController::class);
    Route::post('/peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve')->middleware('role:Admin,Supervisor');
    Route::post('/peminjaman/{id}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject')->middleware('role:Admin,Supervisor');
    Route::post('/peminjaman/{id}/return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.return');

    // 6. Mutasi & Pemindahan Aset Tetap (Approval & Pemindahan Lokasi 1:1)
    Route::resource('mutasi', MutasiController::class);
    Route::post('/mutasi/{id}/approve', [MutasiController::class, 'approve'])->name('mutasi.approve')->middleware('role:Admin,Supervisor');
    Route::post('/mutasi/{id}/reject', [MutasiController::class, 'reject'])->name('mutasi.reject')->middleware('role:Admin,Supervisor');

    // 7. Pemeliharaan & Tiket Servis
    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/{id}/update-status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.updateStatus');

    // 8. Laporan Resmi Ber-KOP Surat & Export XLSX / PDF
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');

    // 9. Studio Label Cetak QR Code
    Route::get('/qr-studio', [QrStudioController::class, 'index'])->name('qr.index');
    Route::post('/qr-studio/print', [QrStudioController::class, 'print'])->name('qr.print');

    // 10. Pengaturan RBAC & Profil
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index')->middleware('role:Admin');
    Route::post('/pengaturan/update-kop', [SettingController::class, 'updateKop'])->name('pengaturan.updateKop')->middleware('role:Admin');
    Route::post('/pengaturan/update-wa', [SettingController::class, 'updateWa'])->name('pengaturan.updateWa')->middleware('role:Admin');
    Route::post('/pengaturan/update-rbac', [SettingController::class, 'updateRbac'])->name('pengaturan.updateRbac')->middleware('role:Admin');
    Route::post('/pengaturan/reset-data', [SettingController::class, 'resetData'])->name('pengaturan.resetData')->middleware('role:Admin');

    // 11. Audit Trail & Log
    Route::get('/audit-trail', [AuditLogController::class, 'index'])->name('audit.index');

    // 12. WhatsApp Reminder Dispatcher
    Route::post('/whatsapp/send-reminder', [WhatsAppController::class, 'sendReminder'])->name('whatsapp.sendReminder');
});
