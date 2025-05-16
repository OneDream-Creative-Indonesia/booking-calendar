<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
class PackageResource extends Resource
{
    protected static ?string $model = Package::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Paket Settings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->label('Nama Paket')
                ->required()
                ->unique(ignoreRecord: true),
            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3),
            TextInput::make('duration_minutes')
                ->label('Durasi (menit)')
                ->required()
                ->numeric(),
            TextInput::make('price')
                ->label('Harga (Rp)')
                ->required()
                ->numeric(),
            TextInput::make('extras')
                ->label('Bonus Tambahan')
                ->placeholder('Contoh: Cetak 4R, Album'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->label('Nama Paket'),
            Tables\Columns\TextColumn::make('price')  ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))->label('Harga'),
            Tables\Columns\TextColumn::make('duration_minutes')->label('Durasi (menit)'),
        ]) ->filters([
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
