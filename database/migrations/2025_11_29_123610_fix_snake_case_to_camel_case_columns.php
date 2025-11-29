<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix snake_case columns to camelCase for consistency
     */
    public function up(): void
    {
        // transactions: member_id -> memberId
        if (Schema::hasColumn('transactions', 'member_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->renameColumn('member_id', 'memberId');
            });
        }

        // transaction_items: transaction_id -> transactionId, product_id -> productId
        if (Schema::hasColumn('transaction_items', 'transaction_id')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->renameColumn('transaction_id', 'transactionId');
            });
        }
        if (Schema::hasColumn('transaction_items', 'product_id')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->renameColumn('product_id', 'productId');
            });
        }

        // stock_movements: product_id -> productId
        if (Schema::hasColumn('stock_movements', 'product_id')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->renameColumn('product_id', 'productId');
            });
        }

        // suppliers: is_active -> isActive (if exists and different from existing isActive)
        if (Schema::hasColumn('suppliers', 'is_active') && !Schema::hasColumn('suppliers', 'isActive')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->renameColumn('is_active', 'isActive');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert: memberId -> member_id
        if (Schema::hasColumn('transactions', 'memberId')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->renameColumn('memberId', 'member_id');
            });
        }

        // Revert: transactionId -> transaction_id, productId -> product_id
        if (Schema::hasColumn('transaction_items', 'transactionId')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->renameColumn('transactionId', 'transaction_id');
            });
        }
        if (Schema::hasColumn('transaction_items', 'productId')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->renameColumn('productId', 'product_id');
            });
        }

        // Revert: productId -> product_id
        if (Schema::hasColumn('stock_movements', 'productId')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->renameColumn('productId', 'product_id');
            });
        }

        // Revert: isActive -> is_active
        if (Schema::hasColumn('suppliers', 'isActive')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->renameColumn('isActive', 'is_active');
            });
        }
    }
};
