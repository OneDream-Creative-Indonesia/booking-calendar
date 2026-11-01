<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Addon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\DateRangeFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'List Booking';
    protected static ?string $navigationGroup = 'Studio';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->required()
                    ->maxLength(15),

                Forms\Components\TextInput::make('people_count')
                    ->label('Jumlah Orang')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),

                Forms\Components\TimePicker::make('time')
                    ->label('Waktu')
                    ->required(),

                Forms\Components\Select::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'title')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('voucher_id')
                    ->label('Code Voucher')
                    ->relationship('voucher', 'code_voucher')
                    ->searchable(),

                Forms\Components\Select::make('background_id')
                    ->label('Nama Background')
                    ->relationship('background', 'name')
                    ->searchable(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Harga Paket Utama')
                    ->numeric()
                    ->prefix('Rp')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotalPrice($get, $set);
                    }),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->required(),
                
                Forms\Components\Toggle::make('confirmation')
                    ->label('Confirmed'),

                Forms\Components\Repeater::make('bookingAddons')
                    ->relationship()
                    ->label('Tambahan (Add-ons / Keychain)')
                    ->schema([
                        Forms\Components\Select::make('addon_id')
                            ->label('Item Add-on')
                            ->options(fn () => Addon::pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                // Trik sinkronisasi: Ambil data master saat item dipilih
                                $addon = Addon::find($state);
                                if ($addon) {
                                    $set('discount', $addon->discount ?? 0);
                                }
                                self::calculateAddonSubtotal($set, $get);
                            }),

                        Forms\Components\TextInput::make('qty')
                            ->label('Jumlah')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::calculateAddonSubtotal($set, $get);
                            }),

                        Forms\Components\TextInput::make('discount')
                            ->label('Diskon (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::calculateAddonSubtotal($set, $get);
                            }),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->required(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotalPrice($get, $set);
                    }),

                Forms\Components\TextInput::make('total_price')
                    ->label('Total Harga Keseluruhan')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function calculateAddonSubtotal(Set $set, Get $get): void
    {
        $price = Addon::find($get('addon_id'))?->price ?? 0;
        $qty = (int) ($get('qty') ?: 1);
        $discount = (float) ($get('discount') ?: 0);
        
        $totalAwal = $price * $qty;
        $potongan = $totalAwal * ($discount / 100);
        
        $set('subtotal', $totalAwal - $potongan);
    }

    public static function updateTotalPrice(Get $get, Set $set): void
    {
        $basePrice = (int) $get('price') ?: 0;
        $addonsTotal = collect($get('bookingAddons'))->sum('subtotal');
        $set('total_price', $basePrice + $addonsTotal);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable()->label('Nama'),
                TextColumn::make('package.title')->label('Paket')->sortable()->searchable(),
                TextColumn::make('background.name')->sortable()->searchable()->label('Background')
                    ->getStateUsing(fn ($record) => $record->background?->name ?? '-'),
                TextColumn::make('people_count')->sortable()->label('Jumlah Orang'),
                TextColumn::make('date')->sortable()->label('Tanggal')->date('d M Y'),
                TextColumn::make('time')->sortable()->label('Jam')->time('H:i'),
                
                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_addon_qty')
                    ->label('Select Add (Qty)')
                    ->getStateUsing(function (Booking $record) {
                        $qty = $record->bookingAddons->sum('qty');
                        return $qty > 0 ? $qty . ' Item (Edit)' : '+ Tambah Add-on';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === '+ Tambah Add-on' ? 'gray' : 'success')
                    ->action(
                        Tables\Actions\Action::make('manage_addons')
                            ->modalHeading('Kelola Tambahan (Add-ons)')
                            ->modalDescription('Tambahkan berbagai item add-on beserta diskonnya.')
                            ->modalWidth('5xl')
                            ->form([
                                Forms\Components\Repeater::make('bookingAddons')
                                    ->relationship()
                                    ->label('')
                                    ->schema([
                                        Forms\Components\Select::make('addon_id')
                                            ->label('Item Add-on')
                                            ->options(fn () => Addon::pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                // Sinkronisasi diskon juga diterapkan di pop-up modal
                                                $addon = Addon::find($state);
                                                if ($addon) {
                                                    $set('discount', $addon->discount ?? 0);
                                                }
                                                self::calculateAddonSubtotal($set, $get);
                                            }),

                                        Forms\Components\TextInput::make('qty')
                                            ->label('Jumlah')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                self::calculateAddonSubtotal($set, $get);
                                            }),

                                        Forms\Components\TextInput::make('discount')
                                            ->label('Diskon (%)')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                self::calculateAddonSubtotal($set, $get);
                                            }),

                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal Harga')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->readOnly()
                                            ->required(),
                                    ])
                                    ->columns(4)
                                    ->addActionLabel('+ Tambah Item Lain')
                            ])
                    ),

                TextColumn::make('tambahan_harga')
                    ->label('Tambahan Harga')
                    ->getStateUsing(fn ($record) => 'Rp ' . number_format($record->bookingAddons->sum('subtotal'), 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('voucher.code_voucher')
                    ->sortable()
                    ->searchable()
                    ->label('Voucher')
                    ->getStateUsing(fn ($record) => $record->voucher?->code_voucher ?? '-'),

                TextColumn::make('total_harga_semua')
                    ->label('Total Harga')
                    ->getStateUsing(fn ($record) => 'Rp ' . number_format($record->price + $record->bookingAddons->sum('subtotal'), 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('whatsapp')->sortable()->searchable()->label('WhatsApp')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->sortable()->searchable()->label('Email')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBookings::route('/'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}