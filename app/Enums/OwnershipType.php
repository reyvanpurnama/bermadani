<?php

namespace App\Enums;

enum OwnershipType: string
{
    case TOKO = 'TOKO';
    case TITIPAN = 'TITIPAN';
    case SUPPLIER = 'SUPPLIER';

    public function getLabel(): string
    {
        return match($this) {
            self::TOKO => 'Milik Toko',
            self::TITIPAN => 'Titipan/Konsinyasi',
            self::SUPPLIER => 'Dari Supplier',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::TOKO => 'success',
            self::TITIPAN => 'warning',
            self::SUPPLIER => 'info',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::TOKO => '🏪',
            self::TITIPAN => '🤝',
            self::SUPPLIER => '📦',
        };
    }
}
