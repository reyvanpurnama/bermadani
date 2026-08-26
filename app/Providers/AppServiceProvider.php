<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Morph Map: Ensures polymorphic types (e.g. activity_logs.loggable_type)
         * always store consistent short aliases, even after the Domain refactoring.
         * This prevents mismatches between old data ('App\Models\User') and
         * new code ('App\Shared\Models\User').
         */
        Relation::enforceMorphMap([
            // Shared
            'user'                  => \App\Shared\Models\User::class,
            'activity_log'          => \App\Shared\Models\ActivityLog::class,
            // Koperasi
            'member'                => \App\Domains\Koperasi\Models\Member::class,
            'member_koperasi'       => \App\Domains\Koperasi\Models\MemberKoperasi::class,
            'saving'                => \App\Domains\Koperasi\Models\Saving::class,
            'simpanan_transaction'  => \App\Domains\Koperasi\Models\SimpananTransaction::class,
            'simpanan_payment'      => \App\Domains\Koperasi\Models\SimpananPayment::class,
            'loan'                  => \App\Domains\Koperasi\Models\Loan::class,
            'loan_payment'          => \App\Domains\Koperasi\Models\LoanPayment::class,
            'shu_distribution'      => \App\Domains\Koperasi\Models\MemberShuDistribution::class,
            'rat_session'           => \App\Domains\Koperasi\Models\RatSession::class,
            'rat_manual_entry'      => \App\Domains\Koperasi\Models\RatManualEntry::class,
            // Minimarket
            'product'               => \App\Domains\Minimarket\Models\Product::class,
            'category'              => \App\Domains\Minimarket\Models\Category::class,
            'transaction'           => \App\Domains\Minimarket\Models\Transaction::class,
            'transaction_item'      => \App\Domains\Minimarket\Models\TransactionItem::class,
            'cashier_shift'         => \App\Domains\Minimarket\Models\CashierShift::class,
            'stock_movement'        => \App\Domains\Minimarket\Models\StockMovement::class,
            'restock_request'       => \App\Domains\Minimarket\Models\RestockRequest::class,
            'member_minimarket'     => \App\Domains\Minimarket\Models\MemberMinimarket::class,
            'member_points_history' => \App\Domains\Minimarket\Models\MemberPointsHistory::class,
            // Supplier
            'supplier'              => \App\Domains\Supplier\Models\Supplier::class,
            'consignment_batch'     => \App\Domains\Supplier\Models\ConsignmentBatch::class,
            'consignment_item'      => \App\Domains\Supplier\Models\ConsignmentItem::class,
            'supplier_payout'       => \App\Domains\Supplier\Models\SupplierPayout::class,
            'supplier_notification' => \App\Domains\Supplier\Models\SupplierNotification::class,
            // Accounting
            'financial_transaction' => \App\Domains\Accounting\Models\FinancialTransaction::class,
            'bank_transaction'      => \App\Domains\Accounting\Models\BankTransaction::class,
            'work_log'              => \App\Domains\Accounting\Models\WorkLog::class,
        ]);
    }
}
