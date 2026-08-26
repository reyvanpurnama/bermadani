<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Loan;
use Illuminate\Support\Facades\DB;

class ProcessLoanPaymentAction
{
    /**
     * Add payment to a loan
     */
    public function execute(Loan $loan, float $amount, ?string $description = null, mixed $date = null): mixed
    {
        return DB::transaction(function () use ($loan, $amount, $description, $date) {
            $payment = $loan->payments()->create([
                'amount' => $amount,
                'paymentDate' => $date ?? now(),
                'description' => $description,
            ]);

            $loan->decrement('remainingAmount', $amount);

            if ($loan->remainingAmount <= 0) {
                $loan->update(['status' => 'COMPLETED']);
            }

            return $payment;
        });
    }
}
