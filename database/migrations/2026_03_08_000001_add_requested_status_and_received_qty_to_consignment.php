<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add REQUESTED to consignment_batches.status enum
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('consignment_batches', function (Blueprint $table) {
                $table->string('status')->default('ACTIVE')->change();
            });
        } else {
            DB::statement("ALTER TABLE consignment_batches MODIFY COLUMN status ENUM('REQUESTED','ACTIVE','PENDING_SETTLEMENT','SETTLED','CANCELLED') DEFAULT 'ACTIVE'");
        }

        // 2. Add receivedQty to consignment_items (jumlah fisik yang benar-benar diterima kasir)
        if (! Schema::hasColumn('consignment_items', 'receivedQty')) {
            Schema::table('consignment_items', function (Blueprint $table) {
                $table->integer('receivedQty')->default(0)->after('initialQty')->comment('Qty fisik yang diterima kasir');
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::table('consignment_batches')
                ->where('status', 'REQUESTED')
                ->update(['status' => 'ACTIVE']);
        } else {
            DB::statement("ALTER TABLE consignment_batches MODIFY COLUMN status ENUM('ACTIVE','PENDING_SETTLEMENT','SETTLED','CANCELLED') DEFAULT 'ACTIVE'");
        }

        if (Schema::hasColumn('consignment_items', 'receivedQty')) {
            Schema::table('consignment_items', function (Blueprint $table) {
                $table->dropColumn('receivedQty');
            });
        }
    }
};
