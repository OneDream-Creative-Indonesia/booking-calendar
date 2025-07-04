<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;


class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 TextInput::make('code_voucher')
                ->label('Code Voucher')
                ->required()
                ->unique(ignoreRecord: true),
                 TextInput::make('discount_percent')
                ->label('Potongan Harga(%)')
                ->suffix('%')
                ->required()
                ->minValue(0)
                ->maxValue(100)
                ->numeric()
                ->currencyMask(thousandSeparator: ',',decimalSeparator: '.',precision: 2),
                 DatePicker::make('start_date')
                ->label('Start Voucher')
                ->required(),
                 DatePicker::make('end_date')
                 ->label('End Voucher')
                 ->required(),
                 TextInput::make('usage_limit')
                 ->label('Limit Penggunaan')
                 ->required()
                 ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 \Filament\Tables\Columns\TextColumn::make('code_voucher')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Potongan(%)'),

                \Filament\Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date(),

                \Filament\Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->date(),

                \Filament\Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Batas'),

                \Filament\Tables\Columns\TextColumn::make('used_count')
                    ->label('Dipakai'),

                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
