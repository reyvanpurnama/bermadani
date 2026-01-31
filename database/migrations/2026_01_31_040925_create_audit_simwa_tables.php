<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_simwa_imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->index(); // e.g., "agustus-2024.csv"
            $table->string('period')->index(); // e.g., "2024-08"

            // Raw data from CSV
            $table->string('raw_name'); // e.g., "Rivai (Pa Fey)"
            $table->text('raw_uraian')->nullable();
            $table->decimal('amount', 15, 2);

            // Verification Status
            $table->unsignedBigInteger('matched_member_id')->nullable();
            $table->boolean('is_processed')->default(false); // If verified and ready to sync
            $table->timestamps();

            // Foreign key (optional, can be nullable if valid member not found yet)
            $table->foreign('matched_member_id')->references('id')->on('members')->onDelete('set null');
        });

        Schema::create('audit_simwa_name_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('raw_name')->unique(); // The weird name from CSV
            $table->unsignedBigInteger('member_id'); // The real member ID
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_simwa_name_mappings');
        Schema::dropIfExists('audit_simwa_imports');
    }
};
