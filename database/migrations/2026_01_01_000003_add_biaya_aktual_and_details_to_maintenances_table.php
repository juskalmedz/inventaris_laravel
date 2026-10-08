<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to add missing biaya_aktual and maintenance fields.
     */
    public function up(): void
    {
        if (Schema::hasTable('maintenances')) {
            Schema::table('maintenances', function (Blueprint $table) {
                if (!Schema::hasColumn('maintenances', 'nomor_tiket')) {
                    $table->string('nomor_tiket', 50)->nullable()->after('id');
                }
                if (!Schema::hasColumn('maintenances', 'jenis_maintenance')) {
                    $table->string('jenis_maintenance', 50)->default('Perbaikan')->after('inventaris_id');
                }
                if (!Schema::hasColumn('maintenances', 'tanggal_mulai')) {
                    $table->date('tanggal_mulai')->nullable()->after('jenis_maintenance');
                }
                if (!Schema::hasColumn('maintenances', 'deskripsi_masalah')) {
                    $table->text('deskripsi_masalah')->nullable()->after('tanggal_selesai');
                }
                if (!Schema::hasColumn('maintenances', 'vendor')) {
                    $table->string('vendor')->nullable()->after('deskripsi_masalah');
                }
                if (!Schema::hasColumn('maintenances', 'estimasi_biaya')) {
                    $table->decimal('estimasi_biaya', 15, 2)->default(0)->after('vendor');
                }
                if (!Schema::hasColumn('maintenances', 'biaya_aktual')) {
                    $table->decimal('biaya_aktual', 15, 2)->default(0)->after('estimasi_biaya');
                }
                if (!Schema::hasColumn('maintenances', 'tindakan_perbaikan')) {
                    $table->text('tindakan_perbaikan')->nullable()->after('biaya_aktual');
                }
            });

            // Sinkronkan data baris lama jika ada (tiket -> nomor_tiket, teknisi -> vendor, biaya -> biaya_aktual)
            try {
                if (Schema::hasColumn('maintenances', 'nomor_tiket') && Schema::hasColumn('maintenances', 'tiket')) {
                    DB::table('maintenances')->whereNull('nomor_tiket')->orWhere('nomor_tiket', '')->update([
                        'nomor_tiket' => DB::raw("COALESCE(NULLIF(tiket, ''), CONCAT('MTN-', LPAD(id, 4, '0')))")
                    ]);
                }
                if (Schema::hasColumn('maintenances', 'vendor') && Schema::hasColumn('maintenances', 'teknisi')) {
                    DB::table('maintenances')->whereNull('vendor')->orWhere('vendor', '')->update([
                        'vendor' => DB::raw("COALESCE(NULLIF(teknisi, ''), 'Vendor Rekanan')")
                    ]);
                }
                if (Schema::hasColumn('maintenances', 'deskripsi_masalah') && Schema::hasColumn('maintenances', 'deskripsi')) {
                    DB::table('maintenances')->whereNull('deskripsi_masalah')->orWhere('deskripsi_masalah', '')->update([
                        'deskripsi_masalah' => DB::raw("COALESCE(NULLIF(deskripsi, ''), 'Pemeliharaan aset')")
                    ]);
                }
                if (Schema::hasColumn('maintenances', 'tanggal_mulai') && Schema::hasColumn('maintenances', 'tanggal_lapor')) {
                    DB::table('maintenances')->whereNull('tanggal_mulai')->update([
                        'tanggal_mulai' => DB::raw("tanggal_lapor")
                    ]);
                }
                if (Schema::hasColumn('maintenances', 'biaya_aktual') && Schema::hasColumn('maintenances', 'biaya')) {
                    DB::table('maintenances')->where('biaya_aktual', 0)->where('biaya', '>', 0)->update([
                        'biaya_aktual' => DB::raw("biaya")
                    ]);
                }
                if (Schema::hasColumn('maintenances', 'estimasi_biaya') && Schema::hasColumn('maintenances', 'biaya')) {
                    DB::table('maintenances')->where('estimasi_biaya', 0)->where('biaya', '>', 0)->update([
                        'estimasi_biaya' => DB::raw("biaya")
                    ]);
                }
            } catch (\Throwable $e) {
                // Abaikan kesalahan jika data sync tidak krusial saat migrate
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('maintenances')) {
            Schema::table('maintenances', function (Blueprint $table) {
                $columnsToDrop = [];
                foreach (['nomor_tiket', 'jenis_maintenance', 'tanggal_mulai', 'deskripsi_masalah', 'vendor', 'estimasi_biaya', 'biaya_aktual', 'tindakan_perbaikan'] as $col) {
                    if (Schema::hasColumn('maintenances', $col)) {
                        $columnsToDrop[] = $col;
                    }
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
