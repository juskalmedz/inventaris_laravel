<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('inventaris')) {
            Schema::table('inventaris', function (Blueprint $table) {
                if (!Schema::hasColumn('inventaris', 'barcode')) {
                    $table->string('barcode', 100)->nullable()->after('kode_barang')->index();
                }
            });

            // Backfill barcode for existing items if empty
            try {
                $items = DB::table('inventaris')->whereNull('barcode')->orWhere('barcode', '')->get();
                foreach ($items as $item) {
                    $generatedBarcode = '899' . str_pad($item->id, 9, '0', STR_PAD_LEFT);
                    DB::table('inventaris')->where('id', $item->id)->update(['barcode' => $generatedBarcode]);
                }
            } catch (\Throwable $e) {
                // Ignore backfill errors on unusual drivers
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventaris')) {
            Schema::table('inventaris', function (Blueprint $table) {
                if (Schema::hasColumn('inventaris', 'barcode')) {
                    $table->dropColumn('barcode');
                }
            });
        }
    }
};
