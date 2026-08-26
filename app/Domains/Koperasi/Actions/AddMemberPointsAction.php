<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Member;
use Illuminate\Support\Facades\DB;

class AddMemberPointsAction
{
    public function __construct(
        private readonly UpdateMemberTierAction $updateMemberTierAction
    ) {}

    /**
     * Add points to member and update tier
     */
    public function execute(Member $member, int $points, string $description, ?string $transactionId = null, ?string $expiresAt = null): mixed
    {
        return DB::transaction(function () use ($member, $points, $description, $transactionId, $expiresAt) {
            $newBalance = $member->points + $points;

            $member->pointsHistory()->create([
                'transactionId' => $transactionId,
                'type' => 'EARNED',
                'points' => $points,
                'balance' => $newBalance,
                'description' => $description,
                'expiresAt' => $expiresAt,
            ]);

            $member->increment('points', $points);
            
            $this->updateMemberTierAction->execute($member);

            return $member;
        });
    }
}
