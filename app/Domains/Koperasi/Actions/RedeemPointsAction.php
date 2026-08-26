<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Member;
use Illuminate\Support\Facades\DB;
use Exception;

class RedeemPointsAction
{
    /**
     * Redeem member points
     */
    public function execute(Member $member, int $points, string $description): mixed
    {
        return DB::transaction(function () use ($member, $points, $description) {
            if ($member->points < $points) {
                throw new Exception('Insufficient points');
            }

            $newBalance = $member->points - $points;

            $member->pointsHistory()->create([
                'type' => 'REDEEMED',
                'points' => -$points,
                'balance' => $newBalance,
                'description' => $description,
            ]);

            $member->decrement('points', $points);

            return $member;
        });
    }
}
