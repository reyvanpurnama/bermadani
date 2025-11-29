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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('ownerName');
            $table->string('businessName');
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address');
            $table->text('description')->nullable();
            $table->string('productCategory')->nullable();
            $table->string('password');
            
            // Payment settings
            $table->decimal('monthlyFee', 15, 2)->default(25000);
            $table->enum('preferredPaymentMethod', ['CASH', 'TRANSFER', 'CREDIT'])->default('TRANSFER');
            $table->string('paymentTerms')->nullable();
            $table->boolean('isPaymentActive')->default(false);
            $table->enum('paymentStatus', ['UNPAID', 'PARTIAL', 'PAID', 'PAID_PENDING_APPROVAL', 'PAID_APPROVED', 'PAID_REJECTED'])->default('UNPAID');
            $table->timestamp('lastPaymentDate')->nullable();
            $table->timestamp('nextPaymentDue')->nullable();
            
            // Grace period & suspension
            $table->integer('paymentGraceDays')->default(7);
            $table->boolean('isSuspendedForPayment')->default(false);
            $table->timestamp('suspendedAt')->nullable();
            $table->text('suspensionReason')->nullable();
            
            // Product limits
            $table->integer('maxActiveProducts')->default(10);
            $table->integer('currentActiveProducts')->default(0);
            
            // Approval workflow
            $table->enum('status', ['PENDING', 'PENDING_REVIEW', 'APPROVED_PENDING_PAYMENT', 'PAID_PENDING_APPROVAL', 'ACTIVE', 'REJECTED', 'SUSPENDED'])->default('PENDING');
            $table->timestamp('approvedAt')->nullable();
            $table->unsignedBigInteger('approvedById')->nullable();
            $table->text('rejectedReason')->nullable();
            
            // Product evaluation
            $table->tinyInteger('productQualityScore')->nullable();
            $table->tinyInteger('productPriceScore')->nullable();
            $table->tinyInteger('productPackagingScore')->nullable();
            $table->decimal('productAverageScore', 3, 2)->nullable();
            $table->text('evaluationNotes')->nullable();
            $table->unsignedBigInteger('evaluatedBy')->nullable();
            $table->timestamp('evaluatedAt')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'is_active']);
            $table->index('email');
            $table->index('approvedById');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
