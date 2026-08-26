<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\MemberShuDistribution;
use App\Domains\Koperasi\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;

class DisburseShuAction
{
    /**
     * Mark SHU as disbursed and create financial transaction
     */
    public function execute(MemberShuDistribution $memberShuDistribution): mixed
    {
        return DB::transaction(function () use ($memberShuDistribution) {
            if ($memberShuDistribution->is_disbursed) {
                return null;
            }

            $transaction = FinancialTransaction::create([
                'transactionDate' => now(),
                'type' => 'EXPENSE',
                'category' => 'Pembagian SHU',
                'amount' => $memberShuDistribution->shu_amount,
                'description' => "Pencairan SHU RAT {$memberShuDistribution->ratSession?->year} untuk anggota {$memberShuDistribution->member?->name} ({$memberShuDistribution->member?->nomorAnggota})",
                'userId' => auth()->id(),
            ]);

            $memberShuDistribution->update([
                'is_disbursed' => true,
                'disbursed_at' => now(),
                'financial_transaction_id' => $transaction->id,
            ]);
            
            return $memberShuDistribution;
        });
    }
}
