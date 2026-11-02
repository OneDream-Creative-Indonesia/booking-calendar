<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddonResource\Pages;
use App\Models\Addon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Filament\Tables\Table;

class AddonResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_addon') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_addon') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_addon') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_addon') ?? false;
    }

    protected static ?string $model = Addon::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    
    protected static ?string $navigationGroup = 'Operational';
    protected static ?string $navigationLabel = 'Harga Add-ons';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Add-on'),
                
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->label('Harga Dasar'),
                
                // FIELD DISKON BARU DI MASTER ADD-ON
                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->label('Diskon Persentase'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Add-on')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Dasar')
                    ->money('IDR')
                    ->sortable(),

                // MENAMPILKAN DISKON DI TABEL
                Tables\Columns\TextColumn::make('discount')
                    ->label('Diskon')
                    ->formatStateUsing(fn ($state) => $state . ' %')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAddons::route('/'),
            'create' => Pages\CreateAddon::route('/create'),
            'edit' => Pages\EditAddon::route('/{record}/edit'),
        ];
    }
}