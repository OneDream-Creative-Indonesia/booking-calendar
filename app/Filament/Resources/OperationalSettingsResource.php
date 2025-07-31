<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperationalSettingsResource\Pages;
use App\Filament\Resources\OperationalSettingsResource\RelationManagers;
use App\Models\OperationalHour;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperationalSettingsResource extends Resource
{
    protected static ?string $model = OperationalHour::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Jam Operasional';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day')
                ->label('Hari')
                ->options([
                    'Senin' => 'Senin',
                    'Selasa' => 'Selasa',
                    'Rabu' => 'Rabu',
                    'Kamis' => 'Kamis',
                    'Jumat' => 'Jumat',
                    'Sabtu' => 'Sabtu',
                    'Minggu' => 'Minggu',
                ])
                ->required(),
                Forms\Components\TimePicker::make('open_time')->visible(fn (callable $get) => $get('is_open')),
                Forms\Components\TimePicker::make('close_time')->visible(fn (callable $get) => $get('is_open')),
                Forms\Components\Toggle::make('is_open')->label('Buka?')->default(false)->live()->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day')->label('Hari'),
                Tables\Columns\TextColumn::make('open_time')->label('Jam Buka')
                  ->formatStateUsing(function ($state) {
                    return $state ? \Carbon\Carbon::parse($state)->format('H:i') : '-';
                }),
                Tables\Columns\TextColumn::make('close_time')->label('Jam Tutup')
                ->formatStateUsing(function ($state) {
                    return $state ? \Carbon\Carbon::parse($state)->format('H:i') : '-';
                }),
                Tables\Columns\BooleanColumn::make('is_open')->label('Buka?'),
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
            'index' => Pages\ListOperationalSettings::route('/'),
            'create' => Pages\CreateOperationalSettings::route('/create'),
            'edit' => Pages\EditOperationalSettings::route('/{record}/edit'),
        ];
    }
}
