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
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->enum('approvalStatus', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->after('status');
            $table->text('rejectionReason')->nullable()->after('approvalStatus');
            $table->timestamp('approvedAt')->nullable()->after('rejectionReason');
            $table->unsignedBigInteger('approvedBy')->nullable()->after('approvedAt');
            $table->boolean('isDraft')->default(false)->after('isActive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image', 'approvalStatus', 'rejectionReason', 'approvedAt', 'approvedBy', 'isDraft']);
        });
    }
};
