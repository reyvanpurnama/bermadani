<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rat_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('year')->comment('Tahun Buku');
            $table->date('event_date')->comment('Tanggal Pelaksanaan RAT');
            $table->string('title')->comment('Judul RAT');
            $table->decimal('total_net_profit', 15, 2)->default(0)->comment('Total Laba Bersih / SHU Koperasi');
            $table->decimal('member_allocation_percentage', 5, 2)->default(100.00)->comment('Persentase Alokasi SHU Anggota');
            $table->decimal('total_member_shu', 15, 2)->default(0)->comment('Nominal SHU yang Dibagikan ke Anggota');
            $table->decimal('total_simpanan_wajib_snapshot', 15, 2)->default(0)->comment('Total Simpanan Wajib Anggota Aktif pada RAT');
            $table->string('status')->default('DRAFT')->comment('DRAFT / FINALIZED');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique('year', 'rat_sessions_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rat_sessions');
    }
};
