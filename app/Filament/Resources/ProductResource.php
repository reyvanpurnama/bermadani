<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('isActive', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStock = static::getModel()::whereColumn('stock', '<=', 'threshold')->count();
        return $lowStock > 0 ? 'danger' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->description('Data dasar produk')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Indomie Goreng')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU / Barcode')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: IND-001')
                            ->columnSpan(1),

                        Forms\Components\Select::make('categoryId')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('icon')
                                    ->default('📦'),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Select::make('unit')
                            ->label('Satuan')
                            ->options([
                                'pcs' => 'Pcs (Satuan)',
                                'pack' => 'Pack',
                                'box' => 'Box',
                                'kg' => 'Kilogram',
                                'liter' => 'Liter',
                                'meter' => 'Meter',
                                'lusin' => 'Lusin (12 pcs)',
                            ])
                            ->default('pcs')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi produk (opsional)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Harga & Stok')
                    ->description('Pengaturan harga dan inventori')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('buyPrice')
                            ->label('Harga Beli')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $buyPrice = (float) $get('buyPrice') ?: 0;
                                $margin = 20; // default margin 20%
                                if ($buyPrice > 0 && !$get('sellPrice')) {
                                    $set('sellPrice', round($buyPrice * (1 + $margin/100)));
                                }
                            }),

                        Forms\Components\TextInput::make('sellPrice')
                            ->label('Harga Jual')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->placeholder('0'),

                        Forms\Components\TextInput::make('avgCost')
                            ->label('HPP Rata-rata')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Dihitung otomatis dari pembelian'),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stok Saat Ini')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('threshold')
                            ->label('Batas Minimum')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->minValue(0)
                            ->helperText('Notifikasi saat stok di bawah angka ini'),

                        Forms\Components\Select::make('stockCycle')
                            ->label('Siklus Restock')
                            ->options([
                                'HARIAN' => 'Harian',
                                'MINGGUAN' => 'Mingguan',
                                'DUA_MINGGUAN' => 'Dua Mingguan',
                            ])
                            ->default('MINGGUAN')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Kepemilikan & Supplier')
                    ->description('Pengaturan konsinyasi dan supplier')
                    ->icon('heroicon-o-building-storefront')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('ownershipType')
                            ->label('Tipe Kepemilikan')
                            ->options([
                                'TOKO' => '🏪 Milik Toko',
                                'TITIPAN' => '📦 Titipan (Consignment)',
                                'SUPPLIER' => '🚚 Produk Supplier',
                            ])
                            ->default('TOKO')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $set('isConsignment', $state !== 'TOKO');
                                if ($state === 'TOKO') {
                                    $set('supplierId', null);
                                    $set('profitShareRate', 100);
                                }
                            }),

                        Forms\Components\Toggle::make('isConsignment')
                            ->label('Produk Konsinyasi')
                            ->helperText('Produk titipan dari supplier')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('supplierId')
                            ->label('Supplier')
                            ->relationship('supplier', 'businessName')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => $get('ownershipType') !== 'TOKO')
                            ->required(fn (Get $get) => $get('ownershipType') !== 'TOKO'),

                        Forms\Components\TextInput::make('profitShareRate')
                            ->label('Bagi Hasil Toko (%)')
                            ->numeric()
                            ->suffix('%')
                            ->default(90)
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (Get $get) => $get('ownershipType') !== 'TOKO')
                            ->helperText('Persentase keuntungan untuk toko'),

                        Forms\Components\TextInput::make('supplierContact')
                            ->label('Kontak Supplier')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('ownershipType') !== 'TOKO')
                            ->placeholder('Nomor HP atau email'),
                    ]),

                Forms\Components\Section::make('Status')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Produk')
                            ->options([
                                'ACTIVE' => '✅ Aktif',
                                'INACTIVE' => '❌ Tidak Aktif',
                                'SEASONAL' => '🌸 Seasonal',
                            ])
                            ->default('ACTIVE')
                            ->required(),

                        Forms\Components\Toggle::make('isActive')
                            ->label('Tampilkan di POS')
                            ->default(true)
                            ->helperText('Produk bisa dijual'),

                        Forms\Components\Textarea::make('expiryPolicy')
                            ->label('Kebijakan Expired')
                            ->rows(2)
                            ->placeholder('Contoh: 30 hari sebelum expired harus retur')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record) => $record->sku ? "SKU: {$record->sku}" : null),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sellPrice')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn (Product $record) => $record->isLowStock() ? 'danger' : 'success')
                    ->badge()
                    ->suffix(fn (Product $record) => " {$record->unit}"),

                Tables\Columns\TextColumn::make('ownershipType')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'TOKO' => '🏪 Toko',
                        'TITIPAN' => '📦 Titipan',
                        'SUPPLIER' => '🚚 Supplier',
                        default => $state,
                    })
                    ->color(fn (string $state) => match($state) {
                        'TOKO' => 'success',
                        'TITIPAN' => 'warning',
                        'SUPPLIER' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('supplier.businessName')
                    ->label('Supplier')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('isActive')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoryId')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('ownershipType')
                    ->label('Tipe Kepemilikan')
                    ->options([
                        'TOKO' => 'Milik Toko',
                        'TITIPAN' => 'Titipan',
                        'SUPPLIER' => 'Supplier',
                    ]),

                Tables\Filters\TernaryFilter::make('isActive')
                    ->label('Status Aktif'),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Stok Rendah')
                    ->query(fn (Builder $query) => $query->whereColumn('stock', '<=', 'threshold')),
            ])
            ->actions([
                Tables\Actions\Action::make('addStock')
                    ->label('+ Stok')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextInput::make('unitCost')
                            ->label('Harga Beli per Unit')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan'),
                    ])
                    ->action(function (Product $record, array $data) {
                        $record->addStock(
                            $data['quantity'],
                            'RESTOCK',
                            $data['note'] ?? null,
                            $data['unitCost'] ?? null
                        );
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['isActive' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['isActive' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
