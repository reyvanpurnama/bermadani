<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_loan_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->index();
            $table->string('period')->index(); // e.g. "2025-11"

            $table->string('raw_name');
            $table->text('raw_uraian')->nullable();
            $table->decimal('pokok_amount', 15, 2)->default(0);
            $table->decimal('jasa_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->unsignedBigInteger('matched_member_id')->nullable();
            $table->string('status')->default('UNVERIFIED'); // MATCH, ARREARS, UNVERIFIED
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('matched_member_id')->references('id')->on('members')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_loan_imports');
    }
};
