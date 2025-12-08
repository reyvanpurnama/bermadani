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
        Schema::table('suppliers', function (Blueprint $table) {
            // Bank account fields (nullable for future use)
            $table->string('bankName')->nullable()->after('password');
            $table->string('bankAccountNumber')->nullable()->after('bankName');
            $table->string('bankAccountHolderName')->nullable()->after('bankAccountNumber');
            
            // Registration payment fields
            $table->decimal('registrationFee', 10, 2)->default(25000)->after('bankAccountHolderName');
            $table->string('registrationPaymentProof')->nullable()->after('registrationFee');
            $table->enum('registrationPaymentStatus', ['UNPAID', 'PENDING_VERIFICATION', 'VERIFIED', 'REJECTED'])->default('UNPAID')->after('registrationPaymentProof');
            $table->timestamp('registrationPaymentVerifiedAt')->nullable()->after('registrationPaymentStatus');
            $table->unsignedBigInteger('registrationPaymentVerifiedBy')->nullable()->after('registrationPaymentVerifiedAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'bankName', 
                'bankAccountNumber', 
                'bankAccountHolderName',
                'registrationFee',
                'registrationPaymentProof',
                'registrationPaymentStatus',
                'registrationPaymentVerifiedAt',
                'registrationPaymentVerifiedBy'
            ]);
        });
    }
};
