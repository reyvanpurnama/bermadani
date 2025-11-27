<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            // 1. Ganti Primary Key jadi standar Laravel (BigInt Auto Increment)
            // Biar lu gak pusing mikirin UUID generator dulu.
            $table->id(); 

            // 2. INI PERBAIKANNYA BRO!
            // Kita pake unsignedBigInteger biar COCOK sama id di tabel users.
            // Kita kasih unique() karena 1 User cuma boleh jadi 1 Member.
            $table->unsignedBigInteger('userId')->unique();

            $table->string('nomorAnggota')->unique();
            $table->string('name');
            $table->string('email')->unique(); // Hati-hati, user udah punya email. Perlu email lagi? (Opsional)
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->string('unitKerja');
            
            // Timestamp default current oke
            $table->timestamp('joinDate')->useCurrent();
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'SUSPENDED'])->default('ACTIVE');
            $table->boolean('isMemberKoperasi')->default(true);
            
            // Simpanan fields (Uang)
            $table->decimal('simpananPokok', 15, 2)->default(0);
            $table->decimal('simpananWajib', 15, 2)->default(0);
            $table->decimal('simpananSukarela', 15, 2)->default(0);
            
            // Loyalty
            $table->integer('points')->default(0);
            $table->enum('tier', ['BRONZE', 'SILVER', 'GOLD', 'PLATINUM'])->default('BRONZE');
            $table->decimal('totalSpent', 15, 2)->default(0);
            $table->timestamp('lastPurchase')->nullable();
            
            $table->timestamps();
            
            // Foreign key DEFINITION
            // Karena tipe data di atas udah diganti jadi unsignedBigInteger, ini bakal BERHASIL.
            $table->foreign('userId')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['status', 'joinDate']);
            $table->index('nomorAnggota');
            $table->index('tier');
            $table->index('unitKerja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};