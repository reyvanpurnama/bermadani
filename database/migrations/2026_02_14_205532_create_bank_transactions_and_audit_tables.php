<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table 1: Category Rules for Auto-Categorization
        Schema::create('audit_bank_category_rules', function (Blueprint $table) {
            $table->id();
            $table->string('pattern')->unique()->comment('Regex pattern untuk matching keterangan');
            $table->enum('type', ['INCOME', 'EXPENSE']);
            $table->string('category');
            $table->integer('priority')->default(0)->comment('Higher = check first');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });

        // Table 2: Staging/Import table for CSV data
        Schema::create('audit_bank_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('period', 7)->comment('YYYY-MM');
            $table->date('transaction_date');
            $table->time('transaction_time');
            $table->text('keterangan');
            $table->decimal('debet', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2);

            // Auto-detected fields
            $table->enum('detected_type', ['INCOME', 'EXPENSE'])->nullable();
            $table->string('detected_category')->nullable();

            // Manual override fields
            $table->enum('manual_type', ['INCOME', 'EXPENSE'])->nullable();
            $table->string('manual_category')->nullable();
            $table->text('manual_description')->nullable();

            // Status tracking
            $table->boolean('is_reviewed')->default(false);
            $table->boolean('is_synced')->default(false);
            $table->unsignedBigInteger('synced_bank_transaction_id')->nullable();

            $table->timestamps();

            $table->index('period');
            $table->index('transaction_date');
            $table->index(['detected_type', 'detected_category']);
            $table->index('is_synced');
            $table->index('is_reviewed');
        });

        // Table 3: Final Bank Transactions (approved data)
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->time('transaction_time');
            $table->text('description');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2);
            $table->enum('type', ['INCOME', 'EXPENSE']);
            $table->string('category');
            $table->string('period', 7)->comment('YYYY-MM');
            $table->string('source_file')->nullable();
            $table->timestamps();

            $table->index('transaction_date');
            $table->index(['type', 'category']);
            $table->index('period');
        });

        // Insert default category rules
        DB::table('audit_bank_category_rules')->insert([
            ['pattern' => 'QRIS (MPM|OVB)', 'type' => 'INCOME', 'category' => 'Penjualan QRIS', 'priority' => 100, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'TRF DR RISMA', 'type' => 'INCOME', 'category' => 'Transfer Masuk', 'priority' => 90, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'BIFAST:INC', 'type' => 'INCOME', 'category' => 'Transfer Masuk', 'priority' => 90, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'INSKN MUABIDJA', 'type' => 'INCOME', 'category' => 'Dana Universitas', 'priority' => 90, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'SBCM:PINBUK.*[Gg]aji', 'type' => 'INCOME', 'category' => 'Potongan Gaji', 'priority' => 85, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'BAGI HASIL', 'type' => 'INCOME', 'category' => 'Bagi Hasil', 'priority' => 80, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'PENARIKAN TUNAI', 'type' => 'EXPENSE', 'category' => 'Penarikan Tunai', 'priority' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'TELLER.*BI-FAST', 'type' => 'EXPENSE', 'category' => 'Transfer Keluar', 'priority' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'PINBUK TRF', 'type' => 'EXPENSE', 'category' => 'Transfer Keluar', 'priority' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'FEE.*BI-FAST', 'type' => 'EXPENSE', 'category' => 'Biaya Transfer', 'priority' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'Fee PINBUK', 'type' => 'EXPENSE', 'category' => 'Biaya Transfer', 'priority' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'POTONGAN.*ADM', 'type' => 'EXPENSE', 'category' => 'Biaya Admin Bank', 'priority' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['pattern' => 'POT\.PAJAK', 'type' => 'EXPENSE', 'category' => 'Pajak Bagi Hasil', 'priority' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('audit_bank_imports');
        Schema::dropIfExists('audit_bank_category_rules');
    }
};
