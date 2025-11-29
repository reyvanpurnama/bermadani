<?php

namespace App\Enums;

enum TransactionType: string
{
    case SALE = 'SALE';
    case PURCHASE = 'PURCHASE';
    case RETURN = 'RETURN';
    case INCOME = 'INCOME';
    case EXPENSE = 'EXPENSE';

    public function getLabel(): string
    {
        return match($this) {
            self::SALE => 'Penjualan',
            self::PURCHASE => 'Pembelian',
            self::RETURN => 'Retur',
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::SALE => 'success',
            self::PURCHASE => 'warning',
            self::RETURN => 'danger',
            self::INCOME => 'info',
            self::EXPENSE => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::SALE => 'heroicon-o-shopping-cart',
            self::PURCHASE => 'heroicon-o-shopping-bag',
            self::RETURN => 'heroicon-o-arrow-uturn-left',
            self::INCOME => 'heroicon-o-arrow-trending-up',
            self::EXPENSE => 'heroicon-o-arrow-trending-down',
        };
    }
}
