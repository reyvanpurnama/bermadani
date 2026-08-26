<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Member;
use Illuminate\Support\Facades\DB;
use Exception;

class PayWithSukarelaAction
{
    /**
     * Pay for shopping using Simpanan Sukarela balance
     */
    public function execute(Member $member, float $amount, string $description, ?string $transactionId = null): mixed
    {
        return DB::transaction(function () use ($member, $amount, $description, $transactionId) {
            if ($member->simpananSukarela < $amount) {
                throw new Exception('Saldo Simpanan Sukarela tidak mencukupi');
            }

            // Record withdrawal with transaction reference
            $saving = $member->savings()->create([
                'type' => 'WITHDRAWAL',
                'amount' => $amount,
                'description' => $description,
                'date' => now(),
            ]);

            $member->decrement('simpananSukarela', $amount);

            return $saving;
        });
    }
}
