<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Loan;
use Illuminate\Support\Facades\DB;

class RejectLoanAction
{
    /**
     * Reject a loan
     */
    public function execute(Loan $loan): mixed
    {
        return DB::transaction(function () use ($loan) {
            $loan->update(['status' => 'REJECTED']);
            return $loan;
        });
    }
}
