<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Member;
use Illuminate\Support\Facades\DB;

class PaySimpananAction
{
    /**
     * Add simpanan and update member's balance
     */
    public function execute(Member $member, string $type, float $amount, ?string $description = null): mixed
    {
        return DB::transaction(function () use ($member, $type, $amount, $description) {
            // Create saving record
            $saving = $member->savings()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'date' => now(),
            ]);

            // Update member's simpanan balance
            switch ($type) {
                case 'POKOK':
                    $member->increment('simpananPokok', $amount);
                    break;
                case 'WAJIB':
                    $member->increment('simpananWajib', $amount);
                    break;
                case 'SUKARELA':
                    $member->increment('simpananSukarela', $amount);
                    break;
                case 'WITHDRAWAL':
                    $member->decrement('simpananSukarela', $amount);
                    break;
            }

            return $saving;
        });
    }
}
