<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooperative_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general'); // general, officers, bank, rat
            $table->string('label')->nullable(); // human-readable label for admin UI
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooperative_settings');
    }
};
