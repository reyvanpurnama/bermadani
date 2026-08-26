<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Loan;
use Illuminate\Support\Facades\DB;

class ApproveLoanAction
{
    /**
     * Approve a loan
     */
    public function execute(Loan $loan, mixed $approvedBy): mixed
    {
        return DB::transaction(function () use ($loan, $approvedBy) {
            $loan->update([
                'status' => 'ACTIVE',
                'approvedAt' => now(),
                'approvedBy' => $approvedBy,
            ]);

            return $loan;
        });
    }
}
