<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'POS';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('invoiceNumber')
                            ->label('Invoice Number')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('date')
                            ->label('Transaction Date')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('memberId')
                            ->label('Member')
                            ->relationship('member', 'name')
                            ->searchable()
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'SALE' => 'Penjualan',
                                'PURCHASE' => 'Pembelian',
                                'RETURN' => 'Retur',
                                'INCOME' => 'Pemasukan',
                                'EXPENSE' => 'Pengeluaran',
                            ])
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('paymentMethod')
                            ->label('Payment Method')
                            ->options([
                                'CASH' => '💵 Tunai',
                                'TRANSFER' => '🏦 Transfer',
                                'CREDIT' => '💳 Kredit',
                            ])
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'PENDING' => 'Pending',
                                'COMPLETED' => 'Selesai',
                                'CANCELLED' => 'Dibatalkan',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('totalAmount')
                            ->label('Total Amount')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('note')
                            ->label('Notes')
                            ->rows(2)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoiceNumber')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('member.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SALE' => 'success',
                        'PURCHASE' => 'warning',
                        'RETURN' => 'danger',
                        'INCOME' => 'info',
                        'EXPENSE' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'SALE' => 'Penjualan',
                        'PURCHASE' => 'Pembelian',
                        'RETURN' => 'Retur',
                        'INCOME' => 'Pemasukan',
                        'EXPENSE' => 'Pengeluaran',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('paymentMethod')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CASH' => 'success',
                        'TRANSFER' => 'info',
                        'CREDIT' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'CASH' => '💵 Tunai',
                        'TRANSFER' => '🏦 Transfer',
                        'CREDIT' => '💳 Kredit',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('totalAmount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'COMPLETED' => 'success',
                        'CANCELLED' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PENDING' => 'Pending',
                        'COMPLETED' => 'Selesai',
                        'CANCELLED' => 'Dibatalkan',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'SALE' => 'Penjualan',
                        'PURCHASE' => 'Pembelian',
                        'RETURN' => 'Retur',
                        'INCOME' => 'Pemasukan',
                        'EXPENSE' => 'Pengeluaran',
                    ]),

                SelectFilter::make('paymentMethod')
                    ->label('Metode Pembayaran')
                    ->options([
                        'CASH' => 'Tunai',
                        'TRANSFER' => 'Transfer',
                        'CREDIT' => 'Kredit',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'PENDING' => 'Pending',
                        'COMPLETED' => 'Selesai',
                        'CANCELLED' => 'Dibatalkan',
                    ]),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->whereDate('date', today())),

                Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(fn (Builder $query) => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])),

                Filter::make('this_month')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query) => $query->whereMonth('date', now()->month)->whereYear('date', now()->year)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Transaction $record): string => route('transaction.receipt', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Disable delete for now
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }
}
