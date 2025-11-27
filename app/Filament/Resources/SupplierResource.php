<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'businessName';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'PENDING')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Bisnis')
                    ->description('Data supplier dan pemilik')
                    ->icon('heroicon-o-building-storefront')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Supplier')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('SUP-001')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('businessName')
                            ->label('Nama Bisnis')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('CV Maju Jaya'),

                        Forms\Components\TextInput::make('ownerName')
                            ->label('Nama Pemilik')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('productCategory')
                            ->label('Kategori Produk')
                            ->maxLength(255)
                            ->placeholder('Sembako, Minuman, dll'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Bisnis')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Kontak')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Akun & Keamanan')
                    ->icon('heroicon-o-lock-closed')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->helperText('Kosongkan jika tidak ingin mengubah password'),
                    ]),

                Forms\Components\Section::make('Pengaturan Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('monthlyFee')
                            ->label('Fee Bulanan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(25000),

                        Forms\Components\Select::make('preferredPaymentMethod')
                            ->label('Metode Pembayaran')
                            ->options([
                                'CASH' => '💵 Cash',
                                'TRANSFER' => '🏦 Transfer',
                                'CREDIT' => '💳 Kredit',
                            ])
                            ->default('TRANSFER'),

                        Forms\Components\TextInput::make('paymentTerms')
                            ->label('Syarat Pembayaran')
                            ->placeholder('Net 30'),

                        Forms\Components\Toggle::make('isPaymentActive')
                            ->label('Pembayaran Aktif')
                            ->default(false),

                        Forms\Components\Select::make('paymentStatus')
                            ->label('Status Bayar')
                            ->options([
                                'UNPAID' => '❌ Belum Bayar',
                                'PARTIAL' => '⏳ Sebagian',
                                'PAID' => '✅ Lunas',
                            ])
                            ->default('UNPAID'),

                        Forms\Components\TextInput::make('paymentGraceDays')
                            ->label('Grace Period (hari)')
                            ->numeric()
                            ->default(7),
                    ]),

                Forms\Components\Section::make('Limit & Status')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'PENDING' => '⏳ Pending Review',
                                'PENDING_REVIEW' => '🔍 Menunggu Review',
                                'APPROVED_PENDING_PAYMENT' => '✅ Approved - Tunggu Bayar',
                                'PAID_PENDING_APPROVAL' => '💰 Dibayar - Tunggu Konfirm',
                                'ACTIVE' => '🟢 Aktif',
                                'REJECTED' => '❌ Ditolak',
                                'SUSPENDED' => '🚫 Ditangguhkan',
                            ])
                            ->default('PENDING')
                            ->required(),

                        Forms\Components\TextInput::make('maxActiveProducts')
                            ->label('Max Produk')
                            ->numeric()
                            ->default(10),

                        Forms\Components\TextInput::make('currentActiveProducts')
                            ->label('Produk Aktif')
                            ->numeric()
                            ->default(0)
                            ->disabled(),

                        Forms\Components\Toggle::make('isActive')
                            ->label('Supplier Aktif')
                            ->default(false),

                        Forms\Components\Textarea::make('rejectedReason')
                            ->label('Alasan Penolakan')
                            ->rows(2)
                            ->columnSpan(2)
                            ->visible(fn ($get) => $get('status') === 'REJECTED'),
                    ]),

                Forms\Components\Section::make('Evaluasi Produk')
                    ->icon('heroicon-o-star')
                    ->columns(4)
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('productQualityScore')
                            ->label('Kualitas (1-5)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),

                        Forms\Components\TextInput::make('productPriceScore')
                            ->label('Harga (1-5)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),

                        Forms\Components\TextInput::make('productPackagingScore')
                            ->label('Packaging (1-5)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),

                        Forms\Components\TextInput::make('productAverageScore')
                            ->label('Rata-rata')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\Textarea::make('evaluationNotes')
                            ->label('Catatan Evaluasi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Catatan')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan Internal')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('businessName')
                    ->label('Bisnis')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Supplier $record) => $record->ownerName),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('productCategory')
                    ->label('Kategori')
                    ->badge()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Produk')
                    ->counts('products')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'PENDING', 'PENDING_REVIEW' => '⏳ Pending',
                        'APPROVED_PENDING_PAYMENT' => '💰 Tunggu Bayar',
                        'PAID_PENDING_APPROVAL' => '✅ Tunggu Konfirm',
                        'ACTIVE' => '🟢 Aktif',
                        'REJECTED' => '❌ Ditolak',
                        'SUSPENDED' => '🚫 Suspended',
                        default => $state,
                    })
                    ->color(fn (string $state) => match($state) {
                        'PENDING', 'PENDING_REVIEW' => 'warning',
                        'APPROVED_PENDING_PAYMENT', 'PAID_PENDING_APPROVAL' => 'info',
                        'ACTIVE' => 'success',
                        'REJECTED', 'SUSPENDED' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('productAverageScore')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => $state ? "⭐ {$state}" : '-')
                    ->sortable(),

                Tables\Columns\IconColumn::make('isActive')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'ACTIVE' => 'Aktif',
                        'SUSPENDED' => 'Suspended',
                        'REJECTED' => 'Ditolak',
                    ]),

                Tables\Filters\TernaryFilter::make('isActive')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (Supplier $record) => in_array($record->status, ['PENDING', 'PENDING_REVIEW']))
                        ->requiresConfirmation()
                        ->action(fn (Supplier $record) => $record->approve(Auth::id())),

                    Tables\Actions\Action::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn (Supplier $record) => $record->status === 'APPROVED_PENDING_PAYMENT')
                        ->requiresConfirmation()
                        ->action(fn (Supplier $record) => $record->activate()),

                    Tables\Actions\Action::make('suspend')
                        ->label('Suspend')
                        ->icon('heroicon-o-pause')
                        ->color('danger')
                        ->visible(fn (Supplier $record) => $record->status === 'ACTIVE')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Alasan')
                                ->required(),
                        ])
                        ->action(fn (Supplier $record, array $data) => $record->suspend($data['reason'])),

                    Tables\Actions\Action::make('evaluate')
                        ->label('Evaluasi')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('productQualityScore')
                                ->label('Kualitas (1-5)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(5)
                                ->required(),
                            Forms\Components\TextInput::make('productPriceScore')
                                ->label('Harga (1-5)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(5)
                                ->required(),
                            Forms\Components\TextInput::make('productPackagingScore')
                                ->label('Packaging (1-5)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(5)
                                ->required(),
                            Forms\Components\Textarea::make('evaluationNotes')
                                ->label('Catatan'),
                        ])
                        ->action(function (Supplier $record, array $data) {
                            $record->update([
                                'productQualityScore' => $data['productQualityScore'],
                                'productPriceScore' => $data['productPriceScore'],
                                'productPackagingScore' => $data['productPackagingScore'],
                                'evaluationNotes' => $data['evaluationNotes'] ?? null,
                                'evaluatedBy' => Auth::id(),
                                'evaluatedAt' => now(),
                            ]);
                            $record->calculateAverageScore();
                        }),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate_bulk')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->activate())
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'view' => Pages\ViewSupplier::route('/{record}'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
