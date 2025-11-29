<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class POSPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.pages.p-o-s-page';

    protected static ?string $navigationLabel = 'POS (Point of Sale)';

    protected static ?string $title = 'Point of Sale';

    protected static ?string $navigationGroup = 'POS';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Only ADMIN, KASIR, SUPER_ADMIN, and DEVELOPER can access POS
        return in_array($user->role, ['ADMIN', 'KASIR', 'SUPER_ADMIN', 'DEVELOPER']);
    }
}
