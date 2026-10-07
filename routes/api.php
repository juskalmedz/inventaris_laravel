<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    // 1. Ping & System Health & Security Telemetry
    Route::get('/health', [InventoryApiController::class, 'health']);

    // 2. Autentikasi & RBAC Multi-Role (1:1 dengan UserAccount & LoginModal)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/auth/switch-role', [AuthController::class, 'switchRoleSimulation'])->middleware('auth:sanctum');
    Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

    // 3. Snapshot Data Lengkap & Dashboard KPI
    Route::get('/sync/snapshot', [InventoryApiController::class, 'getFullSnapshot']);
    Route::get('/dashboard/summary', [InventoryApiController::class, 'getDashboardSummary']);

    // 4. Master Inventaris
    Route::get('/inventaris', [InventoryApiController::class, 'indexInventaris']);
    Route::get('/inventaris/{id}', [InventoryApiController::class, 'showInventaris']);
    Route::post('/inventaris', [InventoryApiController::class, 'storeInventaris'])->middleware('auth:sanctum');
    Route::put('/inventaris/{id}', [InventoryApiController::class, 'updateInventaris'])->middleware('auth:sanctum');
    Route::delete('/inventaris/{id}', [InventoryApiController::class, 'destroyInventaris'])->middleware('auth:sanctum');

    // 5. Sirkulasi Barang Masuk & Keluar
    Route::get('/sirkulasi/masuk', [InventoryApiController::class, 'indexBarangMasuk']);
    Route::post('/sirkulasi/masuk', [InventoryApiController::class, 'storeBarangMasuk'])->middleware('auth:sanctum');
    Route::get('/sirkulasi/keluar', [InventoryApiController::class, 'indexBarangKeluar']);
    Route::post('/sirkulasi/keluar', [InventoryApiController::class, 'storeBarangKeluar'])->middleware('auth:sanctum');

    // 6. Peminjaman & Pengembalian Aset
    Route::get('/peminjaman', [InventoryApiController::class, 'indexPeminjaman']);
    Route::post('/peminjaman', [InventoryApiController::class, 'storePeminjaman'])->middleware('auth:sanctum');
    Route::post('/peminjaman/{id}/return', [InventoryApiController::class, 'returnPeminjaman'])->middleware('auth:sanctum');

    // 7. Mutasi & Pengalihan Aset Tetap
    Route::get('/mutasi', [InventoryApiController::class, 'indexMutasi']);
    Route::post('/mutasi', [InventoryApiController::class, 'storeMutasi'])->middleware('auth:sanctum');
    Route::post('/mutasi/{id}/approve', [InventoryApiController::class, 'approveMutasi'])->middleware('auth:sanctum');
    Route::post('/mutasi/{id}/reject', [InventoryApiController::class, 'rejectMutasi'])->middleware('auth:sanctum');

    // 8. Pemeliharaan & Tiket Maintenance
    Route::get('/maintenance', [InventoryApiController::class, 'indexMaintenance']);
    Route::post('/maintenance', [InventoryApiController::class, 'storeMaintenance'])->middleware('auth:sanctum');
    Route::post('/maintenance/{id}/update-status', [InventoryApiController::class, 'updateStatusMaintenance'])->middleware('auth:sanctum');

    // 9. Master Data Lainnya
    Route::apiResource('karyawan', \App\Http\Controllers\KaryawanController::class);
    Route::apiResource('departemen', \App\Http\Controllers\DepartemenController::class);
    Route::apiResource('kategori', \App\Http\Controllers\KategoriController::class);
    Route::apiResource('lokasi', \App\Http\Controllers\LokasiController::class);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);

    // 10. Laporan & Ekspor Ber-KOP Resmi
    Route::get('/laporan/rekap', [InventoryApiController::class, 'laporanRekap']);
    Route::get('/laporan/mutasi-bulanan', [InventoryApiController::class, 'laporanMutasiBulanan']);

    // 11. Audit Trail
    Route::get('/audit-trail', [InventoryApiController::class, 'auditTrail']);
    Route::post('/audit-trail/clear', [InventoryApiController::class, 'clearAuditTrail'])->middleware('auth:sanctum');

    // 12. Integrasi WhatsApp Gateway
    Route::post('/whatsapp/send-reminder', [InventoryApiController::class, 'dispatchWhatsApp'])->middleware('auth:sanctum');
});
