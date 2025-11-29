<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'CASH';
    case TRANSFER = 'TRANSFER';
    case CREDIT = 'CREDIT';

    public function getLabel(): string
    {
        return match($this) {
            self::CASH => 'Tunai',
            self::TRANSFER => 'Transfer Bank',
            self::CREDIT => 'Kredit/Tempo',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::CASH => 'success',
            self::TRANSFER => 'info',
            self::CREDIT => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::CASH => '💵',
            self::TRANSFER => '🏦',
            self::CREDIT => '💳',
        };
    }
}
