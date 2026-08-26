<?php

declare(strict_types=1);

namespace App\Domains\Koperasi\Actions;

use App\Domains\Koperasi\Models\Member;
use Illuminate\Support\Facades\DB;

class UpdateMemberTierAction
{
    /**
     * Update member tier based on points
     */
    public function execute(Member $member): mixed
    {
        return DB::transaction(function () use ($member) {
            // Tier based on POINTS (Rp1.000 = 1 poin)
            // BRONZE: 0-199, SILVER: 200-749, GOLD: 750-1999, PLATINUM: 2000+
            $tier = match (true) {
                $member->points >= 2000 => 'PLATINUM',
                $member->points >= 750 => 'GOLD',
                $member->points >= 200 => 'SILVER',
                default => 'BRONZE',
            };

            if ($member->tier !== $tier) {
                $member->update(['tier' => $tier]);
            }

            return $member;
        });
    }
}
