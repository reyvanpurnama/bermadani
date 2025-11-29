<?php

namespace App\Enums;

enum ProductStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case SEASONAL = 'SEASONAL';

    public function getLabel(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::SEASONAL => 'Musiman',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::SEASONAL => 'warning',
        };
    }
}
