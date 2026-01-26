<?php

namespace App\Models;

/**
 * MemberKoperasi Model
 * 
 * Alias untuk Member model dengan nama yang lebih jelas.
 * Table: members (untuk Member Koperasi dengan simpanan)
 * 
 * Auto-create Member Minimarket ketika Member Koperasi dibuat.
 */
class MemberKoperasi extends Member
{
    /**
     * Force menggunakan table 'members' (bukan 'member_koperasis')
     */
    protected $table = 'members';

    /**
     * Boot method - Auto create Member Minimarket
     */
    protected static function booted()
    {
        parent::booted();

        static::created(function ($memberKoperasi) {
            // Auto-create Member Minimarket DISABLED by user request
            // if ($memberKoperasi->userId && !MemberMinimarket::where('userId', $memberKoperasi->userId)->exists()) {
            //     ...
            // }
        });
    }

    /**
     * Generate Member Number untuk Minimarket
     * Format: MM-YY-XXXXX
     */
    private static function generateMemberNumberMinimarket()
    {
        $year = date('y');

        $lastMember = MemberMinimarket::whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastMember ? intval(substr($lastMember->memberNumber, -5)) + 1 : 1;

        return 'MM-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate EAN-13 Barcode untuk kartu member
     */
    private static function generateCardBarcode()
    {
        // EAN-13 format: 628 (Indonesia) + 1234 (Koperasi) + 5 digit random + 1 check digit
        $prefix = '628';
        $company = '1234';
        $random = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        $code = $prefix . $company . $random;

        // Calculate EAN-13 check digit
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0) ? (int) $code[$i] : (int) $code[$i] * 3;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;

        return $code . $checkDigit;
    }

    /**
     * Relasi ke Member Minimarket
     */
    public function memberMinimarket()
    {
        return $this->hasOne(MemberMinimarket::class, 'userId', 'userId');
    }
}
