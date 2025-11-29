<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListTransactions extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('go_to_pos')
                ->label('Buka POS')
                ->icon('heroicon-o-shopping-cart')
                ->color('success')
                ->url(fn (): string => route('filament.admin.pages.p-o-s-page')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return TransactionResource::getWidgets();
    }
}
