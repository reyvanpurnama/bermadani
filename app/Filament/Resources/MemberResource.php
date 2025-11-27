<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Membership';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Members';
    protected static ?string $modelLabel = 'Member';
    protected static ?string $pluralModelLabel = 'Members';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('nomorAnggota')
                            ->label('Member Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\Select::make('gender')
                            ->options([
                                'MALE' => 'Male',
                                'FEMALE' => 'Female',
                            ])
                            ->required(),
                        
                        Forms\Components\Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Membership Details')
                    ->schema([
                        Forms\Components\Select::make('userId')
                            ->label('User Account')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\TextInput::make('unitKerja')
                            ->label('Work Unit')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('joinDate')
                            ->label('Join Date')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'ACTIVE' => 'Active',
                                'INACTIVE' => 'Inactive',
                                'SUSPENDED' => 'Suspended',
                            ])
                            ->default('ACTIVE')
                            ->required(),
                        
                        Forms\Components\Toggle::make('isMemberKoperasi')
                            ->label('Koperasi Member')
                            ->default(true),
                        
                        Forms\Components\Select::make('tier')
                            ->options([
                                'BRONZE' => 'Bronze',
                                'SILVER' => 'Silver',
                                'GOLD' => 'Gold',
                                'PLATINUM' => 'Platinum',
                            ])
                            ->default('BRONZE'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\TextInput::make('simpananPokok')
                            ->label('Simpanan Pokok')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->required(),
                        
                        Forms\Components\TextInput::make('simpananWajib')
                            ->label('Simpanan Wajib')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->required(),
                        
                        Forms\Components\TextInput::make('simpananSukarela')
                            ->label('Simpanan Sukarela')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->required(),
                        
                        Forms\Components\TextInput::make('totalSpent')
                            ->label('Total Spent')
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('points')
                            ->label('Loyalty Points')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('lastPurchase')
                            ->label('Last Purchase')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomorAnggota')
                    ->label('Member #')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('unitKerja')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'ACTIVE',
                        'warning' => 'INACTIVE',
                        'danger' => 'SUSPENDED',
                    ]),
                
                Tables\Columns\BadgeColumn::make('tier')
                    ->colors([
                        'primary' => 'PLATINUM',
                        'warning' => 'GOLD',
                        'info' => 'SILVER',
                        'secondary' => 'BRONZE',
                    ]),
                
                Tables\Columns\TextColumn::make('totalSimpanan')
                    ->label('Total Savings')
                    ->money('IDR')
                    ->sortable()
                    ->getStateUsing(fn ($record) => 
                        $record->simpananPokok + $record->simpananWajib + $record->simpananSukarela
                    ),
                
                Tables\Columns\TextColumn::make('points')
                    ->label('Points')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('isMemberKoperasi')
                    ->label('Koperasi')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('joinDate')
                    ->label('Join Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('lastPurchase')
                    ->label('Last Purchase')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'INACTIVE' => 'Inactive',
                        'SUSPENDED' => 'Suspended',
                    ]),
                
                Tables\Filters\SelectFilter::make('tier')
                    ->options([
                        'BRONZE' => 'Bronze',
                        'SILVER' => 'Silver',
                        'GOLD' => 'Gold',
                        'PLATINUM' => 'Platinum',
                    ]),
                
                Tables\Filters\SelectFilter::make('unitKerja')
                    ->label('Work Unit')
                    ->options(function () {
                        return Member::query()
                            ->pluck('unitKerja', 'unitKerja')
                            ->unique()
                            ->sort();
                    }),
                
                Tables\Filters\TernaryFilter::make('isMemberKoperasi')
                    ->label('Koperasi Member')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('addSimpanan')
                    ->label('Add Savings')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label('Saving Type')
                            ->options([
                                'POKOK' => 'Simpanan Pokok',
                                'WAJIB' => 'Simpanan Wajib',
                                'SUKARELA' => 'Simpanan Sukarela',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])
                    ->action(function (Member $record, array $data) {
                        $record->addSimpanan(
                            $data['type'],
                            $data['amount'],
                            $data['description'] ?? null
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Savings added successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('joinDate', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Personal Information')
                    ->schema([
                        Components\TextEntry::make('nomorAnggota')
                            ->label('Member Number'),
                        Components\TextEntry::make('name'),
                        Components\TextEntry::make('email'),
                        Components\TextEntry::make('phone'),
                        Components\TextEntry::make('gender')
                            ->badge(),
                        Components\TextEntry::make('address'),
                    ])
                    ->columns(2),

                Components\Section::make('Membership')
                    ->schema([
                        Components\TextEntry::make('user.name')
                            ->label('User Account'),
                        Components\TextEntry::make('unitKerja')
                            ->label('Work Unit'),
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn ($record) => $record->statusBadge),
                        Components\TextEntry::make('tier')
                            ->badge()
                            ->color(fn ($record) => $record->tierBadge),
                        Components\IconEntry::make('isMemberKoperasi')
                            ->label('Koperasi Member')
                            ->boolean(),
                        Components\TextEntry::make('joinDate')
                            ->date(),
                    ])
                    ->columns(3),

                Components\Section::make('Financial Summary')
                    ->schema([
                        Components\TextEntry::make('simpananPokok')
                            ->label('Simpanan Pokok')
                            ->money('IDR'),
                        Components\TextEntry::make('simpananWajib')
                            ->label('Simpanan Wajib')
                            ->money('IDR'),
                        Components\TextEntry::make('simpananSukarela')
                            ->label('Simpanan Sukarela')
                            ->money('IDR'),
                        Components\TextEntry::make('totalSimpanan')
                            ->label('Total Savings')
                            ->money('IDR')
                            ->state(fn ($record) => 
                                $record->simpananPokok + $record->simpananWajib + $record->simpananSukarela
                            ),
                        Components\TextEntry::make('totalSpent')
                            ->money('IDR'),
                        Components\TextEntry::make('points')
                            ->numeric()
                            ->suffix(' pts'),
                    ])
                    ->columns(3),

                Components\Section::make('Activity')
                    ->schema([
                        Components\TextEntry::make('lastPurchase')
                            ->dateTime()
                            ->placeholder('No purchases yet'),
                        Components\TextEntry::make('transactions_count')
                            ->label('Total Transactions')
                            ->state(fn ($record) => $record->transactions()->count()),
                        Components\TextEntry::make('loans_count')
                            ->label('Active Loans')
                            ->state(fn ($record) => $record->loans()->where('status', 'ACTIVE')->count()),
                    ])
                    ->columns(3),
            ]);
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'ACTIVE')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
