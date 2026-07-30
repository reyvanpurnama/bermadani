<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_shu_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rat_session_id')->constrained('rat_sessions')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->decimal('simpanan_wajib_amount', 15, 2)->default(0);
            $table->decimal('portion_percentage', 8, 4)->default(0)->comment('Porsi (% simwa / total simwa)');
            $table->decimal('shu_amount', 15, 2)->default(0)->comment('Nominal SHU Rp yang didapat');
            $table->boolean('is_disbursed')->default(false)->comment('Apakah sudah dicairkan/diambil');
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();

            $table->unique(['rat_session_id', 'member_id'], 'rat_member_shu_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_shu_distributions');
    }
};
