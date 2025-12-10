<?php

namespace App\Enums;

enum SupplierStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case ACTIVE = 'ACTIVE';
    case SUSPENDED = 'SUSPENDED';
    case REJECTED = 'REJECTED';

    /**
     * Get badge color for display
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'blue',
            self::ACTIVE => 'green',
            self::SUSPENDED => 'gray',
            self::REJECTED => 'red',
        };
    }

    /**
     * Get label in Bahasa Indonesia
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::APPROVED => 'Disetujui',
            self::ACTIVE => 'Aktif',
            self::SUSPENDED => 'Ditangguhkan',
            self::REJECTED => 'Ditolak',
        };
    }
}
