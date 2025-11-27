<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'POS';

    protected static ?string $title = 'Point of Sale';

    protected static ?string $navigationGroup = 'POS';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.point-of-sale';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['SUPER_ADMIN', 'ADMIN', 'KASIR', 'DEVELOPER']);
    }
}
