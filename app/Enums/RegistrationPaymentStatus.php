<?php

namespace App\Enums;

enum RegistrationPaymentStatus: string
{
    case UNPAID = 'UNPAID';
    case PENDING_VERIFICATION = 'PENDING_VERIFICATION';
    case VERIFIED = 'VERIFIED';
    case REJECTED = 'REJECTED';

    /**
     * Get badge color for display
     */
    public function color(): string
    {
        return match($this) {
            self::UNPAID => 'gray',
            self::PENDING_VERIFICATION => 'yellow',
            self::VERIFIED => 'green',
            self::REJECTED => 'red',
        };
    }

    /**
     * Get label in Bahasa Indonesia
     */
    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Belum Dibayar',
            self::PENDING_VERIFICATION => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
        };
    }
}
