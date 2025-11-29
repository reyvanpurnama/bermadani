<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ADMIN = 'ADMIN';
    case KASIR = 'KASIR';
    case SUPPLIER = 'SUPPLIER';
    case USER = 'USER';
    case DEVELOPER = 'DEVELOPER';

    public function getLabel(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Administrator',
            self::KASIR => 'Kasir',
            self::SUPPLIER => 'Supplier',
            self::USER => 'User',
            self::DEVELOPER => 'Developer',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'danger',
            self::ADMIN => 'warning',
            self::KASIR => 'success',
            self::SUPPLIER => 'info',
            self::USER => 'gray',
            self::DEVELOPER => 'purple',
        };
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this, [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::DEVELOPER,
        ]);
    }

    public function canAccessPOS(): bool
    {
        return in_array($this, [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::KASIR,
            self::DEVELOPER,
        ]);
    }
}
